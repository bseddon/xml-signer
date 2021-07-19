<?php

/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * MIT License 
 * 
 * Allows a document to be signed using one of the XAdES forms up to XAdES-T.
 * https://www.w3.org/TR/XAdES/
 * 
 * Builds on XMLSecurityDSig to sign and verify Xml documents using the XAdES 
 * extensions which take it into the domain of non-repudiation by defining XML 
 * formats for advanced electronic signatures that remain valid over long periods 
 * and are compliant with the European "Directive 1999/93/EC.
 * 
 * The XAdES support distinguishes two types of signing action:
 * 	1) The creation of an initial signature complete will policy and commitment details
 * 	2) Updates to add features like a timestamp and counter signatures.
 * 
 * If a document is signed second and subsequent times and a set XAdES signed properties
 * already exist then an additional individual signature covering the existing properties.
 * 
 * If the action is to update an existing signature then second and subsequent signatures
 * will be counter-signatures. In this case the signer is able to select the unique id
 * of the signature their counter-signature will cover.
 * 
 * The caller has two ways to create the properties to be signed in the first place.
 * The first is to call a set of functions.  The other is similar in style to xadesjs
 * where a nested array of properties can be passed to the sign function and these
 * properties will drive the process of creating the XAdES <SignedProperties> and
 * XmlDSig <Signature> elements.
 */

namespace lyquidity\xmldsig;

/**
 */
class XAdES extends XMLSecurityDSig
{
	/**
	 * Namespace defined in ETSI 319 132-1 V1.1.0 (2016-02)
	 */
	const NamespaceUrl2016 = "http://uri.etsi.org/01903/v1.3.2#";
	const NamespaceUrl2003 = "http://uri.etsi.org/01903/v1.1.1#";

	const counterSignatureTypeUrl = "http://uri.etsi.org/01903#CountersignedSignature";

	// Xades specification requires "http://uri.etsi.org/01903/v1.1.1#SignedProperties" but the receiving party currently does not accept this value
	const ReferenceType = "http://uri.etsi.org/01903#SignedProperties";
	const SignedPropertiesId = "signed-properties";
	const UnsignedPropertiesId = "unsigned-properties";
	const SignatureRootId = "signature-root";

	// All the XPath queries assume ds=XMLSecurityDSig::XMLDSIGNS and xa=self::NamespaceUrl
	const qualifyingPropertiesQuery = "/ds:Signature/ds:Object/*[local-name() = 'QualifyingProperties']";
	const signedPropertiesQuery = "/ds:Signature/ds:Object/xa:QualifyingProperties/xa:SignedProperties[@Id=\"" . self::SignedPropertiesId . "\"]";
	const objRefQuery = "./xa:SignedDataObjectProperties/xa:DataObjectFormat[@ObjectReference]/@ObjectReference";
	const unsignedPropertiesQuery = "/ds:Signature/ds:Object/xa:QualifyingProperties/xa:unsignedProperties[@Id=\"" . self::UnsignedPropertiesId . "\"]";

	// Certificate queries
	/**
	 * This query assumes the query context will be <SignedProperties>
	 * @var string 
	 */
	const certQuery = "./xa:SignedSignatureProperties/xa:SigningCertificate/xa:Cert";
	const serialNumberQuery = self::certQuery . "/xa:IssuerSerial/ds:X509SerialNumber";
	const issuerQuery = self::certQuery . "/xa:IssuerSerial/ds:X509IssuerName";
	const issuerSerialV2Query = self::certQuery . "/xa:IssuerSerialV2";

	// Policy queries
	/**
	 * This query assumes the query context will be <SignedProperties>
	 * @var string 
	 */
	const sigPolicyIdentifierQuery = "./xa:SignedSignatureProperties/xa:SignaturePolicyIdentifier";
	const sigPolicyIdQuery = self::sigPolicyIdentifierQuery . "/xa:SignaturePolicyId";
	const policyIdentifierQuery = self::sigPolicyIdQuery . "/xa:SigPolicyId/xa:Identifier";
	const policyDigestQuery = self::sigPolicyIdQuery . "/xa:SigPolicyHash/ds:DigestValue";
	const policyMethodQuery = self::sigPolicyIdQuery . "/xa:SigPolicyHash/ds:DigestMethod/@Algorithm";
	const policyImpliedQuery = self::sigPolicyIdentifierQuery . "/xa:SignaturePolicyImplied";

