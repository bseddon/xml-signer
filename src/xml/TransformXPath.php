<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\xmldsig\XMLSecurityDSig;

/**
 *   <element name="Transform" type="ds:TransformType"/>
 * 
 *   <complexType name="TransformType" mixed="true">
 *     <choice minOccurs="0" maxOccurs="unbounded"> 
 *       <any namespace="##other" processContents="lax"/>
 *       <!-- (1,1) elements from (0,unbounded) namespaces -->
 *       <element name="XPath" type="string"/> 
 *     </choice>
 *     <attribute name="Algorithm" type="anyURI" use="required"/> 
 *   </complexType> * 
 */

/**
 * Creates a node for &lt;Transform> 
 */
class TransformXPath extends Transform
{
	/**
	 * Represents an optional collection of &lt;Transform>
	 * @var XPathFilter[]
	 */
	public $xpaths = array();

	/**
	 * Assign one of more <XPath> to this instance
	 * @param XPathFilter|XPathFilter[]|string $xpaths
	 */
	public function __construct(  $xpaths = null )
	{
		parent::__construct( XMLSecurityDSig::CXPATH );

		$this->xpaths = self::createConstructorArray( $xpaths, XPathFilter::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Transform;
	}

	/**
	 * Create &lt;TransformXPath> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::Algorithm => $this->algorithm ), $insertAfter );

		foreach ( $this->xpaths as $xpath )
		{
			$xpath->generateXml( $newElement );
		}
	}

	/**
	 * Generate the correct type of XPath class
	 * @param string $query (optional)
	 * @return XPathFilter
	 */
	protected function createXPathInstance( $query = null )
	{
		return new XPathFilter( $query );
	}

	/**
	 * Load the XPath elements
	 *
	 * @param \DOMElement $node
	 * @return void
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		foreach( $node->childNodes as $subNode )
		{
			/** @var \DOMElement $subNode */
			if ( $subNode->nodeType != XML_ELEMENT_NODE ) continue;
			if ( $subNode->localName != ElementNames::XPath ) continue;

			// This node is going to be read by XPathFilter
			$xpath = $this->createXPathInstance();
			$xpath->loadInnerXml( $subNode );
			$this->xpaths[] = $xpath;
		}
	}

	/**
	 * Validate all references are DocumentationReference instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		foreach ( $this->xpaths as $xpath )
		{
			if ( ! $xpath instanceof XPathFilter )
				throw new \Exception("All <XPath> children must be of type XPath");

			$xpath->validateElement();
		}
	}

	/**
	 * Converts a transform to a simple representation representation for use by XMLSecurityDSig::AddRefInternal()
	 * @return void
	 */
	public function toSimpleRepresentation()
	{
		return array( $this->algorithm => array(
			array_reduce( $this->xpaths, function( $carry, $xpath )
			{
				/** @var XPathFilter $xpath */
				$carry[] = array(
					'query' => $xpath->text,
					'namespaces' => $xpath->namespaces
				);
				return $carry;
			}, array() )
		) );
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

			foreach( $this->xpaths as $xpath )
				$xpath->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
