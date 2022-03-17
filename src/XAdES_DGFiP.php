<?php

/**
 * Copyright (c) 2022 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * MIT License 
 * 
 * Supports the specific semantics of the Direction Générale des Finances Publiques
 * 
 */

namespace lyquidity\xmldsig;

use lyquidity\OCSP\CertificateLoader;
use lyquidity\xmldsig\xml\QualifyingProperties;
use lyquidity\xmldsig\xml\SignaturePolicyId;
use lyquidity\xmldsig\xml\SignaturePolicyIdentifier;
use lyquidity\xmldsig\xml\SignatureProductionPlace;
use lyquidity\xmldsig\xml\SignatureProductionPlaceV2;
use lyquidity\xmldsig\xml\SignedProperties;
use lyquidity\xmldsig\xml\SignedSignatureProperties;
use lyquidity\xmldsig\xml\SignerRole;
use lyquidity\xmldsig\xml\SignerRoleV2;
use lyquidity\xmldsig\xml\SigningCertificate;
use lyquidity\xmldsig\xml\SigningTime;
use lyquidity\xmldsig\xml\SigPolicyHash;
use lyquidity\xmldsig\xml\SigPolicyId;
use lyquidity\xmldsig\xml\SigPolicyQualifier;
use lyquidity\xmldsig\xml\SigPolicyQualifiers;
use lyquidity\xmldsig\xml\SPURI;

class XAdES_DGFiP extends XAdES
{
	/**
	 * The currently supported SBR policy identifier
	 */
	const policyIdentifier = 'urn:oid:1.2.250.1.131.1.5.18.21.1.7';

	/**
	 * The description of the OID
	 */
	const policyIdentifierDescription = 'Politique de signature Helios de la DGFiP';

	/**
	 * Link to the current policy document.  This will be presented in anSPUrl elememt.
	 */
	const policyDocumentUrl = 'https://www.collectivites-locales.gouv.fr/files/files/finances_locales/dematerialisation/ps_helios_dgfip.pdf';

	/**
	 * This is a constant because it is the base 64 encoded hash of the PDF.
	 * It can be regenerated like:
	 * 	base64_encode( hash( 'sha256', file_get_contents( XAdES_DGFiP::policyDocumentUrl ) ) );
	 */
	const policyHash = 'GbP1WjbTrHp6h9zlsz5RN7AqkJbnDNDOAQzgm1qzIJ=';

	const policyHashAlgorithm = '';

	/**
	 * Create a signature for a resource
	 * 
	 * This is a convenience function.  More control over the signature creation can be achieved by creating the XAdES instance directly
	 *
	 * @param InputResourceInfo|string $xmlResource
	 * @param CertificateResourceInfo|string $certificateResource
	 * @param KeyResourceInfo|string $keyResource
	 * @param SignatureProductionPlace|SignatureProductionPlaceV2 $signatureProductionPlace
	 * @param SignerRole|SignerRoleV2 $signerRole
	 * @return XAdES
	 */
	public static function signDocument( $xmlResource, $certificateResource, $keyResource = null, $signatureProductionPlace = null, $signerRole = null, $options = null )
	{
		// $commitmentTypeIdentifier = $options['commitmentTypeIdentifier'] ?? null;
		$canonicalizationMethod =  $options['canonicalizationMethod'] ?? self::C14N;
		$addTimestamp = $options['addTimestamp'] ?? false;

		$prefix = $options['prefix'] ?? XMLSecurityDSig::defaultPrefix;
		self::$xadesPrefix = $options['xadesPrefix'] ?? self::$xadesPrefix;

		$instance = new static( $prefix, $xmlResource->signatureId );
		
		$instance->signXAdESFile( $xmlResource, $certificateResource, $keyResource, $signatureProductionPlace, $signerRole, $canonicalizationMethod, $addTimestamp );
		return $instance;
	}

