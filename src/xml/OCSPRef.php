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
 *	<xsd:element name="OCSPRef" type="OCSPRefType"/>
 *
 *	<xsd:complexType name="OCSPRefType">
 *		<xsd:sequence>
 *			<xsd:element name="OCSPIdentifier" type="OCSPIdentifierType" minOccurs="0"/>
 *			<xsd:element name="DigestAlgAndValue" type="DigestAlgAndValueType"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 */

/**
 * Create &lt;OCSPRef>
 */
class OCSPRef extends XmlCore
{
	/**
	 * Represents &lt;DigestAlgAndValue>
	 * @var DigestAlgAndValue (required)
	 */
	public $digestAlgAndValue;

	/**
	 * Represents &lt;OCSPIdentifier>
	 * @var OCSPIdentifier (required)
	 */
	public $ocspIdentifier;

	/**
	 * Create an &lt;OCSPRef> instance
	 * @param string $digestAlgAndValue
	 * @param string $ocspIdentifier
	 */
	public function __construct( $digestAlgAndValue = null, $ocspIdentifier = null )
	{
		$this->digestAlgAndValue = $digestAlgAndValue;
		$this->ocspIdentifier = $ocspIdentifier;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OCSPRef;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return OCSPRef
	 */
	public function loadInnerXml( $node )
	{
		$newElement = parent::loadInnerXml( $node );

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::DigestAlgAndValue:
					$this->digestAlgAndValue = new DigestAlgAndValue();
					$this->digestAlgAndValue->loadInnerXml( $childNode );
					break;

				case ElementNames::OCSPIdentifier:
					$this->ocspIdentifier = new OCSPIdentifier();
					$this->ocspIdentifier->loadInnerXml( $childNode );
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

		if ( $this->digestAlgAndValue )
		{
			$this->digestAlgAndValue->generateXml( $newElement );
		}

		if ( $this->ocspIdentifier )
		{
			$this->ocspIdentifier->generateXml( $newElement );
		}
	}

	/** 
	 * Validate OCSPRef
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->digestAlgAndValue )
		{
			$this->digestAlgAndValue->validateElement();
		}

		if ( $this->ocspIdentifier )
		{
			$this->ocspIdentifier->validateElement();
		}
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

			if ( $this->digestAlgAndValue )
				$this->digestAlgAndValue->traverse( $callback, $depthFirst );

			if ( $this->ocspIdentifier )
				$this->ocspIdentifier->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}