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
 *	<xsd:element name="Cert" type="CertIDTypeV2" maxOccurs="unbounded"/>
 *
 *	<xsd:complexType name="CertIDTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *			<xsd:element name="IssuerSerialV2" type="xsd:base64Binary" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;Cert>
 */
class CertV2 extends XmlCore
{
	/**
	 * A &lt;CertDigest>
	 * @var CertDigest
	 */
	public $certDigest = null;

	/**
	 * A &lt;IssuerSerial>
	 * @var IssuerSerialV2
	 */
	public $issuerSerialV2 = null;

	/**
	 * The value for @URI
	 * @var string 
	 */
	public $uri;

	/**
	 * Create an instance of &lt;Cert> and pass in an instance of &lt;CertDigest> and &lt;IssuerSerial>
	 * @param CertDigest $certDigest
	 * @param IssuerSerialV2 $issuerSerial
	 * @param string $uri 
	 */
	public function __construct( $certDigest = null, $issuerSerial = null, $uri = null)
	{
		$this->certDigest = $certDigest;
		$this->issuerSerialV2 = $issuerSerial;
		$this->uri = $uri;
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
	 * Create &lt;CertV2> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		// Only add @URI if the value is not null (an empty string is OK)
		$newElement = parent::generateXml( $parentNode, is_null( $this->uri ) ? null: array( AttributeNames::URI => $this->uri ), $insertAfter );

		if ( $this->certDigest )
			$this->certDigest->generateXml( $newElement );

		if ( $this->issuerSerialV2 )
			$this->issuerSerialV2->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return CertV2
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Uri );
		if ( $attr )
		{
			$this->uri = $attr->value;
		}

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

					case ElementNames::IssuerSerialV2:
						$this->issuerSerialV2 = new IssuerSerialV2();
						$this->issuerSerialV2->loadInnerXml( $childNode );
						break;
				}
		}

		return $this;
	}

	/** 
	 * Validate &lt;CertDigest> and &lt;IssuerSerialV2>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		if ( $this->certDigest )
			$this->certDigest->validateElement();

		if ( $this->issuerSerialV2 )
			$this->issuerSerialV2->validateElement();
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

			if ( $this->issuerSerialV2 )
				$this->issuerSerialV2->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
