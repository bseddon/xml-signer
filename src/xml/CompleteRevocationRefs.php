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
 *	<xsd:element name="CompleteRevocationRefs" type="CompleteRevocationRefsType"/>
 *	
 *	<xsd:complexType name="CompleteRevocationRefsType">
 *		<xsd:sequence>
 *			<xsd:element name="CRLRefs" type="CRLRefsType" minOccurs="0"/>
 *			<xsd:element name="OCSPRefs" type="OCSPRefsType" minOccurs="0"/>
 *			<xsd:element name="OtherRefs" type="OtherCertStatusRefsType" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CompleteRevocationRefs>
 */
class CompleteRevocationRefs extends XmlCore implements UnsignedSignatureProperty
{
	/**
	 * A &lt;CRLRefs>
	 * @var CRLRefs
	 */
	public $crlRefs = null;

	/**
	 * A &lt;OCSPRefs>
	 * @var OCSPRefs
	 */
	public $ocspRefs = null;

	/**
	 * A &lt;OtherRefs>
	 * @var OtherRefs
	 */
	public $otherRefs = null;

	/**
	 * Create an instance of &lt;CompleteRevocationRefs> and pass in an instance of &lt;CRLRefs>, &lt;OCSPRefs> and &lt;OtherRefs>
	 * @param CRLRefs $crlRefs
	 * @param OCSPRefs $ocspRefs
	 * @param OtherRefs $otherRefs
	 */
	public function __construct( $crlRefs = null, $ocspRefs = null, $otherRefs = null, $id = null )
	{
		$this->crlRefs = $crlRefs;
		$this->ocspRefs = $ocspRefs;
		$this->otherRefs = $otherRefs;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CompleteRevocationRefs;
	}

	/**
	 * Create &lt;CompleteRevocationRefs> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->crlRefs )
			$this->crlRefs->generateXml( $newElement );

		if ( $this->ocspRefs )
			$this->ocspRefs->generateXml( $newElement );

		if ( $this->otherRefs )
			$this->otherRefs->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;CompleteRevocationRefs>
	 *
	 * @param \DOMElement $node
	 * @return CompleteRevocationRefs
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
				case ElementNames::CRLRefs:
					$this->crlRefs = new CRLRef();
					$this->crlRefs->loadInnerXml( $childNode );
					break;

				case ElementNames::OCSPRefs:
					$this->ocspRefs = new OCSPRef();
					$this->ocspRefs->loadInnerXml( $childNode );
					break;

				case ElementNames::OtherRefs:
					$this->otherRefs = new OtherRefs();
					$this->otherRefs->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Vaildate &lt;CompleteRevocationRefs> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->crlRefs )
			$this->crlRefs->validateElement();

		if ( $this->ocspRefs )
			$this->ocspRefs->validateElement();

		if ( $this->otherRefs )
			$this->otherRefs->validateElement();
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

			if ( $this->crlRefs )
				$this->crlRefs->traverse( $callback, $depthFirst );

			if ( $this->ocspRefs )
				$this->ocspRefs->traverse( $callback, $depthFirst  );

			if ( $this->otherRefs )
				$this->otherRefs->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
