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
 *	<element name="CRLRefs" type="CRLCertStatusRefsType"/>
 *
 *	<xsd:complexType name="CRLRefsType">
 *		<xsd:sequence>
 *			<xsd:element name="CRLRef" type="CRLRefType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CRLRefs>
 */
class CRLRefs extends PropertiesCollection
{
	/**
	 * Create &lt;CRLRef> 
	 *
	 * @param CRLRef[]|CRLRef $crlRefs
	 */
	public function __construct( $crlRefs = null )
	{
		parent::__construct( self::createConstructorArray( $crlRefs, CRLRef::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CRLRefs;
	}
}
