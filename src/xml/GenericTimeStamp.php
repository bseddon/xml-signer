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
 *	<xsd:element name="GenericTimeStamp" type="xsd:string" minOccurs="0"/>
 *
 *	<xsd:complexType name="GenericTimeStampType" abstract="true">
 *		<xsd:sequence>
 *			<xsd:choice minOccurs="0">
 *				<xsd:element ref="Include" minOccurs="0" maxOccurs="unbounded"/>
 *				<xsd:element ref="ReferenceInfo" maxOccurs="unbounded"/>
 *			</xsd:choice>
 *			<xsd:element ref="ds:CanonicalizationMethod" minOccurs="0"/>
 *			<xsd:choice maxOccurs="unbounded">
 *				<xsd:element name="EncapsulatedTimeStamp" type="EncapsulatedPKIDataType"/>
 *				<xsd:element name="XMLTimeStamp" type="AnyType"/>
 *			</xsd:choice>
 *		</xsd:sequence>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType> 
 */

/**
 * Defines a timestamp with every possible feature.  Real timestamps will have 
 * some subset of the components used in this class.
 */
abstract class GenericTimeStamp extends XmlCore
{
	/**
	 * If used defines URIs to elements that are to be included as part of the message imprint
	 * This is one of two possible ways to define the content to include. The other, mutually
	 * exclusive option, is the use of ReferenceInfo
	 * @var Incl
	 */
	public $include = null;

	/**
	 * If used, it defines references to &lt;Reference> elements
	 * This is one of two possible ways to define the content to include. The other, mutually
	 * exclusive option, is the use of Include
	 * @var ReferenceInfo|ReferenceInfo[]
	 */
	public $referenceInfo = null;

	/**
	 * An element to define the canonicalization algorithm to use
	 * @var CanonicalizationMethod
	 */
	public $canonicalizationMethod = null;

	/**
	 * Represents the elements containing the DER encoded data of the timestamp
	 * This is one of two ways that timestamp data can be included.  The other,
	 * mutually exclusive way, is the XMLTimeStamp.
	 * @var EncapsulatedTimeStamp|EncapsulatedTimeStamp[]
	 */
	public $encapsulatedTimeStamp = null;

	/**
	 * Represents the elements containing timestamp data in an XML format.  This 
	 * is a place holder as it is not supported and an attempt to use it will
	 * generate an error.
	 * This is one of two ways that timestamp data can be included.  The other,
	 * mutually exclusive way, is the XMLTimeStamp.
	 * @var XMLTimeStamp|XMLTimeStamp[]
	 */
	public $xmlTimestamp = null;

	/**
	 * Constructor
	 *
	 * @param Incl $include
	 * @param ReferenceInfo $referenceInfo
	 * @param CanonicalizationMethod $canonicalizationMethod
	 * @param EncapsulatedTimeStamp|EncapsulatedTimeStamp[] $encapsulatedTimeStamp
	 * @param XMLTimeStamp $xmlTimestamp
	 * @param string $id
	 */
	public function __construct(
		$include = null, 
		$referenceInfo = null, 
		$canonicalizationMethod = null, 
		$encapsulatedTimeStamp = null, 
		$xmlTimestamp = null,
		$id = null )
	{
		if ( $include && $referenceInfo )
			throw new \Exception("Only Include OR ReferenceInfo allowed.  They are mutually exclusive.");

		$this->include = $include; 
		$this->referenceInfo = self::createConstructorArray( $referenceInfo, ReferenceInfo::class ); 
		$this->canonicalizationMethod = $canonicalizationMethod; 
		$this->encapsulatedTimeStamp = self::createConstructorArray( $encapsulatedTimeStamp, EncapsulatedTimeStamp::class );
		$this->xmlTimestamp = self::createConstructorArray( $xmlTimestamp, XMLTimeStamp::class );
		$this->id = $id;

		if ( ! is_null( $xmlTimestamp ) )
			throw new \Exception("The XMLTimeStamp is not yet supported");
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::GenericTimeStamp;
	}

	/**
	 * Generate the XML for the element and descendents
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->include )
			$this->include->generateXml( $newElement );

		if ( $this->referenceInfo )
		foreach( $this->referenceInfo as $referenceInfo )
			$referenceInfo->generateXml( $newElement );

		if ( $this->canonicalizationMethod )
			$this->canonicalizationMethod->generateXml( $newElement );

		if ( $this->encapsulatedTimeStamp )
		foreach( $this->encapsulatedTimeStamp as $encapsulatedTimeStamp )
			$encapsulatedTimeStamp->generateXml( $newElement );

		if ( $this->xmlTimestamp )
		foreach( $this->xmlTimestamp as $xmlTimestamp )
			$xmlTimestamp->generateXml( $newElement );
	}

	/**
	 * Create theobject hierarchy from an XML node
	 *
	 * @param \DOMElement $node
	 * @return GenericTimeStamp
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Include:
					$this->include = new Incl();
					$this->include->loadInnerXml( $childNode );
					break;

				case ElementNames::ReferenceInfo:
					$referenceInfo = new ReferenceInfo();
					$referenceInfo->loadInnerXml( $childNode );
					$this->referenceInfo = $referenceInfo;
					break;

				case ElementNames::CanonicalizationMethod:
					$this->canonicalizationMethod = CanonicalizationMethod::$defaultMethod;
					$this->canonicalizationMethod->loadInnerXml( $childNode );
					break;

				case ElementNames::EncapsulatedTimeStamp:
					$encapsulatedTimeStamp = new EncapsulatedTimeStamp();
					$encapsulatedTimeStamp->loadInnerXml( $childNode );
					$this->encapsulatedTimeStamp = $encapsulatedTimeStamp;
					break;

				case ElementNames::XMLTimeStamp:
					$xmlTimestamp = new XMLTimeStamp();
					$xmlTimestamp->loadInnerXml( $childNode );
					$this->xmlTimestamp = $xmlTimestamp;
					break;
			}
		}
	}

	/**
	 * Validate the objects
	 * @return void
	 */
	public function validateElement()
	{
		if ( $this->include && $this->referenceInfo )
			throw new \Exception("Only Include OR ReferenceInfo allowed.  They are mutually exclusive.");

		if ( $this->encapsulatedTimeStamp && $this->xmlTimestamp )
			throw new \Exception("Only EncapsulatedTimeStamp OR XMLTimestamp allowed.  They are mutually exclusive.");

		if ( $this->encapsulatedTimeStamp && $this->xmlTimestamp )
			throw new \Exception("Only EncapsulatedTimeStamp OR XMLTimestamp allowed.  They are mutually exclusive.");

		parent::validateElement();

		if ( $this->include )
			$this->include->validateElement();

		if ( $this->referenceInfo )
		foreach( $this->referenceInfo as $referenceInfo )
			$referenceInfo->validateElement();

		if ( $this->canonicalizationMethod )
			$this->canonicalizationMethod->validateElement();

		if ( $this->encapsulatedTimeStamp )
		foreach( $this->encapsulatedTimeStamp as $encapsulatedTimeStamp )
			$encapsulatedTimeStamp->validateElement();

		if ( $this->xmlTimestamp )
		{
			// $this->xmlTimestamp->validateElement();
			throw new \Exception("XMLTimeStamp is not supported yet");
		}
	}
}
