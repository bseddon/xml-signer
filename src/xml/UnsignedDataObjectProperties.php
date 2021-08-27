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
 *	<xsd:element name="UnsignedDataObjectProperties" type="UnsignedDataObjectPropertiesType"/>
 *
 *	<xsd:complexType name="UnsignedDataObjectPropertiesType">
 *		<xsd:sequence>
 *			<xsd:element name="UnsignedDataObjectProperty" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;unsignedDataObjectProperties> which contains one or more arbitrary child nodes>
 */
class UnsignedDataObjectProperties extends XmlCore
{
	/**
	 * A set of nodes represented by a Generic array
	 *
	 * @var Generic[]
	 */
	public $unsignedDataObjectProperty;

	/**
	 * Creates an UnsignedDataObjectProperties instance
	 * @param Generic|Generic|string $content This is the subordinate content
	 * @param string $id (optional)
	 */
	public function __construct( $content = null, $id = null )
	{
		// if ( $content )
		//	throw new \Exception("<UnsignedDataObjectProperties> is not supported yet.");

		$this->unsignedDataObjectProperty = self::createConstructorArray( $content, Generic::class );

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::UnsignedDataObjectProperties;
	}

	public function generateXml( $parentNode, $attributes = array() )
	{
		parent::validateElement();

		// TODO
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


			// TODO
				
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
