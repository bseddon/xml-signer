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
 *	<xsd:element name="Identifier" type="IdentifierType"/>
 *
 *	<xsd:complexType name="IdentifierType">
 *		<xsd:simpleContent>
 *			<xsd:extension base="xsd:anyURI">
 *				<xsd:attribute name="Qualifier" type="QualifierType" use="optional"/>
 *			</xsd:extension>
 *		</xsd:simpleContent>
 *	</xsd:complexType>
 *
 *	<xsd:simpleType name="QualifierType">
 *		<xsd:restriction base="xsd:string">
 *			<xsd:enumeration value="OIDAsURI"/>
 *			<xsd:enumeration value="OIDAsURN"/>
 *		</xsd:restriction>
 *	</xsd:simpleType>
 */

/**
 * Creates a node for &lt;Identifier>
 */
class Identifier extends TextBase
{
	/**
	 * This will become an attrubute @Qualifier with two legal values:
	 * 	OIDAsURI or OIDAsURN
	 *
	 * @var QualifierValues
	 */
	public $qualifier = null;

	/**
	 * Create an &lt;Identifier> instance
	 *
	 * @param string $identifier
	 * @param QualifierValues $qualifier
	 */
	public function __construct( $identifier = null, $qualifier = null )
	{
		// $this->qualifier = QualifierValues::$OIDAsURN;

		parent::__construct( $identifier );
		if ( $qualifier )
		{
			$this->validateElement();
			$this->qualifier = $qualifier;
		}
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::Identifier;
	}

	/**
	 * Create &lt;Identifier> and any descendent elements 
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		// Create a node for this element
		return parent::generateXml( $parentNode, array( AttributeNames::Qualifier => ( $this->qualifier ? $this->qualifier->getName() : null ) ) );
	}

	/**
	 * Create objects from an Xml node
	 *
	 * @param \DOMElement $node
	 * @return void
	 */
	public function loadInnerXml( $node )
	{
		$newElement = parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Qualifier );
		if ( ! $attr ) return;

		$this->qualifier = QualifierValues::fromName( $attr->value );
	}

	/**
	 * Validate @Qualifier and make sure there is an identifier
	 * @return void
	 * @throws \Exception
	 */
	public function validateElement()
	{
		parent::validateElement();

		if ( $this->qualifier )
		if ( $this->qualifier->getName() != QualifierValues::$OIDAsURI->getName() && $this->qualifier->getName() != QualifierValues::$OIDAsURN->getName() )
			throw new \Exception("@Qualifier MUST be 'OIDAsURI' or 'OIDAsURN'");
	}
}

/**
 * Simple class to provide enumerated values for the @Qualifier
 */
abstract class QualifierValues
{
	public static $OIDAsURN;
	public static $OIDAsURI;

	protected const OIDAsURNName = 'OIDAsURN';
	protected const OIDAsURIName = 'OIDAsURI';

	public static function fromName( $name )
	{
		switch( $name )
		{
			case self::OIDAsURIName:
				return new QualifierOIDAsURI();
				break;

			case self::OIDAsURNName:
				return new QualifierOIDAsURN();
				break;

			default:
				error_log("The identifier qualifier found in the Xml is '$name'.  Expected 'OIDAsURI' or 'OIDAsURN'.");
				return new QualifierOIDAsURN();				
				break;
		}
	}

	public abstract function getName();
}

QualifierValues::$OIDAsURI = new QualifierOIDAsURI();
QualifierValues::$OIDAsURN = new QualifierOIDAsURN();

class QualifierOIDAsURN extends QualifierValues
{
	public function getName() { return self::OIDAsURNName; }
}

class QualifierOIDAsURI extends QualifierValues
{
	public function getName() { return self::OIDAsURIName; }
}