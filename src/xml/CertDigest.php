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
 *	<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *
 *	<xsd:complexType name="DigestAlgAndValueType">
 *		<xsd:sequence>
 *			<xsd:element ref="ds:DigestMethod"/>
 *			<xsd:element ref="ds:DigestValue"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CertDigest>
 */
class CertDigest extends DigestAlgAndValue
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertDigest;
	}
}