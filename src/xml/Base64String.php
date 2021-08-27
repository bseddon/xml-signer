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
 * Acts as a common base for all text elements like 
 * Description, Street, City, etc. so they only need 
 * to specify an element name
 */
abstract class Base64String extends TextBase
{
	/**
	 * Validates the structure of a string is compatible with base 64 encoding.
	 * It does not imply that the string is appropriate base 64 string.
	 * 
	 * The following will be checked as a valid base 64 encoded string:
	 * 
	 * $x = validateBase64String( 'Z3VydQ==' ); return;
	 *
	 * @param string $input
	 * @return void
	 * @throws \Exceptions
	 */
	public static function validateBase64String( $input )
	{
		$input = str_replace( ["\r", "\n"], '', $input );

		// By default PHP will ignore “bad” characters, so we need to enable the “$strict” mode
		$str = base64_decode($input, true );

		// If $input cannot be decoded the $str will be a Boolean “FALSE”
		if ( $str === false )
			throw new \Exception("The string is not correctly base 64 encoded");

		// Even if $str is not FALSE, this does not mean that the input is valid
		// This is why now we should encode the decoded string and check it against input
		$b64 = base64_encode( $str );

		// Finally, check if input string and real Base64 are identical
		if ( rtrim( $input, '=' ) !== rtrim( $b64, '=' ) )
			throw new \Exception("The string is not base 64 encoded correctly");
	}

	/** 
	 * Validate &lt;DigestMethod> and &lt;DigestValue>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();
		self::validateBase64String( $this->text );
	}
}
