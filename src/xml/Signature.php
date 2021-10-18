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
 *	<xsd:element name="Signature" type="ds:SignatureType"/>
 *
 *	<xsd:complexType name="SignatureType">
 *		<xsd:sequence> 
 *			<xsd:element ref="ds:SignedInfo"/> 
 *			<xsd:element ref="ds:SignatureValue"/> 
 *			<xsd:element ref="ds:KeyInfo" minOccurs="0"/> 
 *			<xsd:element ref="ds:Object" minOccurs="0" maxOccurs="unbounded"/> 
 *		</xsd:sequence>  
 *		<xsd:attribute name="Id" type="ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;Signature> which is a container for a collection of elements
 */
class Signature extends XmlCore
{
	/**
	 * @var SignedInfo
	 */
	public $signedInfo = null;

	/**
	 * @var SignatureValue
	 */
	public $signatureValue = null;

	/**
	 * @var KeyInfo
	 */
	public $keyInfo = null;

	/**
	 * @var Obj
	 */
	public $object = null;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param SignedInfo $signedInfo
	 * @param SignatureValue $signatureValue
	 * @param KeyInfo $keyInfo
	 * @param Obj $object
	 * @param string $id
	 */
	public function __construct( 
		$signedInfo = null, 
		$signatureValue = null, 
		$keyInfo = null, 
		$object = null, 
		$id = null
	)
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;

		$this->signedInfo = self::createConstructor( $signedInfo, SignedInfo::class );
		$this->signatureValue = self::createConstructor( $signatureValue, SignatureValue::class );
		if ( is_string( $keyInfo ) )
			$this->keyInfo = new KeyInfo( $keyInfo );
		else
			$this->keyInfo = self::createConstructor( $keyInfo, KeyInfo::class );
		$this->object = self::createConstructor( $object, Obj::class );

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Signature;
	}

	/**
	 * Create &lt;Signature> and any descendent elements
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

		// Now create a node for all the sub-nodes where they exist
		foreach ( $this->elementsToArray() as $tag => $element )
		{
			if ( ! $element ) continue;
			$element->generateXml( $newElement, $attributes );
		}
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Signature
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
				case ElementNames::SignedInfo:
					$this->signedInfo = new SignedInfo();
					$this->signedInfo->loadInnerXml( $childNode );
					break;

				case ElementNames::SignatureValue:
					$this->signatureValue = new SignatureValue();
					$this->signatureValue->loadInnerXml( $childNode );
					break;

				case ElementNames::KeyInfo:
					$this->keyInfo = new KeyInfo();
					$this->keyInfo->loadInnerXml( $childNode );
					break;

				case ElementNames::Object:
					$this->object = new Obj();
					$this->object->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/**
	 * Return the four elements as an iterable array
	 * @return XmlCore[]
	 */
	private function elementsToArray()
	{
		return array(	
			'SignedInfo' => $this->signedInfo, 
			'SignatureValue' => $this->signatureValue, 
			'KeyInfo' => $this->keyInfo, 
			'Object' => $this->object 
		);
	}

	/**
	 * Allow the properties to validate themselves
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		foreach( $this->elementsToArray() as $tag  => $element )
		{
			if ( $element )
				$element->validateElement();
			else if ( $tag != 'Object' && $tag != 'SignatureValue' )
				// An <Object> element is optional and the Xml maybe being created to generate the <SignatureValue>  
				throw new \Exception("<$tag> is missing from the signature");
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

			foreach( $this->elementsToArray() as $tag  => $element )
			{
				if ( $element )
				$element->traverse( $callback, $depthFirst  );
			}
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
