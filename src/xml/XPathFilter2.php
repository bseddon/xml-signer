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
 *	http://www.w3.org/2002/06/xmldsig-filter2
 *
 *	<element name="XPath" type="xf:XPathType"/>
 *	
 *	<complexType name="XPathType">
 *		<simpleContent>
 *			<extension base="string">
 *				<attribute name="Filter">
 *					<simpleType>
 *						<restriction base="string">
 *							<enumeration value="intersect"/>
 *							<enumeration value="subtract"/>
 *							<enumeration value="union"/>
 *						</restriction>
 *					</simpleType>
 *				</attribute>
 *			</extension>
 *		</simpleContent>
 *	</complexType> 
 */

/**
 * Creates a node for &lt;Transform> 
 */
class XPathFilter2 extends XPathFilter
{
	/**
	 * The filter to use with the xpath query.  Can be one of the constants defined in the Filter class.
	 * @var string
	 */
	public $filter = XPathFilterName::union;

	/**
	 * Assign one of more &lt;XPath> to this instance
	 * @param string $query
	 * @param string $filter
	 * @param string[] $namespaces A set of namespaces used in the XPath query indexed by prefix
	 */
	public function __construct( $query = null, $filter = null, $namespaces = array() )
	{
		$this->filter = $filter;
		parent::__construct( $query, $namespaces );
		$this->defaultNamespace = XMLSecurityDSig::XPATH_FILTER2;
	}

	/**
	 * Create an &lt;XPath> with @Filter
	 * @param \DOMElement $parentNode
	 * @param string[] $attributes
	 * @return void
	 */
	public function generateXml( $parentNode, $attributes = array() )
	{
		$newElement = parent::generateXml( $parentNode, array( AttributeNames::Filter => $this->filter ) );

		$filter = $newElement->ownerDocument->createAttribute( AttributeNames::Filter );
		$filter->value = $this->filter;
		$newElement->appendChild( $filter );
		return $newElement;
	}

	/**
	 * Read the filter attribute
	 * @param \DOMElement $node
	 * @return void
	 */
	public function loadInnerXml($node)
	{
		parent::loadInnerXml( $node );

		$attr = $node->getAttributeNode( AttributeNames::Filter );
		if ( ! $attr )
			throw new \Exception("The XPath filter 2 @Filter cannot be found.  This is a required attribute.");

		$this->filter = $attr->value;
	}

	/**
	 * Validate the filter: that it exists and has one of the three valid value
	 * @return void
	 */
	public function validateElement()
	{
		parent::validateElement();

		$reflection = new \ReflectionClass( XPathFilterName::class );
		$filter = $reflection->getConstant( $this->filter );
		if ( $filter ) return;
		throw new \Exception("The XPath filter 2 @Filter does not have a valid value: {$this->filter}");
	}
}

/**
 * Defines the three filters
 */
class XPathFilterName
{
	const intersect = "intersect";
	const subtract = "subtract";
	const union = "union";
}