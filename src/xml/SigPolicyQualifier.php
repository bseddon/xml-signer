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
 * <xsd:element name="SigPolicyQualifier" type="AnyType" maxOccurs="unbounded"/> 
 */

/**
 * Creates a node for &lt;SigPolicyQualifier>
 */
class SigPolicyQualifier extends Generic
{
	/**
	 * Constructor
	 *
	 * @param string|XmlCore $childNode
	 */
	public function __construct( $childNode = null )
	{
		parent::__construct( $this->getLocalName() );
		$this->childNodes = array();
		$this->defaultNamespace = XmlCore::getDefaultNamespace();

		if ( is_string( $childNode ) )
			$this->text = $childNode;
		else if ( is_object( $childNode ) )
			$this->addChildNode( $childNode );
	}

	/**
	 * Returns the instance local name
	 * @return string
	 */
	public function getLocalName()
	{
		return ElementNames::SigPolicyQualifier;
	}

}
