<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\Asn1\Der\Encoder;
use \lyquidity\Asn1\Element\Sequence;
use lyquidity\OCSP\CertificateInfo;
use lyquidity\OCSP\Ocsp;
use lyquidity\xmldsig\XMLSecurityDSig;

/**
 * 
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<xsd:element name="SigningCertificateV2" type="CertIDListV2Type"/>
 *
 *	<xsd:complexType name="CertIDListV2Type">
 *		<xsd:sequence>
 *			<xsd:element name="Cert" type="CertIDTypeV2" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="CertIDTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *			<xsd:element name="IssuerSerialV2" type="xsd:base64Binary" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="DigestAlgAndValueType">
 *		<xsd:sequence>
 *			<xsd:element ref="ds:DigestMethod"/>
 *			<xsd:element ref="ds:DigestValue"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * SigningCertificateV2 is referenced in the updated ETSI document
 * https://www.etsi.org/deliver/etsi_en/319100_319199/31913201/01.01.01_60/en_31913201v010101p.pdf
 * with namespace http://uri.etsi.org/01903/v1.3.2#
 * This class obsoletes SigningCertificate
 */
class SigningCertificateV2 extends XmlCore
{
	const defaultAlgorithm = "sha256";

	/**
	 * The <Cert> to be created
	 * @var CertV2
	 */
	public $cert;

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigningCertificateV2;
	}

	/**
	 * Create a &lt;SigningCertificateV2> instance
	 * @param CertV2 $certV2
	 * @param string $algorithm
	 * @param string $uri
	 */
	public function __construct( $certV2 = null )
	{
		$this->cert = $certV2;
	}

	/**
	 * Create a &lt;SigningCertificate> from a certificate
	 *
	 * @param Sequence $certificate
	 * @param Sequence $name
	 * @param Sequence|string $issuer
	 * @param string $algorithm optional (default: SHA256)
	 * @param string $uri (optional)
	 * @return SigningCertificate
	 */
	public static function fromCertificate( $certificate, $issuer = null, $algorithm = self::defaultAlgorithm, $uri = null )
	{
		// Add the digest
		$digest = base64_encode( hash( $algorithm,  (new \lyquidity\Asn1\Der\Encoder())->encodeElement( $certificate ), true ) );

		list( $certificate, $certificateInfo, $ocspResponderUrl, $issuerCertBytes, $issuerCertificate ) = array_values( Ocsp::getCertificate( $certificate, $issuer ) );
		/** @var Sequence $certificate */
		/** @var CertificateInfo $certificateInfo */
		/** @var Sequence $issuerCertificate */

		// If the issuer certificate can be found the use its values
		$issuerSerialDER = null;
		if ( $issuerCertificate )
		{
			/*
			* From RFC 5035
			* 
			*	ESSCertIDv2 ::= SEQUENCE {
			* 		hashAlgorithm           AlgorithmIdentifier DEFAULT {algorithm id-sha256},
			* 		certHash                Hash,
			* 		issuerSerial            IssuerSerial OPTIONAL
			*	}
			*
			*	Hash ::= OCTET STRING  
			*
			*	IssuerSerial ::= SEQUENCE {
			* 		issuer                   GeneralNames,
			* 		serialNumber             CertificateSerialNumber
			*	}
			*
			* The fields of ESSCertIDv2 are defined as follows:
			*
			* hashAlgorithm
			* 	contains the identifier of the algorithm used in computing certHash.
			* 
			* certHash
			* 	is computed over the entire DER-encoded certificate (including the
			* 	signature) using the SHA-1 algorithm.
			* 
			* issuerSerial
			* 	holds the identification of the certificate.  The issuerSerial
			* 	would normally be present unless the value can be inferred from
			* 	other information (e.g., the sid field of the SignerInfo object).
			* 
			* The fields of IssuerSerial are defined as follows:
			* 
			* issuer
			* 	contains the issuer name of the certificate.  For non-attribute
			* 	certificates, the issuer MUST contain only the issuer name from
			* 	the certificate encoded in the directoryName choice of
			* 	GeneralNames.  For attribute certificates, the issuer MUST contain
			* 	the issuer name field from the attribute certificate.
			*
			* serialNumber
			*	holds the serial number that uniquely identifies the certificate
			*	for the issuer.
			*/

			$isserSerial = Sequence::create(
				array(
					$certificateInfo->extractSubject( $issuerCertificate ),
					$certificateInfo->extractSerialNumberAsInteger( $issuerCertificate )
				)
			);

			// $essCertIDV2 = Sequence::create(
			// 	array(
			// 		Sequence::create(
			// 			array(
			// 				ObjectIdentifier::create( OID::$digests[ strtolower( $algorithm ) ] ),
			// 				NullElement::create()
			// 			)
			// 		),
			// 		OctetString::create( $digest ),
			// 		$isserSerial
			// 	)
			// );

			$issuerSerialDER = (new Encoder())->encodeElement( $isserSerial );
		}

		// Add the algorithm attribute
		$reflection = new \ReflectionClass('\lyquidity\xmldsig\XMLSecurityDSig');
		$algorithm = $reflection->getConstant( strtoupper( $algorithm ) );
		
		return new SigningCertificateV2(
			new CertV2(
				new CertDigest(
					new DigestMethod( $algorithm ),
					new DigestValue( $digest )
				),
				$issuerSerialDER ? new IssuerSerialV2( base64_encode( $issuerSerialDER ) ) : null,
				$uri
			),
			$algorithm
		);
	}

	/**
	 * Create &lt;SigningCertificateV2> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );
		$this->cert->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return SigningCertificateV2
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Cert:
					$this->cert = new CertV2();
					$this->cert->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/**
	 * Calls the closure in $callback and does the same on any descendents
	 * @param Closure $callback
	 * @param bool $depthFirst (optional: default = false)  When true this will call on child nodes first
	 * @return XmlCore
	 */
	public function traverse( $callback, $depthFirst = false )
	{
		if ( $callback instanceof \Closure )
		{
			if ( ! $depthFirst )
				parent::traverse( $callback, $depthFirst );

			if ( $this->cert )
				$this->cert->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}