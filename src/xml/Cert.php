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
 *	<xsd:element name="Cert" type="CertIDType" maxOccurs="unbounded"/>
 *
 *	<xsd:complexType name="CertIDType">
 *		<xsd:sequence>
 *			<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *			<xsd:element name="IssuerSerial" type="ds:X509IssuerSerialType"/>
 *		</xsd:sequence>
 *	</xsd:complexType>	
 */

/**
 * Creates a node for &lt;Cert>
 */
class Cert extends XmlCore
{
	/**
	 * A &lt;CertDigest>
	 * @var CertDigest
	 */
	public $certDigest = null;

	/**
	 * A &lt;IssuerSerial>
	 * @var IssuerSerial
	 */
	public $issuerSerial = null;

	/**
	 * Create an instance of &lt;Cert> and pass in an instance of &lt;CertDigest> and &lt;IssuerSerial>
	 * @param CertDigest $certDigest
	 * @param IssuerSerial $issuerSerial
	 */
	public function __construct( $certDigest = null, $issuerSerial = null )
	{
		$this->certDigest = $certDigest;
		$this->issuerSerial = $issuerSerial;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Cert;
	}

	/**
	 * Create &lt;Cert> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		$this->certDigest->generateXml( $newElement );
		$this->issuerSerial->generateXml( $newElement );
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
				case ElementNames::CertDigest:
					$this->certDigest = new CertDigest();
					$this->certDigest->loadInnerXml( $childNode );
					break;

				case ElementNames::IssuerSerial:
					$this->issuerSerial = new IssuerSerial();
					$this->issuerSerial->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/** 
	 * Validate &lt;Cert>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->certDigest )
			$this->certDigest->validateElement();

		if ( $this->issuerSerial )
			$this->issuerSerial->validateElement();
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

			if ( $this->certDigest )
				$this->certDigest->traverse( $callback, $depthFirst );

			if ( $this->issuerSerial )
				$this->issuerSerial->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
