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
 *	<xsd:element name="Encoding" type="xsd:anyURI" minOccurs="0"/>
 *
 *	The encoding value will usually be one of these URIs:
 *
 *	http://uri.etsi.org/01903/v1.2.2#DER for denoting that the original PKI data were ASN.1 data encoded in DER.
 *	http://uri.etsi.org/01903/v1.2.2#BER for denoting that the original PKI data were ASN.1 data encoded in BER.
 *	http://uri.etsi.org/01903/v1.2.2#CER for denoting that the original PKI data were ASN.1 data encoded in CER.
 *	http://uri.etsi.org/01903/v1.2.2#PER for denoting that the original PKI data were ASN.1 data encoded in PER.
 *	http://uri.etsi.org/01903/v1.2.2#XER for denoting that the original PKI data were ASN.1 data encoded in XER
 */

/**
 * Creates a node for &lt;Encoding>
 */
class Encoding extends TextBase
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Encoding;
	}
}
