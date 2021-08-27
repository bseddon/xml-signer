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
 *	<xsd:element name="CertifiedRoles" type="CertifiedRolesListType" minOccurs="0"/>
 * 
 *	<xsd:complexType name="CertifiedRolesListType">
 *		<xsd:sequence>
 *			<xsd:element name="CertifiedRole" type="EncapsulatedPKIDataType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 * 
 *	<xsd:complexType name="EncapsulatedPKIDataType">
 *		<xsd:complexContent>
 *			<xsd:extension base="xsd:base64Binary">
 *				<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *			</xsd:extension>
 *		</xsd:complexContent>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CertifiedRoles> which contains one or more &lt;CertifiedRole>
 */
class CertifiedRoles extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;CertifiedRole> to this instance
	 *
	 * @param CertifiedRole|CertifiedRole[]|string $certifiedRoles (optional)
	 */
	public function __construct( $certifiedRoles = null )
	{
		parent::__construct( self::createConstructorArray( $certifiedRoles, CertifiedRole::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertifiedRoles;
	}

	/**
	 * Validate all references are CertifiedRole instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$certifiedRoles = $this->getPropertiesOfClass( CertifiedRole::class );
		if ( ! $certifiedRoles )
			throw new \Exception("There must be one or more certified roles if a <CertifiedRoles> is used");

		if ( count( $certifiedRoles ) != count( $this->properties  ) )
			throw new \Exception("All <CertifiedRoles> children must of type CertifiedRole");
	}
}
