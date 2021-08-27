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
 *
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<xsd:element name="RevocationValues" type="RevocationValuesType"/>
 *
 *	<xsd:complexType name="RevocationValuesType">
 *		<xsd:sequence>
 *			<xsd:element name="CRLValues" type="CRLValuesType" minOccurs="0"/>
 *			<xsd:element name="OCSPValues" type="OCSPValuesType" minOccurs="0"/>
 *			<xsd:element name="OtherValues" type="OtherCertStatusValuesType" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;RevocationValues>
 */
class RevocationValues extends XmlCore implements UnsignedSignatureProperty
{
	/**
	 * A &lt;CRLValues>
	 * @var CRLValues
	 */
	public $crlValues = null;

	/**
	 * A &lt;OCSPValues>
	 * @var OCSPValues
	 */
	public $ocspValues = null;

	/**
	 * A &lt;OtherValues>
	 * @var OtherValues
	 */
	public $otherValues = null;

	/**
	 * Create an instance of &lt;RevocationValues> and pass in an instance of &lt;CRLValues>, &lt;OCSPValues> and &lt;OtherValues>
	 * @param CRLValues $crlValues
	 * @param OCSPValues $ocspValues
	 * @param OtherValues $otherValues
	 */
	public function __construct( $crlValues = null, $ocspValues = null, $otherValues = null, $id = null )
	{
		$this->crlValues = $crlValues;
		$this->ocspValues = $ocspValues;
		$this->otherValues = $otherValues;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::RevocationValues;
	}

	/**
	 * Create &lt;RevocationValues> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->crlValues )
			$this->crlValues->generateXml( $newElement );

		if ( $this->ocspValues )
			$this->ocspValues->generateXml( $newElement );

		if ( $this->otherValues )
			$this->otherValues->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;RevocationValues>
	 *
	 * @param \DOMElement $node
	 * @return RevocationValues
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::CRLValues:
					$this->crlValues = new CRLValues();
					$this->crlValues->loadInnerXml( $childNode );
					break;

				case ElementNames::OCSPValues:
					$this->ocspValues = new OCSPValues();
					$this->ocspValues->loadInnerXml( $childNode );
					break;

				case ElementNames::OtherValues:
					$this->otherValues = new OtherValues();
					$this->otherValues->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Vaildate &lt;RevocationValues> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->ocspValues )
			$this->ocspValues->validateElement();

		if ( $this->crlValues )
			$this->crlValues->validateElement();

		if ( $this->otherValues )
			$this->otherValues->validateElement();
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

			if ( $this->ocspValues )
				$this->ocspValues->traverse( $callback, $depthFirst );

			if ( $this->crlValues )
				$this->crlValues->traverse( $callback, $depthFirst  );

			if ( $this->otherValues )
				$this->otherValues->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
