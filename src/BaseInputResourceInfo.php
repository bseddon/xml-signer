<?php
/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * GPL 3.0
 */

namespace lyquidity\xmldsig;

/**
 * This is an abstract  base class for all input XML documents
 */
abstract class BaseInputResourceInfo extends ResourceInfo
{
	/**
	 * An optional location (folder or dir) to save the signature file.
	 * If this is not specified and the resource is a file location then 
	 * the signature will be saved in the same folder as the file.
	 * If the resource is something else (DOMDOcument, URL, etc.) the 
	 * signature will be saved in the current folder.
	 * @var string
	 */
	public $saveLocation = null;

	/**
	 * (optional)The name of the file to save the output
	 * @var string
	 */
	public $saveFilename = null;

	/**
	 * Create signature resource descriptor
	 * @param string $resource
	 * @param int $type  (optional: default = file) An or'd value of ResourceInfo::file ResourceInfo::binary ResourceInfo::des with ResourceInfo::base64
	 * @param string $saveLocation (optional: default = file location)
	 * @param string $saveFilename (optional: default = file name)
	 */
	public function __construct( $resource, $type = self::file, $saveLocation = null, $saveFilename = null )
	{
		parent::__construct( $resource, $type );
		$this->saveLocation = $saveLocation;
		$this->saveFilename = $saveFilename;
	}

}