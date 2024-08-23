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
	 * Allows a caller to show the file is large so the LIBXML_PARSEHUGE flag will be used
	 * This is not relevant for an existing DOM document
	 */
	public $hugeFile = false;

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

	/**
	 * Generate dom document from resource data
	 * @return \DOMDocument
	 */
	public function generateDomDocument()
	{
		if ( $this->isFile() )
		{
			if (!file_exists($this->resource))
			{
				throw new XAdESException("XML file does not exist");
			}

			// Load the XML to be signed
			$doc = new \DOMDocument();
			$doc->load( $this->resource, $this->hugeFile ? LIBXML_PARSEHUGE  : 0 );
		} 
		else if ( $this->isXmlDocument() )
		{
			$doc = clone( $this->resource );
		} 
		else if ( $this->isURL() )
		{
			// Load the XML to be signed
			$doc = new \DOMDocument();
			if ( ! $doc->load( $this->resource, $this->hugeFile ? LIBXML_PARSEHUGE  : 0 ) )
			{
				throw new XAdESException( "URL does not reference a valid XML document" );
			}
		}
		else if ($this->isString())
		{
			$doc = new \DOMDocument();
			if ( ! $doc->loadXML( $this->resource, $this->hugeFile ? LIBXML_PARSEHUGE  : 0 ) )
			{
				throw new XAdESException( "Unable to load XML string" );
			}
		}
		else
		{
			throw new XAdESException("The resource supplied representing the document to be signed is not valid.");
		}

		return $doc;
	}
}
