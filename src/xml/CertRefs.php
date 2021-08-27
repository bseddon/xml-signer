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
 *	<!-- targetNamespace="http://uri.etsi.org/01903/v1.3.2#"
 * 
 *	<xsd:element name="CertRefs" type="CertIDListType" minOccurs="0"/>
 * 
 *	<xsd:complexType name="CertIDListType">
 *		<xsd:sequence>
 *			<xsd:element name="Cert" type="CertIDType" maxOccurs="unbounded"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;CertRefs> which contains one or more &lt;Cert>
 */
class CertRefs extends PropertiesCollection
{
	/**
	 * Assign one of more &lt;Cert> to this instance
	 *
	 * @param Cert|Cert[] $certRefs (optional)
	 */
	public function __construct( $certRefs = null )
	{
		parent::__construct( self::createConstructorArray( $certRefs, Cert::class ) );
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

		$certRefs = $this->getPropertiesOfClass( Cert::class );
		if ( ! $certRefs )
			throw new \Exception("There must be one or more <Cert> instances if a <CertRefs> is used");

		if ( count( $certRefs ) != count( $this->properties  ) )
			throw new \Exception("All <CertRefs> children must of type Cert");
	}

}
