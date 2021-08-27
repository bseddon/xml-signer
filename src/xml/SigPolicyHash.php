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
 *	<xsd:element name="SigPolicyHash" type="DigestAlgAndValueType"/>
 *
 *	<xsd:complexType name="DigestAlgAndValueType">
 *		<xsd:sequence>
 *			<xsd:element ref="ds:DigestMethod"/>
 *			<xsd:element ref="ds:DigestValue"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SigPolicyHash>
 */
class SigPolicyHash extends XmlCore
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
	 * Create an instance of &lt;SigPolicyHash> and pass in an instance of &lt;DigestMethod> and &lt;DigestValue>
	 * @param DigestMethod $digestMethod
	 * @param DigestValue $digestValue
	 */
	public function __construct( $digestMethod = null, $digestValue = null )
	{
		$this->digestMethod = self::createConstructor( $digestMethod, DigestMethod::class );
		$this->digestValue = self::createConstructor( $digestValue, DigestValue::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigPolicyHash;
	}

	/**
	 * Load the child elements of &lt;SigPolicyHash>
	 * @param \DOMElement $node
	 * @return SigPolicyHash
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
	}

	/**
	 * Create &lt;SigPolicyHash> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->digestMethod )
			$this->digestMethod->generateXml( $newElement );

		if ( $this->digestValue )
			$this->digestValue->generateXml( $newElement );
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
