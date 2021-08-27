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
 *	<xsd:element name="Int" type="xsd:integer" minOccurs="0"/>
 */

/**
 * Creates a node for &lt;Int>
 */
class Integer extends TextBase
{
	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Int;
	}

	/**
	 * Allow the integer to be validated
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( is_integer( $this->text ) ) return;

		throw new \Exception("<NoticeNumbers> must be integer");
	}
}
