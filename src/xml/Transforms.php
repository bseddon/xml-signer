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
 * 
 *	<element name="Transforms" type="ds:TransformsType"/>
 *
 *	<complexType name="TransformsType">
 *     <sequence>
 *       <element ref="ds:Transform" maxOccurs="unbounded"/>  
 *     </sequence>
 *   </complexType>
 *
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
 * Creates a node for &lt;Transforms> which contains one or more &lt;Transform>
 */
class Transforms extends XmlCore
{
	/**
	 * Represents a collection of &lt;Transform>
	 * @var Transform[]
	 */
	public $transforms = array();

	/**
	 * Assign one of more references to this instance
	 *
	 * @param Transform|Transform[]|string $transforms (optional)
	 */
	public function __construct( $transforms = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		$this->transforms = self::createConstructorArray( $transforms, Transform::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Transforms;
	}

	/**
	 * Create &lt;Transforms> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		if ( ! $this->transforms ) return;

		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		foreach ( $this->transforms as $transform )
		{
			$transform->generateXml( $newElement );
		}
	}

	/**
	 * Validate all references are DocumentationReference instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		foreach ( $this->transforms as $transform )
		{
			if ( ! $transform instanceof Transform )
				throw new \Exception("All <Transforms> children must of type Transform");

			$transform->validateElement();
		}
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Transforms
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// There are no attributes for this element

		// Look for elements with the tag <Transform>
		foreach( $node->childNodes as $node )
		{
			/** @var \DOMElement $node */
			if ( $node->nodeType != XML_ELEMENT_NODE || $node->localName != ElementNames::Transform ) continue;

			// Get @Algorithm 
			$attr = $node->getAttributeNode(AttributeNames::Algorithm );
			// Its an error if there is no @Algorithm as its a required attribute
			if ( ! $attr )
				throw new \Exception("There is no @Algorithm");

			$classname = str_replace( '/', '\\', dirname( str_replace( '\\', '/', get_class( $this ) ) ) ) . '\\' . Transform::transformMap[ $attr->value ] ?? ElementNames::Transform;
			$transform = new $classname();
			$transform->algorithm = $attr->value;
			$transform->loadInnerXml( $node );
			$this->transforms[] = $transform;
		}

		return $this;
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

			foreach( $this->transforms as $transform )
				$transform->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}

	/**
	 * Returns true if any child transform is enveloped
	 *
	 * @return boolean
	 */
	public function hasEnveloped()
	{
		if ( ! $this->transforms ) return false;

		foreach( $this->transforms as $transform )
		{
			if ( $transform->isEnveloped() ) return true;
		}

		return false;
	}
}
