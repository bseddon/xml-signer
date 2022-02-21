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
 * Creates a node for &lt;SigningTime>
 */
class SigningTime extends TextBase
{
	/**
	 * Record the signing data.  By default the value is now using the ISO 8601 date format.
	 *
	 * @param string $date
	 */
	public function __construct( $date = null )
	{
		$this->text = $date ?? ( date_default_timezone_get() == 'UTC' ? date('Y-m-d\TH:i:s\Z') : date( 'c' ) ); //Do not use DateTime::ISO8601
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigningTime;
	}
}
