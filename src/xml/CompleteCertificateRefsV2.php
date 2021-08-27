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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.4.1#"
 *		The preamble of the XML Schema file also includes the following namespace declaration:
 *		xmlns:xades="http://uri.etsi.org/01903/v1.3.2#",
 *		which assigns the prefix "xades" to the namespace whose URI is shown in the declaration.
 *	-->
 *	<xsd:element name="CompleteCertificateRefsV2" type="CompleteCertificateRefsTypeV2"/>
 *	
 *	<xsd:complexType name="CompleteCertificateRefsTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertRefs" type="xades:CertIDListV2Type"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CompleteCertificateRefsV2>
 */
class CompleteCertificateRefsV2 extends XmlCore
{
	/**
	 * A &lt;CertRefs>
	 * @var CertRefsV2
	 */
	public $certRefs = null;

	/**
	 * Create an instance of &lt;CompleteCertificateRefs> and pass in an instance of &lt;CertRefs>
	 * @param CertRefsV2 $certRefs
	 * @param string $id
	 */
	public function __construct( $certRefs = null, $id = null )
	{
		$this->certRefs = $certRefs;
		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CompleteCertificateRefsV2;
	}

	/**
	 * Create &lt;CompleteCertificateRefsV2> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->certRefs )
			$this->certRefs->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;CompleteCertificateRefsV2>
	 *
	 * @param \DOMElement $node
	 * @return CompleteCertificateRefsV2
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
				case ElementNames::CertRefs:
					$this->certRefs = new CertRefsV2();
					$this->certRefs->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Vaildate &lt;CompleteCertificateRefsV2> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->certRefs )
			$this->certRefs->validateElement();
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

			if ( $this->certRefs )
				$this->certRefs->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