	const CommitmentTypeIdentifierQuery = "./xa:SignedDataObjectProperties/xa:CommitmentTypeIndication/xa:CommitmentTypeId/xa:Identifier";

	// Countersignature query
	const counterSignatureQuery = self::unsignedPropertiesQuery . "/";

	private $currentNamespace = self::NamespaceUrl2016;

	/**
	 * Extends the core XmlDSig verification to also verify <Object/QualifyingProperties/SignedProperties>
	 *
	 * @param string $signatureFile This might be a standalone signature file
	 * @param string $certificateFile (optional) If provided it is an absolute path to the relevant .crt file or a path relative to the signature file
	 * @return bool
	 */
	function verifyXAdES( $signatureFile, $certificateFile = null )
	{
		if ( ! file_exists( $signatureFile ) )
		{
			echo "Signature file does not exist";
			return false;
		}

		try
		{
			// Load the XML to be signed
			$signatureDoc = new \DOMDocument();
			$signatureDoc->load( $signatureFile );

			// Assume this is true for now
			$dataDoc = null;

			$xpath = new \DOMXPath( $signatureDoc );
			$xpath->registerNamespace( 'ds', XMLSecurityDSig::XMLDSIGNS );

			// Get the namespace of the qualified properties as the namespace determines some of the elements to expect
			$qualifiedProperties = $xpath->query( self::qualifyingPropertiesQuery );
			$this->currentNamespace = $qualifiedProperties[0]->namespaceURI;

			$xpath->registerNamespace( 'xa', $this->currentNamespace );

			// This is the base node for most queries
			$signedProperties = $xpath->evaluate( self::signedPropertiesQuery );

			if ( count( $signedProperties ) )
			{
				$objRef = $xpath->evaluate( self::objRefQuery, $signedProperties[0] );

				if ( count( $objRef ) )
				{
					// The should be an external file.  Look for it in the <Reference> with @Id $objRef
					$fileQuery = "/ds:Signature/ds:SignedInfo/ds:Reference[@Id=\"" . ltrim( $objRef[0]->value, '#' ) . "\"]/@URI";
					$fileRef = $xpath->evaluate( $fileQuery );

					if ( ! count( $fileRef ) )
						throw new \Exception("The object reference file '$objRef' cannot be located within the signature.");

					// Create a uri to the file. For some reason PHP reports 'file:/...' not 
					// 'file://...' for the document URI which is invalid so needs fixing
					$dataFile = self::resolve_path( preg_replace( '!file:/([a-z]:)!i', "file://$1", $signatureDoc->documentURI ), urldecode( $fileRef[0]->value ) );

					// There is an external file
					$dataDoc = new \DOMDocument();
					$dataDoc->load( $dataFile );
				}
			}

			// Create a new Security object
			// $XAdES  = new XAdES();

			$objDSig = $this->locateSignature( $signatureDoc );
			if ( ! $objDSig )
			{
				throw new \Exception("Cannot locate Signature Node");
			}
			$this->canonicalizeSignedInfo();
			
			$return = $this->validateReference( $dataDoc? $dataDoc->documentElement : null );

			if (! $return) {
				throw new \Exception("Reference Validation Failed");
			}
			
			$objKey = $this->locateKey();
			if ( ! $objKey ) 
			{
				throw new \Exception("We have no idea about the key");
			}
			$key = NULL;
			
			$objKeyInfo = XMLSecEnc::staticLocateKeyInfo( $objKey, $objDSig );

			if ( ! $objKeyInfo->key && empty( $key ) && $certificateFile ) 
			{
				// Load the certificate
				$certificateFile = self::resolve_path( $signatureDoc->documentURI, $certificateFile );
				if ( ! file_exists( $certificateFile ) )
				{
					throw new \Exception( "Certificate file does not exist" );
				}
				$objKey->loadKey( $certificateFile, true );
			}

			if ( $this->verify( $objKey ) === 1 )
			{
				echo "XAdES signature validated!\n";
			} 
			else
			{
				throw new \Exception( "The XAdES signature is not valid: it may have been tampered with." );
			}

			// Grab the serial number from the certificate used to compare it with the number stored in the signed properties
			$certificateData = $objKeyInfo->getCertificateData();
			$serialNumber = $certificateData['serialNumber' ];

			// Get the serial number from the signed properties
			$serialNumberElement = $xpath->query( self::serialNumberQuery, $signedProperties[0] );
			if ( ! count( $serialNumberElement ) )
			{
				if ( $this->currentNamespace == self::NamespaceUrl2003 )
					throw new \Exception('The certificate serial number does not exist in the signature');

				if ( $serialNumber != $serialNumberElement[0]->textContent )
				{
					throw new \Exception('The certificate serial number in the signature does not match the certificate serial number');
				}
			}

			// If version 1.3.2 then there should be <IssuerSerialV2>
			$issuerSerialElement = $xpath->query( self::issuerSerialV2Query, $signedProperties[0] );

			// Grab the issuer from the certificate used to compare it with the number stored in the signed properties
			/** @var string[] $issuer */
			$issuer = $certificateData['issuer'];

			$issuerElement = $xpath->query( self::issuerQuery, $signedProperties[0] );
			if ( ! count( $issuerElement ) )
			{
				throw new \Exception('The certificate issuer does not exist in the signature');
			}

			if ( ! \lyquidity\OCSP\CertificateInfo::compareIssuerStrings( $issuerElement[0]->textContent, $issuer ) )
			{
				throw new \Exception('The certificate issuer in the signature does not match the certificate issuer number');
			}

			// Look for a signature policy and validate.  Will either return or throw an error.
			$this->validateSignaturePolicy( $signedProperties[0], $xpath );

			echo "\n";
		}
		catch( \Exception $ex )
		{
			print $ex->getMessage();
		}

	}

