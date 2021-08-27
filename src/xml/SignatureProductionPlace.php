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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.1.1#" -->
 *	<xsd:element name="SignatureProductionPlace" type="SignatureProductionPlaceType"/>
 *	<xsd:complexType name="SignatureProductionPlaceType">
 *		<xsd:sequence>
 *			<xsd:element name="City" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="StateOrProvince" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="PostalCode" type="xsd:string" minOccurs="0"/>
 *			<xsd:element name="CountryName" type="xsd:string" minOccurs="0"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Reprents and instance of &lt;SignatureProductionPlace>
 */
class SignatureProductionPlace extends XmlCore
{
	/**
	 * Implements &lt;City>
	 * @var City
	 */
	public $city = null;

	/**
	 * Implements &lt;StateOrProvince>
	 * @var StateOrProvince
	 */
	public $stateOrProvince = null;

	/**
	 * Implements &lt;PostalCode>
	 * @var PostalCode
	 */
	public $postalCode = null;

	/**
	 * Implements &lt;CountryName>
	 * @var CountryName
	 */
	public $countryName = null;

	/**
	 * Create a SignatureProductionPlace instance
	 * @param City|string $city 
	 * @param StateOrProvince|string $stateOrProvince 
	 * @param PostalCode|string $postalCode 
	 * @param CountryName|string $countryName 
	 * @return void 
	 */
	public function __construct( $city = null, $stateOrProvince = null, $postalCode = null, $countryName  = null )
	{
		$this->city = is_string( $city ) ? new City( $city ) : $city;
		$this->stateOrProvince = is_string( $stateOrProvince ) ? new StateOrProvince( $stateOrProvince ) : $stateOrProvince;
		$this->postalCode = is_string( $postalCode ) ? new PostalCode( $postalCode ) : $postalCode;
		$this->countryName  = is_string( $countryName ) ? new CountryName( $countryName ) : $countryName;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SignatureProductionPlace;
	}

	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode );

		if ( $this->city )
			$this->city->generateXml( $newElement );

		if ( $this->stateOrProvince )
			$this->stateOrProvince->generateXml( $newElement );

		if ( $this->postalCode )
			$this->postalCode->generateXml( $newElement );

		if ( $this->countryName )
			$this->countryName->generateXml( $newElement );
	}

	/** 
	 * Create a new Xml representation for $node
	 * @param \DOMElement $node
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		foreach ( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $childNode */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch ( $childNode->localName )
			{
				case ElementNames::City:
					$this->city = new City();
					$this->city->loadInnerXml( $childNode );
					break;

				case ElementNames::StateOrProvince:
					$this->stateOrProvince = new StateOrProvince();
					$this->stateOrProvince->loadInnerXml( $childNode );
					break;

				case ElementNames::PostalCode:
					$this->postalCode = new PostalCode();
					$this->postalCode->loadInnerXml( $childNode );
					break;

				case ElementNames::CountryName:
					$this->countryName = new CountryName();
					$this->countryName->loadInnerXml( $childNode );
					break;
			}
		}
	}

	/**
	 * Validate this element and all tjhe child elements that are used
	 *
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->city )
			$this->city->validateElement( );

		if ( $this->stateOrProvince )
			$this->stateOrProvince->validateElement();

		if ( $this->postalCode )
			$this->postalCode->validateElement();

		if ( $this->countryName )
			$this->countryName->validateElement();
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

			if ( $this->city )
				$this->city->traverse( $callback, $depthFirst );

			if ( $this->stateOrProvince )
				$this->stateOrProvince->traverse( $callback, $depthFirst  );

			if ( $this->postalCode )
				$this->postalCode->traverse( $callback, $depthFirst  );

			if ( $this->countryName )
				$this->countryName->traverse( $callback, $depthFirst  );

			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}
