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
 * SigningCertificate is defined in https://www.w3.org/TR/XAdES/#Syntax_for_XAdES_The_SigningCertificate_element
 * with namespace http://uri.etsi.org/01903/v1.1.1#  It is referenced as obsolete in the later document
 * https://www.etsi.org/deliver/etsi_en/319100_319199/31913201/01.01.01_60/en_31913201v010101p.pdf
 * It has been replaced by SigningCertificateV2
 */
class SigningCertificate extends XmlCore
{
	private $template = '<SigningCertificate %s>' .
			'<Cert>' .
				'<CertDigest>' .
					'<DigestMethod Algorithm="%s" xmlns="http://www.w3.org/2000/09/xmldsig#"/>' .
					'<DigestValue xmlns="http://www.w3.org/2000/09/xmldsig#">%s</DigestValue>' .
				'</CertDigest>' .
				'<IssuerSerial>' .
					'<X509IssuerName xmlns="http://www.w3.org/2000/09/xmldsig#">%s</X509IssuerName>' .
					'<X509SerialNumber xmlns="http://www.w3.org/2000/09/xmldsig#">%s</X509SerialNumber>' . 
				'</IssuerSerial>' .
			'</Cert>' .
		'</SigningCertificate>';

	/**
	 * The algorithm used to generate the certificate digest
	 * @var string
	 */
	public $algorithm = "SHA256";

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return self::class;
	}

	/**
	 * The certificate used for signing
	 * @var Sequence
	 */
	private $certificate;

	/**
	 * Represents a SigningCertificate
	 * @param Sequence $certificate
	 */
	public function __construct( $certificate )
	{
		$this->certificate = $certificate;
	}

	/**
	 * Generates Xml nodes for the instance.  
	 *
	 * @param \DOMElement|\DOMDocument $parentNode
	 * @param string[] $namespaces
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// $xml = sprintf( $this->template, "xmlns=\"" . XAdES::NamespaceUrl . "\"", 'algorithm', 'digest', 'issuer', 'serial' );
		/** @var \DOMDocument $doc */
		$doc = $parentNode instanceof \DOMElement ? $parentNode->ownerDocument : $parentNode;
		// $fragment = $doc->createDocumentFragment();
		// $fragment->appendXML( $xml );
		// $parentNode->appendChild( $fragment );

		$signingCertificateNode = $doc->createElementNS( XAdES::NamespaceUrl2003, ElementNames::SigningCertificate );
		$parentNode->appendChild( $signingCertificateNode );

			$certNode = $doc->createElementNS( XAdES::NamespaceUrl2003, ElementNames::Cert );
			$signingCertificateNode->appendChild( $certNode );

				$certDigestNode = $doc->createElementNS( XAdES::NamespaceUrl2003, ElementNames::CertDigest );
				$certNode->appendChild( $certDigestNode );

					$digestMethodNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::DigestMethod );
					$certDigestNode->appendChild( $digestMethodNode );

						// Add the algorithm attribute
						$reflection = new \ReflectionClass('\lyquidity\xmldsig\XMLSecurityDSig');
						$algorithm = $reflection->getConstant( $this->algorithm );
						$algorithmAttr = $doc->createAttribute( AttributeNames::Algorithm );
						$algorithmAttr->value = $algorithm;
						$digestMethodNode->appendChild( $algorithmAttr );

					// Add the digest
					$digest = base64_encode( hash( $this->algorithm,  (new \lyquidity\Asn1\Der\Encoder())->encodeElement( $this->certificate ), true ) );
					$digestValueNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::DigestValue );
					$digestValueNode->textContent = $digest;
					$certDigestNode->appendChild( $digestValueNode );

				$issuerSerialNode = $doc->createElementNS( XAdES::NamespaceUrl2003, ElementNames::IssuerSerial );
				$issuerSerialNode = $doc->createElementNS( XAdES::NamespaceUrl2003, ElementNames::IssuerSerial );
				$certNode->appendChild( $issuerSerialNode );

				list( $certificate, $certificateInfo, $ocspResponderUrl, $issuerCertBytes, $issuerCertificate ) = array_values( Ocsp::getCertificate( $this->certificate ) );
				/** @var Sequence $certificate */
				/** @var CertificateInfo $certificateInfo */
				/** @var Sequence $issuerCertificate */

				// If the issuer certificate can be found the use its values
				if ( $issuerCertificate )
				{
					$serialNumber = $certificateInfo->extractSerialNumber( $issuerCertificate, true );
					$issuer = $certificateInfo->getDNString( $issuerCertificate, false );

					$issuerNameNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::X509IssuerName );
					$issuerNameNode->textContent = $issuer;
					$issuerSerialNode->appendChild( $issuerNameNode );

					$issuerSerialNumberNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::X509SerialNumber );
					$issuerSerialNumberNode->textContent = $serialNumber;
					$issuerSerialNode->appendChild( $issuerSerialNumberNode );
				}
				else
				{
					// If the issuer certificate cannot be found, it this an error?
					// For now just use the issuer details from the signing certificate
					$issuer = $certificateInfo->getDNString( $this->certificate, true );

					$issuerNameNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::X509IssuerName );
					$issuerNameNode->textContent = $issuer;
					$issuerSerialNode->appendChild( $issuerNameNode );
				}
		}
}