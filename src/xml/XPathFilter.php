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
 *   <element name="XPath" type="string"/> 
 */

/**
 * Creates a node for &lt;Transform> 
 */
class XPathFilter extends TextBase
{
	/**
	 * Assign one of more &lt;XPath> to this instance
	 *
	 * @param string $query
	 * @param string[] $namespaces A set of namespaces used in the XPath query indexed by prefix
	 */
	public function __construct( $query = null, $namespaces = array() )
	{
		$this->defaultNamespace = XMLSecurityDSig::XMLDSIGNS;
		// $this->query = $query;
		parent::__construct( $query );
		if ( is_array( $namespaces ) )
			$this->namespaces = $namespaces;
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::XPath;
	}

}