	/**
	 * Find and validate any signature policy
	 * @param \DOMElement $signedProperties
	 * @param \DOMXPath $xpath
	 * @throws \Exception If no policy is found
	 */
	function validateSignaturePolicy( $signedProperties, $xpath )
	{
		// If the policy implied?  That is there is no policy document and instead some other agreed means to validate the properties
		$policyImplied = $xpath->query( self::policyImpliedQuery, $signedProperties );
		if ( count( $policyImplied ) )
		{
			$this->validateImpliedPolicy( $signedProperties, $xpath );
			return;
		}

		// If there is a policy and a policy hash
		$policyIdentifier = $xpath->query( self::policyIdentifierQuery, $signedProperties );
		if ( ! count( $policyIdentifier ) )
		{
			throw new \Exception('A signature policy element is expected but is not in the signature document.');
		}

		$policyIdentifier = $policyIdentifier[0]->textContent;

		// Is there a digest for the policy document?
		$policyDigest = $xpath->query( self::policyDigestQuery, $signedProperties );
		if ( count( $policyDigest ) )
		{
			$policyDigest = $policyDigest[0]->textContent;

			// Gat the hash method
			$policymethod = $xpath->query( self::policyMethodQuery, $signedProperties );
			$policymethod = count( $policymethod ) ? $policymethod[0]->textContent : XMLSecurityDSig::SHA256;

			$xml = file_get_contents( $this->getPolicyDocument( $policyIdentifier ) );
			$policyDoc = new \DOMDocument();
			$policyDoc->loadXML( $xml );
		
			// Create a new Security object 
			// $objXMLSecDSig  = new XMLSecurityDSig();
			$output = $this->processTransforms( $policyDoc->documentElement, $policyDoc->documentElement, false );
			$digest = $this->calculateDigest( $policymethod, $output );
			
			if ( ! $policyDigest == $digest )
			{
				throw new \Exception('The digest generated from the policy document does not match the digest contained in the poliocy document');
			}
		}

		$this->validateExplicitPolicy( $signedProperties, $xpath, $policyDoc );
	}

	/**
	 * Its expected this will be overridden in a descendent class
	 * @var string $policyIdentifier
	 * @return string A path or URL to the policy document
	 */
	public function getPolicyDocument( $policyIdentifier )
	{
		return null;
	}

	/**
	 * A descendent can provide a method to validate the signature properties when the policy is implied
	 *
	 * @param \DOMElement $signedProperties
	 * @param \DOMXPath $xpath
	 * @return void
	 */
	public function validateImpliedPolicy( $signedProperties, $xpath )
	{
		// Do nothing
	}

	/**
	 * Overridden by a descendent to check the policy rules are met in the signature
	 *
	 * @param \DOMElement $signedProperties
	 * @param \DOMXPath $sigDocXPath
	 * @param \DOMElement $policyDocument
	 * @return void
	 * @throws \Exception If the signature does not meet the policy rules
	 */
	public function validateExplicitPolicy( $signedProperties, $sigDocXPath, $policyDocument )
	{
		// Do nothing
	}

