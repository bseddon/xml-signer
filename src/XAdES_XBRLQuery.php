<?php

/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * MIT License 
 * 
 * Example custom class using a custom policy
 * 
 */

namespace lyquidity\xmldsig;

use lyquidity\xmldsig\xml\AttributeNames;
use lyquidity\xmldsig\xml\CommitmentTypeId;
use lyquidity\xmldsig\xml\CommitmentTypeIndication;
use lyquidity\xmldsig\xml\DataObjectFormat;
use lyquidity\xmldsig\xml\Generic;
use lyquidity\xmldsig\xml\Signature;
use lyquidity\xmldsig\xml\SignaturePolicyId;
use lyquidity\xmldsig\xml\SignaturePolicyIdentifier;
use lyquidity\xmldsig\xml\SignedDataObjectProperties;
use lyquidity\xmldsig\xml\SignedProperties;
use lyquidity\xmldsig\xml\SigPolicyHash;
use lyquidity\xmldsig\xml\Transforms;

class XAdES_XBRLQuery extends XAdES
{
	/**
	 * The currently supported custom policy identifier
	 */
	const policyIdentifier = 'urn:xbrlquery:signature-policy:xml:2.0';

	/**
	 * Link to the current policy document
	 */
	const policyDocumentUrl = 'http://www.xbrlquery.com/xades/policy.xml';

	/**
	 * The namespace of the policy document
	 */
	const policyDocumentNamespace = 'http://www.nltaxonomie.nl/sbr/signature_policy_schema/v2.0/signature_policy';

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
		$commitmentTypeIdentifier = $options['commitmentTypeIdentifier'] ?? null;
		$canonicalizationMethod =  $options['canonicalizationMethod'] ?? self::C14N;
		$addTimestamp = $options['addTimestamp'] ?? false;

		$instance = new static( XMLSecurityDSig::defaultPrefix, $xmlResource->signatureId );
		$instance->signXAdESFile( $xmlResource, $certificateResource, $keyResource, $signatureProductionPlace, $signerRole, $commitmentTypeIdentifier, $canonicalizationMethod, $addTimestamp );
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
		$commitmentTypeIdentifier = $options['commitmentTypeIdentifier'] ?? null;
		$canonicalizationMethod =  $options['canonicalizationMethod'] ?? self::C14N;
		$addTimestamp = $options['addTimestamp'] ?? false;

		$instance = new static( XMLSecurityDSig::defaultPrefix, $xmlResource->signatureId );
		$instance->commitmentTypeIdentifier = $commitmentTypeIdentifier;

