<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\xmldsig\XMLSecurityDSig;

/**
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<element name="KeyInfo" type="ds:KeyInfoType"/>
 *
 *	<complexType name="KeyInfoType" mixed="true">
 *		<choice maxOccurs="unbounded">
 *			<element ref="ds:KeyName"/>
 *			<element ref="ds:KeyValue"/>
 *			<element ref="ds:RetrievalMethod"/>
 *			<element ref="ds:X509Data"/>
 *			<element ref="ds:PGPData"/>
 *			<element ref="ds:SPKIData"/>
 *			<element ref="ds:MgmtData"/>
 *			<any processContents="lax" namespace="##other"/>
 *			<!-- (1,1) elements from (0,unbounded) namespaces -->
 *		</choice>
 *		<attribute name="Id" type="ID" use="optional"/>
 *	</complexType>
 */

/**
 * Creates a node for &lt;KeyInfo>
 */
class KeyInfo extends XmlCore
{
	/**
	 * A collection of references
	 * @var KeyInfoType
	 */
	public $keyInfoType;

	/**
	 * Assign one of more references to this instance
	 *
	 * @param KeyInfoType $references
	 */
	public function __construct( $keyInfoType = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;

		if ( ! $keyInfoType ) return;

		if ( is_string( $keyInfoType ) )
		{
			// Assume its a base 64 encoded X509 certificate
			$this->keyInfoType = new X509Data( $keyInfoType );
		}
		else if ( $keyInfoType instanceof KeyInfoType )
			$this->keyInfoType = $keyInfoType;

		if ( ! $keyInfoType ) 
			throw new \Exception("The parameter passd to KeyInfo is not of a KeyInfoType");
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::KeyInfo;
	}

	/**
	 * Create &lt;KeyInfo> and any descendent elements
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		if ( ! $this->keyInfoType ) return;

		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		$this->keyInfoType->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;KeyInfo>
	 * @param \DOMElement $node
	 * @return KeyInfo
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			$this->keyInfoType = KeyInfoType::fromName( $childNode->localName );
			$this->keyInfoType->loadInnerXml( $childNode );
		}
	}

	/**
	 * Validate all keyInfoType instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->keyInfoType )
			$this->keyInfoType->validateElement();
	}

	/**
	 * Calls the closure in $callback and does the same on any descendents
	 * @param Closure $callback
	 * @param bool $depthFirst (optional: default = false)  When true this will call on child nodes first
	 * @return XmlCore
	 */
	public function traverse( $callback, $depthFirst = false )
	{
		if ( $callback instanceof \Closure )
		{
			if ( ! $depthFirst )
				parent::traverse( $callback, $depthFirst );

			if ( $this->keyInfoType )
				$this->keyInfoType->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}

/**
 * Simple class to provide enumerated values for the @Qualifier
 */
abstract class KeyInfoType extends XmlCore
{
	public static $KeyName;
	public static $KeyValue;
	public static $RetrievalMethod;
	public static $X509Data;
	public static $PGPData;
	public static $SPKIData;
	public static $MgmtData;

	/**
	 * Create and instance from a name
	 * @param string $name
	 * @return KeyInfoType
	 */
	public static function fromName( $name )
	{
		$classname = str_replace( '/', '\\', dirname( str_replace( '\\', '/', self::class ) ) ) . "\\$name";
		if ( ! class_exists( $classname, true ) )
		{
			throw new \Exception("The element <$name> is not supported as a child of <KeyInfo>");
		}
		$instance = new $classname();

		if ( $instance instanceof KeyInfoType )
			return $instance;

		throw new \Exception("The <KeyInfo> child node with element local name '$name' is not supported");
	}
}

KeyInfoType::$KeyName = new KeyName();
KeyInfoType::$KeyValue = new KeyValue();
KeyInfoType::$RetrievalMethod = new RetrievalMethod();
KeyInfoType::$X509Data = new X509Data();
KeyInfoType::$PGPData = new PGPData();
KeyInfoType::$SPKIData = new SPKIData();
KeyInfoType::$MgmtData = new MgmtData();

/**
 * This is a placeholder class for possible future implementation
 */
class KeyName extends KeyInfoType
{
	public function getLocalName() { return ElementNames::KeyName; }
}

/**
 * This is a placeholder class for possible future implementation
 */
class KeyValue extends KeyInfoType
{
	public function getLocalName() { return ElementNames::KeyValue; }
}

/**
 * This is a placeholder class for possible future implementation
 */
class RetrievalMethod extends KeyInfoType
{
	public function getLocalName() { return ElementNames::RetrievalMethod; }
}

/**
 * Implementation records the X509 certificate 
 */
class X509Data extends KeyInfoType
{
	/**
	 * The X509 certificate instance
	 * @var X509Certificate
	 */
	public $certificate;

	/**
	 * Creates an &lt;X509Data> instance with a child &lt;X509Certificate>
	 *
	 * @param X509Certificate|string $certificate
	 */
	public function __construct( $certificate = null )
	{
		$this->certificate = self::createConstructor( $certificate, X509Certificate::class );
	}

	/**
	 * The local name for this element
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::X509Data;
	}

	/**
	 * Generate the element and child nodes
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $attributes );

		$this->certificate->generateXml( $newElement );
	}

	/**
	 * Read the nodes
	 * @param \DOMElement $node
	 * @return void
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		foreach( $node->childNodes as $childNode )
		{
			if ( $childNode->nodeType != XML_ELEMENT_NODE || $childNode->localName != ElementNames::X509Certificate ) continue;

			$this->certificate = new X509Certificate();
			$this->certificate->loadInnerXml( $childNode );
		}
	}

	/**
	 * Validate the certificate
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->certificate )
			$this->certificate->validateElement();
	}

	/**
	 * Calls the closure in $callback and does the same on any descendents
	 * @param Closure $callback
	 * @param bool $depthFirst (optional: default = false)  When true this will call on child nodes first
	 * @return XmlCore
	 */
	public function traverse( $callback, $depthFirst = false )
	{
		if ( $callback instanceof \Closure )
		{
			if ( ! $depthFirst )
				parent::traverse( $callback, $depthFirst );

			if ( $this->certificate )
				$this->certificate->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}

/**
 * This is a placeholder class for possible future implementation
 */
class PGPData extends KeyInfoType
{
	public function getLocalName() { return ElementNames::PGPData; }
}

/**
 * This is a placeholder class for possible future implementation
 */
class SPKIData extends KeyInfoType
{
	public function getLocalName() { return ElementNames::SPKIData; }
}

/**
 * This is a placeholder class for possible future implementation
 */
class MgmtData extends KeyInfoType
{
	public function getLocalName() { return ElementNames::MgmtData; }
}
