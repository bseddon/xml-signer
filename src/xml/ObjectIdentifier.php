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
 *	<xsd:element name="ObjectIdentifier" type="ObjectIdentifierType"/>
 *
 *	<xsd:complexType name="ObjectIdentifierType">
 *		<xsd:sequence>
 *			<xsd:element name="Identifier" type="IdentifierType"/>
 *			<xsd:element name="Description" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="DocumentationReferences" type="DocumentationReferencesType" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="IdentifierType">
 *		<xsd:simpleContent>
 *			<xsd:extension base="xsd:anyURI">
 *				<xsd:attribute name="Qualifier" type="QualifierType" use="optional"/>
 *			</xsd:extension>
 *		</xsd:simpleContent>
 *	</xsd:complexType>
 *
 *	<xsd:simpleType name="QualifierType">
 *		<xsd:restriction base="xsd:string">
 *			<xsd:enumeration value="OIDAsURI"/>
 *			<xsd:enumeration value="OIDAsURN"/>
 *		</xsd:restriction>
 *	</xsd:simpleType>
 *
 *	<xsd:complexType name="DocumentationReferencesType">
 *		<xsd:sequence maxOccurs="unbounded">
 *			<xsd:element name="DocumentationReference" type="xsd:anyURI"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;ObjectIdentifier>
 */
class ObjectIdentifier extends XmlCore
{
	/**
	 * The identifier value
	 * @var Identifier
	 */
	public $identifier = null;

	/**
	 * Records the description (if there is one)
	 * @var Description
	 */
	public $description = null;

	/**
	 * Represents a &lt;DocumentationReferences>
	 *
	 * @var DocumentationReferences
	 */
	public $documentationReferences = null;

	/**
	 * Create an &lt;ObjectIdentifier> instance
	 *
	 * @param Identifier|string $identifier
	 * @param Description|string $description
	 * @param DocumentationReferences $documentationReferences
	 */
	public function __construct( $identifier = null, $description = null, $documentationReferences = null )
	{
		$this->identifier = self::createConstructor( $identifier, Identifier::class );
		$this->description = TextBase::instanceFromParam( $description, Description::class );
		$this->documentationReferences = self::createConstructor( $documentationReferences, DocumentationReferences::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::ObjectIdentifier;
	}

	/**
	 * Create <ObjectIdentifier> and any descendent elements
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->identifier )
			$this->identifier->generateXml( $newElement );

		if ( $this->description )
			$this->description->generateXml( $newElement );

		if ( $this->documentationReferences )
			$this->documentationReferences->generateXml( $newElement );

		return $newElement;
	}

	/**
	 * Create objects from an Xml node
	 *
	 * @param \DOMElement $node
	 * @return void
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
				case ElementNames::Identifier:
					$this->identifier = new Identifier();
					$this->identifier->loadInnerXml( $childNode );
					break;

				case ElementNames::Description:
					$this->description = new Description();
					$this->description->loadInnerXml( $childNode );
					break;

				case ElementNames::DocumentationReferences:
					$this->documentationReferences = new DocumentationReferences();
					$this->documentationReferences->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Validate @Qualifier and make sure there is an identifier
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		if ( ! $this->identifier )
			throw new \Exception("The <Identifier> must have a value");

		$this->identifier->validateElement();

		if ( $this->description )
			$this->description->validateElement();

		if ( $this->documentationReferences )
			$this->documentationReferences->validateElement();
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

			if ( $this->identifier )
				$this->identifier->traverse( $callback, $depthFirst );

			if ( $this->description )
				$this->description->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
