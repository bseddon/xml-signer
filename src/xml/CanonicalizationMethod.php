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
 *	<element name="CanonicalizationMethod" type="ds:CanonicalizationMethodType"/>
 *
 *	<complexType name="CanonicalizationMethodType" mixed="true">
 *		<sequence>
 *			<any namespace="##any" minOccurs="0" maxOccurs="unbounded"/>
 *			<!-- (0,unbounded) elements from (1,1) namespace -->
 *		</sequence>
 *		<attribute name="Algorithm" type="anyURI" use="required"/> 
 *	</complexType>
 */

/**
 * Creates a node for &lt;CanonicalizationMethod>
 */
class CanonicalizationMethod extends DigestMethod
{
	/**
	 * Provides a ready made instance for C14N 
	 * @var CanonicalizationMethod
	 */
	public static $defaultMethod;

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CanonicalizationMethod;
	}
}

CanonicalizationMethod::$defaultMethod = new CanonicalizationMethod( XMLSecurityDSig::C14N );