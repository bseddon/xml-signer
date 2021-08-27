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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#"
 * 
 *	<xsd:element name="CertRefs" type="CertIDListV2Type" minOccurs="0"/>
 *
 *	<xsd:complexType name="CertIDListV2Type">
 *		<xsd:sequence>
 *			<xsd:element name="Cert" type="CertIDTypeV2" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *
 *	<xsd:complexType name="CertIDTypeV2">
 *		<xsd:sequence>
 *			<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *			<xsd:element name="IssuerSerialV2" type="xsd:base64Binary" minOccurs="0"/>
 *		</xsd:sequence>
 *		<xsd:attribute name="URI" type="xsd:anyURI" use="optional"/>
 *	</xsd:complexType> 
 */

/**
 * Creates a node for &lt;CertRefsV2> which contains one or more &lt;Cert>
 */
class CertRefsV2 extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;Cert> to this instance
	 *
	 * @param CertV2|CertV2[] $certRefs (optional)
	 */
	public function __construct( $certRefs = null )
	{
		if ( ! $certRefs ) return;
		parent::__construct(
			self::createConstructorArray( $certRefs, CertV2::class ), 
			array( ElementNames::Cert => basename( CertV2::class ) )
		);
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertRefs;
	}

	/**
	 * Validate all cert refs are CertRef instances
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$certRefs = $this->getPropertiesOfClass( CertV2::class );
		if ( ! $certRefs )
			throw new \Exception("There must be one or more <Cert> instances if a <CertRefs> is used");

		if ( count( $certRefs ) != count( $this->properties  ) )
			throw new \Exception("All <CertRefs> children must of type Cert");
	}
}
