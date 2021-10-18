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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.4.1#" -->
 *
 *	<xsd:element name="SignaturePolicyStore" type="SignaturePolicyStoreType"/>
 *
 *	<xsd:complexType name="SignaturePolicyStoreType">
 *		<xsd:sequence>
 *			<xsd:element ref="SPDocSpecification"/>
 *			<xsd:choice>
 *				<xsd:element name="SignaturePolicyDocument" type="xsd:base64Binary"/>
 *				<xsd:element name="SigPolDocLocalURI" type="xsd:anyURI"/>
 *			</xsd:choice>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;SignaturePolicyStore>
 */
class SignaturePolicyStore extends XmlCore
{
	/**
	 * A &lt;SPDocSpecification>
	 * @var SPDocSpecification
	 */
	public $spdocSpecification = null;

	/**
	 * A &lt;SignaturePolicyDocument>
	 * @var SignaturePolicyDocument
	 */
	public $signaturePolicyDocument = null;

	/**
	 * Create an instance of &lt;SignaturePolicyStore> and pass in an instance of &lt;SPDocSpecification> and &lt;SignaturePolicyDocument>
	 * @param string $id
	 * @param SPDocSpecification $spdocSpecification
	 * @param SignaturePolicyDocument|SigPolDocLocalURI $signaturePolicyDocument
	 */
	public function __construct( $id = null, $spdocSpecification = null, $signaturePolicyDocument = null )
	{
		$this->spdocSpecification = $spdocSpecification;
		$this->signaturePolicyDocument = $signaturePolicyDocument;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignaturePolicyStore;
	}

	/**
	 * Create &lt;SignaturePolicyStore> and any descendent elements
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

		if ( $this->spdocSpecification )
			$this->spdocSpecification->generateXml( $newElement );

		if ( $this->signaturePolicyDocument )
			$this->signaturePolicyDocument->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;SignaturePolicyStore>
	 *
	 * @param \DOMElement $node
	 * @return SignaturePolicyStore
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
				case ElementNames::SPDocSpecification:
					$this->spdocSpecification = new SPDocSpecification();
					$this->spdocSpecification->loadInnerXml( $childNode );
					break;

				case ElementNames::SignaturePolicyDocument:
					$this->signaturePolicyDocument = new SignaturePolicyDocument();
					$this->signaturePolicyDocument->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;SignaturePolicyStore> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->spdocSpecification )
			$this->spdocSpecification->validateElement();

		if ( $this->signaturePolicyDocument )
			$this->signaturePolicyDocument->validateElement();
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

			if ( $this->spdocSpecification )
				$this->spdocSpecification->traverse( $callback, $depthFirst );

			if ( $this->signaturePolicyDocument )
				$this->signaturePolicyDocument->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
