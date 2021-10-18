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
 *	<xsd:element name="DataObjectFormat" type="DataObjectFormatType"/>
 *
 *	<xsd:complexType name="DataObjectFormatType">
 *		<xsd:sequence>
 *			<xsd:element name="Description" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="ObjectIdentifier" type="ObjectIdentifierType" minOccurs="0"/>
 *			<xsd:element name="MimeType" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="Encoding" type="xsd:anyURI" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="ObjectReference" type="xsd:anyURI" use="required"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;DataObjectFormat> which is a container for a collection of elements
 */
class DataObjectFormat extends XmlCore
{
	/**
	 * An optional @ObjectReference
	 *
	 * @var string
	 */
	public $objectReference = null;

	/**
	 * @var Description
	 */
	public $description = null;
	/**
	 * @var ObjectIdentifier
	 */
	public $objectIdentifier = null;
	/**
	 * @var MimeType
	 */
	public $mimeType = null;
	/**
	 * @var Encoding
	 */
	public $encoding = null;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param Description $description
	 * @param ObjectIdentifier $objectIdentifier
	 * @param MimeType $mimeType
	 * @param Encoding $encoding
	 * @param string $objectReference
	 */
	public function __construct( 
		$description = null,
		$objectIdentifier = null,
		$mimeType = null,
		$encoding = null,
		$objectReference = null
	)
	{
		$this->description = TextBase::instanceFromParam( $description, Description::class );

		$this->objectIdentifier = $objectIdentifier;

		$this->mimeType = TextBase::instanceFromParam( $mimeType, MimeType::class );
		$this->encoding = TextBase::instanceFromParam( $encoding, Encoding::class );

		if ( ! is_null( $objectReference ) )
		{
			$this->objectReference = $objectReference;
		}
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::DataObjectFormat;
	}

	/**
	 * Create &lt;DataObjectFormat> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::ObjectReference => $this->objectReference ), $insertAfter );

		// Now create a node for all the sub-nodes where they exist
		if ( $this->description )
		{
			$this->description->generateXml( $newElement );
		}

		if ( $this->objectIdentifier )
		{
			$this->objectIdentifier->generateXml( $newElement );
		}

		if ( $this->mimeType )
		{
			$this->mimeType->generateXml( $newElement );
		}

		if ( $this->encoding )
		{
			$this->encoding->generateXml( $newElement );
		}
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return DataObjectFormat
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::ObjectReference );
		if ( $attr )
		{
			$this->objectReference = $attr->value;
		}
		
		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Description:
					$this->description = new Description();
					$this->description->loadInnerXml( $childNode );
					break;

				case ElementNames::ObjectIdentifier:
					$this->objectIdentifier = new ObjectIdentifier();
					$this->objectIdentifier->loadInnerXml( $childNode );
					break;

				case ElementNames::MimeType:
					$this->mimeType = new MimeType();
					$this->mimeType->loadInnerXml( $childNode );
					break;

				case ElementNames::Encoding:
					$this->encoding = new Encoding();
					$this->encoding->loadInnerXml( $childNode );
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
		if ( ! $this->objectReference )
		{
			throw new \Exception("An @ObjectReference is required");
		}

		parent::validateElement();

		if ( $this->description )
			$this->description->validateElement();

		if ( $this->objectIdentifier )
			$this->objectIdentifier->validateElement();

		if ( $this->mimeType )
			$this->mimeType->validateElement();
		else
			throw new \Exception("A <MimeType> is required. Although the schema specifies 0 or 1 of these elements section 6.3 of ETSI EN 319 132-1 V1.1.1 (2016-04) states that a <MimeType> is required.");

		if ( $this->encoding )
			$this->encoding->validateElement();
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

			if ( $this->description )
				$this->description->traverse( $callback, $depthFirst );

			if ( $this->objectIdentifier )
				$this->objectIdentifier->traverse( $callback, $depthFirst  );

			if ( $this->mimeType )
				$this->mimeType->traverse( $callback, $depthFirst  );

			if ( $this->encoding )
				$this->encoding->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
