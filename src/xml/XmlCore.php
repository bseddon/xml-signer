<?php

/**
 * This file contains all the classes used to represent the various property elements used by
 * XAdES.  All  utimately descend from XmlCore which provides core properties and functions.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

use Closure;
use lyquidity\Asn1\Element\Integer;
use lyquidity\xmldsig\XAdES;
use Reflection;

abstract class XmlCore
{
	/**
	 * Often there's no guarantee what type value will be passed to a constructor
	 * so this function checks it and returns an appropriate type or an exception.
	 * @param mixed $param
	 * @param string $type
	 * @return mixed[]
	 */
	public static function createConstructorArray( $param, $type )
	{
		if ( is_null( $param ) ) return null;
		if ( is_string( $param ) ) return array( new $type( $param ) );
		if ( is_object( $param ) && $param instanceof $type ) return array( $param );
		if ( is_array( $param ) )
		{
			foreach( $param as $index => $item )
			{
				if ( $item instanceof $type ) continue;
				if ( is_string( $item ) )
				{
					if ( is_a( $type, TextBase::class, true ) )
					{
						$param[ $index ] = new $type( $item );
						continue;
					}
				}
				throw new \Exception("The type of {$type} parameter is not valid.");
			}

			return $param;
		}

		throw new \Exception("The type of {$type} parameter is not valid.");
	}

	/**
	 * Often there's no guarantee what type value will be passed to a constructor
	 * so this function checks it and returns an appropriate type or an exception.
	 * @param mixed $param
	 * @param string $type
	 * @return mixed
	 */
	public static function createConstructor( $param, $type )
	{
		if ( is_null( $param ) ) return null;
		if ( is_string( $param ) ) return new $type( $param );
		if ( is_object( $param ) && $param instanceof $type ) return $param;
		throw new \Exception("The type of {$type} parameter is not valid.");
	}

	/**
	 * A list of the arrays in the document after loadInnerXml(). If there
	 * are multiple ids with the same value then only the first will survive.
	 * @var XmlCore[]
	 */
	private static $ids = null;

	/** 
	 * Add the id in the instance to the Ids list 
	 * @param XmlCore $xmlCore
	 * */
	private static function addId( $xmlCore )
	{
		// If there is no id to add or if the id already exists in the ids list
		if ( is_null( self::$ids ) || ! $xmlCore || ! $xmlCore->id  || ( self::$ids[ $xmlCore->id ] ?? false ) ) return;

		self::$ids[ $xmlCore->id ] = $xmlCore;
	}

	/**
	 * Returns a copy of the ids array (so the caller can't mess with it)
	 *
	 * @return void
	 */
	public static function getIds()
	{
		return self::$ids;
	}

	/**
	 * Returns the node with the id or null
	 * @param string $id
	 * @return XmlCore
	 */
	public static function getNodeWithId( $id )
	{
		return self::$ids[ $id ] ?? null;
	}

	/**
	 * Reset the liost of Ids
	 * @return void
	 */
	public static function resetIds()
	{
		self::$ids = array();
	}

	/**
	 * When null the class will assume the default namespace
	 * @var string
	 */
	public $defaultNamespace = null;

	/**
	 * Used when loading Xml
	 * @var string
	 */
	public $prefix = null;

	/**
	 * A list of namespaces to apply to an element indexed by the prefix to use
	 * @var array
	 */
	public $namespaces = array();

	/**
	 * Any element can potentially have @id
	 * @var string
	 */
	public $id = null;

	/**
	 * @var string[]
	 */
	public $beforeText;

	/**
	 * @var string[]
	 */
	public $afterText;

	/**
	 * The parent object of $this
	 * @var XmlCore
	 */
	public $parent = null;

	/**
	 * The Xml node corresponding to this instance
	 * @var \DOMElement
	 */
	public $node = null;

	/**
	 * Get the path to the corresponding XmlNode
	 * @return string
	 */
	public function getPath()
	{
		if ( ! $this->node )
		{
			$classname = get_class( $this );
			throw new \Exception("An Xml node is missing for $classname");
		}

		return $this->node->getNodePath();
	}

	/**
	 * When non-empty the contents of this field will be added to a node as textContent
	 * @var string
	 */
	public function getValue() { return null; }

	/**
	 * Clear the parent so it does not stop this instance being released
	 */
	function __destruct()
	{
		unset( $this->parent );
		unset( $this->node );
	}

	/**
	 * Returns the default namespace for this instance
	 * @return string
	 */
	public function getDefaultNamespace()
	{
		if ( $this->defaultNamespace ) return $this->defaultNamespace;

		global $xadesNamespace;
		return $xadesNamespace
			? $xadesNamespace
			: XAdES::NamespaceUrl2016;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return '';
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

		$properties = array_filter( $reflection->getProperties( \ReflectionProperty::IS_PUBLIC ), function( $property ) use( $name )
		{
			return strtolower( $name ) == strtolower( $property->name );
		} );

		if ( ! $properties ) 
		{
			if ( $exceptionMessage )
			{
				throw new \Exception( $exceptionMessage );
			}
			return null;
		}

		$property = $this->{ reset( $properties )->name };

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
	 * Get the root <Signature>
	 * @param string $exceptionMessage
	 * @return Signature
	 */
	public function getRootSignature( $exceptionMessage = null )
	{
		if ( $this instanceof Signature ) return $this;

		if ( ! $this->parent ) 
			if ( $exceptionMessage )
				throw new \Exception( $exceptionMessage );
			else
				return null;

		return $this->parent->getRootSignature( $exceptionMessage );
	}

	/** 
	 * Allows the structure to be validated. For example to check that required 
	 * attributes have a value or that one of choice of elements has been set.
	 * Implementations will throw an exception when a validation error is found.
	 * 
	 * Its expected this function will be overridden.
	 * @throws \Exception
	 */
	public function validateElement()
	{}

	/**
	 * Create a node for the node type
	 * @param \DOMDocument $doc
	 * @param Integer $nodeType
	 * @param string $value
	 * @param string $target
	 * @return \DOMNode
	 */
	private function createNode( $doc, $nodeType, $value, $target = null )
	{
		switch( $nodeType )
		{
			case XML_TEXT_NODE:
				return $doc->createTextNode( $value );

			case XML_CDATA_SECTION_NODE:
				return null;

			case XML_ENTITY_REF_NODE:
			case XML_ENTITY_NODE:
				return $doc->createEntityReference( $value );

			case XML_PI_NODE:
				return $doc->createProcessingInstruction( $target, $value );

			case XML_COMMENT_NODE:
				return $doc->createComment( $value);

			case XML_NOTATION_NODE:
				return null;

			case XML_DTD_NODE:
				return null;

			case XML_ATTRIBUTE_ID:
			case XML_ATTRIBUTE_IDREF:
			case XML_ATTRIBUTE_IDREFS:
			case XML_ATTRIBUTE_ENTITY:
			case XML_ATTRIBUTE_NMTOKEN:
			case XML_ATTRIBUTE_NMTOKENS:
			case XML_ATTRIBUTE_ENUMERATION:
			case XML_ATTRIBUTE_NOTATION:
				return $doc->createAttribute( $value);
				break;

		}
	}

	/**
	 * Generates Xml nodes for the instance.  This default implementation adds a 
	 * default namespace if its different to the parents and any namespaces that
	 * are not yet defined.
	 * 
	 * Its expected this function will be overridden.
	 * 
	 * Note that a descendent might call on all of its sub-elements or might call one
	 * or more other instances to add elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $namespaces
	 * @param string[] $attributes
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		/**
		 * @var \DOMDocument $doc
		 */
		$doc = $parentNode instanceof \DOMDocument ? $parentNode : $parentNode->ownerDocument;
		$namespaces = self::getNamespaces( $doc );

		$defaultNamespace = $this->getDefaultNamespace();
		$prefix = $this->prefix ?? array_search( $defaultNamespace, $namespaces );
		$nodeName = $prefix === false
			? $this->getLocalName()
			: "$prefix:" . $this->getLocalName();

		if ( $this->beforeText )
		{
			if ( ! is_array( $this->beforeText ) ) $this->beforeText[ XML_TEXT_NODE ] = $this->beforeText;

			foreach( $this->beforeText as $nodeType => $text )
			{
				$doc = $parentNode instanceof \DOMDocument ? $parentNode : $parentNode->ownerDocument;
				$newNode = $this->createNode( $doc, $nodeType, $text );
				$parentNode->appendChild( $newNode );
			}
		}

		$this->node = $newElement = $doc->createElementNS( $defaultNamespace, $nodeName );
		
		if ( $value = $this->getValue() )
		{
			$newElement->nodeValue = $value;
		}

		$parentNode->appendChild( $newElement );

		$additionalNamespaces = array_diff_key( $this->namespaces, $namespaces );
		foreach( $additionalNamespaces as $prefix => $namespaceURI )
		{
			$tag = 'xmlns' . ( $prefix ? ":$prefix" : '' );
			$attr = $newElement->getAttribute( $tag );
			if ( $attr ) continue;
			$newNode = $doc->createAttribute( $tag );
			$newNode->value = $namespaceURI;
			$newElement->appendChild( $newNode );
		}

		// If the attributes array does not already contain @id the prepend it
		if ( ! is_null( $this->id ) && ! isset( $attributes[ AttributeNames::Id ] ) )
		{
			$attributes = array_merge( array( AttributeNames::Id => $this->id ), $attributes );
		}

		if ( $attributes )
		foreach( $attributes as $name => $value )
		{
			if ( is_null ( $value ) ) continue;

			$newNode = $doc->createAttribute( $name );
			$newNode->value = $value;
			$newElement->appendChild( $newNode );
		}

		if ( $this->afterText )
		{
			if ( ! is_array( $this->afterText ) ) $this->afterText[ XML_TEXT_NODE ] = $this->afterText;

			foreach( $this->afterText as $nodeType => $text )
			{
				$doc = $parentNode instanceof \DOMDocument ? $parentNode : $parentNode->ownerDocument;
				$newNode = $this->createNode( $doc, $nodeType, $text );
				$parentNode->appendChild( $newNode );
			}
		}

		return $newElement;
	}

	/**
	 * Gets a list of the current document namespaces indexed by prefix
	 * @param \DOMDocument $doc
	 * @return string[]
	 */
	public static function getNamespaces( $doc )
	{
		$xpath = new \DOMXPath( $doc );
		$namespaceNodes = $xpath->query( "namespace::*" );
		return array_reduce( iterator_to_array( $namespaceNodes ), function( $carry, $node )
		{
			/** @var \DOMNameSpaceNode $node */
			/** @var string[] $carry */
			if ( $node->localName != 'xmlns' )
				$carry[ $node->localName ] = $node->namespaceURI;
			return $carry;
		}, array() );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate).
	 * @param \DOMElement $node
	 * @return XmlCore
	 */
	public function loadInnerXml( $node )
	{
		// Crawl the stack to find the first object that is not $this
		// The object, if there is one, is the parent object
		foreach( debug_backtrace() as $frame )
		{
			if ( ! isset( $frame['object'] ) || $frame['object'] == $this ) continue;
			$this->parent = $frame['object'];
			break;
		}
		
		// Record the node
		$this->node = $node;

		// Get generic information from the element
		$this->prefix = $node->prefix ? $node->prefix : null;
		$this->defaultNamespace = $node->namespaceURI;
		
		$doc = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
	
		$xpath = new \DOMXPath( $doc );

		$namespaces = $xpath->query( 'namespace::*', $node );
		$skipNamespaces = array( "http://www.w3.org/XML/1998/namespace", "http://www.w3.org/2001/XMLSchema-instance", $this->defaultNamespace );

		foreach( $namespaces as $namespaceNode )
		{
			/** @var \DOMNameSpaceNode $namespaceNode */
			// Check that the namespaceURI is not the default and that the 'attribute exists
			// if ( array_search( $namespaceNode->namespaceURI, $skipNamespaces ) !== false ) continue;
			$attr = $node->getAttribute( $namespaceNode->nodeName );
			if ( ! $attr ) continue;
			$this->namespaces[ $namespaceNode->prefix ] = $namespaceNode->namespaceURI;
		}

		$attr = $node->getAttributeNode( AttributeNames::Id );
		if ( $attr )
		{
			$this->id = $attr->value;
			self::addId( $this );
		}

		return $this;
	}

	/**
	 * Calls the closure in $callback and does the same on any descendents
	 * Descendents with child nodes should override and call on all children.
	 * @param Closure $callback
	 * @param bool $depthFirst (optional: default = false)  When true this will call on child nodes first
	 * @return XmlCore
	 */
	public function traverse( $callback, $depthFirst = false )
	{
		if ( $callback instanceof \Closure )
			$callback( $this );
		return $this;
	}
}
