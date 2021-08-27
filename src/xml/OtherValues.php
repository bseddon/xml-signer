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
 *	<xsd:element name="OtherValues" type="OtherValuesType" minOccurs="0"/>
 * 
 *	<xsd:complexType name="OtherValuesType">
 *		<xsd:sequence>
 *			<xsd:element name="OtherValue" type="xs:AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;OtherValues> which contains one or more &lt;OtherValue>
 */
class OtherValues extends PropertiesCollection
{
	/**
	 * Represents a collection of &lt;OtherValue>
	 * @var OtherValue[]
	 */
	public $otherValues = array();

	/**
	 * Assign one of more &lt;OtherValue> to this instance
	 *
	 * @param OtherValue|OtherValue[]|string $otherValues (optional)
	 */
	public function __construct( $otherValues = null )
	{
		parent::__construct( self::createConstructorArray( $otherValues, OtherValue::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::OtherValues;
	}


	/**
	 * Validate all values are OtherValues instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$otherValues = $this->getPropertiesOfClass( OtherValue::class );
		if ( ! $otherValues )
			throw new \Exception("There must be one or more OtherValue if <OtherValues> is used");

		if ( count( $otherValues ) != count( $this->properties  ) )
			throw new \Exception("All <OtherValues> children must of type OtherValue");
	}
}
