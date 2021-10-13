<?php
/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * GPL 3.0
 */

namespace lyquidity\xmldsig;

/**
 * Records information about to data to be signed.
 */
class SignedDocumentResourceInfo extends BaseInputResourceInfo
{
	/**
	 * If provided it will be the id if the signature to counter sign
	 * @var string 
	 */
	public $id = null;

	/**
	 * This is an optional id that will be added to the counter signature to it is possible to counter sign the counter signature
	 * @var string
	 */
	public $elementSignatureId = null;

	/**
	 * Create signature resource descriptor
	 * @param string $resource
	 * @param int $type  (optional: default = file) An or'd value of ResourceInfo::file ResourceInfo::binary ResourceInfo::des with ResourceInfo::base64
	 * @param string $id (optional: default = true)
	 * @param string $saveLocation (optional: default = file location)
	 * @param string $saveFilename (optional: default = file name)
	 * @param string $elementSignatureId
	 */
	public function __construct( $resource, $type = self::file, $id = null, $saveLocation = null, $saveFilename = null, $elementSignatureId = null )
	{
		parent::__construct( $resource, $type, $saveLocation, $saveFilename );
		$this->id = $id;
		$this->elementSignatureId = $elementSignatureId;
	}
}