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
 *	<xsd:element name="CRLValues" type="CRLValuesType" minOccurs="0"/>
 * 
 *	<xsd:complexType name="CRLValuesType">
 *		<xsd:sequence>
 *			<xsd:element name="EncapsulatedCRLValue" type="EncapsulatedPKIDataType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CRLValues> which contains one or more &lt;EncapsulatedCRLValue>
 */
class CRLValues extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;EncapsulatedCRLValue> to this instance
	 *
	 * @param EncapsulatedCRLValue|EncapsulatedCRLValue[]|string $encapsulatedCRLValues (optional)
	 */
	public function __construct( $encapsulatedCRLValues = null )
	{
		parent::__construct( self::createConstructorArray( $encapsulatedCRLValues, EncapsulatedCRLValue::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CRLValues;
	}

	/**
	 * Validate all values are CRLValues instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		$encapsulatedCRLValues = $this->getPropertiesOfClass( EncapsulatedCRLValue::class );

		if ( count( $encapsulatedCRLValues ) != count( $this->properties  ) )
			throw new \Exception("All <EncapsulatedCRLValues> children must of type EncapsulatedCRLValue");
	}
}
