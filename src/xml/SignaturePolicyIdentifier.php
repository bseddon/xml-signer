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
 *	<xsd:element name="SignaturePolicyIdentifier" type="SignaturePolicyIdentifierType"/>
 *
 *	<xsd:complexType name="SignaturePolicyIdentifierType">
 *		<xsd:choice>
 *			<xsd:element name="SignaturePolicyId" type="SignaturePolicyIdType"/>
 *			<xsd:element name="SignaturePolicyImplied"/>
 *		</xsd:choice>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="SignaturePolicyIdType">
 *		<xsd:sequence>
 *			<xsd:element name="SigPolicyId" type="ObjectIdentifierType"/>
 *			<xsd:element ref="ds:Transforms" minOccurs="0"/>
 *			<xsd:element name="SigPolicyHash" type="DigestAlgAndValueType"/>
 *			<xsd:element name="SigPolicyQualifiers"	type="SigPolicyQualifiersListType" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="SigPolicyQualifiersListType">
 *		<xsd:sequence>
 *			<xsd:element name="SigPolicyQualifier" type="AnyType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

 /**
  * Class begins the definition of the signature policy
  */
class SignaturePolicyIdentifier extends XmlCore
{
	/**
	 * @var SignaturePolicyImplied | SignaturePolicyId
	 */
	public $type = null;

	/**
	 * @var SignaturePolicyImplied
	 */
	public $signaturePolicyImplied = null;

	/**
	 * @var SignaturePolicyId
	 */
	public $signaturePolicyId = null;

	/**
	 * Constructor
	 * @param SignaturePolicyImplied|SignaturePolicyId $type
	 * @return void
	 */
	public function __construct( $type = null )
	{
		if ( ! $type ) return null;

		$this->type = $type;

		if ( $type instanceof SignaturePolicyImplied )
		{
			$this->signaturePolicyImplied = $type;
		}
		else if ( $type instanceof SignaturePolicyId )
		{
			$this->signaturePolicyId = $type;
		}
		else
		{
			throw new \Exception("The type passed in is not valid.  Must be <SignaturePolicyImplied> or <SignaturePolicyId>");
		}
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignaturePolicyIdentifier;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode );
		$this->type->generateXml( $newElement );
	}

		/**
	 * Load the child elements of &lt;SignaturePolicyId>
	 *
	 * @param \DOMElement $node
	 * @return DocumentationReference
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
				case ElementNames::SignaturePolicyId:
					$this->type = new SignaturePolicyId();
					$this->type->loadInnerXml( $childNode );
					$this->signaturePolicyId = $this->type;
					break;

				case ElementNames::SignaturePolicyImplied:
					$this->type = new SignaturePolicyImplied();
					$this->type->loadInnerXml( $childNode );
					$this->signaturePolicyImplied = $this->type;
					break;
			}
		}

		return $this;
	}

	/** 
	 * Allows the structure to be validated. 
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$this->type->validateElement();
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

			if ( $this->type )
				$this->type->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
	
	/**
	 * Sets the type of child element to create the optional Implied element
	 *
	 * @return SignaturePolicyImplied
	 */
	public static function Implied()
	{
		$result = new SignaturePolicyIdentifier( new SignaturePolicyImplied() );
		return $result;
	}

	/**
	 * Sets the type of child element to create the optional Implied element
	 *
	 * @param SignaturePolicyId $signaturePolicyId
	 *
	 * @return SignaturePolicyIdentifier
	 */
	public static function ById( $signaturePolicyId )
	{
		$result = new SignaturePolicyIdentifier( $signaturePolicyId );
		return $result;
	}
}

