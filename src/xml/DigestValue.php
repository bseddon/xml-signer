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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<element name="DigestValue" type="ds:DigestValueType"/>
 *
 *	<simpleType name="DigestValueType">
 *		<restriction base="base64Binary"/>
 *	</simpleType>
 */

/**
 * Creates a node for &lt;DigestValue>
 */
class DigestValue extends Base64String
{
	/**
	 * Create an instance with text
	 * @param string $description
	 */
	public function __construct( $base64 = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		parent::__construct( $base64 );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::DigestValue;
	}

}
