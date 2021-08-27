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
 *	<xsd:element name="NoticeRef" type="NoticeReferenceType" minOccurs="0"/>
 *
 *	<xsd:complexType name="NoticeReferenceType">
 *		<xsd:sequence>
 *			<xsd:element name="Organization" type="xsd:string"/>
 *			<xsd:element name="NoticeNumbers" type="IntegerListType"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;NoticeRef>
 */
class NoticeRef extends XmlCore
{
	/**
	 * A &lt;Organization>
	 * @var Organization
	 */
	public $organization = null;

	/**
	 * A &lt;NoticeNumbers>
	 * @var NoticeNumbers
	 */
	public $noticeNumbers = null;

	/**
	 * Create an instance of &lt;NoticeRef> and pass in an instance of &lt;Organization> and &lt;NoticeNumbers>
	 * @param Organization $organization
	 * @param NoticeNumbers $noticeNumbers
	 */
	public function __construct( $organization = null, $noticeNumbers = null )
	{
		$this->organization = self::createConstructor( $organization, Organization::class );
		$this->noticeNumbers = self::createConstructor( $noticeNumbers, NoticeNumbers::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::NoticeRef;
	}

	/**
	 * Create &lt;NoticeRef> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		if ( $this->organization )
			$this->organization->generateXml( $newElement );

		if ( $this->noticeNumbers )
			$this->noticeNumbers->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;NoticeRef>
	 *
	 * @param \DOMElement $node
	 * @return NoticeRef
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
				case ElementNames::Organization:
					$this->organization = new Organization();
					$this->organization->loadInnerXml( $childNode );
					break;

				case ElementNames::NoticeNumbers:
					$this->noticeNumbers = new NoticeNumbers();
					$this->noticeNumbers->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;NoticeRef> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->organization )
			$this->organization->validateElement();

		if ( $this->noticeNumbers )
			$this->noticeNumbers->validateElement();
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

			if ( $this->organization )
				$this->organization->traverse( $callback, $depthFirst );

			if ( $this->noticeNumbers )
				$this->noticeNumbers->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
