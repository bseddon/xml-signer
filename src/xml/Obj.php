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
 *	<!-- targetNamespace="http://www.w3.org/2000/09/xmldsig#" -->
 *
 *	<xsd:element name="Object" type="xsd:string" minOccurs="0"/>
 */

/**
 * Creates a node for &lt;Object>
 * This element is just a placeholder so there are no properties
 * and just the XmlCore behaviour will be used
 */
class Obj extends XmlCore
{
	/**
	 * One or a list of nodes to include as child nodes
	 *
	 * @var XmlCore[]
	 */
	public $childNodes;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 */
	public function __construct( $childNodes = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		if ( is_array( $childNodes ) )
		{
			foreach( $childNodes as $childNode )
			{
				if ( $childNode instanceof XmlCore ) continue;
				throw new \Exception("All <Object> children must be descendents of XmlCore");
			}
		}
		else if ( $childNodes instanceof XmlCore )
		{
			$this->childNodes[] = $childNodes;
		}
	}

	/**
	 * Add a node to the child nodes collection 
	 *
	 * @param XmlCore $childNode
	 * @return XmlCode
	 */
	public function addChildNode( $childNode )
	{
		array_push( $this->childNodes, $childNode );
		return $childNode;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Object;
	}

	/**
	 * Generate the Xml for the node
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode, $attributes );

		if ( $this->childNodes )
			foreach( $this->childNodes as $childNode )
			{
				$childNode->generateXml( $newElement );
			}

		return $newElement;
	}

	/**
	 * Get the child element that is of type QualifyingProperties
	 * @return QualifyingProperties
	 */
	public function getQualifyingProperties()
	{
		foreach( $this->childNodes as $childNode )
		{
			if ( $childNode instanceof QualifyingProperties ) return $childNode;
		}

		return null;
	}

	/**
	 * Load the node and child nodes
	 *
	 * @param \DOMElement $node
	 * @return Obj
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			$this->childNodes[] = Generic::fromNode( $childNode );
		}
	}

	/**
	 * Validate
	 *
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		foreach( $this->childNodes as $childNode )
		{
			/** @var XmlCore $childNode */
			$childNode->validateElement();
		}
	}

	/**
	 * Returns an object or null for the instance on the 
	 * path described by the element in the parameter array
	 * @param string[] $pathElements
	 * @param string $exceptionMessage
	 * @return mixed
	 */
	public function getObjectFromPath( $pathElements, $exceptionMessage = null )
	{
		/** @var ReflectionClass $reflection */
		$reflection = new \ReflectionClass( $this );

		// Use the first element in the list which names the next object to return
		$name = array_shift( $pathElements );

		if ( strtolower( basename( $reflection->name ) ) == strtolower( $name ) )
		{
			return $this->getObjectFromPath( $pathElements, $exceptionMessage );
		}

		$properties = array_filter( $this->childNodes, function( $property ) use( $name )
		{
			return strtolower( $name ) == strtolower( basename( get_class( $property ) ) );
		} );

		if ( ! $properties ) 
		{
			if ( $exceptionMessage )
			{
				throw new \Exception( $exceptionMessage );
			}
			return null;
		}

		$property = reset( $properties );

		if ( $property && $pathElements )
		{
			return $property->getObjectFromPath( $pathElements, $exceptionMessage );
		}
		else if ( ! $property && $exceptionMessage )
		{
			throw new \Exception( $exceptionMessage );
		}
		else
		{
			return $property;
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

			foreach( $this->childNodes as $childNode )
				$childNode->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
