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
 *	<element name="OCSPRefs" type="OCSPCertStatusRefsType"/>
 *
 *	<xsd:complexType name="OCSPRefsType">
 *		<xsd:sequence>
 *			<xsd:element name="OCSPRef" type="OCSPRefType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;OCSPRefs>
 */
class OCSPRefs extends PropertiesCollection
{
	/**
	 * Create &lt;OCSPRef> 
	 * @param OCSPRef[]|OCSPRef $ocspRefs
	 */
	public function __construct( $ocspRefs = null )
	{
		parent::__construct( self::createConstructorArray( $ocspRefs, OCSPRef::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OCSPRefs;
	}

	/**
	 * Vaildate &lt;OCSPRefs> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$ocspRefs = $this->getPropertiesOfClass( OCSPRef::class );
		if ( ! $ocspRefs )
			throw new \Exception("There must be one or more OCSP ref if <OCSPRefs> is used");

		if ( count( $ocspRefs ) != count( $this->properties  ) )
			throw new \Exception("All <OCSPRefs> children must be of type OCSPRef");
	}
}
