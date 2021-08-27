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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.4.1#"
 *
 *	The preamble of the XML Schema file also includes the following namespace declaration:
 *		xmlns:xades="http://uri.etsi.org/01903/v1.3.2#",
 *	which assigns the prefix "xades" to the namespace whose URI is shown in the declaration.
 *
 *	-->
 *
 *	<xsd:element name="SigAndRefsTimeStampV2" type="xades:XAdESTimeStampType"/>
 */

/**
 * Placeholder for &lt;SigAndRefsTimeStampV2> which is not supported yet
 */
class SigAndRefsTimeStampV2 extends XAdESTimeStamp
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigAndRefsTimeStampV2;
	}
}
