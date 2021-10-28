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
 *	<xsd:element name="CommitmentTypeQualifiers" type="CommitmentTypeQualifiersListType" minOccurs="0"/>
 *
 *	<xsd:complexType name="CommitmentTypeQualifiersListType">
 *		<xsd:sequence>
 *			<xsd:element name="CommitmentTypeQualifier" type="AnyType" minOccurs="0" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CommitmentTypeQualifiers>
 */
class CommitmentTypeQualifiers extends PropertiesCollection
{
	/**
	 * Assign one of more CommitmentTypeQualifier to this instance
	 * @param CommitmentTypeQualifier|CommitmentTypeQualifier[]|string $commitmentTypeQualifiers
	 */
	public function __construct( $commitmentTypeQualifiers = null )
	{
		parent::__construct( self::createConstructorArray( $commitmentTypeQualifiers, CommitmentTypeQualifier::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CommitmentTypeQualifiers;
	}

	/**
	 * Validate all Commitment Type Qualifiers are CommitmentTypeQualifier instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$commitmentTypeQualifiers = $this->getPropertiesOfClass( CommitmentTypeQualifier::class );

		if ( count( $commitmentTypeQualifiers ) != count( $this->properties  ) )
			throw new \Exception("All <CommitmentTypeQualifiers> children must be of type CommitmentTypeQualifier");
	}

}
