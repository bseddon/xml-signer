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
 *	<xsd:element name="SignerRole" type="SignerRoleType"/>
 * 
 *	<xsd:complexType name="SignerRoleType">
 *		<xsd:sequence>
 *			<xsd:element name="ClaimedRoles" type="ClaimedRolesListType" minOccurs="0"/>
 *			<xsd:element name="CertifiedRoles" type="CertifiedRolesListType" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *	
 *	<xsd:complexType name="ClaimedRolesListType">
 *		<xsd:sequence>
 *			<xsd:element name="ClaimedRole" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *	
 *	<xsd:complexType name="CertifiedRolesListType">
 *		<xsd:sequence>
 *			<xsd:element name="CertifiedRole" type="EncapsulatedPKIDataType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Acts as a common base for all text elements like 
 * Description, Street, City, etc. so they only need 
 * to specify an element name
 */
class SignerRole extends XmlCore
{
	/**
	 * Implements &lt;ClaimedRoles>
	 * @var ClaimedRoles
	 */
	public $claimedRoles = null;

	/**
	 * Implements &lt;CertifiedRoles>
	 * @var CertifiedRoles
	 */
	public $certifiedRoles = null;

	/**
	 * Create a SignerRole instance
	 * @param ClaimedRoles|string|string[] $claimedRoles 
	 * @param CertifiedRoles|string|string[] $certifiedRoles
	 * @return void 
	 */
	public function __construct( $claimedRoles = null, $certifiedRoles = null )
	{
		$this->claimedRoles = self::createConstructor( $claimedRoles, ClaimedRoles::class );
		$this->certifiedRoles = self::createConstructor( $certifiedRoles, CertifiedRoles::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignerRole;
	}

	/**
	 * Create &lt;SignerRole> and any descendent elements
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

		if ( $this->certifiedRoles )
			$this->certifiedRoles->generateXml( $newElement );
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

				case ElementNames::CertifiedRoles:
					$this->certifiedRoles = new CertifiedRoles();
					$this->certifiedRoles->loadInnerXml( $childNode );
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

		if ( $this->certifiedRoles )
			$this->certifiedRoles->validateElement();
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

			if ( $this->certifiedRoles )
				$this->certifiedRoles->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
