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
 *	<xsd:element name="SignerRoleV2" type="SignerRoleV2Type"/>
 *
 *	<xsd:complexType name="SignerRoleV2Type">
 *		<xsd:sequence>
 *			<xsd:element ref="ClaimedRoles" minOccurs="0"/>
 *			<xsd:element ref="CertifiedRolesV2" minOccurs="0"/>
 *			<xsd:element ref="SignedAssertions" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:element name="ClaimedRoles" type="ClaimedRolesListType"/>
 *	<xsd:element name="CertifiedRolesV2" type="CertifiedRolesListTypeV2"/>
 *	<xsd:element name="SignedAssertions" type="SignedAssertionsListType"/>
 *
 *	<xsd:complexType name="ClaimedRolesListType">
 *		<xsd:sequence>
 *			<xsd:element name="ClaimedRole" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="CertifiedRolesListTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertifiedRole" type="CertifiedRoleTypeV2" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 *
 *	<xsd:complexType name="CertifiedRoleTypeV2">
 * 		<xsd:choice>
 *			<xsd:element ref="X509AttributeCertificate"/>
 *			<xsd:element ref="OtherAttributeCertificate"/>
 *		</xsd:choice>
 *	</xsd:complexType>
 *
 *	<xsd:element name="X509AttributeCertificate" type="EncapsulatedPKIDataType"/>
 *	<xsd:element name="OtherAttributeCertificate" type="AnyType"/>
 *
 * <xsd:complexType name="SignedAssertionsListType">
 *		<xsd:sequence>
 *			<xsd:element ref="SignedAssertion" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:element name="SignedAssertion" type="AnyType"/> 
 */

/**
 * Acts as a common base for all text elements like 
 * Description, Street, City, etc. so they only need 
 * to specify an element name
 */
class SignerRoleV2 extends XmlCore
{
	/**
	 * Implements &lt;ClaimedRoles>
	 * @var ClaimedRoles
	 */
	public $claimedRoles = null;

	/**
	 * Implements &lt;CertifiedRolesV2>
	 * @var CertifiedRolesV2
	 */
	public $certifiedRolesV2 = null;

	/**
	 * Implements &lt;SignedAssertions>
	 * @var SignedAssertions
	 */
	public $signedAssertions = null;

	/**
	 * Create a SignerRoleV2 instance
	 * @param ClaimedRoles $claimedRoles 
	 * @param CertifiedRolesV2 $certifiedRolesV2
	 * @param SignedAssertions $signedAssertions 
	 * @return void 
	 */
	public function __construct( $claimedRoles = null, $certifiedRolesV2 = null, $signedAssertions = null )
	{
		$this->claimedRoles = self::createConstructor( $claimedRoles, ClaimedRoles::class );
		$this->certifiedRolesV2 = self::createConstructorArray( $certifiedRolesV2, CertifiedRoleV2::class );
		$this->signedAssertions = self::createConstructorArray( $signedAssertions, SignedAssertion::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignerRoleV2;
	}

	/**
	 * Create &lt;SignerRoleV2> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->claimedRoles )
			$this->claimedRoles->generateXml( $newElement );

		if ( $this->certifiedRolesV2 )
			$this->certifiedRolesV2->generateXml( $newElement );

		if ( $this->signedAssertions )
			$this->signedAssertions->generateXml( $newElement );
	}

	/** 
	 * Create a new Xml representation for $node
	 * @param \DOMElement $node
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch ( $childNode->localName )
			{
				case ElementNames::ClaimedRoles:
					$this->claimedRoles = new ClaimedRoles();
					$this->claimedRoles->loadInnerXml( $childNode );
					break;

				case ElementNames::CertifiedRolesV2:
					$this->certifiedRolesV2 = new CertifiedRolesV2();
					$this->certifiedRolesV2->loadInnerXml( $childNode );
					break;

				case ElementNames::SignedAssertions:
					$this->signedAssertions = new SignedAssertions();
					$this->signedAssertions->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Validate this element and all tjhe child elements that are used
	 *
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->claimedRoles )
			$this->claimedRoles->validateElement( );

		if ( $this->certifiedRolesV2 )
			$this->certifiedRolesV2->validateElement();

		if ( $this->signedAssertions )
			$this->signedAssertions->validateElement();
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

			if ( $this->claimedRoles )
				$this->claimedRoles->traverse( $callback, $depthFirst );

			if ( $this->certifiedRolesV2 )
				$this->certifiedRolesV2->traverse( $callback, $depthFirst  );

			if ( $this->signedAssertions )
				$this->signedAssertions->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
