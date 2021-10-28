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
 *
 *	<element name="OtherRefs" type="OtherCertStatusRefsType"/>
 *
 *	<xsd:complexType name="OtherRefsType">
 *		<xsd:sequence>
 *			<xsd:element name="OtherRef" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;OtherRefs>
 */
class OtherRefs extends PropertiesCollection
{
	/**
	 * Create &lt;OtherRef> 
	 *
	 * @param Generic[]|Generic $otherRefs
	 */
	public function __construct( $otherRefs = null )
	{
		parent::__construct( self::createConstructorArray( $otherRefs, OtherRef::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OtherRefs;
	}

	/**
	 * Vaildate &lt;OtherRefs> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$otherRefs = $this->getPropertiesOfClass( OtherRef::class );
		if ( ! $otherRefs )
			throw new \Exception("There must be one or more Other ref if <OtherRefs> is used");

		if ( count( $otherRefs ) != count( $this->properties  ) )
			throw new \Exception("All <OtherRefs> children must be of type OtherRef");
	}
}
