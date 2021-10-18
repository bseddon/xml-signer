<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

/**
 * <!-- targetNamespace="http://uri.etsi.org/01903/v1.1.1#" -->
 *
 * <xsd:element name="Include" type="IncludeType"/>
 *
 *	<xsd:complexType name="IncludeType">
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="required"/>
 *		<xsd:attribute name="referencedData" type="xsd:boolean" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Include is part of GenericTimeStampType
 * The classname 'Incl' is used because 'Include' is a keyword so cannot be used.
 * However, the Xml tag used will still be 'Include'
 */
class Incl extends XmlCore
{
	/**
	 * Represents @ReferenceData
	 * @var bool
	 */
	public $referenceData;

	/**
	 * The Uri defining the reference to include
	 * @var string
	 */
	public $uri = null;

	/**
	 * Create an &lt;Include> instance
	 * @param boolean $referenceData
	 * @param string $uri
	 */
	public function __construct( $uri = null, $referenceData = true )
	{
		$this->referenceData = $referenceData;
		$this->uri = $uri;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Include;
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Include
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );
		
		$attr = $node->getAttributeNode( AttributeNames::URI );
		if ( $attr )
		{
			$this->uri = $attr->value;
		}

		$attr = $node->getAttributeNode( AttributeNames::ReferencedData );
		if ( $attr )
		{
			$this->referenceData = $attr->value;
		}

		return $this;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::URI => $this->uri,  AttributeNames::ReferencedData => $this->referenceData ? 'true' : 'false' ), $insertAfter );
	}

	/** 
	 * Validate @Uri
	 * @throws \Exception
	 */
	public function validateElement()
	{
		if ( ! $this->uri )
			throw new \Exception("A Uri MUST be provided");
	}
}