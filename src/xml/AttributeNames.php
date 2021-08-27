<?php

/**
 * This class is part of the support for XAdES properties generation.
 * 
 * Copyright © 2021 Lyquidity Solutions Limited
 * License GPL 3.0.0
 * Bill Seddon 2021-07-16
 */

namespace lyquidity\xmldsig\xml;

/**
 * Defines all the attribute names used by XAdES
 */
class AttributeNames
{
	// XmlDSig
	const Algorithm = "Algorithm";

	// XAdES 
	const Id = "Id";
	const Encoding = "Encoding";
	const Target = "Target";
	const ObjectReference = "ObjectReference";
	const Qualifier = "Qualifier";
	const Uri = "Uri";
	const URI = "URI";
	const ReferencedData = "referencedData";
	const Filter = "Filter";
	const Type = "Type";
}