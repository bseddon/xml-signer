<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\Asn1\Element\Sequence;
use lyquidity\OCSP\Ocsp;
use lyquidity\xmldsig\XAdES;
use lyquidity\xmldsig\XMLSecurityDSig;

/**
 * <!-- targetNamespace="http://uri.etsi.org/01903/v1.1.1#" -->
 *
 *	<xsd:element name="SigningCertificate" type="CertIDListType"/>
 *
 *	<xsd:complexType name="CertIDListType">
 *		<xsd:sequence>
 *			<xsd:element name="Cert" type="CertIDType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *	
 *	<xsd:complexType name="CertIDType">
 *		<xsd:sequence>
 *			<xsd:element name="CertDigest" type="DigestAlgAndValueType"/>
 *			<xsd:element name="IssuerSerial" type="ds:X509IssuerSerialType"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 *	
 *	<xsd:complexType name="DigestAlgAndValueType">
 *		<xsd:sequence>
 *			<xsd:element name="DigestMethod" type="ds:DigestMethodType"/>
 *			<xsd:element name="DigestValue" type="ds:DigestValueType"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * SigningCertificate is defined in https://www.w3.org/TR/XAdES/#Syntax_for_XAdES_The_SigningCertificate_element
 * with namespace http://uri.etsi.org/01903/v1.1.1#  It is referenced as obsolete in the later document
 * https://www.etsi.org/deliver/etsi_en/319100_319199/31913201/01.01.01_60/en_31913201v010101p.pdf
 * It has been replaced by SigningCertificateV2
 */
class SigningCertificate extends XmlCore
{
	const defaultAlgorithm = "sha256";

	/**
	 * The &lt;Cert> to be created
	 * @var Cert
	 */
	public $cert;

	/**
	 * The algorithm used to generate the certificate digest
	 * @var string
	 */
	public $algorithm = self::defaultAlgorithm;

	/**
	 * Create a &lt;SigningCertificate> instance
	 * @param Cert $cert
	 * @param string $algorithm
	 */
	public function __construct( $cert = null, $algorithm = self::defaultAlgorithm )
	{
		$this->cert = $cert;
		$this->algorithm = $algorithm;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigningCertificate;
	}

	/**
	 * Create a &lt;SigningCertificate> from a certificate
	 *
	 * @param Sequence $certificate
	 * @param string $algorithm
	 * @return SigningCertificate
	 */
	public static function fromCertificate( $certificate, $algorithm = self::defaultAlgorithm )
	{
		// Add the digest
		$digest = base64_encode( hash( $algorithm,  (new \lyquidity\Asn1\Der\Encoder())->encodeElement( $certificate ), true ) );

		list( $certificate, $certificateInfo, $ocspResponderUrl, $issuerCertBytes, $issuerCertificate ) = array_values( Ocsp::getCertificate( $certificate ) );
		/** @var Sequence $certificate */
		/** @var CertificateInfo $certificateInfo */
		/** @var Sequence $issuerCertificate */

		// If the issuer certificate can be found the use its values
		if ( $issuerCertificate )
		{
			$serialNumber = $certificateInfo->extractSerialNumber( $issuerCertificate, true );
			$issuer = $certificateInfo->getDNString( $issuerCertificate, false );
		}
		else
		{
			// If the issuer certificate cannot be found, it this an error?
			// For now just use the issuer details from the signing certificate
			$issuer = $certificateInfo->getDNString( $certificate, true );
			// TODO: This is wrong but use the cert serial number for now
			$serialNumber = $certificateInfo->extractSerialNumber( $certificate, true );
		}

		// Add the algorithm attribute
		$reflection = new \ReflectionClass('\lyquidity\xmldsig\XMLSecurityDSig');
		$algorithm = $reflection->getConstant( strtoupper( $algorithm ) );
		
		return new SigningCertificate(
			new Cert(
				new CertDigest(
					new DigestMethod( $algorithm ),
					new DigestValue( $digest )
				),
				new IssuerSerial(
					new X509IssuerName( $issuer ),
					$serialNumber ? new X509SerialNumber( $serialNumber ) : null
				)
			),
			$algorithm
		);
	}

	/**
	 * Read the Xml node and create appropriate classes
	 * By the time this function has been called, the class has been 
	 * instantiated so the work to do is to read the contents and create 
	 * other classes (if appropriate)
	 * @param \DOMElement $node
	 * @return SigningCertificate
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );
		// There are no attributes for this element

		// Look for elements with the tag &lt;X509AttributeCertificate> or  &lt;OtherAttributeCertificate>
		foreach( $node->childNodes as $childNode )
		{
			/** @var \DOMElement $node */
			if ( $childNode->nodeType != XML_ELEMENT_NODE ) continue;

			switch( $childNode->localName )
			{
				case ElementNames::Cert:
					$this->cert = new Cert();
					$this->cert->loadInnerXml( $childNode );
					break;
			}
		}

		return $this;
	}

	/**
	 * Create &lt;SigningCertificate> and any descendent elements
	 *
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @param \DOMElement $insertAfter
	 * @return \DOMElement
	 */
	public function generateXml( $parentNode, $attributes = array(), $insertAfter = null )
	{
		$newElement = parent::generateXml( $parentNode, $attributes, $insertAfter );
		$this->cert->generateXml( $newElement );
	}

	/** 
	 * Validate &lt;Cert>.
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		$this->cert->validateElement();
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

			if ( $this->cert )
				$this->cert->traverse( $callback, $depthFirst );
		
			if ( $depthFirst )
				parent::traverse( $callback, $depthFirst );
		}
		return $this;
	}
}