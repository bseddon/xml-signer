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
 * Creates a collection of XmlCore nodes
 */
abstract class PropertiesCollection extends XmlCore
{
	/**
	 * A list of the unsigned signature properties
	 * @var XmlCore|XmlCore[] $properties
	 */
	public $properties = array();

	/**
	 * If supplied is will map zero or more tag to a different class.  Eg. CertifiedRole in CertifiedRolesV2 need to map to CertifiedRoleV2
	 * @var string[]
	 */
	private $classMap;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * @param XmlCore|XmlCore[] $properties
	 * @param string[] $classMap (optional) If supplied is will map zero or more tags to a different class.  Eg. CertifiedRole in CertifiedRolesV2 need to map to CertifiedRoleV2
	 */
	public function __construct( 
		$properties = null,
		$classMap = null
	)
	{
		$this->classMap = $classMap;

		if ( $properties instanceof XmlCore )
			$this->properties[] = $properties;
		else if ( ! is_null( $properties ) && ! is_array( $properties ) )
		{
			$propertyName = basename( get_class( $this ) );
			throw new \Exception("The parameter passed to <$propertyName> must be an appropriate property of an array of them");
		}
		else if ( is_array( $properties ) )
			$this->properties = array_filter( $properties );
	}

	/**
	 * Add a property to the properties collection 
	 *
	 * @param XmlCore $property
	 * @return XmlCore
	 */
	public function addProperty( $property )
	{
		array_push( $this->properties, $property );
		return $property;
	}

	/**
	 * Add a property to the properties collection 
	 *
	 * @param XmlCore $property
	 * @param int $position
	 * @return XmlCore
	 */
	public function addPropertyAtPosition( $property, $position )
	{
		if ( ! count( $this->properties ) || $position >= count( $this->properties ) )
		{
			$this->addProperty( $property );
		}
		else
		{
			array_splice( $this->properties, $position, 0, array( $property ) ); // splice in at position $position
		}
		return $property;
	}

	/**
	 * Create &lt;properties> and any descendent elements
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( ! $this->properties ) return false;

		foreach( $this->properties as $property )
		{
			$property->generateXml( $newElement );
		}
	}

	/**
	 * Return a list of the properties that match the class name
	 *
	 * @param string $classname
	 * @return XmlCore[]
	 */
	public function getPropertiesOfClass( $classname )
	{
		return array_filter( $this->properties, function( $property ) use( $classname )
		{
			return $property instanceof $classname;
		} );
	}

	/**
	 * Returns an object or null for the instance on the 
	 * path described by the element in the parameter array
	 * @param string[] $pathElements
	 * @param string $exceptionMessage
	 * @return mixed
	 * @throws \Exception
	 */
	public function getObjectFromPath( $pathElements, $exceptionMessage = null )
	{
		// Use the first element in the list which names the next object to return
		$name = array_shift( $pathElements );

		$property = null;

		if ( strtolower( $this->getLocalName() ) == strtolower( $name ) )
		{
			return $this->getObjectFromPath( $pathElements, $exceptionMessage );
		}
	
		foreach( $this->properties as $prop )
		{
			if ( $prop instanceof Generic )
			{
				if ( strtolower( $prop->localName ) == strtolower( $name ) )
				{
					$property = $prop;
					break;
				}
			}
			else
			{
				if ( strtolower( basename( get_class( $prop ) ) ) == strtolower( $name ) )
				{
					$property = $prop;
					break;
				}
			}
		}

		if ( $property && $pathElements )
		{
			return $property->getObjectFromPath( $pathElements );
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
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return PropertiesCollection
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$this->properties = Generic::loadInnerXmlChildNodes( $node->childNodes, null, null, false, $this->classMap );

		return $this;
	}

	/**
	 * Allow the properties to validate themselves
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		foreach( $this->properties as $property )
		{
			$property->validateElement();
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

			foreach( $this->properties as $property )
				$property->traverse( $callback, $depthFirst );

			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
