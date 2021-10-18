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
 *	<xsd:element name="SignedDataObjectProperties" type="SignedDataObjectPropertiesType" />
 *
 * 	<xsd:complexType name="SignedDataObjectPropertiesType">
 * 		<xsd:sequence>
 * 			<xsd:element ref="DataObjectFormat" minOccurs="0" maxOccurs="unbounded"/>
 * 			<xsd:element ref="CommitmentTypeIndication" minOccurs="0" maxOccurs="unbounded"/>
 * 			<xsd:element ref="AllDataObjectsTimeStamp" minOccurs="0" maxOccurs="unbounded"/>
 * 			<xsd:element ref="IndividualDataObjectsTimeStamp" minOccurs="0" maxOccurs="unbounded"/>
 * 			<xsd:any namespace="##other" minOccurs="0" maxOccurs="unbounded"/>
 * 		</xsd:sequence>
 * 		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 * 	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SignedDataObjectProperties> which is a container for a collection of elements
 */
class SignedDataObjectProperties extends XmlCore
{
	/**
	 * @var DataObjectFormat[]
	 */
	public $dataObjectFormat = null;

	/**
	 * @var CommitmentTypeIndication[]
	 */
	public $commitmentTypeIndication = null;

	/**
	 * @var AllDataObjectsTimeStamp[]
	 */
	public $allDataObjectsTimeStamp = null;

	/**
	 * @var IndividualDataObjectsTimeStamp[]
	 */
	public $individualDataObjectsTimeStamp = null;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param DataObjectFormat|DataObjectFormat[] $dataObjectFormat
	 * @param CommitmentTypeIndication|CommitmentTypeIndication[] $commitmentTypeIndication
	 * @param AllDataObjectsTimeStamp|AllDataObjectsTimeStamp[] $allDataObjectsTimeStamp
	 * @param IndividualDataObjectsTimeStamp|IndividualDataObjectsTimeStamp[] $individualDataObjectsTimeStamp
	 * @param string $id
	 */
	public function __construct( 
		$dataObjectFormat = null, 
		$commitmentTypeIndication = null, 
		$allDataObjectsTimeStamp = null, 
		$individualDataObjectsTimeStamp = null, 
		$id = null
	)
	{
		$this->dataObjectFormat = self::createConstructorArray( $dataObjectFormat, DataObjectFormat::class );
		$this->commitmentTypeIndication = self::createConstructorArray( $commitmentTypeIndication, CommitmentTypeIndication::class );
		$this->allDataObjectsTimeStamp = self::createConstructorArray( $allDataObjectsTimeStamp, AllDataObjectsTimeStamp::class );
		$this->individualDataObjectsTimeStamp = self::createConstructorArray( $individualDataObjectsTimeStamp, IndividualDataObjectsTimeStamp::class );

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignedDataObjectProperties;
	}

	/**
	 * Create &lt;SignedDataObjectProperties> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$processProperty = function( $parentNode, $attributes, $values )
		{
			if ( is_null( $values ) ) return;

			foreach( $values as $value )
				/** @var XmlCore $value */
				$value->generateXml( $parentNode, $attributes );
		};

		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		// Now create a node for all the sub-nodes where they exist
		$processProperty ( $newElement, $attributes, $this->dataObjectFormat );
		$processProperty ( $newElement, $attributes, $this->commitmentTypeIndication );
		$processProperty ( $newElement, $attributes, $this->allDataObjectsTimeStamp );
		$processProperty ( $newElement, $attributes, $this->individualDataObjectsTimeStamp );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return SignedDataObjectProperties
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::DataObjectFormat:
					$dataObjectFormat = new DataObjectFormat();
					$dataObjectFormat->loadInnerXml( $childNode );
					$this->dataObjectFormat[] = $dataObjectFormat;
					break;

				case ElementNames::CommitmentTypeIndication:
					$commitmentTypeIndication = new CommitmentTypeIndication();
					$commitmentTypeIndication->loadInnerXml( $childNode );
					$this->commitmentTypeIndication[] = $commitmentTypeIndication;
					break;

				case ElementNames::AllDataObjectsTimeStamp:
					$allDataObjectsTimeStamp = new AllDataObjectsTimeStamp();
					$allDataObjectsTimeStamp->loadInnerXml( $childNode );
					$this->allDataObjectsTimeStamp[] = $allDataObjectsTimeStamp;
					break;

				case ElementNames::IndividualDataObjectsTimeStamp:
					$individualDataObjectsTimeStamp = new IndividualDataObjectsTimeStamp();
					$individualDataObjectsTimeStamp->loadInnerXml( $childNode );
					$this->individualDataObjectsTimeStamp[] = $individualDataObjectsTimeStamp;
					break;
			}
		}

		return $this;
	}
	
	/**
	 * Allow the properties to validate themselves
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		$processProperty = function( $values )
		{
			if ( is_null( $values ) ) return;

			foreach( $values as $value )
				/** @var XmlCore $value */
				$value->validateElement();
		};

		$processProperty ( $this->dataObjectFormat );
		$processProperty ( $this->commitmentTypeIndication );
		$processProperty ( $this->allDataObjectsTimeStamp );
		$processProperty ( $this->individualDataObjectsTimeStamp );
	}

	/**
	 * Calls the closure in $callback and does the same on any descendents
	 * @param Closure $callback
	 * @param bool $depthFirst (optional: default = false)  When true this will call on child nodes first
	 * @return XmlCore
	 */
	public function traverse( $callback, $depthFirst = false )
	{
		$processProperty = function( $values ) use( $callback, $depthFirst )
		{
			if ( is_null( $values ) ) return;

			foreach( $values as $value )
				/** @var XmlCore $value */
				$value->traverse( $callback, $depthFirst );
		};

		if ( $callback instanceof \Closure )
		{
			if ( ! $depthFirst )
				parent::traverse( $callback, $depthFirst );

			$processProperty ( $this->dataObjectFormat );
			$processProperty ( $this->commitmentTypeIndication );
			$processProperty ( $this->allDataObjectsTimeStamp );
			$processProperty ( $this->individualDataObjectsTimeStamp );

			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
