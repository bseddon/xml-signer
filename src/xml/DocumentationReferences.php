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
 *	<xsd:element name="DocumentationReferences" type="DocumentationReferencesType" minOccurs="0"/>
 *
 *	<xsd:complexType name="DocumentationReferencesType">
 *		<xsd:sequence maxOccurs="unbounded">
 *			<xsd:element name="DocumentationReference" type="xsd:anyURI"/>
 *		</xsd:sequence>
 *	</xsd:complexType>
 */

/**
 * Creates a node for &lt;DocumentationReferences>
 */
class DocumentationReferences extends PropertiesCollection
{
	/**
	 * Assign one of more references to this instance
	 * @param DocumentationReference|DocumentationReference[]|string $references
	 */
	public function __construct( $references = null )
	{
		parent::__construct( self::createConstructorArray( $references, DocumentationReference::class ) );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::DocumentationReferences;
	}

	/**
	 * Vaildate &lt;DocumentationReferences> and any descendent elements 
	 * @return void
	 */
	public function validateElement()
	{
		// Create a node for this element
		parent::validateElement();

		$documentationReferences = $this->getPropertiesOfClass( DocumentationReference::class );
		if ( ! $documentationReferences )
			throw new \Exception("There must be one or more Documentation Reference if <DocumentationReferences> is used");

		if ( count( $documentationReferences ) != count( $this->properties  ) )
			throw new \Exception("All <DocumentationReferences> children must of type DocumentationReference");
	}
}