	/**
	 * Create a signature for a resource but without the SignatureValue element
	 * The canonicalized SignedInfo will be returned as a string for signing by
	 * a third party.
	 * 
	 * This is a convenience function.  More control over the signature creation can be achieved by creating the XAdES instance directly
	 *
	 * @param InputResourceInfo|string $xmlResource
	 * @param CertificateResourceInfo|string $certificateResource
	 * @param SignatureProductionPlace|SignatureProductionPlaceV2 $signatureProductionPlace
	 * @param SignerRole|SignerRoleV2 $signerRole
	 * @param string[] $options (optional) A list of other, variable properties such as canonicalizationMethod and addTimestamp
	 * @return string
	 */
	public static function getCanonicalizedSI( $xmlResource, $certificateResource, $signatureProductionPlace = null, $signerRole = null, $options = array() )
	{
		$canonicalizationMethod =  $options['canonicalizationMethod'] ?? self::C14N;
		$addTimestamp = $options['addTimestamp'] ?? false;

		$prefix = $options['prefix'] ?? XMLSecurityDSig::defaultPrefix;
		self::$xadesPrefix = $options['xadesPrefix'] ?? self::$xadesPrefix;

		$instance = new static( $prefix, $xmlResource->signatureId );

		return $instance->getCanonicalizedSignedInfo( $xmlResource, $certificateResource, $signatureProductionPlace, $signerRole, $canonicalizationMethod, $addTimestamp );
	}

	/**
	 * The current policy identifier
	 * @var string
	 */
	private $policyIdentifier = null;

	/**
	 * Create a signature for a resource
	 *
	 * @param InputResourceInfo|string $xmlResource
	 * @param CertificateResourceInfo|string $certificateResource
	 * @param KeyResourceInfo|string $keyResource
	 * @param SignatureProductionPlace|SignatureProductionPlaceV2 $signatureProductionPlace
	 * @param SignerRole|SignerRoleV2 $signerRole
	 * @param string $canonicalizationMethod
	 * @param bool $addTimestamp (optional)
	 * @return bool
	 */
	function signXAdESFile( $xmlResource, $certificateResource, $keyResource = null, $signatureProductionPlace = null, $signerRole = null, $canonicalizationMethod = self::C14N, $addTimestamp = null )
	{
		// The Xml to be signed MUST be provided as a file or url reference
		if ( ! $xmlResource->isFile() && ! $xmlResource->isURL() )
		{
			throw new \Exception("The data to be signed must be provided as a reference to a file containing Xml");
		}

		return parent::signXAdESFile( $xmlResource, $certificateResource, $keyResource, $signatureProductionPlace, $signerRole, $canonicalizationMethod, $addTimestamp );
	}

		/**
	 * Creates the &lt;QualifyingProperties> to be added to the &lt;Object>
	 * If you have a need to produce a custom &lt;QualifyingProperties> then
	 * override this method in a descendent class.
	 *
	 * @param string $signatureId
	 * @param string $certificate
	 * @param SignatureProductionPlace|SignatureProductionPlaceV2 $signatureProductionPlace
	 * @param SignerRole|SignerRoleV2 $signerRole
	 * @param string $signaturePropertiesId
	 * @param string $referenceId The id that will be added to the signed info reference.  Used as @Target on &lt;QualifyingProperties>
	 * @param string $signedPropertiesId The @Id to be assined to the &lt;SignedProperties>
	 * @return QualifyingProperties
	 */
	protected function createQualifyingProperties(
		$signatureId,
		$certificate = null,
		$signatureProductionPlace = null, 
		$signerRole = null,
		$signaturePropertiesId = null,
		$referenceId = null,
		$signedPropertiesId = self::SignedPropertiesId
	)
	{
		$loader = new CertificateLoader();
		$certs = CertificateLoader::getCertificates( $certificate );
		$cert = null;
		$issuer = null;
		if ( $certs )
		{
			$cert = $loader->fromString( reset( $certs ) );
			if ( next( $certs ) )
				$issuer = $loader->fromString( current( $certs ) );
		}
		else
		{
			$cert = $loader->fromFile( $certificate );
		}

		$signingCertificate = SigningCertificate::fromCertificate( $cert );
		$signingCertificateV2 = null; // SigningCertificateV2::fromCertificate( $cert, $issuer );

		$qualifyingProperties = new QualifyingProperties(
			new SignedProperties(
				new SignedSignatureProperties(
					new SigningTime(),
					$signingCertificate,
					$signingCertificateV2,
					$this->getSignaturePolicyIdentifier(),
					$signatureProductionPlace instanceof SignatureProductionPlace ? $signatureProductionPlace : null,
					$signatureProductionPlace instanceof SignatureProductionPlaceV2 ? $signatureProductionPlace : null,
					$signerRole instanceof SignerRole ? $signerRole : null,
					$signerRole instanceof SignerRoleV2 ? $signerRole : null,
					$signaturePropertiesId
				),
				$this->getSignedDataObjectProperties( $referenceId ),
				$signedPropertiesId
			),
			null,
			$signatureId
		);

		return $qualifyingProperties;
	}

