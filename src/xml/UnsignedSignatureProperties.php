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
 *	<xsd:element name="UnsignedSignatureProperties" type="UnsignedSignaturePropertiesType" />
 *
 *	<xsd:complexType name="UnsignedSignaturePropertiesType">
 *		<xsd:choice maxOccurs="unbounded">
 *			<xsd:element ref="CounterSignature" />
 *			<xsd:element ref="SignatureTimeStamp" />
 *			<xsd:element ref="CompleteCertificateRefs"/>
 *			<xsd:element ref="CompleteRevocationRefs"/>
 *			<xsd:element ref="AttributeCertificateRefs"/>
 *			<xsd:element ref="AttributeRevocationRefs" />
 *			<xsd:element ref="SigAndRefsTimeStamp" />
 *			<xsd:element ref="RefsOnlyTimeStamp" />
 *			<xsd:element ref="CertificateValues" />
 *			<xsd:element ref="RevocationValues"/>
 *			<xsd:element ref="AttrAuthoritiesCertValues" />
 *			<xsd:element ref="AttributeRevocationValues"/>
 *			<xsd:element ref="ArchiveTimeStamp" />
 *			<xsd:any namespace="##other"/>
 *		</xsd:choice>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;UnsignedSignatureProperties> which is a container for a collection of elements
 */
class UnsignedSignatureProperties extends PropertiesCollection
{
	/**
	 * Allow a user to pass in the objects for which elements are to be created
	 * @param XmlCore|XmlCore[] $unsignedSignatureProperties
	 * @param string $id
	 */
	public function __construct( 
		$unsignedSignatureProperties = null, 
		$id = null
	)
	{
		parent::__construct( $unsignedSignatureProperties );

		// Check any instances are valid
		$this->checkProperties();

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::UnsignedSignatureProperties;
	}
	
	/**
	 * Check all properties are valid property types
	 * @return bool
	 * @throws \Exception
	 */
	private function checkProperties()
	{
		if ( ! $this->properties ) return false;

		foreach( $this->properties as $unsignedSignatureProperty )
		{
			if ( $unsignedSignatureProperty instanceof UnsignedSignatureProperty ) continue;

			$basename = get_class( $unsignedSignatureProperty );
			throw new \Exception("All unsigned signature properties must be valid types.  Found <$basename>");
		}

		return true;
	}

	/**
	 * Allow the properties to validate themselves
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( ! $this->checkProperties() ) return;
	}
}
