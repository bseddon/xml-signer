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
abstract class TextBase extends XmlCore
{
	public static function instanceFromParam( $param, $className )
	{
		if ( is_null( $param ) ) return null;
		if ( is_string( $param ) ) return new $className( $param );
		if ( get_class( $param ) == $className ) return $param;
		$basename = basename( $className );
		throw new \Exception("The {$basename} parameter value type is not valid");
	}

	/**
	 * The element text
	 * @var string
	 */
	public $text = null;

	/**
	 * Create an instance with text
	 * @param string $text
	 */
	public function __construct( $text = null )
	{
		$this->text = $text;
	}

	/**
	 * Returns the text
	 * @var string
	 */
	public function getValue()
	{
		return $this->text;
	}

	/** 
	 * Create a new Xml representation for $node
	 * @param  \DOMElement $node
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$this->text = $node->nodeValue;
	}

}
