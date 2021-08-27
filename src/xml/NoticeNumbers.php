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
 *	<xsd:element name="NoticeNumbers" type="IntegerListType"/>
 *
 *	<xsd:complexType name="IntegerListType">
 *		<xsd:sequence>
 *			<xsd:element name="int" type="xsd:integer" minOccurs="0" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;NoticeNumbers> which is a list of integers
 */
class NoticeNumbers extends PropertiesCollection
{
	/**
	 * Create &lt;CRLRef> 
	 *
	 * @param Integer[]|Integer $integers
	 */
	public function __construct( $integers = null )
	{
		parent::__construct( self::createConstructorArray( $integers, Integer::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::NoticeNumbers;
	}

	
}
