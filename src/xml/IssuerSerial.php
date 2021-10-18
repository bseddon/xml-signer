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
 *	<xsd:element name="IssuerSerial" type="ds:X509IssuerSerialType"/>
 *
 *	<complexType name="ds:X509IssuerSerialType"> 
 *		<sequence> 
 *			<element name="X509IssuerName" type="string"/> 
 *			<element name="X509SerialNumber" type="integer"/> 
 *		</sequence>
 *	</complexType>
 */

/**
 * Creates a node for &lt;IssuerSerial>
 */
class IssuerSerial extends XmlCore
{
	/**
	 * A &lt;X509IssuerName>
	 * @var X509IssuerName
	 */
	public $x509IssuerName = null;

	/**
	 * A &lt;X509SerialNumber>
	 * @var X509SerialNumber
	 */
	public $x509SerialNumber = null;

	/**
	 * Create an instance of &lt;IssuerSerial> and pass in instances of &lt;X509IssuerName> and &lt;X509SerialNumber>
	 * @param X509IssuerName $x509IssuerName
	 * @param X509SerialNumber $x509SerialNumber
	 */
	public function __construct( $x509IssuerName = null, $x509SerialNumber = null )
	{
		$this->x509IssuerName = $x509IssuerName;
		$this->x509SerialNumber = $x509SerialNumber;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::IssuerSerial;
	}

	/**
	 * Create &lt;IssuerSerial> and any descendent elements
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		$this->x509IssuerName->generateXml( $newElement );
		$this->x509SerialNumber->generateXml( $newElement );
	}

		/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Cert
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// No attributes for this element

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::X509IssuerName:
					$this->x509IssuerName = new X509IssuerName();
					$this->x509IssuerName->loadInnerXml( $childNode );
					break;

				case ElementNames::X509SerialNumber:
					$this->x509SerialNumber = new X509SerialNumber();
					$this->x509SerialNumber->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/** 
	 * Validate &lt;IssuerSerial>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		$this->x509IssuerName->validateElement();
		$this->x509SerialNumber->validateElement();
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

			if ( $this->x509IssuerName )
				$this->x509IssuerName->traverse( $callback, $depthFirst );

			if ( $this->x509SerialNumber )
				$this->x509SerialNumber->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
