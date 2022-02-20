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
 *	<xsd:element name="SigPolicyQualifiers" type="SigPolicyQualifiersListType" minOccurs="0"/>
 *
 *	<xsd:complexType name="SigPolicyQualifiersListType">
 *		<xsd:sequence>
 *			<xsd:element name="SigPolicyQualifier" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SigPolicyQualifiers>
 */
class SigPolicyQualifiers extends PropertiesCollection
{
	/**
	 * Assign one of more references to this instance
	 *
	 * @param SigPolicyQualifier|SigPolicyQualifier[]|string $references
	 */
	public function __construct( $sigPolicyQualifiers = null )
	{
		parent::__construct( self::createConstructorArray( $sigPolicyQualifiers, SigPolicyQualifier::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigPolicyQualifiers;
	}

	/**
	 * Validate all sigPolicyQualifiers are SigPolicyQualifier instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$sigPolicyQualifiers = $this->getPropertiesOfClass( SigPolicyQualifier::class );

		if ( count( $sigPolicyQualifiers ) != count( $this->properties  ) )
			throw new \Exception("All <SigPolicyQualifiers> children must be of type SigPolicyQualifier");
	}

	/**
	 * Create &lt;properties> and any descendent elements
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a nodes for this element
		parent::generateXml( $parentNode, $attributes, $insertAfter );
	}

}
