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
 *	<!-- targetNamespace="http://www.w3.org/2000/09/xmldsig#" -->
 *
 *	<xsd:element name="SignedInfo" type="ds:SignedInfoType"/>
 *
 *	<xsd:complexType name="SignedInfoType">
 *		<xsd:sequence>
 *			<element ref="ds:CanonicalizationMethod"/>
 *			<element ref="ds:SignatureMethod"/> 
 *			<element ref="ds:Reference" maxOccurs="unbounded"/> 
 *		</xsd:sequence>  
 *		<xsd:attribute name="Id" type="ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;SignedInfo> which is a container for a collection of elements
 */
class SignedInfo extends XmlCore
{
	/**
	 * @var CanonicalizationMethod
	 */
	public $canonicalizationMethod = null;

	/**
	 * @var SignatureMethod
	 */
	public $signatureMethod = null;

	/**
	 * @var Reference[]
	 */
	public $references = null;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param CanonicalizationMethod $canonicalizationMethod
	 * @param SignatureMethod $signatureMethod
	 * @param Reference[] $references
	 * @param string $id
	 */
	public function __construct( 
		$canonicalizationMethod = null, 
		$signatureMethod = null, 
		$references = null,
		$id = null
	)
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;

		$this->canonicalizationMethod = self::createConstructor( $canonicalizationMethod, CanonicalizationMethod::class );
		$this->signatureMethod = self::createConstructor( $signatureMethod, SignatureMethod::class );
		$this->references = self::createConstructorArray( $references, Reference::class );

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignedInfo;
	}

	/**
	 * Create &lt;SignedInfo> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode );

		// Now create a node for all the sub-nodes where they exist
		$this->canonicalizationMethod->generateXml( $newElement );
		$this->signatureMethod->generateXml( $newElement );
		foreach( $this->references as $reference )
			$reference->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return SignedInfo
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
				case ElementNames::CanonicalizationMethod:
					$canonicalizationMethod = new CanonicalizationMethod();
					$canonicalizationMethod->loadInnerXml( $childNode );
					$this->canonicalizationMethod = $canonicalizationMethod;
					break;

				case ElementNames::SignatureMethod:
					$signatureMethod = new SignatureMethod();
					$signatureMethod->loadInnerXml( $childNode );
					$this->signatureMethod = $signatureMethod;
					break;

				case ElementNames::Reference:
					$reference = new Reference();
					$reference->loadInnerXml( $childNode );
					$this->references[] = $reference;
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
		if ( $this->canonicalizationMethod )
			$this->canonicalizationMethod->validateElement();

		if ( $this->canonicalizationMethod )
			$this->signatureMethod->validateElement();

		foreach( $this->references AS $reference )
			if ( $reference )
				$reference->validateElement();
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

			if ( $this->canonicalizationMethod )
				$this->canonicalizationMethod->traverse( $callback, $depthFirst );
	
			if ( $this->canonicalizationMethod )
				$this->signatureMethod->traverse( $callback, $depthFirst );
	
			foreach( $this->references AS $reference )
				if ( $reference )
					$reference->traverse( $callback, $depthFirst );
			
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
