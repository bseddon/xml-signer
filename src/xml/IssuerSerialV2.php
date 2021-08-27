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
 *	<xsd:element name="IssuerSerialV2" type="xsd:base64Binary" minOccurs="0"/>
 */

/**
 * Creates a node for &lt;IssuerSerialV2>
 */
class IssuerSerialV2 extends Base64String
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::IssuerSerialV2;
	}
}
