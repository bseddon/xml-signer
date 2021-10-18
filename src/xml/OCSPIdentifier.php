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
 *	<xsd:element name="OCSPIdentifier" type="OCSPIdentifierType"/>
 *
 *	<xsd:complexType name="OCSPIdentifierType">
 *		<xsd:sequence>
 *			<xsd:element name="ResponderID" type="ResponderIDType"/>
 *			<xsd:element name="ProducedAt" type="xsd:dateTime"/> 
 *		</xsd:sequence>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Represents &lt;OCSPIdentifier>
 */
class OCSPIdentifier extends XmlCore
{
	/**
	 * Represents &lt;ResponderID>
	 * @var ResponderID (required)
	 */
	public $responderID;

	/**
	 * Represents &lt;ProducedAt>
	 * @var string (required)
	 */
	public $producedAt;

	/**
	 * The Uri defining the reference to include
	 * @var string
	 */
	public $uri = null;

	/**
	 * Create an &lt;OCSPIdentifier> instance
	 * @param string $uri
	 * @param ResponderID $responderID
	 * @param string $producedAt
	 * @param int $number
	 */
	public function __construct( $uri = null, $responderID = null, $producedAt = null )
	{
		$this->responderID = $responderID;
		$this->producedAt = $producedAt;
		$this->uri = $uri;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OCSPIdentifier;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return OCSPIdentifier
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
				case ElementNames::ResponderID:
					$this->responderID = new ResponderID();
					$this->responderID->loadInnerXml( $childNode );
					break;

				case ElementNames::ProducedAt:
					$this->producedAt = $childNode->nodeValue;
					break;
			}
		}

		return $this;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::URI => $this->uri ), $insertAfter );

		if ( $this->responderID )
		{
			$responderID = $parentNode->ownerDocument->createElement( ElementNames::ResponderID, $this->responderID );
			$newElement->appendChild( $responderID );
		}

		if ( $this->producedAt )
		{
			$producedAt = $parentNode->ownerDocument->createElement( ElementNames::ProducedAt, $this->producedAt );
			$newElement->appendChild( $producedAt );
		}
	}

	/** 
	 * Validate @Uri
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( ! $this->responderID )
			throw new \Exception("<ResponderID> MUST be provided");

		if ( ! $this->producedAt )
			throw new \Exception("<ProducedAt> MUST be provided");

		$this->responderID->validateElement();

		if ( preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|(\+|-)\d{2}(:?\d{2})?)$/', $this->producedAt, $parts ) ) 
		{
			// valid string format, can now check parts
			$year  = $parts[1];
			$month = $parts[2];
			$day   = $parts[3];
			
			if ( checkdate( $month, $day, $year ) )
				return;
		}

		throw new \Exception("The date <ProducedAt> '{$this->producedAt}' is not valid");
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

			if ( $this->responderID )
				$this->responderID->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}}