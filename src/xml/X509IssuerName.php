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
 *	<element name="X509IssuerName" type="xs:string"/>
 */

/**
 * Creates a node for &lt;X509IssuerName>
 */
class X509IssuerName extends TextBase
{
	/**
	 * Create an instance with text
	 * @param string $issuerName
	 */
	public function __construct( $issuerName = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		parent::__construct( $issuerName );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::X509IssuerName;
	}
}
