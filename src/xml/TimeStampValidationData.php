<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\xmldsig\XAdES;

/**
 * <!-- targetNamespace="http://uri.etsi.org/01903/v1.4.1#"
 * The preamble of the XML Schema file also includes the following namespace declaration:
 * 	xmlns:xades="http://uri.etsi.org/01903/v1.3.2#",
 * which assigns the prefix "xades" to the namespace whose URI is shown in the declaration.
 * -->
 * <xsd:element name="TimeStampValidationData" type="ValidationDataType"/>
 * <xsd:complexType name="ValidationDataType">
 *		<xsd:sequence>
 *			<xsd:element ref="xades:CertificateValues" minOccurs="0"/>
 *			<xsd:element ref="xades:RevocationValues" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 * </xsd:complexType> 
 */

/**
 * Creates a node for &lt;TimeStampValidationData>
 */
class TimeStampValidationData extends XmlCore implements UnsignedSignatureProperty
{
	/**
	 * A &lt;CertificateValues>
	 * @var CertificateValues
	 */
	public $certificateValues = null;

	/**
	 * A &lt;RevocationValues>
	 * @var RevocationValues
	 */
	public $revocationValues = null;

	/**
	 * Create an instance of &lt;TimeStampValidationData> and pass in an instance of &lt;CertificateValues> and &lt;RevocationValues>
	 * @param CertificateValues $certificateValues
	 * @param RevocationValues $revocationValues
	 */
	public function __construct( $certificateValues = null, $revocationValues = null )
	{
		$this->defaultNamespace = XAdES::NamespaceUrl1v41;
		$this->prefix = 'xa141';

		$this->certificateValues = $certificateValues;
		$this->revocationValues = $revocationValues;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::TimeStampValidationData;
	}

	/**
	 * Create &lt;TimeStampValidationData> and any descendent elements
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

		if ( $this->certificateValues )
			$this->certificateValues->generateXml( $newElement );

		if ( $this->revocationValues )
			$this->revocationValues->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return TimeStampValidationData
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// No attributes for this element

		// Look for elements with the tag &lt;CertificateValues> or  &lt;RevocationValues>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::CertificateValues:
					$this->certificateValues = new CertificateValues();
					$this->certificateValues->loadInnerXml( $childNode );
					break;

				case ElementNames::RevocationValues:
					$this->revocationValues = new RevocationValues();
					$this->revocationValues->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/** 
	 * Validate &lt;TimeStampValidationData>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->certificateValues )
			$this->certificateValues->validateElement();

		if ( $this->revocationValues )
			$this->revocationValues->validateElement();
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

			if ( $this->certificateValues )
				$this->certificateValues->traverse( $callback, $depthFirst );

			if ( $this->revocationValues )
				$this->revocationValues->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
