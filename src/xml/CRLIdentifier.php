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
 *	<xsd:element name="CRLIdentifier" type="CRLIdentifierType"/>
 *
 *	<xsd:complexType name="CRLIdentifierType">
 *		<xsd:sequence>
 *			<xsd:element name="Issuer" type="xsd:string"/>
 *			<xsd:element name="IssueTime" type="xsd:dateTime" />
 *			<xsd:element name="Number" type="xsd:integer" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Represents &lt;CRLIdentifier>
 */
class CRLIdentifier extends XmlCore
{
	/**
	 * Represents &lt;Issuer>
	 * @var string (required)
	 */
	public $issuer;

	/**
	 * Represents &lt;IssueTime>
	 * @var string (required)
	 */
	public $issueTime;

	/**
	 * Represents &lt;Number>
	 * @var int
	 */
	public $number;

	/**
	 * The Uri defining the reference to include
	 * @var string
	 */
	public $uri = null;

	/**
	 * Create an &lt;CRLIdentifier> instance
	 * @param string $uri
	 * @param string $issuer
	 * @param string $issueTime
	 * @param int $number
	 */
	public function __construct( $uri = null, $issuer = null, $issueTime = null, $number = null )
	{
		$this->issuer = $issuer;
		$this->issueTime = $issueTime;
		$this->number = $number;
		$this->uri = $uri;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CRLIdentifier;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return CRLIdentifier
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );
		
		$attr = $node->getAttributeNode( AttributeNames::Uri );
		if ( $attr )
		{
			$this->uri = $attr->value;
		}

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Issuer:
					$this->issuer = $childNode->nodeValue;
					break;

				case ElementNames::IssueTime:
					$this->issueTime = $childNode->nodeValue;
					break;

				case ElementNames::Number:
					$this->number = $childNode->nodeValue;
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

		if ( $this->issuer )
		{
			$issuer = $parentNode->ownerDocument->createElement( ElementNames::Issuer, $this->issuer );
			$newElement->appendChild( $issuer );
		}

		if ( $this->issueTime )
		{
			$issueTime = $parentNode->ownerDocument->createElement( ElementNames::IssueTime, $this->issueTime );
			$newElement->appendChild( $issueTime );
		}

		if ( $this->number )
		{
			$number = $parentNode->ownerDocument->createElement( ElementNames::Number, $this->number );
			$newElement->appendChild( $number );
		}
	}

	/** 
	 * Validate @Uri
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( ! $this->issuer )
			throw new \Exception("<Issuer> MUST be provided");

		if ( ! $this->issueTime )
			throw new \Exception("<IssueTime> MUST be provided");

		if ( preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|(\+|-)\d{2}(:?\d{2})?)$/', $this->issueTime, $parts ) ) 
		{
			// valid string format, can now check parts
			$year  = $parts[1];
			$month = $parts[2];
			$day   = $parts[3];
			
			if ( checkdate( $month, $day, $year ) )
				return;
		}

		throw new \Exception("The date <IssueTime> '{$this->issueTime}' is not valid");
	}
}