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
 *	<xsd:element name="XMLTimeStamp" type="AnyType"/>
 */

/**
 * Creates a node for &lt;XMLTimeStamp>
 */
class XMLTimeStamp extends Generic
{
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
		parent::__construct( $localName ?? $this->getLocalName(), $prefix, $namespace, $attributes, $childNodes, $preserveWhitespace );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::XMLTimeStamp;
	}
}
