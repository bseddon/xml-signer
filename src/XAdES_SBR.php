<?php

/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * MIT License 
 * 
 * Supports the specific semantics of the NL SBR
 * 
 */

namespace lyquidity\xmldsig;

class XAdES_SBR extends XAdES
{
	/**
	 * The currently supported SBR policy identifier
	 */
	const policyIdentifier = 'urn:sbr:signature-policy:xml:2.0';

	/**
	 * LInk to the current policy document
	 */
	const policyDocumentUrl = 'http://nltaxonomie.nl/sbr/signature_policy_schema/v2.0/SBR-signature-policy-v2.0.xml';

	/**
	 * The namespace of the policy document
	 */
	const policyDocumentNamespace = 'http://www.nltaxonomie.nl/sbr/signature_policy_schema/v2.0/signature_policy';

	/**
	 * The current policy identifier
	 * @var string
	 */
	private $policyIdentifier = null;

	/**
	 * Its expected this will be overridden in a descendent class
	 * @var string $policyIdentifier
	 * @return string A path or URL to the policy document
	 */
	public function getPolicyDocument( $policyIdentifier )
	{
		$this->policyIdentifier = $policyIdentifier;

		if ( $policyIdentifier == self::policyIdentifier )
			return self::policyDocumentUrl;
		else
			throw new \Exception("The policy identifier '$policyIdentifier' is not supported");
	}

	/**
	 * Override to check the policy rules are met in the signature
	 *
	 * @param \DOMElement $signedProperties
	 * @param \DOMXPath $xpath
	 * @param \DOMDocument $policyDocument
	 * @return void
	 * @throws \Exception If the signature does not meet the policy rules
	 */
	public function validateExplicitPolicy( $signedProperties, $sigDocXPath, $policyDocument )
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
		$commitmentTypeIdentifier = $sigDocXPath->query( XAdES::CommitmentTypeIdentifierQuery, $signedProperties );
		if ( ! count( $commitmentTypeIdentifier ) )
		{
			throw new \Exception('The signature is not valid as it does not contain a commitment type');
		}

		$commitmentTypeIdentifier = $commitmentTypeIdentifier[0]->textContent;
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
		$manadatoryPropertiesQuery = $policyInfoQuery . "/sbrsp:SignatureValidationPolicy/sbrsp:CommonRules/sbrsp:SignerAndVerifierRules/sbrsp:SignerRules/sbrsp:MandatedSignedQProperties/*";
		$manadatoryProperties = $policyDocXPath->query( $manadatoryPropertiesQuery );
		foreach( $manadatoryProperties as $node )
		{
			/** @var \DOMElement $node */ 
			$manadatoryProperty = join( '', array_map( function( $localName ) { return $localName ? "/*[local-name()='$localName']" : ''; }, explode( '/', $node->textContent ) ) );
			$property = $sigDocXPath->query( $manadatoryProperty );
			if ( ! count( $property ) )
			{
				throw new \Exception("The mandatory property does not exist: '{$node->textContent}'");
			}
		}
	}
}


 ?>