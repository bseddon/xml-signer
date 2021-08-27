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
 *	<element name="SignatureMethod" type="ds:SignatureMethodType"/>
 *	<complexType name="SignatureMethodType" mixed="true"> 
 *		<sequence>
 *			<any namespace="##other" processContents="lax" minOccurs="0" maxOccurs="unbounded"/>
 *		</sequence>    
 *		<attribute name="Algorithm" type="anyURI" use="required"/> 
 *	</complexType>
 */

/**
 * Creates a node for &lt;Description>
 */
class SignatureMethod extends DigestMethod
{
	/**
	 * Provides an instance for SHA256
	 * @var SignatureMethod
	 * A static method of this name exists in the ancestor but is repeated
	 * so each class can have its own default method type
	 */
	public static $defaultMethod;

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignatureMethod;
	}
}

SignatureMethod::$defaultMethod = new SignatureMethod( XMLSecurityDSig::SHA256 );