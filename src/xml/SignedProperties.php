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
 *	<xsd:element name="SignedProperties" type="SignedPropertiesType" />
 *
 *	<xsd:complexType name="SignedPropertiesType">
 *		<xsd:sequence>
 *			<xsd:element ref="SignedSignatureProperties" minOccurs="0"/>
 *			<xsd:element ref="SignedDataObjectProperties" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SignedProperties>
 */
class SignedProperties extends XmlCore
{
	/**
	 * A &lt;SignedSignatureProperties>
	 * @var SignedSignatureProperties
	 */
	public $signedSignatureProperties = null;

	/**
	 * A &lt;SignedDataObjectProperties>
	 * @var SignedDataObjectProperties
	 */
	public $signedDataObjectProperties = null;

	/**
	 * Create an instance of &lt;SignedProperties> and pass in an instance of &lt;SignedSignatureProperties> and &lt;SignedDataObjectProperties>
	 * @param SignedSignatureProperties $signedSignatureProperties
	 * @param SignedDataObjectProperties $signedDataObjectProperties
	 * @param string $id
	 */
	public function __construct( $signedSignatureProperties = null, $signedDataObjectProperties = null, $id = null )
	{
		$this->signedSignatureProperties = $signedSignatureProperties;
		$this->signedDataObjectProperties = $signedDataObjectProperties;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignedProperties;
	}

	/**
	 * Create &lt;SignedProperties> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->signedSignatureProperties )
			$this->signedSignatureProperties->generateXml( $newElement );

		if ( $this->signedDataObjectProperties )
			$this->signedDataObjectProperties->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;SignedProperties>
	 *
	 * @param \DOMElement $node
	 * @return SignedProperties
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::SignedSignatureProperties:
					$this->signedSignatureProperties = new SignedSignatureProperties();
					$this->signedSignatureProperties->loadInnerXml( $childNode );
					break;

				case ElementNames::SignedDataObjectProperties:
					$this->signedDataObjectProperties = new SignedDataObjectProperties();
					$this->signedDataObjectProperties->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;SignedProperties> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->signedSignatureProperties )
			$this->signedSignatureProperties->validateElement();

		if ( $this->signedDataObjectProperties )
			$this->signedDataObjectProperties->validateElement();
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

			if ( $this->signedSignatureProperties )
				$this->signedSignatureProperties->traverse( $callback, $depthFirst );

			if ( $this->signedDataObjectProperties )
				$this->signedDataObjectProperties->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
