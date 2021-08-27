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
 *	<xsd:element name="AllDataObjectsTimeStamp" type="XAdESTimeStamp" minOccurs="0"/>
 */

/**
 * Creates a node for &lt;AllDataObjectsTimeStamp>
 */
class AllDataObjectsTimeStamp extends XAdESTimeStamp
{
	/**
	 * When this element is used, the implicit mechanism will be used to compute and verify
	 * Because of this, the Include element is not needed.
	 * @param CanonicalizationMethod $canonicalizationMethod
	 * @param EncapsulatedPKIData $encapsulatedTimeStamp
	 * @param XMLTimeStamp $xmlTimestamp
	 * @param string $id
	 */
	public function __construct(
		$canonicalizationMethod = null, 
		$encapsulatedTimeStamp = null, 
		$xmlTimestamp = null,
		$id = null )
	{
		parent::__construct( null, $canonicalizationMethod, $encapsulatedTimeStamp, $xmlTimestamp, $id );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::AllDataObjectsTimeStamp;
	}
}
