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
 *	<xsd:element name="SignedAssertions" type="SignedAssertionsListType"/>
 *
 *	<xsd:complexType name="SignedAssertionsListType">
 *		<xsd:sequence>
 *			<xsd:element ref="SignedAssertion" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:element name="SignedAssertion" type="AnyType"/> 
 */

/**
 * Creates a node for &lt;SignedAssertions>
 */
class SignedAssertions extends PropertiesCollection
{
	/**
	 * A collection of assertions
	 * @var SignedAssertion[]
	 */
	public $assertions = array();

	/**
	 * Assign one of more assertions to this instance
	 *
	 * @param SignedAssertion|SignedAssertion[]|string $assertions
	 */
	public function __construct( $assertions = null )
	{
		parent::__construct( self::createConstructorArray( $assertions, SignedAssertion::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignedAssertions;
	}

	/**
	 * Validate all assertions are SignedAssertion instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$signedAssertion = $this->getPropertiesOfClass( SignedAssertion::class );
		if ( ! $signedAssertion )
			throw new \Exception("There must be one or more SignedAssertion if <SignedAssertions> is used");

		if ( count( $signedAssertion ) != count( $this->properties  ) )
			throw new \Exception("All <SignedAssertions> children must of type SignedAssertion");	}
}
