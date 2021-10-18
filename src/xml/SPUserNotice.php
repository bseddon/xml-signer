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
 *	<xsd:element name="SPUserNotice" type="SPUserNoticeType" minOccurs="0"/>
 *
 *	<xsd:complexType name="SPUserNoticeType">
 *		<xsd:sequence>
 *			<xsd:element name="NoticeRef" type="NoticeReferenceType"/>
 *			<xsd:element name="ExplicitText" type="xsd:string"/>
 *		</xsd:sequence>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;SPUserNotice>
 */
class SPUserNotice extends XmlCore
{
	/**
	 * A &lt;NoticeRef>
	 * @var NoticeRef
	 */
	public $noticeref = null;

	/**
	 * A &lt;ExplicitText>
	 * @var ExplicitText
	 */
	public $explicitText = null;

	/**
	 * Create an instance of &lt;SPUserNotice> and pass in an instance of &lt;NoticeRef> and &lt;ExplicitText>
	 * @param NoticeRef $noticeref
	 * @param ExplicitText $explicitText
	 */
	public function __construct( $noticeref = null, $explicitText = null )
	{
		$this->noticeref = self::createConstructor( $noticeref, NoticeRef::class );
		$this->explicitText = self::createConstructor( $explicitText, ExplicitText::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SPUserNotice;
	}

	/**
	 * Create &lt;SPUserNotice> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );

		if ( $this->noticeref )
			$this->noticeref->generateXml( $newElement );

		if ( $this->explicitText )
			$this->explicitText->generateXml( $newElement );
	}

	/**
	 * Load the child elements of &lt;SPUserNotice>
	 *
	 * @param \DOMElement $node
	 * @return SPUserNotice
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
				case ElementNames::NoticeRef:
					$this->noticeref = new NoticeRef();
					$this->noticeref->loadInnerXml( $childNode );
					break;

				case ElementNames::ExplicitText:
					$this->explicitText = new ExplicitText();
					$this->explicitText->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Create &lt;SPUserNotice> and any descendent elements 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		if ( $this->noticeref )
			$this->noticeref->validateElement();

		if ( $this->explicitText )
			$this->explicitText->validateElement();
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

			if ( $this->noticeref )
				$this->noticeref->traverse( $callback, $depthFirst );

			if ( $this->explicitText )
				$this->explicitText->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
