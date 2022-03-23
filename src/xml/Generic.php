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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 */

/**
 * Creates a generic node when there is no explicit class for a node
 */
class Generic extends XmlCore
{
	/**
	 * Loads an arbitaray node
	 * @param \DOMElement|\DOMDocument $node 
	 * @param bool $preserveWhitespace (optional default false)
	 * @param string[] $classMap (optional) If supplied is will map zero or more tag to a different class.  Eg. CertifiedRole in CertifiedRolesV2 need to map to CertifiedRoleV2
	 * @return XmlCore 
	 */
	public static function fromNode( $node, $preserveWhitespace = false, $classMap = null )
	{
		if ( is_null( $node ) )
			throw new \Exception('Node passed to Generic::fromNode is null');

		if ( $node instanceof \DOMDocument )
		{
			$childNodes = self::loadInnerXmlChildNodes( $node->childNodes, null, null, $preserveWhitespace, $classMap );
			return $childNodes[0];
		}

		// Use the map if one exists
		$localName = $classMap[ $node->localName ] ?? $node->localName;

		// This jiggery pokery is necessary for Linux where dirname cannot cope with backslashes which are no problem on Windows
		$classname = dirname( str_replace('\\', '/', self::class)  ) . '/' . $localName;
		$classname = str_replace('/', '\\', $classname);
		$newNode = null;

		if ( class_exists( $classname, true ) )
		{
			$newNode = new $classname();
		}
		else
		{
			$attributes = array_reduce( iterator_to_array( $node->attributes ), function( $carry, $attr )
			{
				/** @var \DOMAttr $attr */
				$carry[ ( $attr->prefix ? "{$attr->prefix}:" : '' ) . $attr->localName ] = $attr->value;
				return $carry;
			}, array() );

			$newNode = new Generic( $node->localName, $node->prefix, $node->namespaceURI, $attributes, null, $preserveWhitespace );
		}

		if ( $newNode )
			$newNode->loadInnerXml( $node );

		return $newNode;
	}

	/**
	 * Return one or more XmlCore instances representing &lt;Signature> elements
	 * @param Generic $root This will either be a &lt;Signature> node or a &lt;Generic>
	 * @param bool $firstOnly When true only the first instance will be returned
	 * @return Signature|Signature[]
	 */
	public static function getSignature( $root, $firstOnly )
	{
		$result = array();

		if ( $root instanceof Signature )
		{
			if ( $firstOnly ) return $root;
			$result[] = $root;
		}

		// A &lt;<Signature> will only appear inside a Generic element
		// Sure, one can appear inside a &lt;CounterSignature> element
		// but that's not what is going on here which is to find root
		// signatures
		if ( $root instanceof Generic )
		{
			foreach( $root->childNodes ?? array() as $node )
			{
				$signature = self::getSignature( $node, $firstOnly );
				if ( ! $signature ) continue;

				if ( $firstOnly	) return $signature;

				// Getting here $signature should be an array
				$result[] = array_merge( $result, $signature );
			}
		}

		return $result;
	}

	/**
	 * The string to use as the local name in a generated XML node
	 * @var string
	 */
	public $localName;

	/**
	 * A list of zero or more child nodes
	 * @var Generic[]
	 */
	public $childNodes;

	/**
	 * A list of zero or more attribute names and values
	 * @var string[]
	 */
	public $attributes;

	/**
	 * Any text content recorded here
	 *
	 * @var string
	 */
	public $text;

	/** @var flag indicating whether whitespace should be preserved */
	private $preserveWhitespace = false;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 * @param string $localName	 
	 * @param string $prefix	 
	 * @param string $namespace
	 * @param string[] $attributes
	 * @param XmlCore[] $childNodes
	 * @param bool $preserveWhitespace (optional default false)
	 */
	public function __construct( $localName = null, $prefix = null, $namespace = null, $attributes = null, $childNodes = null, $preserveWhitespace = false )
	{
		$this->defaultNamespace = null; // XMLSecurityDSig::XMLDSIGNS;

		$this->localName = $localName;
		$this->prefix = $prefix;
		$this->defaultNamespace = $namespace;
		$this->attributes = $attributes;
		$this->childNodes = $childNodes;
		$this->preserveWhitespace = $preserveWhitespace;
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
		return $this->localName;
	}

	public function getValue()
	{
		return $this->text;
	}

	/**
	 * Returns the default namespace for this instance
	 * @return string
	 */
	public function getDefaultNamespace()
	{
		return $this->defaultNamespace;
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

		if ( strtolower( $this->localName ) == strtolower( $name ) )
		{
			return $this->getObjectFromPath( $pathElements, $exceptionMessage );
		}
	
		foreach( $this->childNodes as $childNode )
		{
			if ( $childNode instanceof Generic )
			{
				if ( strtolower( $childNode->localName ) == strtolower( $name ) )
				{
					$property = $childNode;
					break;
				}
			}
			else
			{
				if ( strtolower( basename( get_class( $childNode ) ) ) == strtolower( $name ) )
				{
					$property = $childNode;
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
	 * Generate the Xml nodes
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $this->attributes, $insertAfter );

		if ( $this->childNodes )
			foreach( $this->childNodes as $childNode)
			{
				$childNode->generateXml( $newElement );
			}
	}

	/**
	 * Load the node and child nodes
	 * @param \DOMElement $node
	 * @return Generic
	 */
	public function loadInnerXml( $node )
	{
		$newElement = parent::loadInnerXml( $node );
		$this->childNodes = self::loadInnerXmlChildNodes( $node->childNodes, $newElement, $node->nodeValue, $this->preserveWhitespace );
		return $this;
	}

	/**
	 * Load the node and child nodes
	 * @param \DOMElement $node
	 * @param bool $preserveWhitespace (optional default false)
	 * @param string[] $classMap (optional) If supplied is will map zero or more tag to a different class.  Eg. CertifiedRole in CertifiedRolesV2 need to map to CertifiedRoleV2
	 * @return XmlCore[]
	 */
	public static function loadInnerXmlChildNodes( $childNodes, $newElement = null, $nodeValue = null, $preserveWhitespace = false, $classMap = null )
	{
		/** @var XmlCore[] $childNodes */
		$resultNodes = null;
		$otherNodes = null;
		foreach( $childNodes as $childNode )
		{
			/** @var \DOMNode $childNode */
			if ( $childNode->nodeType == XML_ELEMENT_NODE ) 
			{
				$newElement = self::fromNode( $childNode, $preserveWhitespace, $classMap );
				$resultNodes[] = $newElement;
				$newElement->beforeText = $otherNodes;
				$otherNodes = null;
			}
			else if ( $childNode->nodeType == XML_TEXT_NODE )
			{
				// Have to get the node value this way because the nodeValue property 
				// of the DOM contains all the text from the descendent nodes
				if ( $childNode->nodeValue == $nodeValue )
				{
					$newElement->text = $nodeValue;
				}
				else if ( $preserveWhitespace )
					$otherNodes[ $childNode->nodeType ] = $childNode->nodeValue;
				}
			else if ( $preserveWhitespace )
			{
				$otherNodes[ $childNode->nodeType ] = $childNode->nodeValue;
			}
		}
		$newElement->afterText = $otherNodes;
		return $resultNodes;
	}

	/**
	 * Validate
	 *
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( ! $this->localName )
		{
			throw new \Exception("The element local name must be valid");
		}

		if ( $this->childNodes )
		{
			foreach( $this->childNodes as $childNode )
				$childNode->validateElement();
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
				$childNode->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
