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
 *	<element name="Reference" type="ds:ReferenceType"/>
 *	
 *	<complexType name="ReferenceType">
 *		<sequence> 
 *			<element ref="ds:Transforms" minOccurs="0"/> 
 *			<element ref="ds:DigestMethod"/> 
 *			<element ref="ds:DigestValue"/> 
 *		</sequence>
 *		<attribute name="Id" type="ID" use="optional"/> 
 *		<attribute name="URI" type="anyURI" use="optional"/> 
 *		<attribute name="Type" type="anyURI" use="optional"/> 
 *	</complexType>
 */

/**
 * Creates a node for &lt;Reference> which is a container for a collection of elements
 */
class Reference extends XmlCore
{
	/**
	 * @var Transforms
	 */
	public $transforms = null;

	/**
	 * @var DigestMethod
	 */
	public $digestMethod = null;

	/**
	 * @var DigestValue
	 */
	public $digestvalue = null;

	/**
	 * Identifies a data object using a URI-Reference
	 * @var string
	 */
	public $uri;

	/**
	 * Contains information about the type of object being signed. This is represented as a URI. 
	 * For example:
	 *
	 *	Type="http://www.w3.org/2000/09/xmldsig#Object"
	 * 	Type="http://www.w3.org/2000/09/xmldsig#Manifest"
	 *
	 * The Type attribute applies to the item being pointed at, not its contents
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * Would be nice to used named parameters here but that ties the code v8.0
	 *
	 * @param Transforms $transforms
	 * @param DigestMethod $digestMethod
	 * @param DigestValue $digestvalue
	 * @param string $id An optional @Id
	 * @param string $uri An optional reference to the nodes being processed
	 * @param string $type An optional uri to indicate special processing such as XAdES
	 */
	public function __construct( 
		$transforms = null, 
		$digestMethod = null, 
		$digestvalue = null,
		$id = null,
		$uri = null,
		$type = null
	)
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;

		if ( $transforms instanceof Transform )
			$this->transforms = new Transforms( $transforms );
		else
			$this->transforms = self::createConstructor( $transforms, Transforms::class );
		$this->digestMethod = self::createConstructor( $digestMethod, DigestMethod::class );
		$this->digestvalue = self::createConstructor( $digestvalue, DigestValue::class );

		$this->id = $id;
		$this->uri = $uri;
		$this->type = $type;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Reference;
	}

	/**
	 * Create &lt;Reference> and any descendent elements
	 * 
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		// Create a node for this element
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::URI => $this->uri, AttributeNames::Type => $this->type ), $insertAfter );

		// Now create a node for all the sub-nodes where they exist
		$this->transforms->generateXml( $newElement );
		$this->digestMethod->generateXml( $newElement );
		$this->digestvalue->generateXml( $newElement );
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return Reference
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::URI );
		if ( $attr )
		{
			$this->uri = $attr->value;
		}

		$attr = $node->getAttributeNode( AttributeNames::Type );
		if ( $attr )
		{
			$this->type = $attr->value;
		}

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Transforms:
					$transforms = new Transforms();
					$transforms->loadInnerXml( $childNode );
					$this->transforms = $transforms;
					break;

				case ElementNames::DigestMethod:
					$digestMethod = new DigestMethod();
					$digestMethod->loadInnerXml( $childNode );
					$this->digestMethod = $digestMethod;
					break;

				case ElementNames::DigestValue:
					$digestvalue = new DigestValue();
					$digestvalue->loadInnerXml( $childNode );
					$this->digestvalue = $digestvalue;
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
		if ( $this->transforms )
			$this->transforms->validateElement();

		if ( $this->digestMethod )
			$this->digestMethod->validateElement();

		if ( $this->digestvalue )
			$this->digestvalue->validateElement();
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

			if ( $this->transforms )
				$this->transforms->traverse( $callback, $depthFirst );

			if ( $this->digestMethod )
				$this->digestMethod->traverse( $callback, $depthFirst  );

			if ( $this->digestvalue )
				$this->digestvalue->traverse( $callback, $depthFirst  );

			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
