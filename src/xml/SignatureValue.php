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
 *	<!-- targetNamespace="http://www.w3.org/2000/09/xmldsig#" -->
 *
 *	<element name="SignatureValue" type="ds:SignatureValueType"/>
 *
 *	<complexType name="SignatureValueType">
 *		<simpleContent>
 *			<extension base="base64Binary">
 *				<attribute name="Id" type="ID" use="optional"/>
 *			</extension>
 *		</simpleContent>
 *	</complexType>
 */

/**
 * reates a node for &lt;SignatureValues> which contains one or more &lt;SignatureValue>
 */
class SignatureValue extends Base64String
{
	/**
	 * Creates an SignatureValue instance
	 * @param string $base64 This is a base 64 encoded binary of a DER encoded certificate
	 * @param string $id (optional)
	 */
	public function __construct( $base64 = null, $id = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;

		$this->id = $id;
		parent::__construct( $base64 );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignatureValue;
	}
}
