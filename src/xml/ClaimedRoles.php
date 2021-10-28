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
 *
 *	<xsd:element name="ClaimedRoles" type="ClaimedRolesListType"/>
 *
 *	<xsd:complexType name="ClaimedRolesListType">
 *		<xsd:sequence>
 *			<xsd:element name="ClaimedRole" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;ClaimedRoles> which contains one or more &lt;ClaimedRole>
 */
class ClaimedRoles extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;ClaimedRole> to this instance
	 * @param ClaimedRole|ClaimedRole[]|string $claimedRoles (optional)
	 */
	public function __construct( $claimedRoles = null )
	{
		parent::__construct( self::createConstructorArray( $claimedRoles, ClaimedRole::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::ClaimedRoles;
	}

	/**
	 * Validate all claimed roles are ClaimedRole instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$claimedRoles = $this->getPropertiesOfClass( ClaimedRole::class );
		if ( ! $claimedRoles )
			throw new \Exception("There must be one or more claimed roles if a <ClaimedRoles> is used");

		if ( count( $claimedRoles ) != count( $this->properties  ) )
			throw new \Exception("All <ClaimedRoles> children must be of type ClaimedRoles");
	}

}
