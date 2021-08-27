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
 *	<xsd:element name="CertifiedRole" type="CertifiedRoleType" minOccurs="0"/>
 *
 *	<xsd:complexType name="CertifiedRoleType">  
 *		<xsd:complexContent>
 *			<xsd:extension base="xsd:base64Binary">
 *				<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *			</xsd:extension>
 *		</xsd:complexContent>
 *	</xsd:complexType>
 */

/**
 * reates a node for &lt;CertifiedRoles> which contains one or more &lt;CertifiedRole>
 */
class CertifiedRole extends Base64String
{
	/**
	 * Creates an CertifiedRole instance
	 * @param string $base64 This is a base 64 encoded binary of a DER encoded certificate
	 * @param string $id (optional)
	 */
	public function __construct( $base64 = null, $id = null )
	{
		$this->id = $id;
		parent::__construct( $base64 );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertifiedRole;
	}

}