	/**
	 * Its expected this will be overridden in a descendent class
	 * @var string $policyIdentifier
	 * @return string A path or URL to the policy document
	 */
	public function getPolicyDocument( $policyIdentifier = null )
	{
		$this->policyIdentifier = $policyIdentifier ?? self::policyIdentifier;

		if ( $this->policyIdentifier == self::policyIdentifier )
			return self::policyDocumentUrl;
		else
			throw new \Exception("The policy identifier '$policyIdentifier' is not supported");
	}

	/**
	 * Overrides to provide a SBR specific identifier
	 * @return SignaturePolicyIdentifier
	 */
	protected function getSignaturePolicyIdentifier()
	{
		/**
			<xad:SignaturePolicyIdentifier>
				<xad:SignaturePolicyId>
					<xad:SigPolicyId>
						<xad:Identifier>urn:oid:1.2.250.1.131.1.5.18.21.1.7</xad:Identifier>
						<xad:Description>Politique de signature Helios de la DGFiP</xad:Description>
					</xad:SigPolicyId>
					<xad:SigPolicyHash>
						<xad:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
						<xad:DigestValue>GbP1WjbTrHp6h9zlsz5RN7AqkJbnDNDOAQzgm1qzIJ4=</xad:DigestValue>
					</xad:SigPolicyHash>
					<xad:SigPolicyQualifiers>
						<xad:SigPolicyQualifier>
							<xad:SPURI>https://www.collectivites-locales.gouv.fr/files/files/finances_locales/dematerialisation/ps_helios_dgfip.pdf</xad:SPURI>
						</xad:SigPolicyQualifier>
					</xad:SigPolicyQualifiers>
				</xad:SignaturePolicyId>
			</xad:SignaturePolicyIdentifier>
		 */

		// Create the policy object
		$spi = new SignaturePolicyIdentifier(
			new SignaturePolicyId(
				new SigPolicyId(
					XAdES_DGFiP::policyIdentifier,
					XAdES_DGFiP::policyIdentifierDescription
				),
				null,
				new SigPolicyHash(
					XMLSecurityDSig::SHA256,
					XAdES_DGFiP::policyHash
				),
				new SigPolicyQualifiers( 
					new SigPolicyQualifier( new SPURI( XAdES_DGFiP::policyDocumentUrl ) )
				)
			)
		);

		return $spi;
	}

	/**
	 * Override to check the policy rules are met in the signature
	 *
	 * @param SignedProperties $signedProperties
	 * @param \DOMDocument $policyDocument
	 * @return void
	 * @throws \Exception If the signature does not meet the policy rules
	 */
	public function validateExplicitPolicy( $signedProperties, $policyDocument )
	{
		// This is not yet implemented.  Signatures can be validated using the http://xemelios.org/ application.
	}
}

 ?>