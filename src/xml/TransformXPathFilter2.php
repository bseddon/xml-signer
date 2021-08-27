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
 *   <element name="Transform" type="ds:TransformType"/>
 * 
 *   <complexType name="TransformType" mixed="true">
 *     <choice minOccurs="0" maxOccurs="unbounded"> 
 *       <any namespace="##other" processContents="lax"/>
 *       <!-- (1,1) elements from (0,unbounded) namespaces -->
 *       <element name="XPath" type="string"/> 
 *     </choice>
 *     <attribute name="Algorithm" type="anyURI" use="required"/> 
 *   </complexType> * 
 */

/**
 * Creates a node for &lt;Transform> but where there are XPath-Filter 2 XPath nodes
 */
class TransformXPathFilter2 extends TransformXPath
{
	/**
	 * Assign one of more <XPath> to this instance
	 * @param XPathFilter2|XPathFilter2[]|string $xpaths
	 */
	public function __construct( $xpaths = null )
	{
		Transform::__construct( XMLSecurityDSig::XPATH_FILTER2 );
		if ( ! $xpaths ) return;

		// Check the xpaths are XPathFilter2
		if ( is_string( $xpaths ) )
		{ 
			$this->xpaths[] = $this->createXPathInstance( $xpaths );
		}
		else if ( $xpaths instanceof XPathFilter2 )
		{
			$this->xpaths[] = $xpaths;
		}
		else if ( is_array( $xpaths ) )
		{
			// Check all array members are XPathFilter2
			foreach( $xpaths as $xpath )
			{
				if ( ! $xpath instanceof XPathFilter2 && ! is_string( $xpath ) )
					throw new \Exception("All the members of the array passed to the TransformXPathFilter2 constructor must be of type XPathFilter2 or string");
				$this->xpaths[] = $xpath instanceof XPathFilter ? $xpath : new XPathFilter2( $xpath );
			}
		}
		else throw new \Exception("The XPaths parameter passed to the TransformXPathFilter2 constructor is not valid");
	}

	/**
	 * Generate the correct type of XPath class
	 * @param string $query (optional)
	 * @return XPathFilter
	 */
	protected function createXPathInstance( $query = null )
	{
		return new XPathFilter2( $query );
	}

	/**
	 * Converts a transform to a simple representation representation for use by XMLSecurityDSig::AddRefInternal()
	 * @return void
	 */
	public function toSimpleRepresentation()
	{
		return array( $this->algorithm => array(
			array_reduce( $this->xpaths, function( $carry, $xpath )
			{
				/** @var XPathFilter2 $xpath */
				$carry[] = array(
					'query' => $xpath->text,
					'namespaces' => $xpath->namespaces,
					'filter' => $xpath->filter
				);
				return $carry;
			}, array() )
		) );
	}
}
