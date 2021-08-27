<?php

/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * GPL 3.0
 */

namespace lyquidity\xmldsig;

use lyquidity\xmldsig\xml\Transforms;

/**
 * Records information about a resource.  The flags 64, 128 are reserved.
 * Some combinations are not valid.  For example, an Xml document cannot 
 * be both a file and an xmlDocument and a document cannot be both a local
 * file and a url.
 */
abstract class ResourceInfo
{
	/**
	 * A flag indicating the resource is a path to a local file
	 */
	const file = 1;
	/**
	 * A flag indicating the resource is a string
	 */
	const string = 2;
	/**
	 * A flag indicating the resource is a binary string
	 */
	const binary = 4;
	/**
	 * A flag indicating the resource is a DER encoded binary string
	 */
	const der = 8;
	/**
	 * A flag indicating the resource is a DER encoded binary string
	 */
	const xmlDocument = 16;
	/**
	 * A flag indicating the resource is a PEM encoded string
	 */
	const pem = 32;
	/**
	 * A flag indicating the resource is a PEM encoded string
	 */
	const url = 64;
	/**
	 * A flag indicating the resource is base64 encoded. This can be or'd with binary and der.
	 */
	const base64 = 256;

	/**
	 * Returns true if the flag base64 is used
	 * @return boolean
	 */
	public static function isTypeBase64( $type )
	{
		return $type & self::base64;
	}

	/**
	 * Returns true if the flag base64 is used
	 * @return boolean
	 */
	public static function isTypeBinary( $type )
	{
		return $type & self::binary;
	}

	/**
	 * Returns true if the flag base64 is used
	 * @return boolean
	 */
	public static function isTypeDER( $type )
	{
		return $type & self::der;
	}

	/**
	 * Returns true if the flag base64 is used
	 * @return boolean
	 */
	public static function isTypeFile( $type )
	{
		return $type & self::file;
	}

	/**
	 * Returns true if the flag string is used
	 * @return boolean
	 */
	public static function isTypeString( $type )
	{
		return $type & self::string;
	}

	/**
	 * Returns true if the flag xmlDocument is used
	 * @return boolean
	 */
	public static function isTypeXmlDocument( $type )
	{
		return $type & self::xmlDocument;
	}

	/**
	 * Returns true if the flag xmlDocument is used
	 * @return boolean
	 */
	public static function isTypePEM( $type )
	{
		return $type & self::pem;
	}

	/**
	 * Returns true if the flag url is used
	 * @return boolean
	 */
	public static function isTypeURL( $type )
	{
		return $type & self::url;
	}

	/**
	 * The resource.  Might be a path to a file or a string containing a binary encoded resource.
	 * @var string
	 */
	public $resource = null;

	/**
	 * A flag to indicate the type
	 * @var boolean
	 */
	public $type = false;

	/**
	 * Create signature resource descriptor
	 * @param string $resource
	 * @param int $type An or'd value of ResourceInfo::file ResourceInfo::binary ResourceInfo::des with ResourceInfo::base64
	 */
	public function __construct( $resource, $type = self::file )
	{
		$this->resource = $resource;
		$this->type = $type;
	}

	/**
	 * Returns true if the flag base64 is used
	 * @return boolean
	 */
	public function isBase64()
	{
		return self::isTypeBase64( $this->type );
	}

	/**
	 * Returns true if the flag binary is used
	 * @return boolean
	 */
	public function isBinary()
	{
		return self::isTypeBinary( $this->type );
	}

	/**
	 * Returns true if the flag DER is used
	 * @return boolean
	 */
	public function isDER()
	{
		return self::isTypeDER( $this->type );
	}

	/**
	 * Returns true if the flag file is used
	 * @return boolean
	 */
	public function isFile()
	{
		return self::isTypeFile( $this->type );
	}

	/**
	 * Returns true if the flag string is used
	 * @return boolean
	 */
	public function isString()
	{
		return self::isTypeString( $this->type );
	}

	/**
	 * Returns true if the flag xmlDocument is used
	 * @return boolean
	 */
	public function isXmlDocument()
	{
		return self::isTypeXmlDocument( $this->type );
	}

	/**
	 * Returns true if the flag xmlDocument is used
	 * @return boolean
	 */
	public function isPEM()
	{
		return self::isTypePEM( $this->type );
	}

	/**
	 * Returns true if the flag xmlDocument is used
	 * @return boolean
	 */
	public function isURL()
	{
		return self::isTypeURL( $this->type );
	}

}