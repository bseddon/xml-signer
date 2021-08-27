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
 *	<xsd:element name="CRLRef" type="CRLRefType"/>
 *
 *	<xsd:complexType name="CRLRefType">
 *		<xsd:sequence>
 *			<xsd:element name="DigestAlgAndValue" type="DigestAlgAndValueType"/>
 *			<xsd:element name="CRLIdentifier" type="CRLIdentifierType" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 */

/**
 * Create &lt;CRLRef>
 */
class CRLRef extends XmlCore
{
	/**
	 * Represents &lt;DigestAlgAndValue>
	 * @var DigestAlgAndValue (required)
	 */
	public $digestAlgAndValue;

	/**
	 * Represents &lt;CRLIdentifier>
	 * @var CRLIdentifier (required)
	 */
	public $crlIdentifier;

	/**
	 * Create an &lt;CRLRef> instance
	 * @param string $digestAlgAndValue
	 * @param string $crlIdentifier
	 */
	public function __construct( $digestAlgAndValue = null, $crlIdentifier = null )
	{
		$this->digestAlgAndValue = $digestAlgAndValue;
		$this->crlIdentifier = $crlIdentifier;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CRLRef;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return CRLRef
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

				case ElementNames::CRLIdentifier:
					$this->crlIdentifier = new CRLIdentifier();
					$this->crlIdentifier->loadInnerXml( $childNode );
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

		if ( $this->digestAlgAndValue )
		{
			$this->digestAlgAndValue->generateXml( $newElement );
		}

		if ( $this->crlIdentifier )
		{
			$this->crlIdentifier->generateXml( $newElement );
		}
	}

	/** 
	 * Validate CRLRef
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->digestAlgAndValue )
		{
			$this->digestAlgAndValue->validateElement();
		}

		if ( $this->crlIdentifier )
		{
			$this->crlIdentifier->validateElement();
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

			if ( $this->crlIdentifier )
				$this->crlIdentifier->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}