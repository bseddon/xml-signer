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
 *
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#" -->
 *	
 *	<xsd:element name="CertificateValues" type="CertificateValuesType"/>
 *	
 *	<xsd:complexType name="CertificateValuesType">
 *		<xsd:choice minOccurs="0" maxOccurs="unbounded">
 *			<xsd:element name="EncapsulatedX509Certificate" type="EncapsulatedPKIDataType"/>
 *			<xsd:element name="OtherCertificate" type="AnyType"/>
 *		</xsd:choice>
 *		<xsd:attribute name="Id" type="xsd:ID" use="optional"/>
 *	/xsd:complexType>
 */

/**
 * Creates a node for &lt;CertificateValues>
 */
class CertificateValues extends PropertiesCollection implements UnsignedSignatureProperty
{
	/**
	 * Create an instance of &lt;CertificateValues> and pass in instances of &lt;EncapsulatedX509Certificate>, &lt;OtherCertificate>
	 * @param EncapsulatedX509Certificate|EncapsulatedX509Certificate[] $encapsulatedX509Certificates
	 * @param OtherCertificate|OtherCertificate[] $otherCertificates
	 */
	public function __construct( $encapsulatedX509Certificates = null, $otherCertificates = null, $id = null )
	{
		parent::__construct( array_merge(
			self::createConstructorArray( $encapsulatedX509Certificates, EncapsulatedX509Certificate::class ) ?? array(),
			self::createConstructorArray( $otherCertificates, OtherCertificate::class ) ?? array()
		) );

		$this->id = $id;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::CertificateValues;
	}

}
