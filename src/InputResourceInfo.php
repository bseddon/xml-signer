<?php
/**
 * Copyright (c) 2021 and later years, Bill Seddon <bill.seddon@lyquidity.com>.
 * All rights reserved.
 *
 * GPL 3.0
 */

namespace lyquidity\xmldsig;

use lyquidity\xmldsig\xml\Transform;
use lyquidity\xmldsig\xml\Transforms;

/**
 * Records information about to data to be signed.
 */
class InputResourceInfo extends BaseInputResourceInfo
{
	/**
	 * Flag defining whether the signature should be detached or added to the source document
	 * @var boolean (default: true)
	 */
	public $detached = true;

	/**
	 * An optional Transforms instance allows a caller to define how the referenced 
	 * content should be transformed before the digest is computed.  For example, it
	 * might be necessary to make sure only certain types of content are in the XML
	 * to be signed.  An example is removing aany existing signature. Another is to
	 * specify a particular type of canonicalization.
	 *
	 * @var Transforms
	 */
	public $transforms = null;

	/**
	 * This will be used to set @Id when the static sign function is used
	 *
	 * @var string
	 */
	public $signatureId = null;

	/**
	 * Returns true if the transforms contains one that is enveloped
	 *
	 * @var boolean
	 */
	public function hasEnveloped()
	{
		return $this->transforms
			? $this->transforms->hasEnveloped()
			: false;
	}

	/**
	 * Create signature resource descriptor
	 * @param string $resource
	 * @param int $type  (optional: default = file) An or'd value of ResourceInfo::file ResourceInfo::binary ResourceInfo::des with ResourceInfo::base64
	 * @param string $saveLocation (optional: default = file location)
	 * @param string $saveFilename (optional: default = file name)
	 * @param Transforms $transforms (optional)
	 * @param bool $detached (optional: default = true)
	 * @param string $signatureId (optional)
	 */
	public function __construct( $resource, $type = self::file, $saveLocation = null, $saveFilename = null, $transforms = null, $detached = true, $signatureId = null )
	{
		parent::__construct( $resource, $type, $saveLocation, $saveFilename );
		$this->transforms = $transforms;
		$this->detached = $detached;
		$this->signatureId = $signatureId;

		if ( ! $this->isFile() ) return;

		if ( ! $this->saveLocation ) $this->saveLocation = dirname( $this->resource );
		if ( ! $this->saveFilename ) $this->saveFilename = basename( $this->resource );
	}

	/**
	 * Converts a typed Transforms instance into the type of array used by XMLSecurityDSig
	 * @param bool $removeSignatures
	 * @return string[]
	 */
	public function convertTransforms( $removeSignatures )
	{
		$result = array();

		if ( $removeSignatures && ( ! $this->transforms || ! $this->transforms instanceof Transforms ) && ! $this->hasEnveloped() )
		{
			// Whe working with an attached signature, there will be a signature 
			// in the input document so add a transform to remove it/them
			$envelopedTransform = new Transform( XMLSecurityDSig::ENV_SIG );
			$result[] = $envelopedTransform->toSimpleRepresentation();
			unset( $envelopedTransform );
		}
		else if( ! $removeSignatures && ! $this->transforms )
		{
			// Otherwise, if there are no transforms, create one that canonicalizes
			$canonicalizedTransform = new Transform( XMLSecurityDSig::C14N );
			$result[] = $canonicalizedTransform->toSimpleRepresentation();
			unset( $canonicalizedTransform );
		}

		if ( $this->transforms && $this->transforms instanceof Transforms )
		foreach( $this->transforms->transforms as $transform )
		{
			$result[] = $transform->toSimpleRepresentation();
		}

		return $result;
	}
}