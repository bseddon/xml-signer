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
 * <!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 * 
 *	<xsd:element name="SignaturePolicyIdentifier" type="SignaturePolicyIdentifierType"/>
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
 * Begins the creation of &lt;SignaturePolicyId> 
 */
class SignaturePolicyId extends SignaturePolicyBase
{
	/**
	 * Set a &lt;SigPolicyId>
	 * @var SigPolicyId
	 */
	public $sigPolicyId = null;

	/**
	 * Set a &lt;Transforms>
	 * @var Transforms
	 */
	public $transforms = null;

	/**
	 * Set as &lt;SigPolicyHash>
	 * @var SigPolicyHash
	 */
	public $sigPolicyHash = null;

	/**
	 * Set a &lt;SigPolicyQualifiers>
	 *
	 * @var SigPolicyQualifiers
	 */
	public $sigPolicyQualifiers = null;

	/**
	 * Creat a 
	 *
	 * @param SigPolicyId $sigPolicyId
	 * @param Transforms $transforms
	 * @param SigPolicyHash $sigPolicyHash
	 * @param SigPolicyQualifiers $sigPolicyQualifiers
	 */
	public function __construct(
		$sigPolicyId = null,
		$transforms = null,
		$sigPolicyHash = null,
		$sigPolicyQualifiers  = null
	)
	{
		$this->sigPolicyId = self::createConstructor( $sigPolicyId, SigPolicyId::class );
		$this->transforms = self::createConstructor( $transforms, Transforms::class );
		if ( is_string( $sigPolicyHash ) )
		{
			// If only a string is passed, assume its the digest value and the method is sha256
			$this->sigPolicyHash = new SigPolicyHash( XMLSecurityDSig::SHA256, $sigPolicyHash );
		}
		else
			$this->sigPolicyHash = self::createConstructor( $sigPolicyHash, SigPolicyHash::class );
		$this->sigPolicyQualifiers = self::createConstructor( $sigPolicyQualifiers, SigPolicyQualifiers::class );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignaturePolicyId;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 * @param \DOMElement $parentNode
	 * @param string[] $namespaces
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode );

		if ( $this->sigPolicyId )
			$this->sigPolicyId->generateXml( $newElement );

		if ( $this->transforms )
			$this->transforms->generateXml( $newElement );

		if ( $this->sigPolicyHash )
			$this->sigPolicyHash->generateXml( $newElement );

		if ( $this->sigPolicyQualifiers )
			$this->sigPolicyQualifiers->generateXml( $newElement );
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
				case ElementNames::SigPolicyId:
					$this->sigPolicyId = new SigPolicyId();
					$this->sigPolicyId->loadInnerXml( $childNode );
					break;

				case ElementNames::Transforms:
					$this->transforms = new Transforms();
					$this->transforms->loadInnerXml( $childNode );
					break;

				case ElementNames::SigPolicyHash:
					$this->sigPolicyHash = new SigPolicyHash();
					$this->sigPolicyHash->loadInnerXml( $childNode );
					break;

				case ElementNames::SigPolicyQualifiers:
					$this->sigPolicyQualifiers = new SigPolicyQualifiers();
					$this->sigPolicyQualifiers->loadInnerXml( $childNode );
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
		if ( $this->sigPolicyId )
			$this->sigPolicyId->validateElement();

		if ( $this->transforms )
			$this->transforms->validateElement();

		if ( $this->sigPolicyHash )
			$this->sigPolicyHash->validateElement();

		if ( $this->sigPolicyQualifiers )
			$this->sigPolicyQualifiers->validateElement();
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

			if ( $this->sigPolicyId )
				$this->sigPolicyId->traverse( $callback, $depthFirst );

			if ( $this->transforms )
				$this->transforms->traverse( $callback, $depthFirst  );

			if ( $this->sigPolicyHash )
				$this->sigPolicyHash->traverse( $callback, $depthFirst  );

			if ( $this->sigPolicyQualifiers )
				$this->sigPolicyQualifiers->traverse( $callback, $depthFirst  );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