		return $instance->getCanonicalizedSignedInfo( $xmlResource, $certificateResource, $signatureProductionPlace, $signerRole, $canonicalizationMethod, $addTimestamp );
	}

	/**
	 * The current policy identifier
	 * @var string
	 */
	private $policyIdentifier = null;

	private $commitmentTypeIdentifier = null;

	/**
	 * Create a signature for a resource
	 *
	 * @param InputResourceInfo|string $xmlResource
	 * @param CertificateResourceInfo|string $certificateResource
	 * @param KeyResourceInfo|string $keyResource
	 * @param SignatureProductionPlace|SignatureProductionPlaceV2 $signatureProductionPlace
	 * @param SignerRole|SignerRoleV2 $signerRole
	 * @param string $commitmentTypeIdentifier
	 * @param string $canonicalizationMethod
	 * @param bool $addTimestamp (optional)
	 * @return bool
	 */
	function signXAdESFile( $xmlResource, $certificateResource, $keyResource = null, $signatureProductionPlace = null, $signerRole = null, $commitmentTypeIdentifier = null, $canonicalizationMethod = self::C14N, $addTimestamp = null )
	{
		// The Xml to be signed MUST be provided as a file or url reference
		if ( ! $xmlResource->isFile() && ! $xmlResource->isURL() )
		{
			throw new \Exception("The data to be signed must be provided as a reference to a file containing Xml");
		}

		$this->commitmentTypeIdentifier = $commitmentTypeIdentifier;

		return parent::signXAdESFile( $xmlResource, $certificateResource, $keyResource, $signatureProductionPlace, $signerRole, $canonicalizationMethod, $addTimestamp );
	}

	/**
	 * Get the filename to use to save the signature
	 * This can be overridden by desendent to specify a jurisdiction specific name
	 *
	 * @param string $location
	 * @param string $signatureName
	 * @return string
	 */
	protected function getSignatureFilename( $location, $signatureName = XAdES::SignatureFilename )
	{
		// If the default file name is being used change it otherwise concatenate location and filename
		if ( $signatureName == XAdES::SignatureFilename )
			$signatureName = XAdES::SignatureFilename;

		return "$location/$signatureName";
	}

	/**
	 * This function is overridden only to illustrate that the &lt;QualifyingProperties>
	 * object can be modified by overriding this call instead of overriding 
	 * getSignaturePolicyIdentifier() and/or getSignedDataObjectProperties()
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
		$signedPropertiesId = self::SignedPropertiesId )
	{
		// As an illustration if just calls the ancestor method
		return parent::createQualifyingProperties(
			$signatureId,
			$certificate,
			$signatureProductionPlace, 
			$signerRole,
			$signaturePropertiesId,
			$referenceId,
			$signedPropertiesId
		);
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
	 * Overridden to provide SBR specific data.  The requirements for this are defined in the policy document
	 * @param string $referenceId The id that will be added to the signed info reference
	 * @return SignedDataObjectProperties
	 */
	protected function getSignedDataObjectProperties( $referenceId = null )
	{
		$sdop = parent::getSignedDataObjectProperties( $referenceId );

		if ( $this->commitmentTypeIdentifier )
		{
			$sdop->commitmentTypeIndication[] = $this->commitmentTypeIdentifier
				? new CommitmentTypeIndication(
					new CommitmentTypeId(
						$this->commitmentTypeIdentifier
					  )
				  )
				: null;
		}

		return $sdop;
	}

	/**
	 * Overrides to provide a SBR specific identifier
	 * @return SignaturePolicyIdentifier
	 */
	protected function getSignaturePolicyIdentifier()
	{
		$policyDoc = $this->getXmlDocument( $this->getPolicyDocument() );

		// Load the policy document
		$sbrPolicy = Generic::fromNode( $policyDoc );

		// Get the policy
		/** @var Generic */
		$policyIdentifier = $sbrPolicy->getObjectFromPath( 
			array( "SignaturePolicy", "SignPolicyInfo", "SignPolicyIdentifier", "Identifier" ), 
			"Unable to locate the policy <Identifier> in the SBR policy document"
		);

		$identifier = $policyIdentifier->text;

		// Get the digest
		/** @var Generic */
		$policyDigest = $sbrPolicy->getObjectFromPath( 
			array( "SignaturePolicy", "SignPolicyDigest" ), 
			"Unable to locate <SignPolicyDigest> in the SBR policy document"
		);

		$digest = $policyDigest->text;

		// Get the algorithm
		/** @var Generic */
		$policyAlgorithm = $sbrPolicy->getObjectFromPath( 
			array( "SignaturePolicy", "SignPolicyDigestAlg" ), 
			"Unable to locate <SignPolicyDigestAlg> in the SBR policy document"
		);

		$algorithm = $policyAlgorithm->attributes[ AttributeNames::Algorithm ] ?? XMLSecurityDSig::SHA256;

		// Get the transforms
		/** @var Transforms */
		$transforms = $sbrPolicy->getObjectFromPath( 
			array( "SignaturePolicy", "Transforms" ), 
			"Unable to locate <Transforms> in the SBR policy document"
		);

		$transforms->parent = null;

		// Use the traverse function to set the prefix to null on this an all descendents
		$transforms->traverse( function( $node ) 
		{
			$node->prefix = null;
		} );

		// Create the policy object
		$spi = new SignaturePolicyIdentifier(
			new SignaturePolicyId(
				$identifier,
				$transforms,
				new SigPolicyHash( $algorithm, $digest ),
				null // No qualifiers
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
		$policyDocXPath = new \DOMXPath( $policyDocument );
		$policyDocXPath->registerNamespace('sbrsp', self::policyDocumentNamespace );

		$policyInfoQuery = "/sbrsp:SignaturePolicy/sbrsp:SignPolicyInfo";

		// The policy identifier is set when getPolicyDocument was called
		// Make sure the policy identifier is contained in the signature policy document otherwise the signature is invalid
		$policyIdentifier = $policyDocXPath->query( $policyInfoQuery . '/sbrsp:SignPolicyIdentifier/sbrsp:Identifier');
		if ( ! count( $policyIdentifier ) || $policyIdentifier[0]->textContent != $this->policyIdentifier )
		{
			throw new \Exception("The policy identifier in the signature does not exist in the policy document");
		}

		// Make sure the commitment type exists in the policy document
		/** @var CommitmentTypeIndication[] */
		$commitmentTypeIdentifiers = $signedProperties->getObjectFromPath(
			array( "SignedDataObjectProperties", "CommitmentTypeIndication" ),
			"The signature is not valid as it does not contain any commitment type identifiers"
		);

		$commitmentTypeIdentifier = $commitmentTypeIdentifiers[0]->getObjectFromPath(
			array( "CommitmentTypeId", "Identifier" ),
			"The signature is not valid as it does not contain a commitment type identifier"
		);

		$commitmentTypeIdentifier = $commitmentTypeIdentifier->text;

		// Get the valid commitment types from the policy document
		$CommitmentTypeQuery = $policyInfoQuery . "/sbrsp:SignatureValidationPolicy/sbrsp:CommitmentRules" . 
			"/sbrsp:CommitmentRule/sbrsp:SelCommitmentTypes/sbrsp:SelCommitmentType/sbrsp:RecognizedCommitmentType". 
			"/sbrsp:CommitmentIdentifier/sbrsp:Identifier";

		$commitmentTypeFound = false;
		foreach( $policyDocXPath->query( $CommitmentTypeQuery ) as $node )
		{
			/** @var \DOMElement $node */ 
			if ( $node->textContent != $commitmentTypeIdentifier ) continue;
			$commitmentTypeFound = true;
			break;
		}

		if ( ! $commitmentTypeFound )
		{
			throw new \Exception("The commitment type used in the signature is not found in the policy document");
		}

		// Make sure the mandatory signature properties exist
		/** @var Signature */
		$signature = $signedProperties->getRootSignature("Unable to trace the root signature");

		$manadatoryPropertiesQuery = $policyInfoQuery . "/sbrsp:SignatureValidationPolicy/sbrsp:CommonRules/sbrsp:SignerAndVerifierRules/sbrsp:SignerRules/sbrsp:MandatedSignedQProperties/*";
		$manadatoryProperties = $policyDocXPath->query( $manadatoryPropertiesQuery );
		foreach( $manadatoryProperties as $node )
		{
			/** @var \DOMElement $node */ 
			$manadatoryProperty = explode( '/', ltrim( $node->textContent, '/' ) );
			$signature->getObjectFromPath( $manadatoryProperty, "The mandatory property does not exist: '{$node->textContent}'" );
		}
	}
}


 ?>