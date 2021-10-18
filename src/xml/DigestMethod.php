<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-19
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\xmldsig\XMLSecurityDSig;

/**
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *
 *	<element name="DigestMethod" type="ds:DigestMethodType"/>
 *	<complexType name="DigestMethodType" mixed="true"> 
 *		<sequence>
 *			<any namespace="##other" processContents="lax" minOccurs="0" maxOccurs="unbounded"/>
 *		</sequence>    
 *		<attribute name="Algorithm" type="anyURI" use="required"/> 
 *	</complexType>
 */

/**
 * Creates a node for &lt;Description>
 */
class DigestMethod extends XmlCore
{
	/**
	 * Provides an instance for SHA256
	 * @var SignatureMethod
	 */
	public static $defaultMethod;

	/**
	 * This will become an attribute @Algorithm with a restricted list 
	 * of legal values that are valid digest uris
	 * @var string
	 */
	public $algorithm = XMLSecurityDSig::SHA256;

	/**
	 * Create an instance with text
	 * @param string $algorithm
	 */
	public function __construct( $algorithm = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		$this->algorithm = $algorithm;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::DigestMethod;
	}

	/**
	 * Create &lt;DigestMethod> and any descendent elements 
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::Algorithm => $this->algorithm ), $insertAfter );
	}

	/**
	 * Load the child elements of &lt;SigPolicyQualifiers>
	 * @param \DOMElement $node
	 * @return DocumentationReference
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Algorithm );
		if ( ! $attr ) return;
		$this->algorithm = $attr->value;
	}

	/** 
	 * Validate &lt;DigestMethod>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// May need to check the algorithm is a valid one but first need a comprehensive list
	}
}

DigestMethod::$defaultMethod = new DigestMethod( XMLSecurityDSig::SHA256 );