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
 *	<xsd:element name="UnsignedProperties" type="UnsignedPropertiesType" />
 *
 *	<xsd:complexType name="UnsignedPropertiesType">
 *		<xsd:sequence>
 *			<xsd:element ref="UnsignedSignatureProperties" minOccurs="0"/>
 *			<xsd:element ref="UnsignedDataObjectProperties" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;UnsignedProperties>
 */
class UnsignedProperties extends XmlCore
{
	/**
	 * A &lt;UnsignedSignatureProperties>
	 * @var UnsignedSignatureProperties
	 */
	public $unsignedSignatureProperties = null;

	/**
	 * A &lt;UnsignedDataObjectProperties>
	 * @var UnsignedDataObjectProperties
	 */
	public $unsignedDataObjectProperties = null;

	/**
	 * Create an instance of &lt;UnsignedProperties> and pass in an instance of &lt;UnsignedSignatureProperties> and &lt;UnsignedDataObjectProperties>
	 * @param UnsignedSignatureProperties $unsignedSignatureProperties
	 * @param UnsignedDataObjectProperties $unsignedDataObjectProperties
	 */
	public function __construct( $unsignedSignatureProperties = null, $unsignedDataObjectProperties = null )
	{
		$this->unsignedSignatureProperties = $unsignedSignatureProperties;
		$this->unsignedDataObjectProperties = $unsignedDataObjectProperties;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::UnsignedProperties;
	}

	/**
	 * Create &lt;UnsignedProperties> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->unsignedSignatureProperties )
			$this->unsignedSignatureProperties->generateXml( $newElement );

		if ( $this->unsignedDataObjectProperties )
			$this->unsignedDataObjectProperties->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;UnsignedProperties>
	 *
	 * @param \DOMElement $node
	 * @return UnsignedProperties
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
				case ElementNames::UnsignedSignatureProperties:
					$this->unsignedSignatureProperties = new UnsignedSignatureProperties();
					$this->unsignedSignatureProperties->loadInnerXml( $childNode );
					break;

				case ElementNames::UnsignedDataObjectProperties:
					$this->unsignedDataObjectProperties = new UnsignedDataObjectProperties();
					$this->unsignedDataObjectProperties->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;UnsignedProperties> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->unsignedSignatureProperties )
			$this->unsignedSignatureProperties->validateElement();

		if ( $this->unsignedDataObjectProperties )
			$this->unsignedDataObjectProperties->validateElement();
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

			if ( $this->unsignedSignatureProperties )
				$this->unsignedSignatureProperties->traverse( $callback, $depthFirst );

			if ( $this->unsignedDataObjectProperties )
				$this->unsignedDataObjectProperties->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
