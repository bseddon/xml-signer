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
 *   <element name="Transform" type="ds:TransformType"/>
 * 
 *   <complexType name="TransformType" mixed="true">
 *     <choice minOccurs="0" maxOccurs="unbounded"> 
 *       <any namespace="##other" processContents="lax"/>
 *       <!-- (1,1) elements from (0,unbounded) namespaces -->
 *       <element name="XPath" type="string"/> 
 *     </choice>
 *     <attribute name="Algorithm" type="anyURI" use="required"/> 
 *   </complexType> * 
 */

/**
 * Creates a node for &lt;Transform> 
 */
class Transform extends XmlCore
{
	/**
	 * Maps the algorithm uri to a class
	 */
	const transformMap= array(
		XMLSecurityDSig::C14N => 'Transform',
		XMLSecurityDSig::C14N_COMMENTS => 'Transform',
		XMLSecurityDSig::EXC_C14N => 'Transform',
		XMLSecurityDSig::EXC_C14N_COMMENTS => 'Transform',
		XMLSecurityDSig::ENV_SIG => 'Transform',
		XMLSecurityDSig::XPATH_FILTER2 => 'TransformXPathFilter2',
		XMLSecurityDSig::CXPATH => 'TransformXPath',
		XMLSecurityDSig::BASE64 => 'Transform',
		XMLSecurityDSig::XSLT => 'Transform'
	);

	/**
	 * Inclusive canonicalization without comments
	 */
	const C14N = XMLSecurityDSig::C14N;
	/**
	 * Inclusive canonicalization with comments
	 */
	const C14N_COMMENTS = XMLSecurityDSig::C14N_COMMENTS;
	/**
	 * Exclusive canonicalization without comments
	 */
	const EXC_C14N = XMLSecurityDSig::EXC_C14N;
	/**
	 * Exclusive canonicalization with comments
	 */
	const EXC_C14N_COMMENTS = XMLSecurityDSig::EXC_C14N_COMMENTS;
	/**
	 * Enveloped signatire canonicalization
	 */
	const ENV_SIG = XMLSecurityDSig::ENV_SIG;
	/**
	 * XPath filter 2.0
	 */
	const XPATH_FILTER2 = XMLSecurityDSig::XPATH_FILTER2;
	/**
	 * XPath filter 1.0
	 */
	const CXPATH = XMLSecurityDSig::CXPATH;
	/**
	 * Base64
	 */
	const BASE64 = XMLSecurityDSig::BASE64;
	/**
	 * An XSLT query to process (not supported)
	 */
	const XSLT = XMLSecurityDSig::XSLT;

	/**
	 * This will become an attribute @Algorithm with a restricted list 
	 * of legal values that are listed in the transformMaps property
	 *
	 * @var string
	 */
	public $algorithm = XMLSecurityDSig::C14N;

	/**
	 * Create a base <Tranform> instance
	 * @param string $algorithm
	 */
	public function __construct( $algorithm = null )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		if ( $algorithm )
			$this->algorithm = $algorithm;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Transform;
	}

	/**
	 * Create <Transform> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		return parent::generateXml( $parentNode, array( AttributeNames::Algorithm => $this->algorithm ) );
	}

	/**
	 * Validate the algorithm supplied
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		// Check the algorithm is valid
		if ( isset( self::transformMap[ $this->algorithm ] ) ) return;

		throw new \Exception("The <Transform> @Algorithm used is not one of the valid Urls: '{$this->algorithm}'");
	}

	/**
	 * Converts a transform to a simple representation representation for use by XMLSecurityDSig::AddRefInternal()
	 * @return void
	 */
	public function toSimpleRepresentation()
	{
		return $this->algorithm;
	}

	/**
	 * Returns true if the transform algorithm is enveloped
	 *
	 * @return boolean
	 */
	public function isEnveloped()
	{
		return $this->algorithm == XMLSecurityDSig::ENV_SIG;
	}
}
