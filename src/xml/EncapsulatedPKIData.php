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
 *	<xsd:element name="EncapsulatedPKIData" type="EncapsulatedPKIDataType"/>
 *
 *	<xsd:complexType name="EncapsulatedPKIDataType">
 *		<xsd:simpleContent>
 *			<xsd:extension base="xsd:base64Binary">
 *				<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *				<xsd:attribute name="Encoding" type="xsd:anyURI" use="optional"/>
 *			</xsd:extension>
 *		</xsd:simpleContent>
 *	</xsd:complexType
 */

/**
 * reates a node for &lt;CertifiedRoles> which contains one or more &lt;EncapsulatedPKIData>
 */
class EncapsulatedPKIData extends Base64String
{
	/**
	 * Content of @Encoding
	 * @var string
	 */
	public $encoding = 'http://uri.etsi.org/01903/v1.2.2#DER';

	/**
	 * Creates an EncapsulatedPKIData instance
	 * @param string $base64 This is a base 64 encoded binary of a DER encoded certificate
	 * @param string $id (optional)
	 * @param Encoding $encoding (optional) Defaults to the DER URI
	 */
	public function __construct( $base64 = null, $id = null, $encoding = null )
	{
		$this->id = $id;
		if ( ! is_null( $encoding ) )
			$this->encoding = $encoding;
		parent::__construct( $base64 );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::EncapsulatedPKIData;
	}

	/**
	 * Create an element with an optional @Id
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		return parent::generateXml( $parentNode, array( AttributeNames::Encoding => $this->encoding ), $insertAfter );
	}

	/** 
	 * Create a new Xml representation for $node
	 * @param  \DOMElement $node
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Encoding );
		if ( $attr )
			$this->encoding = $attr->value;
		}
}
