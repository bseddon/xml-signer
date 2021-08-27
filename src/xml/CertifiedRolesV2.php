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
 *	<xsd:element name="CertifiedRolesV2" type="CertifiedRolesListTypeV2"/>
 *
 *	<xsd:complexType name="CertifiedRolesListTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertifiedRole" type="CertifiedRoleTypeV2" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="CertifiedRoleTypeV2">
 * 		<xsd:choice>
 *			<xsd:element ref="X509AttributeCertificate"/>
 *			<xsd:element ref="OtherAttributeCertificate"/>
 *		</xsd:choice>
 *	</xsd:complexType>
 *
 *	<xsd:element name="X509AttributeCertificate" type="EncapsulatedPKIDataType"/>
 *	<xsd:element name="OtherAttributeCertificate" type="AnyType"/>
 *
 *	<xsd:element name="EncapsulatedPKIData" type="EncapsulatedPKIDataType"/>
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
 * Creates a node for &lt;CertifiedRolesV2> which contains one or more &lt;CertifiedRole>
 */
class CertifiedRolesV2 extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;CertifiedRoleV2> to this instance
	 *
	 * @param CertifiedRoleV2|CertifiedRoleV2[] $certifiedRolesV2 (optional)
	 */
	public function __construct( $certifiedRolesV2 = null )
	{
		parent::__construct(
			self::createConstructorArray( $certifiedRolesV2, CertifiedRoleV2::class ), 
			array( ElementNames::CertifiedRole => basename( CertifiedRoleV2::class ) )
		);
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertifiedRolesV2;
	}

	/**
	 * Validate all references are CertifiedRoleV2 instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$certifiedRoles = $this->getPropertiesOfClass( CertifiedRoleV2::class );
		if ( ! $certifiedRoles )
			throw new \Exception("There must be one or more certified roles if a <CertifiedRolesV2> is used");

		if ( count( $certifiedRoles ) != count( $this->properties  ) )
			throw new \Exception("All <CertifiedRolesV2> children must of type CertifiedRoleV2");
	}
}