	/**
	 * Used to compute an absolute path for a resource ($target) with respect to a source.
	 * For example, the presentation linkbase file will be specified as relative to the
	 * location of the host schema.
	 * @param string $source The resource for the source
	 * @param string $target The resource for the target
	 * @return string
	 */
	public static function resolve_path( $source, $target )
	{
		// $target = urldecode( $target );

		$source = str_replace( '\\', '/', $source );
		// Remove any // instances as they confuse the path normalizer but take care to
		// not to remove ://
		$offset = 0;
		while ( true )
		{
			$pos = strpos( $source, "//", $offset );
			if ( $pos === false ) break;
			$offset = $pos + 2;
			// Ignore :// (eg https://)
			if ( $pos > 0 && $source[ $pos-1 ] == ":" ) continue;
			$source = str_replace( "//", "/", $source );
			$offset--;
		}

		// Using the extension to determine if the source is a file or directory reference is problematic unless it is always terminated with a /
		// This is because the source directory path may include a period such as x:/myroot/some.dir-in-a-path/
		$source = self::endsWith( $source, '/' ) || pathinfo( $source, PATHINFO_EXTENSION ) === "" //  || is_dir( $source )
			? $source
			: pathinfo( $source, PATHINFO_DIRNAME );

		$sourceIsUrl = filter_var( rawurlencode( $source ), FILTER_VALIDATE_URL );
		$targetIsUrl = filter_var( rawurlencode( $target ), FILTER_VALIDATE_URL );

		// Absolute
		if ( $target && ( filter_var( $target, FILTER_VALIDATE_URL ) || ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' && strlen( $target ) > 1 && ( $target[1] === ':' || substr( $target, 0, 2 ) === '\\\\' ) ) ) )
			$path = $target;

		// Relative to root
		elseif ( $target && ( $target[0] === '/' || $target[0] === '\\' ) )
		{
			$root = self::get_schema_root( $source );
			$path = $root . $target;
		}
		// Relative to source
		else
		{
			if ( self::endsWith( $source, ":" ) ) $source .= "/";
			$path =  $source . ( substr( $source, -1 ) == '/' ? '' : '/' ) . $target;
		}

		// Process the components
		// BMS 2018-06-06 By ignoring a leading slash the effect is to create relative paths on linux
		//				  However, its been done to handle http://xxx sources.  But this is not necessary (see below)
		$parts = explode( '/', $path );
		$safe = array();
		foreach ( $parts as $idx => $part )
		{
			// if ( empty( $part ) || ( '.' === $part ) )
			if ( '.' === $part )
			{
				continue;
			}
			elseif ( '..' === $part )
			{
				array_pop( $safe );
				continue;
			}
			else
			{
				$safe[] = $part;
			}
		}

		// BMS 2108-06-06 See above
		return implode( '/', $safe );

		// Return the "clean" path
		return $sourceIsUrl || $targetIsUrl
			? str_replace( ':/', '://', implode( '/', $safe ) )
			: implode( '/', $safe );
	}

	/**
	 * Find out if $haystack ends with $needle
	 * @param string $haystack
	 * @param string $needle
	 * @return boolean
	 */
	public static function endsWith( $haystack, $needle )
	{
		$strlen = strlen( $haystack );
		$testlen = strlen( $needle );
		if ( $testlen > $strlen ) return false;
		return substr_compare( $haystack, $needle, $strlen - $testlen, $testlen ) === 0;
	}

	/**
	 * Used by resolve_path to obtain the root element of a uri or file path.
	 * This is necessary because a schema or linkbase uri may be absolute but without a host.
	 *
	 * @param string The file
	 * @return string The root
	 */
	private static function get_schema_root( $file )
	{
		if ( filter_var( $file, FILTER_VALIDATE_URL ) === false )
		{
			// my else codes goes
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
			{
				// First case is c:\
				if ( strlen( $file ) > 1 && substr( $file, 1, 1 ) === ":" )
					$root = "{$file[0]}:";
				// Second case is a volume
				elseif ( strlen( $file ) > 1 && substr( $file, 0, 2 ) === "\\\\" )
				{
					$pos = strpos( $file, '\\', 2 );

					if ( $pos === false )
						$root = $file;
					else
						$root = substr( $file, 0, $pos );
				}
				// The catch all is that no root is provided
				else
					$root = pathinfo( $file, PATHINFO_EXTENSION ) === ""
						? $file
						: pathinfo( $file, PATHINFO_DIRNAME );
			}
		}
		else
		{
			$components = parse_url( $file );
			$root = "{$components['scheme']}://{$components['host']}";
		}

		return $root;
	}
}