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
 *	<xsd:element name="DigestAlgAndValue" type="DigestAlgAndValueType"/>
 *
 *	<xsd:complexType name="DigestAlgAndValueType">
 *		<xsd:sequence>
 *			<xsd:element ref="ds:DigestMethod"/>
 *			<xsd:element ref="ds:DigestValue"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;DigestAlgAndValue>
 */
class DigestAlgAndValue extends XmlCore
{
	/**
	 * A &lt;DigestMethod>
	 * @var DigestMethod
	 */
	public $digestMethod = null;

	/**
	 * A &lt;DigestValue>
	 * @var DigestValue
	 */
	public $digestValue = null;

	/**
	 * Creage an instance of <DigestAlgAndValue> and pass in an instance of <ObjectIdentifier>
	 * @param DigestMethod $digestMethod
	 * @param DigestValue $digestValue
	 */
	public function __construct( $digestMethod = null, $digestValue = null )
	{
		$this->digestMethod = $digestMethod;
		$this->digestValue = $digestValue;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::DigestAlgAndValue;
	}

	/**
	 * Create &lt;DigestAlgAndValue> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		$this->digestMethod->generateXml( $newElement );
		$this->digestValue->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return DigestAlgAndValue
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// No attributes for this element

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::DigestMethod:
					$this->digestMethod = new DigestMethod();
					$this->digestMethod->loadInnerXml( $childNode );
					break;

				case ElementNames::DigestValue:
					$this->digestValue = new DigestValue();
					$this->digestValue->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/** 
	 * Validate &lt;DigestMethod> and &lt;DigestValue>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$this->digestMethod->validateElement();
		$this->digestValue->validateElement();
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

			if ( $this->digestMethod )
				$this->digestMethod->traverse( $callback, $depthFirst );

			if ( $this->digestValue )
				$this->digestValue->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
