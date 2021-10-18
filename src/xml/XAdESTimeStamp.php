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
 *	<xsd:element name="XAdESTimeStamp" type="XAdESTimeStampType"/>
 *
 *	<xsd:complexType name="XAdESTimeStampType">
 *		<xsd:complexContent>
 *			<xsd:restriction base="GenericTimeStampType">
 *				<xsd:sequence>
 *					<xsd:element ref="Include" minOccurs="0" maxOccurs="unbounded"/>
 *					<xsd:element ref="ds:CanonicalizationMethod" minOccurs="0"/>
 *					<xsd:choice maxOccurs="unbounded">
 *						<xsd:element name="EncapsulatedTimeStamp" type="EncapsulatedPKIDataType"/>
 *						<xsd:element name="XMLTimeStamp" type="AnyType"/>
 *					</xsd:choice>
 *				</xsd:sequence>
 *			<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *			</xsd:restriction>
 *		</xsd:complexContent>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;XAdESTimeStamp>
 */
abstract class XAdESTimeStamp extends XmlCore
{
	/**
	 * If used defines URIs to elements that are to be included as part of the message imprint.
	 * @var Incl
	 */
	public $include = null;

	/**
	 * An element to define the canonicalization algorithm to use
	 * @var CanonicalizationMethod
	 */
	public $canonicalizationMethod = null;

	/**
	 * Represents the elements containing the DER encoded data of the timestamp
	 * This is one of two ways that timestamp data can be included.  The other,
	 * mutually exclusive way, is the XMLTimeStamp.
	 * @var EncapsulatedPKIData
	 */
	public $encapsulatedTimeStamp = null;

	/**
	 * Represents the elements containing timestamp data in an XML format.  This 
	 * is a place holder as it is not supported and an attempt to use it will
	 * generate an error.
	 * This is one of two ways that timestamp data can be included.  The other,
	 * mutually exclusive way, is the XMLTimeStamp.
	 * @var XMLTimeStamp
	 */
	public $xmlTimestamp = null;

	/**
	 * This is an abstract class with descendents AllDataObjectsTimeStamp 
	 * and IndividualDataObjectsTimeStamp.
	 * @param Incl $include
	 * @param CanonicalizationMethod $canonicalizationMethod
	 * @param EncapsulatedPKIData $encapsulatedTimeStamp
	 * @param XMLTimeStamp $xmlTimestamp
	 * @param string $id
	 */
	public function __construct(
		$include = null, 
		$canonicalizationMethod = null, 
		$encapsulatedTimeStamp = null, 
		$id = null,
		$xmlTimestamp = null )
	{
		$this->include = self::createConstructor( $include, Incl::class ); 
		$this->canonicalizationMethod = self::createConstructor( $canonicalizationMethod, CanonicalizationMethod::class ); 
		$this->encapsulatedTimeStamp = self::createConstructor( $encapsulatedTimeStamp, EncapsulatedTimeStamp::class );
		$this->xmlTimestamp = $xmlTimestamp;
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
		return ElementNames::XAdESTimeStamp;
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

		if ( $this->canonicalizationMethod )
			$this->canonicalizationMethod->generateXml( $newElement );

		if ( $this->encapsulatedTimeStamp )
			$this->encapsulatedTimeStamp->generateXml( $newElement );

		if ( $this->xmlTimestamp )
			$this->xmlTimestamp->generateXml( $newElement );
	}

	/**
	 * Create theobject hierarchy from an XML node
	 *
	 * @param \DOMElement $node
	 * @return XAdESTimeStamp
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );

		// Look for elements with tags that are <UnsignedSignatureProperties> children
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

				case ElementNames::EncapsulatedTimeStamp:
					$this->encapsulatedTimeStamp = new EncapsulatedTimeStamp();
					$this->encapsulatedTimeStamp->loadInnerXml( $childNode );
					break;

				case ElementNames::CanonicalizationMethod:
					$this->canonicalizationMethod = new CanonicalizationMethod();
					$this->canonicalizationMethod->loadInnerXml( $childNode );
					break;

				case ElementNames::XMLTimeStamp:
					$this->xmlTimestamp = new XMLTimeStamp();
					$this->xmlTimestamp->loadInnerXml( $childNode );
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
		if ( $this->encapsulatedTimeStamp && $this->xmlTimestamp )
			throw new \Exception("Only EncapsulatedTimeStamp OR XMLTimestamp allowed.  They are mutually exclusive.");

		if ( $this->encapsulatedTimeStamp && $this->xmlTimestamp )
			throw new \Exception("Only EncapsulatedTimeStamp OR XMLTimestamp allowed.  They are mutually exclusive.");

		parent::validateElement();

		if ( $this->include )
			$this->include->validateElement();

		if ( $this->canonicalizationMethod )
			$this->canonicalizationMethod->validateElement();

		if ( $this->encapsulatedTimeStamp )
			$this->encapsulatedTimeStamp->validateElement();

		if ( $this->xmlTimestamp )
		{
			// $this->xmlTimestamp->validateElement();
			throw new \Exception("XMLTimeStamp is not supported yet");
		}
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

			if ( $this->include )
				$this->include->traverse( $callback, $depthFirst );

			if ( $this->canonicalizationMethod )
				$this->canonicalizationMethod->traverse( $callback, $depthFirst  );

			if ( $this->encapsulatedTimeStamp )
				$this->encapsulatedTimeStamp->traverse( $callback, $depthFirst  );

			if ( $this->xmlTimestamp )
				$this->xmlTimestamp->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
