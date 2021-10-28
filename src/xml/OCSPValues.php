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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#"
 * 
 *	<xsd:element name="OCSPValues" type="OCSPValuesType" minOccurs="0"/>
 * 
 *	<xsd:complexType name="OCSPValuesType">
 *		<xsd:sequence>
 *			<xsd:element name="EncapsulatedOCSPValue" type="EncapsulatedPKIDataType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;OCSPValues> which contains one or more &lt;EncapsulatedOCSPValue>
 */
class OCSPValues extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;EncapsulatedOCSPValue> to this instance
	 *
	 * @param EncapsulatedOCSPValue|EncapsulatedOCSPValue[]|string $encapsulatedOCSPValues (optional)
	 */
	public function __construct( $encapsulatedOCSPValues = null )
	{
		parent::__construct( self::createConstructorArray( $encapsulatedOCSPValues, EncapsulatedOCSPValue::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OCSPValues;
	}

	/**
	 * Validate all values are OCSPValues instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$ocspValues = $this->getPropertiesOfClass( EncapsulatedOCSPValue::class );
		if ( ! $ocspValues )
			throw new \Exception("There must be one or more EncapsulatedOCSPValue if <OCSPValues> is used");

		if ( count( $ocspValues ) != count( $this->properties  ) )
			throw new \Exception("All <OCSPValues> children must be of type EncapsulatedOCSPValue");
	}
}
