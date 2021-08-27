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
 *	<xsd:element name="CounterSignature" type="CounterSignatureType"/>
 *	
 *	<xsd:complexType name="CounterSignatureType">
 *		<xsd:sequence>
 *			<xsd:element ref="ds:Signature"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CounterSignature>
 */
class CounterSignature extends PropertiesCollection implements UnsignedSignatureProperty
{
	/**
	 * Create an instance of &lt;CounterSignature> and pass in instances of &lt;Signature>
	 * @param Signature{}|Signature $signatures
	 */
	public function __construct( $signatures = null, $id = null )
	{
		parent::__construct( self::createConstructorArray( $signatures, Signature::class ) );
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CounterSignature;
	}
}
