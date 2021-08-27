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
 *	<xsd:element name="QualifyingProperties" type="QualifyingPropertiesType"/>
 *
 *	<xsd:complexType name="QualifyingPropertiesType">
 *		<xsd:sequence>
 *			<xsd:element ref="SignedProperties" minOccurs="0"/>
 *			<xsd:element ref="UnsignedProperties" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Target" type="xsd:anyURI" use="required"/>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;SigPolicyHash>
 */
class QualifyingProperties extends XmlCore
{
	/**
	 * A &lt;SignedProperties>
	 * @var SignedProperties
	 */
	public $signedProperties = null;

	/**
	 * A &lt;UnsignedProperties>
	 * @var UnsignedProperties
	 */
	public $unsignedProperties = null;

	/**
	 * Represents @Target
	 * @var string
	 */
	public $target = null;

	/**
	 * Create an instance of &lt;QualifyingProperties> and pass in an instance of &lt;SignedProperties> and &lt;UnsignedProperties>
	 * @param SignedProperties $signedProperties
	 * @param UnsignedProperties $unsignedProperties
	 * @param string $target The character to use in the @Target
	 */
	public function __construct( $signedProperties = null, $unsignedProperties = null, $target = null )
	{
		$this->signedProperties = self::createConstructor( $signedProperties, SignedProperties::class );
		$this->unsignedProperties = self::createConstructor( $unsignedProperties, UnsignedProperties::class );
		$this->target = '#' . ltrim( $target, '#' );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::QualifyingProperties;
	}

	/**
	 * Create &lt;QualifyingProperties> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::Target => $this->target ) );

		if ( $this->signedProperties )
			$this->signedProperties->generateXml( $newElement );

		if ( $this->unsignedProperties )
			$this->unsignedProperties->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;QualifyingProperties>
	 *
	 * @param \DOMElement $node
	 * @return QualifyingProperties
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Target );
		if ( $attr )
		{
			$this->target = $attr->value;
		}

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::SignedProperties:
					$this->signedProperties = new SignedProperties();
					$this->signedProperties->loadInnerXml( $childNode );
					break;

				case ElementNames::UnsignedProperties:
					$this->unsignedProperties = new UnsignedProperties();
					$this->unsignedProperties->loadInnerXml( $childNode );
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

		if ( $this->signedProperties )
			$this->signedProperties->validateElement();

		if ( $this->unsignedProperties )
			$this->unsignedProperties->validateElement();
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

			if ( $this->signedProperties )
				$this->signedProperties->traverse( $callback, $depthFirst );

			if ( $this->unsignedProperties )
				$this->unsignedProperties->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
