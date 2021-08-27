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
 *	<xsd:element name="ReferenceInfo" type="ReferenceInfoType"/>
 *
 *	<xsd:complexType name="ReferenceInfoType">
 *		xsd:sequence>
 *			xsd:element ref="ds:DigestMethod"/>
 *			xsd:element ref="ds:DigestValue"/>
 *		/xsd:sequence>
 *		xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *		xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;ReferenceInfo>
 */
class ReferenceInfo extends CertDigest
{
	/**
	 * Represents @URI
	 * @var string
	 */
	private $uri = null;

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::ReferenceInfo;
	}

	/**
	 * Generate xml for the element and the attributes
	 *
	 * @param [type] $parentNode
	 * @param array $attributes
	 * @return void
	 */
	public function generateXml($parentNode, $attributes = array())
	{
		parent::generateXml( $parentNode, array( AttributeNames::Uri => $this->uri ) );
	}

	/**
	 * Load the elements and attributes
	 * @param \DOMElement $node
	 * @return ReferenceInfo
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Uri );
		if ( $attr )
		{
			$this->uri = $attr->value;
		}
	}
}
