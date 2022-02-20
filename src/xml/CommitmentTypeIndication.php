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
 *	<xsd:element name="CommitmentTypeIndication" type="CommitmentTypeIndicationType"/>
 *
 *	<xsd:complexType name="CommitmentTypeIndicationType">
 *		<xsd:sequence>
 *			<xsd:element name="CommitmentTypeId" type="ObjectIdentifierType"/>
 *			<xsd:choice>
 *				<xsd:element name="ObjectReference" type="xsd:anyURI" maxOccurs="unbounded"/>
 *				<xsd:element name="AllSignedDataObjects"/>
 *			</xsd:choice>
 *			<xsd:element name="CommitmentTypeQualifiers" type="CommitmentTypeQualifiersListType" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="CommitmentTypeQualifiersListType">
 *		<xsd:sequence>
 *			<xsd:element name="CommitmentTypeQualifier" type="AnyType" minOccurs="0" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;SigPolicyHash>
 */
class CommitmentTypeIndication extends XmlCore
{
	/**
	 * A &lt;CommitmentTypeId>
	 * @var CommitmentTypeId
	 */
	public $commitmentTypeId = null;

	/**
	 * A &lt;ObjectReference>
	 * @var ObjectReference[]
	 */
	public $objectReference = null;

	/**
	 * A &lt;AllSignedDataObjects>
	 * @var AllSignedDataObjects
	 */
	public $allSignedDataObjects = null;

	/**
	 * A &lt;CommitmentTypeQualifiers>
	 * @var CommitmentTypeQualifiers
	 */
	public $commitmentTypeQualifiers = null;

	/**
	 * Create an instance of &lt;CommitmentTypeIndication> and pass in an instance of &lt;CommitmentTypeId> and &lt;ObjectReference>
	 * @param CommitmentTypeId $commitmentTypeId
	 * @param ObjectReference|ObjectReference[] $objectReference
	 * @param AllSignedDataObjects|true $allSignedDataObjects
	 * @param CommitmentTypeQualifiers $commitmentTypeQualifiers
	 */
	public function __construct( $commitmentTypeId = null, $objectReference = null, $allSignedDataObjects = null, $commitmentTypeQualifiers = null )
	{
		if ( $objectReference && $allSignedDataObjects)
			throw new \Exception("Both ObjectReference and AllSignedDataObjects have been supplied.  Only one or the other is allow.");

		$this->commitmentTypeId = self::createConstructor( $commitmentTypeId, CommitmentTypeId::class );
		$this->objectReference = self::createConstructorArray( $objectReference, ObjectReference::class );
		$this->allSignedDataObjects = $allSignedDataObjects === true ? new AllSignedDataObjects() : self::createConstructor( $allSignedDataObjects, AllSignedDataObjects::class );
		$this->commitmentTypeQualifiers = self::createConstructor( $commitmentTypeQualifiers, CommitmentTypeQualifiers::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CommitmentTypeIndication;
	}

	/**
	 * Create &lt;CommitmentTypeIndication> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->commitmentTypeId )
			$this->commitmentTypeId->generateXml( $newElement );

		if ( $this->objectReference )
		foreach( $this->objectReference as $objectReference )
			$objectReference->generateXml( $newElement );

		if ( $this->allSignedDataObjects )
			$this->allSignedDataObjects->generateXml( $newElement );

		if ( $this->commitmentTypeQualifiers )
			$this->commitmentTypeQualifiers->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;CommitmentTypeIndication>
	 *
	 * @param \DOMElement $node
	 * @return CommitmentTypeIndication
	 */
	public function loadInnerXml( $node )
	{
		parent::loadInnerXml( $node );
		// There are no attributes for this element

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::CommitmentTypeId:
					$this->commitmentTypeId = new CommitmentTypeId();
					$this->commitmentTypeId->loadInnerXml( $childNode );
					break;

				case ElementNames::ObjectReference:
					$objectReference = new ObjectReference();
					$objectReference->loadInnerXml( $childNode );
					$this->objectReference[] = $objectReference;
					break;

				case ElementNames::AllSignedDataObjects:
					$this->allSignedDataObjects = new AllSignedDataObjects();
					$this->allSignedDataObjects->loadInnerXml( $childNode );
					break;

				case ElementNames::CommitmentTypeQualifiers:
					$this->commitmentTypeQualifiers = new CommitmentTypeQualifiers();
					$this->commitmentTypeQualifiers->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;CommitmentTypeId> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->commitmentTypeId )
			$this->commitmentTypeId->validateElement();

		if ( $this->objectReference )
		foreach ( $this->objectReference as $objectReference)
			$objectReference->validateElement();

		if ( $this->allSignedDataObjects )
			$this->allSignedDataObjects->validateElement();

		if ( $this->commitmentTypeQualifiers )
			$this->commitmentTypeQualifiers->validateElement();
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

			if ( $this->commitmentTypeId )
				$this->commitmentTypeId->traverse( $callback, $depthFirst );

			if ( $this->objectReference )
			foreach ( $this->objectReference as $objectReference)
				$objectReference->traverse( $callback, $depthFirst );

			if ( $this->allSignedDataObjects )
				$this->allSignedDataObjects->traverse( $callback, $depthFirst  );

			if ( $this->commitmentTypeQualifiers )
				$this->commitmentTypeQualifiers->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
