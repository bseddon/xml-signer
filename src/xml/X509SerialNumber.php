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
 *	<element name="X509SerialNumber" type="xs:string"/>
 */

/**
 * Creates a node for &lt;X509SerialNumber>
 */
class X509SerialNumber extends TextBase
{
	/**
	 * Create an instance with text
	 * @param string $algorithm
	 */
	public function __construct( $serialNumber = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		parent::__construct( $serialNumber );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::X509SerialNumber;
	}

}
