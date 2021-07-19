<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

use lyquidity\Asn1\Der\Encoder;
use lyquidity\Asn1\Element\NullElement;
use lyquidity\Asn1\Element\ObjectIdentifier;
use lyquidity\Asn1\Element\OctetString;
use \lyquidity\Asn1\Element\Sequence;
use lyquidity\OCSP\CertificateInfo;
use lyquidity\OCSP\Ocsp;
use lyquidity\OID\OID;
use lyquidity\xmldsig\XAdES;
use lyquidity\xmldsig\XMLSecurityDSig;

/**
 * SigningCertificateV2 is referenced in the updated ETSI document
 * https://www.etsi.org/deliver/etsi_en/319100_319199/31913201/01.01.01_60/en_31913201v010101p.pdf
 * with namespace http://uri.etsi.org/01903/v1.3.2#
 * This class obsoletes SigningCertificate
 */
class SigningCertificateV2 extends XmlCore
{
	private $template = '<SigningCertificateV2 %s>' .
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
		'</SigningCertificate>V2';

	/**
	 * The algorithm used to generate the certificate digest and the Issuer digest (RFC 5035)
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
	 * Represents a SigningCertificateV2
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

		$signingCertificateNode = $doc->createElementNS( XAdES::NamespaceUrl2016, ElementNames::SigningCertificate );
		$parentNode->appendChild( $signingCertificateNode );

			$certNode = $doc->createElementNS( XAdES::NamespaceUrl2016, ElementNames::Cert );
			$signingCertificateNode->appendChild( $certNode );

				$certDigestNode = $doc->createElementNS( XAdES::NamespaceUrl2016, ElementNames::CertDigest );
				$certNode->appendChild( $certDigestNode );

					$digestMethodNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::DigestMethod );
					$certDigestNode->appendChild( $digestMethodNode );

						// Add the algorithm attribute
						$reflection = new \ReflectionClass('\lyquidity\xmldsig\XMLSecurityDSig');
						$algorithm = $reflection->getConstant( $this->algorithm );
						$algorithmAttr = $doc->createAttribute('Algorithm');
						$algorithmAttr->value = $algorithm;
						$digestMethodNode->appendChild( $algorithmAttr );

					// Add the digest
					$digest = hash( $this->algorithm,  (new \lyquidity\Asn1\Der\Encoder())->encodeElement( $this->certificate ), true );
					$digestValueNode = $doc->createElementNS( XMLSecurityDSig::XMLDSIGNS, ElementNames::DigestValue );
					$digestValueNode->textContent = base64_encode( $digest );
					$certDigestNode->appendChild( $digestValueNode );

					list( $certificate, $certificateInfo, $ocspResponderUrl, $issuerCertBytes, $issuerCertificate ) = array_values( Ocsp::getCertificate( $this->certificate ) );
					/** @var Sequence $certificate */
					/** @var CertificateInfo $certificateInfo */
					/** @var Sequence $issuerCertificate */

					if ( $issuerCertificate )
					{
						$issuerSerialV2Node = $doc->createElementNS( XAdES::NamespaceUrl2016, ElementNames::IssuerSerialV2 );
						$certNode->appendChild( $issuerSerialV2Node );

						$issuerSerialV2Node->textContent = 'base64 ESSCertIDV2';

						/*
						* From RFC 5035
						* 
						*	ESSCertIDv2 ::= SEQUENCE {
						* 		hashAlgorithm           AlgorithmIdentifier DEFAULT {algorithm id-sha256},
						* 		certHash                Hash,
						* 		issuerSerial            IssuerSerial OPTIONAL
						*	}
						*
						*	Hash ::= OCTET STRING  
						*
						*	IssuerSerial ::= SEQUENCE {
						* 		issuer                   GeneralNames,
						* 		serialNumber             CertificateSerialNumber
						*	}
						*
						* The fields of ESSCertIDv2 are defined as follows:
						*
						* hashAlgorithm
						* 	contains the identifier of the algorithm used in computing certHash.
						* 
						* certHash
						* 	is computed over the entire DER-encoded certificate (including the
						* 	signature) using the SHA-1 algorithm.
						* 
						* issuerSerial
						* 	holds the identification of the certificate.  The issuerSerial
						* 	would normally be present unless the value can be inferred from
						* 	other information (e.g., the sid field of the SignerInfo object).
						* 
						* The fields of IssuerSerial are defined as follows:
						* 
						* issuer
						* 	contains the issuer name of the certificate.  For non-attribute
						* 	certificates, the issuer MUST contain only the issuer name from
						* 	the certificate encoded in the directoryName choice of
						* 	GeneralNames.  For attribute certificates, the issuer MUST contain
						* 	the issuer name field from the attribute certificate.
						*
						* serialNumber
						*	holds the serial number that uniquely identifies the certificate
						*	for the issuer.
						*/

						$isserSerial = Sequence::create(
							array(
								$certificateInfo->extractIssuer( $issuerCertificate ),
								$certificateInfo->extractSerialNumberAsInteger( $issuerCertificate )
							)
						);

						$essCertIDV2 = Sequence::create(
							array(
								Sequence::create(
									array(
										ObjectIdentifier::create( OID::$digests[ strtolower( $this->algorithm ) ] ),
										NullElement::create()
									)
								),
								OctetString::create( $digest ),
								$isserSerial
							)
						);

						$isserSerialDER = (new Encoder())->encodeElement( $isserSerial );

						$issuerSerialV2Node->textContent = base64_encode( $isserSerialDER );
					}
	}
}