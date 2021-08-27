<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\xmldsig\XMLSecurityDSig;

/**
 *	<xsd:element name="CertifiedRole" type="CertifiedRoleTypeV2" maxOccurs="unbounded"/>
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
 *	<xsd:element name="EncapsulatedPKIData" type="EncapsulatedPKIDataType"/>
 * 
 *	<xsd:complexType name="EncapsulatedPKIDataType">
 *		<xsd:complexContent>
 *			<xsd:extension base="xsd:base64Binary">
 *				<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *			</xsd:extension>
 *		</xsd:complexContent>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CertifiedRolesV2> which contains one or more &lt;CertifiedRole>
 */
class CertifiedRoleV2 extends XmlCore
{
	/**
	 * Represents either a &lt;X509AttributeCertificate> of &lt;OtherAttributeCertificate>
	 * @var X509AttributeCertificate|OtherAttributeCertificate
	 */
	public $certificate = array();

	/**
	 * Assign one of more &lt;CertifiedRole> to this instance
	 *
	 * @param X509AttributeCertificate|OtherAttributeCertificate|null $certificate
	 */
	public function __construct( $certificate = null )
	{
		if ( ! is_null( $certificate ) )
		{
			if ( ! $certificate instanceof X509AttributeCertificate && ! $certificate instanceof OtherAttributeCertificate )
				throw new \Exception('The parameter passes to CertifiedRoleVS must X509AttributeCertificate|OtherAttributeCertificate|null');
			$this->certificate = $certificate;
		}
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertifiedRole;
	}

	/**
	 * Create &lt;CertifiedRole> and any descendent elements 
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		if ( ! $this->certificate ) return;

		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );
		$this->certificate->generateXml( $newElement );
		return $newElement;
	}

	/**
	 * Validate all references are DocumentationReference instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( ! ( $this->certificate instanceof X509AttributeCertificate || $this->certificate instanceof OtherAttributeCertificate ) )
			throw new \Exception("The attribute certificate element object is not the right type");
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Transforms
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// There are no attributes for this element

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::X509AttributeCertificate:
					$this->certificate = new X509AttributeCertificate();
					break;

				case ElementNames::OtherAttributeCertificate:
					$this->certificate = new OtherAttributeCertificate();
					break;
			}
			$this->certificate->loadInnerXml( $childNode );
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

			$this->certificate->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}}
