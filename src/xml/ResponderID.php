<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

/**
 * <!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<xsd:element name="ResponderID" type="ResponderIDType"/>
 *
 *	<xsd:complexType name="ResponderIDType">
 *		<xsd:choice>
 *			<xsd:element name="ByName" type="xsd:string"/>
 *			<xsd:element name="ByKey" type="xsd:base64Binary"/>
 *		</xsd:choice>
 *	</xsd:complexType>
 */

/**
 * Represents &lt;ResponderID>
 */
class ResponderID extends XmlCore
{
	/**
	 * Represents &lt;ByName>
	 * @var string (required)
	 */
	public $name;

	/**
	 * Represents &lt;ByKey>
	 * @var string (required)
	 */
	public $key;

	/**
	 * Create &lt;ResponderID> by name
	 * @param string $name
	 * @return ResponderID
	 */
	public static function byName( $name )
	{
		$id = new ResponderID();
		$id->name = $name;
		return $id;
	}

	/**
	 * Create &lt;ResponderID> by key
	 * @param string $name
	 * @return ResponderID
	 */
	public static function byKey( $key )
	{
		$id = new ResponderID();
		$id->key = $key;
		return $id;
	}

	/**
	 * Create an &lt;ResponderID> instance
	 */
	public function __construct() {}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::ResponderID;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return ResponderID
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
				case ElementNames::ByName:
					$this->name = $childNode->nodeValue;
					break;

				case ElementNames::ByKey:
					$this->key = $childNode->nodeValue;
					break;
			}
		}

		return $this;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 *
	 * @param \DOMElement|\DOMDocument $parentNode
	 * @param string[] $namespaces
	 * @param string[] $attributes
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::URI => $this->uri ) );

		if ( $this->name )
		{
			$name = $parentNode->ownerDocument->createElement( ElementNames::ByName, $this->name );
			$newElement->appendChild( $name );
		}

		if ( $this->key )
		{
			$key = $parentNode->ownerDocument->createElement( ElementNames::ByKey, $this->key );
			$newElement->appendChild( $key );
		}
	}

	/** 
	 * Validate @Uri
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->name && $this->key )
			throw new \Exception("Only <ByName> OR <ByKey> should be provided");

		if ( $this->key )
			Base64String::validateBase64String( $this->key );
	}
}