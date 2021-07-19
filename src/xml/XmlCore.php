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

use lyquidity\xmldsig\XAdES;

abstract class XmlCore
{
	/**
	 * The name of the Xml element
	 * @var string
	 */
	protected $localName = null;

	/**
	 * When null the class will assume the default namespace
	 * @var string
	 */
	public $defaultNamespace = null;

	/**
	 * A list of namespaces to apply to an element indexed by the prefix to use
	 *
	 * @var array
	 */
	public $namespaces = array();

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
		return $this->localName;
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
	 * Generates Xml nodes for the instance.  This default implementation adds a 
	 * default namespace if its different to the parents and any namespaces that
	 * are not yet defined.
	 * 
	 * Its expected this function will be overridden.
	 * 
	 * Note that a descendent might all all of if sub-elements or might call one
	 * or more other instances to add elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $namespaces
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$doc = $parentNode->ownerDocument;
		$namespaces = self::getNamespaces( $doc );

		$defaultNamespace = $this->getDefaultNamespace();
		$prefix = array_search( $defaultNamespace, $namespaces );
		$nodeName = $prefix === false
			? $this->getLocalName()
			: "$prefix:" . $this->getLocalName();

		$newElement = $doc->createElement( $nodeName );

		if ( $parentNode->namespaceURI != $defaultNamespace )
		{
			$newNode = $doc->createAttribute( 'xmlns' );
			$newNode->value = $defaultNamespace;
			$newElement->appendChild( $newNode );
		}

		$additionalNamespaces = array_diff_key( $this->namespaces, $namespaces );
		foreach( $additionalNamespaces as $prefix => $namespaceURI )
		{
			$newNode = $doc->createAttribute( 'xmlns:' . $prefix );
			$newNode->value = $namespaceURI;
			$newElement->appendChild( $newNode );
		}

		foreach( $attributes as $name => $value )
		{
			$newNode = $doc->createAttribute( $name );
			$newNode->value = $value;
			$newElement->appendChild( $newNode );
		}
	}

	/**
	 * Undocumented function
	 *
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
}

