<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

/**
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<xsd:element name="SignedSignatureProperties" type="SignedSignaturePropertiesType" />
 *
 *	<xsd:complexType name="SignedSignaturePropertiesType">
 *		<xsd:sequence>
 *			<xsd:element ref="SigningTime" minOccurs="0"/>
 *			<xsd:element ref="SigningCertificate" minOccurs="0"/>
 *			<xsd:element ref="SigningCertificateV2" minOccurs="0"/>
 *			<xsd:element ref="SignaturePolicyIdentifier" minOccurs="0"/>
 *			<xsd:element ref="SignatureProductionPlace" minOccurs="0"/>
 *			<xsd:element ref="SignatureProductionPlaceV2" minOccurs="0"/>
 *			<xsd:element ref="SignerRole" minOccurs="0"/>
 *			<xsd:element ref="SignerRoleV2" minOccurs="0"/>
 *			<xsd:any namespace="##other" minOccurs="0" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SignedSignatureProperties> which is a container for a collection of elements
 */
class SignedSignatureProperties extends XmlCore
{
	/**
	 * @var SigningTime
	 */
	public $signingTime = null;
	/**
	 * @var SigningCertificate
	 */
	public $signingCertificate = null;
	/**
	 * @var SigningCertificateV2
	 */
	public $signingCertificateV2 = null;
	/**
	 * @var SignaturePolicyIdentifier
	 */
	public $signaturePolicyIdentifier = null;
	/**
	 * @var SignatureProductionPlace
	 */
	public $signatureProductionPlace = null;
	/**
	 * @var SignatureProductionPlaceV2
	 */
	public $signatureProductionPlaceV2 = null;
	/**
	 * @var SignerRole
	 */
	public $signerRole = null;
	/**
	 * @var SignerRoleV2
	 */
	public $signerRoleV2 = null;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param SigningTime $signingTime
	 * @param SigningCertificate $signingCertificate
	 * @param SigningCertificateV2 $signingCertificateV2
	 * @param SignaturePolicyIdentifier $signaturePolicyIdentifier
	 * @param SignatureProductionPlace $signatureProductionPlace
	 * @param SignatureProductionPlaceV2 $signatureProductionPlaceV2
	 * @param SignerRole $signerRole
	 * @param SignerRoleV2 $signerRoleV2,
	 * @param string $id
	 */
	public function __construct( 
		$signingTime = null, 
		$signingCertificate = null, 
		$signingCertificateV2 = null, 
		$signaturePolicyIdentifier = null, 
		$signatureProductionPlace = null, 
		$signatureProductionPlaceV2 = null, 
		$signerRole = null, 
		$signerRoleV2 = null,
		$id = null
	)
	{
		$this->signingTime = $signingTime;
		$this->signingCertificate = $signingCertificate; 
		$this->signingCertificateV2 = $signingCertificateV2; 
		$this->signaturePolicyIdentifier = $signaturePolicyIdentifier; 
		$this->signatureProductionPlace = $signatureProductionPlace; 
		$this->signatureProductionPlaceV2 = $signatureProductionPlaceV2; 
		$this->signerRole = $signerRole; 
		$this->signerRoleV2 = $signerRoleV2;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignedSignatureProperties;
	}

	/**
	 * Create &lt;SignedSignatureProperties> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		// Now create a node for all the sub-nodes where they exist
		if ( $this->signingTime )
		{
			$this->signingTime->generateXml( $newElement );
		}

		if ( $this->signingCertificate )
		{
			$this->signingCertificate->generateXml( $newElement );
		}

		if ( $this->signingCertificateV2 )
		{
			$this->signingCertificateV2->generateXml( $newElement );
		}

		if ( $this->signaturePolicyIdentifier )
		{
			$this->signaturePolicyIdentifier->generateXml( $newElement );
		}

		if ( $this->signatureProductionPlace )
		{
			$this->signatureProductionPlace->generateXml( $newElement );
		}

		if ( $this->signatureProductionPlaceV2 )
		{
			$this->signatureProductionPlaceV2->generateXml( $newElement );
		}

		if ( $this->signerRole )
		{
			$this->signerRole->generateXml( $newElement );
		}

		if ( $this->signerRoleV2 )
		{
			$this->signerRoleV2->generateXml( $newElement );
		}
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return SignedSignatureProperties
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
				case ElementNames::SigningTime:
					$this->signingTime = new SigningTime();
					$this->signingTime->loadInnerXml( $childNode );
					break;

				case ElementNames::SigningCertificate:
					$this->signingCertificate = new SigningCertificate();
					$this->signingCertificate->loadInnerXml( $childNode );
					break;

				case ElementNames::SigningCertificateV2:
					$this->signingCertificateV2 = new SigningCertificateV2();
					$this->signingCertificateV2->loadInnerXml( $childNode );
					break;

				case ElementNames::SignaturePolicyIdentifier:
					$this->signaturePolicyIdentifier = new SignaturePolicyIdentifier();
					$this->signaturePolicyIdentifier->loadInnerXml( $childNode );
					break;

				case ElementNames::SignatureProductionPlace:
					$this->signatureProductionPlace = new SignatureProductionPlace();
					$this->signatureProductionPlace->loadInnerXml( $childNode );
					break;

				case ElementNames::SignatureProductionPlaceV2:
					$this->signatureProductionPlaceV2 = new SignatureProductionPlaceV2();
					$this->signatureProductionPlaceV2->loadInnerXml( $childNode );
					break;

				case ElementNames::SignerRole:
					$this->signerRole = new SignerRole();
					$this->signerRole->loadInnerXml( $childNode );
					break;

				case ElementNames::SignerRoleV2:
					$this->signerRoleV2 = new SignerRoleV2();
					$this->signerRoleV2->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}
	
	/**
	 * Allow the properties to validate themselves
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		if ( $this->signingTime )
			$this->signingTime->validateElement();

		if ( $this->signingCertificate )
			$this->signingCertificate->validateElement();

		if ( $this->signingCertificateV2 )
			$this->signingCertificateV2->validateElement();

		if ( $this->signaturePolicyIdentifier )
			$this->signaturePolicyIdentifier->validateElement();

		if ( $this->signatureProductionPlace )
			$this->signatureProductionPlace->validateElement();

		if ( $this->signatureProductionPlaceV2 )
			$this->signatureProductionPlaceV2->validateElement();

		if ( $this->signerRole )
			$this->signerRole->validateElement();

		if ( $this->signerRoleV2 )
			$this->signerRoleV2->validateElement();	
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

			if ( $this->signingTime )
				$this->signingTime->traverse( $callback, $depthFirst );
	
			if ( $this->signingCertificate )
				$this->signingCertificate->traverse( $callback, $depthFirst );
	
			if ( $this->signingCertificateV2 )
				$this->signingCertificateV2->traverse( $callback, $depthFirst );
	
			if ( $this->signaturePolicyIdentifier )
				$this->signaturePolicyIdentifier->traverse( $callback, $depthFirst );
	
			if ( $this->signatureProductionPlace )
				$this->signatureProductionPlace->traverse( $callback, $depthFirst );
	
			if ( $this->signatureProductionPlaceV2 )
				$this->signatureProductionPlaceV2->traverse( $callback, $depthFirst );
	
			if ( $this->signerRole )
				$this->signerRole->traverse( $callback, $depthFirst );
	
			if ( $this->signerRoleV2 )
				$this->signerRoleV2->traverse( $callback, $depthFirst );	
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
