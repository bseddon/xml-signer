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
 *	<xsd:element name="AllSignedDataObjects" type="xsd:string" minOccurs="0"/>
 */

/**
 * Creates a node for &lt;AllSignedDataObjects>
 * This element is just a flag to be used 'empty' so there are no properties
 * and just the XmlCore behaviour will be used
 */
class AllSignedDataObjects extends XmlCore
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::AllSignedDataObjects;
	}
}
