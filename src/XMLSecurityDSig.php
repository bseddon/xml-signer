<?php

namespace lyquidity\xmldsig;

use \DOMDocument;
use \DOMElement;
use \DOMNode;
use \DOMXPath;
use \Exception;
use \lyquidity\xmldsig\Utils\XPath as UtilsXPath;
use lyquidity\xmldsig\xml\AttributeNames;
use lyquidity\xmldsig\xml\DataObjectFormat;
use lyquidity\xmldsig\xml\ElementNames;
use lyquidity\xmldsig\xml\MimeType;
use lyquidity\xmldsig\xml\XPathFilterName;

/**
 * xmlseclibs.php
 *
 * Copyright (c) 2007-2020, Robert Richards <rrichards@cdatazone.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Robert Richards nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    Robert Richards <rrichards@cdatazone.org>
 * @copyright 2007-2020 Robert Richards <rrichards@cdatazone.org>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

/**
 * This include support for the https://www.w3.org/TR/xmldsig-filter2/ 
 * transform specification.  
 * 
 * The example to exercise the new transform use an XML file made publicly
 * available by the Dutch government.  The file describes the valid policies
 * that can be used when an auditor signs the XBRL instance document of a
 * business which is to be submitted to the Dutch treasury SBR system.
 * 
 * The file includes a digest value (base 64 encoded) which is the document
 * excluding the digest element hashed using SHA-256.  The file also contains
 * <Transforms> that includes two transforms one of which uses the Filter 2.0 
 * form to remove the digest element so it an be hashed.  The example downloads
 * the file, gets the digest value, then processes the transforms and generates
 * a digest of the result and confirms it matches the original value.
 * 
 * 	$policy = 'http://nltaxonomie.nl/sbr/signature_policy_schema/v2.0/SBR-signature-policy-v2.0.xml';
 *	$xml = file_get_contents( $policy );
 *	$doc = new \DOMDocument();
 *	$doc->loadXML( $xml );
 *
 *	// Create a new Security object and process the transforms
 *	$objXMLSecDSig  = new XMLSecurityDSig();
 *	$output = $objXMLSecDSig->processTransforms( $doc->documentElement, $doc->documentElement, false );
 *
 *  // Compute the digest
 *  $hash = base64_encode( hash( 'sha256', $output, true ) );
 *
 *  // Get the orginal digest
 *	$xpath = new \DOMXPath( $doc );
 *	$digestElement = $xpath->query('//sbrsp:SignPolicyDigest');
 *	$digest = $digestElement[0]->textContent;
 *
 *	$match = $hash == $digest;
 */

class XMLSecurityDSig
{
    const XMLDSIGNS = 'http://www.w3.org/2000/09/xmldsig#';
    const XMLDSIGNS11 = 'http://www.w3.org/2000/09/xmldsig11#';
    const XMLDSIGNS2 = 'http://www.w3.org/2000/09/xmldsig2#';
    const SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';
    const SHA256 = 'http://www.w3.org/2001/04/xmlenc#sha256';
    const SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#sha384';
    const SHA512 = 'http://www.w3.org/2001/04/xmlenc#sha512';
    const RIPEMD160 = 'http://www.w3.org/2001/04/xmlenc#ripemd160';

    const C14N = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    const C14N_COMMENTS = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments';
    const EXC_C14N = 'http://www.w3.org/2001/10/xml-exc-c14n#';
    const EXC_C14N_COMMENTS = 'http://www.w3.org/2001/10/xml-exc-c14n#WithComments';
    const C14N11 = 'http://www.w3.org/2006/12/xml-c14n11';
    const C14N11_COMMENTS = 'http://www.w3.org/2006/12/xml-c14n11#WithComments';
    const ENV_SIG = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
    const XPATH_FILTER2 = 'http://www.w3.org/2002/06/xmldsig-filter2';
    const CXPATH = 'http://www.w3.org/TR/1999/REC-xpath-19991116';
    const BASE64 = 'http://www.w3.org/2000/09/xmldsig#base64';
    const XSLT = 'http://www.w3.org/TR/1999/REC-xslt-19991116';

	const MimeTypeXML = 'text/xml';

    const xmlNamespace = "http://www.w3.org/2000/xmlns/";

    const template = '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo>
    <ds:SignatureMethod />
  </ds:SignedInfo>
</ds:Signature>';

    const BASE_TEMPLATE = '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
  <SignedInfo>
    <SignatureMethod />
  </SignedInfo>
</Signature>';

    /** @var DOMElement */
    public $sigNode = null;

    /** @var array */
    public $idKeys = array();

    /** @var array */
    public $idNS = array();

    /** @var string */
    private $signedInfo = null;

    /** @var DOMXPath */
    private $xPathCtx = null;

    /** @var string|null */
    protected $canonicalMethod = null;

    /** @var string */
    private $prefix = '';

    /** @var string */
    const searchpfx = 'secdsig';

    /** @var string */
    const defaultPrefix = 'ds';

    /**
     * This variable contains an associative array of validated nodes.
     * @var array
     */
    private $validatedNodes = null;

    /**
     * The @Id of the &lt;Signature> associated with this instance
     * @var string 
     * */
    protected $signatureId = null;

    /**
     * Returns a copy of the $signature value
     * @return string 
     */
    public function getSignatureId()
    {
        return $this->signatureId;
    }

    /**
     * @param string $prefix
     * @param string $id (optional) If supplied it will become the Id attribute of the <Signature>
     */
    public function __construct( $prefix = XMLSecurityDSig::defaultPrefix, $id = null )
    {
        $template = self::BASE_TEMPLATE;

        // Replace the prefix if one is provided
        if ( ! empty( $prefix ) )
        {
            $this->prefix = $prefix.':';
            $search = array( "<S", "</S", "xmlns=" );
            $replace = array( "<$prefix:S", "</$prefix:S", "xmlns:$prefix=" );
            $template = str_replace( $search, $replace, $template );
        }

        // Add the signature fragment
        $sigdoc = new DOMDocument();
    	$sigdoc->loadXML( $template );
        $this->sigNode = $sigdoc->documentElement;

        // Add an Id if the param is valid
        if ( ! $id ) return;
        $this->signatureId = $id;
        $this->sigNode->setAttribute( 'Id', $id );
    }

    /**
     * Reset the XPathObj to null
     */
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }

    /**
     * Returns the XPathObj or null if xPathCtx is set and sigNode is empty.
     * @return DOMXPath
     */
    public function getXPathObj()
    {
        if ( empty( $this->xPathCtx ) && ! empty( $this->sigNode ) )
        {
            $xpath = new DOMXPath( $this->sigNode->ownerDocument );
            $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
            $this->xPathCtx = $xpath;
        }
        return $this->xPathCtx;
    }

    /**
     * Generate guid
     *
     * @param string $prefix Prefix to use for guid. defaults to pfx
     *
     * @return string The generated guid
     */
    public static function generateGUID($prefix='pfx')
    {
        $uuid = md5(uniqid(mt_rand(), true));
        $guid = $prefix.substr($uuid, 0, 8)."-".
                substr($uuid, 8, 4)."-".
                substr($uuid, 12, 4)."-".
                substr($uuid, 16, 4)."-".
                substr($uuid, 20, 12);
        return $guid;
    }

    /**
     * Generate guid
     *
     * @param string $prefix Prefix to use for guid. defaults to pfx
     *
     * @return string The generated guid
     *
     * @deprecated Method deprecated in Release 1.4.1
     */
    public static function generate_GUID($prefix='pfx')
    {
        return self::generateGUID($prefix);
    }

    /**
     * Returns the <Signature> node 
     * @param DOMDocument $objDoc
     * @param int $pos
     * @return DOMNode|null
     */
    public function locateSignature( $objDoc, $pos = 0 )
    {
        $doc = $objDoc instanceof DOMDocument ? $objDoc : $objDoc->ownerDocument;

        if ( $doc )
        {
            // Get the signature node and store it
            $xpath = new DOMXPath( $doc );
            $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
            $query = ".//". self::searchpfx . ":Signature";
            $nodeset = $xpath->query( $query, $objDoc );
            $this->sigNode = $nodeset->item( $pos );

            // Check the number of SignedInfo nodes is valid
            $query = "./". self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );
            if ( $nodeset->length > 1 )
            {
                throw new \Exception("Invalid structure - Too many SignedInfo elements found");
            }
            return $this->sigNode;
        }
        return null;
    }

    /**
     * @param string $name
     * @param string $value
     * @return \DOMElement
     */
    public function createNewSignNode( $name, $value = null, $namespace = self::XMLDSIGNS )
    {
        $doc = $this->sigNode->ownerDocument;
        if ( is_null( $value ) )
        {
            $node = $doc->createElementNS( $namespace, $this->prefix . $name );
        }
        else
        {
            $node = $doc->createElementNS( $namespace, $this->prefix . $name, $value );
        }
        return $node;
    }

    /**
     * @param string $method
     * @throws \Exception
     */
    public function setCanonicalMethod($method)
    {
        switch ( $method )
        {
            case self::C14N:
            case self::C14N_COMMENTS:
            case self::EXC_C14N:
            case self::EXC_C14N_COMMENTS:
                $this->canonicalMethod = $method;
                break;
            default:
                throw new \Exception('Invalid Canonical Method');
        }

        if ( $xpath = $this->getXPathObj() )
        {
            // Get the Signedinfo node if it exists
            $query = "./". self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );
            if ( $sinfo = $nodeset->item(0) )
            {
                $query = "./". self::searchpfx . ":CanonicalizationMethod";
                $nodeset = $xpath->query($query, $sinfo);
                /** @var \DOMElement $canonNode */
                if ( ! ( $canonNode = $nodeset->item(0) ) )
                {
                    $canonNode = $this->createNewSignNode('CanonicalizationMethod');
                    $sinfo->insertBefore( $canonNode, $sinfo->firstChild );
                }
                $canonNode->setAttribute( AttributeNames::Algorithm, $this->canonicalMethod);
            }
        }
    }

    /**
     * @param DOMNode $node
     * @param string $canonicalmethod
     * @param null|array $arXPath
     * @param null|array $prefixList
     * @return string
     */
    protected function canonicalizeData( $node, $canonicalmethod = null, $arXPath=null, $prefixList=null )
    {
        $canonicalmethod = $canonicalmethod ?? $this->canonicalMethod;

        $exclusive = false;
        $withComments = false;
        $version11 = false;

        switch ($canonicalmethod) 
        {
            case self::C14N:
                $exclusive = false;
                $withComments = false;
                break;
            case self::C14N_COMMENTS:
                $withComments = true;
                break;
            case self::C14N11:
                $exclusive = false;
                $withComments = false;
                $version11 = true;
                break;
            case self::C14N11_COMMENTS:
                $withComments = true;
                $version11 = true;
                break;
            case self::EXC_C14N:
                $exclusive = true;
                break;
            case self::EXC_C14N_COMMENTS:
                $exclusive = true;
                $withComments = true;
                break;
        }

        if ( is_null( $arXPath ) && ( $node instanceof DOMNode ) && ( $node->ownerDocument !== null ) && $node->isSameNode( $node->ownerDocument->documentElement ) ) 
        {
            /* Check for any PI or comments as they would have been excluded */
            $element = $node;
            while ($refnode = $element->previousSibling)
            {
                if ( $refnode->nodeType == XML_PI_NODE || ( ( $refnode->nodeType == XML_COMMENT_NODE ) && $withComments ) )
                {
                    break;
                }
                $element = $refnode;
            }
            if ($refnode == null)
            {
                $node = $node->ownerDocument;
            }
        }

        return $version11
            ? $node->C14N( $exclusive, $withComments, $arXPath, $prefixList )
            : $node->C14N( $exclusive, $withComments, $arXPath, $prefixList );
    }

    /**
     * Signed info is the element (and content) that is used to generate the signature hash
     * @return null|string
     */
    public function canonicalizeSignedInfo()
    {
        $doc = $this->sigNode->ownerDocument;
        $canonicalmethod = null;
        if ( $doc )
        {
            $xpath = $this->getXPathObj();
            $query = "./". self::searchpfx . ":" . ElementNames::SignedInfo;
            $nodeset = $xpath->query( $query, $this->sigNode );
            if ( $nodeset->length > 1 )
            {
                throw new \Exception("Invalid structure - Too many SignedInfo elements found");
            }

            if ( $signInfoNode = $nodeset->item(0) )
            {
                $query = "./". self::searchpfx . ":" . ElementNames::CanonicalizationMethod;
                $nodeset = $xpath->query( $query, $signInfoNode );
                $prefixList = null;
                if ( $canonNode = $nodeset->item(0) ) 
                {
                    /** @var \DOMElement $canonNode */
                    $canonicalmethod = $canonNode->getAttribute( AttributeNames::Algorithm );
                    foreach ( $canonNode->childNodes as $node )
                    {
                        if ( $node->localName == 'InclusiveNamespaces' )
                        {
                            if ($pfx = $node->getAttribute('PrefixList'))
                            {
                                $arpfx = array_filter( explode(' ', $pfx ) );
                                if (count($arpfx) > 0)
                                {
                                    $prefixList = array_merge( $prefixList ? $prefixList : array(), $arpfx );
                                }
                            }
                        }
                    }
                }

                $this->signedInfo = $this->canonicalizeData( $signInfoNode, $canonicalmethod, null, $prefixList );
                return $this->signedInfo;
            }
        }
        return null;
    }

    /**
     * Returns a digest short code for an algorithm url
     *
     * @param string $digestAlgorithm
     * @return string
     */
    public static function getDigestName( $digestAlgorithm )
    {
        switch ($digestAlgorithm)
        {
            case self::SHA1:
                return 'sha1';
                break;
            case self::SHA256:
                return 'sha256';
                break;
            case self::SHA384:
                return 'sha384';
                break;
            case self::SHA512:
                return 'sha512';
                break;
            case self::RIPEMD160:
                return 'ripemd160';
                break;
            default:
                throw new \Exception("Cannot validate digest: Unsupported Algorithm <$digestAlgorithm>");
        }
    }

    /**
     * @param string $digestAlgorithm
     * @param string $data
     * @param bool $encode
     * @return string
     * @throws \Exception
     */
    public function calculateDigest($digestAlgorithm, $data, $encode = true)
    {
        $digest = hash( self::getDigestName( $digestAlgorithm ), $data, true );
        if ( $encode ) 
        {
            $digest = base64_encode( $digest );
        }
        return $digest;

    }

    /**
     * @param $refNode
     * @param string $data
     * @return bool
     */
    public function validateDigest($refNode, $data)
    {
        // Retrieve the algorithm
        $xpath = new DOMXPath( $refNode->ownerDocument );
        $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
        $query = "string(./". self::searchpfx . ":DigestMethod/@Algorithm)";
        $digestAlgorithm = $xpath->evaluate($query, $refNode);

        // Compute the digest
        $digValue = $this->calculateDigest( $digestAlgorithm, $data, false );

        // Get the recorded digest
        $query = "string(./". self::searchpfx . ":DigestValue)";
        $digestValue = $xpath->evaluate( $query, $refNode );

        return ($digValue === base64_decode( $digestValue ) );
    }

    /**
     * This function should process each transform independently, the output node-set of one being the input to the next
     * @param $refNode The reference node
     * @param DOMNode $objData The data to be transformed
     * @param bool $includeCommentNodes Allow the use of comments to be overridded for example if the reference uri is null or empty
	 * @param string $dataFile A path to a file to be used as a data object when there are no transforms
     * @return string
     */
    public function processTransforms( $refNode, $objData, $includeCommentNodes = true, $dataFile = null )
    {
        $xpath = new DOMXPath( $refNode->ownerDocument );
        $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
        $query = "./". self::searchpfx . ":Transforms/". self::searchpfx . ":Transform";
        $transforms = $xpath->query( $query, $refNode );

		if ( $transforms->count() )
		{
			foreach ( $transforms AS $transform )
			{
				/** @var \DOMElement $transform */

				$arXPath = null;
				$prefixList = null;
				$canonicalMethod = self::C14N;
				
				if ( is_string( $objData ) )
				{
					$doc = new \DOMDocument();
					$doc->loadXML( $objData );
					$objData = $doc;
					unset( $doc );
				}

				$algorithm = $transform->getAttribute("Algorithm");

				switch ($algorithm)
				{
					case self::EXC_C14N:
					case self::EXC_C14N_COMMENTS:

						// We remove comment nodes by forcing it to use a canonicalization without comments
						$canonicalMethod = $includeCommentNodes ? $algorithm : self::EXC_C14N;

						$node = $transform->firstChild;
						while ( $node ) 
						{
							if ( $node->localName == 'InclusiveNamespaces')
							{
								if ( $pfx = $node->getAttribute('PrefixList') ) 
								{
									$arpfx = array();
									$pfxlist = explode( " ", $pfx );
									foreach ( $pfxlist AS $pfx ) 
									{
										$val = trim( $pfx );
										if ( ! empty( $val  )) 
										{
											$arpfx[] = $val;
										}
									}
									if ( count($arpfx) > 0)
									{
										$prefixList = $arpfx;
									}
								}
								break;
							}
							$node = $node->nextSibling;
						}
						break;

					case self::C14N:
					case self::C14N_COMMENTS:

						// We remove comment nodes by forcing it to use a canonicalization without comments
						$canonicalMethod = $includeCommentNodes ? $algorithm : self::C14N;
						break;

					case self::CXPATH:

						$node = $transform->firstChild;
						while ( $node )
						{
							if ($node->localName == 'XPath') 
							{
								// BMS 2022-02-24 Don't know why an explicit query is being treated as a filter
								$arXPath['query'] = '(.//. | .//@* | .//namespace::*)[' . $node->nodeValue . ']';
								// $arXPath['query'] = $node->nodeValue;
								// $arXPath['namespaces'] = array( $node->prefix => $node->namespaceURI );
								$nslist = $xpath->query('./namespace::*', $node);
								foreach ($nslist AS $nsnode)
								{
									if ($nsnode->localName == "xml") continue;
									$arXPath['namespaces'][$nsnode->localName] = $nsnode->nodeValue;
								}
								break;
							}
							$node = $node->nextSibling;
						}

						break ;

					case self::XPATH_FILTER2:
						
						$filter = new XmlDsigFilterTransform( $objData );
						$filter->LoadinnerXml( $transform->childNodes );
						// The nodes list is the result of the filter
						$nodeList = $filter->getOutput();
						// Create an XML document as a string from the node list
						$exclude = $algorithm == self::EXC_C14N || $algorithm == self::EXC_C14N_COMMENTS;
						$objData = XmlDsigFilterTransform::nodesetToXml( $nodeList, $exclude, $includeCommentNodes );
						continue 2;

					case self::ENV_SIG:

						$canonicalMethod = $includeCommentNodes ? self::C14N_COMMENTS : self::C14N;

						$arXPath['namespaces'] = array( 'ds' => self::XMLDSIGNS );
						$arXPath['query'] = '(.//. | .//@* | .//namespace::*)[not(ancestor-or-self::ds:Signature)]';

						break;

					case self::BASE64:
						throw new \Exception('BASE64 Transform is not supported');

					case self::XSLT:
						throw new \Exception('XSLT Transform is not supported');
				}

				$objData = $this->canonicalizeData( $objData, $canonicalMethod, $arXPath, $prefixList );    
			}
		}
		else
		{
			if ( $dataFile )
				$objData = file_get_contents( $dataFile );
			else
			{
				/** @var \DOMDocument $objData */
				$objData = $objData->saveXML();
			}
		}

        return $objData;
    }

    /**
     * Create a url that has the path part url encoded
     * @param string[] $parsedUrl An array produced by parse_url()
     * @return string
     */
    public static function encodedUrl( $parsedUrl )
    {
        // This line is necessary because filter_var will not accept spaces but will only accept the path being encoded AFTER the first slash.
        $uri = ( isset( $parsedUrl['scheme'] ) ? $parsedUrl['scheme'] . '://' : '' ) . ( isset( $parsedUrl['host'] ) ? $parsedUrl['host'] : '' ) . "/" . str_replace( '+', '%20', urlencode( ltrim( urldecode( $parsedUrl['path'] ), '/' ) ) );
        return $uri;
    }

    /**
     * Each reference is a collection of <Transforms> and has an optional @URI and @Type
     * The idea is start using the document node-set (or $dataObject if one is passed)
     * then process the URI if provided then the <Transforms>
     * @param \DOMElement $refNode The <SignedInfo/reference> element being processed
     * //param \DOMDocument $dataObject Optionally a data object (the XML being validated) can be passed in.  Might be a separate file.
	 * @param string $mimeType (optional) The mime type of the signed document.  If not signed, XML is assumed
     * @return bool
     */
    public function processRefNode( $refNode, $mimeType = XMLSecurityDSig::MimeTypeXML )
    {
        /*
         * Depending on the URI, we may not want to include comments in the result
         * See: http://www.w3.org/TR/xmldsig-core/#sec-ReferenceProcessingModel
         */
        $includeCommentNodes = true;
        $dataObject = null;
		$isXml = $mimeType == XMLSecurityDSig::MimeTypeXML;
		$dataFile = null;

        // If there is a URI it will define the set of nodes to include.  
        // If the URI exists but is empty, the whole document will be 
        // included but comments will be excluded
        if ( $refNode->hasAttribute("URI") && $refNode->getAttribute("URI") ) 
        {
            $uri = $refNode->getAttribute("URI");
            $arUrl = parse_url( $uri );
            if ( isset( $arUrl ['path'] ) )
            {
                $parts = explode( '#', $uri );
                $dataFile = filter_var( self::encodedUrl( $arUrl ), FILTER_VALIDATE_URL )
                    ? reset( $parts )
                        // Create a uri to the file. For some reason PHP reports 'file:/...' not 
                        // 'file://...' for the document URI which is invalid so needs fixing
                    : self::resolve_path( preg_replace( '!file:/([a-z]:)!i', "file://$1", $refNode->baseURI ), urldecode( reset( $parts ) ) );

				if ( $isXml )
				{
					$remoteDoc = new \DOMDocument();
					$remoteDoc->load( $dataFile );
					$dataObject = $remoteDoc->documentElement;
					if ( ! $dataObject )
						throw new \Exception("The resource ");

					unset( $parts );
					unset( $remoteDoc );
				}
            }
            else
            {
                /* 
                 * This reference identifies a node with the given id by using a URI of the
                 * form "#identifier" (or an empty URI). This should not include comments.
                 * 
                 * TODO: Handler XPointer references in the URI.  An XPointer rmight be used 
                 *       if the user wants to retain comments when selecting a node identified by ID
                 */
                $includeCommentNodes = false;

                if ( $identifier = $arUrl['fragment'] ?? '' ) 
                {
                    $xPath = new DOMXPath( $refNode->ownerDocument );
                    if ( $this->idNS && is_array( $this->idNS ) )
                    {
                        foreach ($this->idNS as $nspf => $ns)
                        {
                            $xPath->registerNamespace($nspf, $ns);
                        }
                    }

                    $iDlist = '@Id="' . UtilsXPath::filterAttrValue($identifier, UtilsXPath::DOUBLE_QUOTE) . '"';
                    if ( is_array( $this->idKeys ) )
                    {
                        foreach ( $this->idKeys as $idKey )
                        {
                            $iDlist .= " or @" . UtilsXPath::filterAttrName($idKey) . '="' .
                            UtilsXPath::filterAttrValue( $identifier, UtilsXPath::DOUBLE_QUOTE) . '"';
                        }
                    }

                    $query = '//*['.$iDlist.']';
                    $dataObject = $xPath->query( $query )->item(0);
                } else
                {
                    $dataObject = $refNode->ownerDocument->documentElement;
                }
            }

			if ( $isXml )
			{
				// Create a new document containing the filtered nodes.  This makes sure any filters
				// are applied only to a document that will be used and not affect the source.

				// When $dataObject is not the document element would prefer to just save as XML
				// but the save process screws around with namespaces causing a problem.  So if
				// the object refers to a sub-node the XML is produced using C14N.  The reason
				// being cautious about use C14N is that its performance is really terrible when
				// the document has many nodes.
				if ( $dataObject->isSameNode( $dataObject->ownerDocument->documentElement ) )
				{
					$xml = $dataObject->ownerDocument->saveXML( $dataObject );
				}
				else
				{
					$xPath = new DOMXPath( $dataObject->ownerDocument );
					$nodeList = iterator_to_array( $xPath->query( './/. | .//@*', $dataObject ) );
					$namespaceNodes = $xPath->query("//namespace::*");
					foreach( $namespaceNodes as $namespaceNode )
						$nodeList[] = $namespaceNode;
				
					$xml = XmlDsigFilterTransform::nodesetToXml( $nodeList, false, $includeCommentNodes );
				}
				
				$dataObject = new \DOMDocument();
				$dataObject->loadXML( $xml );
				unset( $xml );
			}
			else
			{
				// Create a dummy XML document
				$dataObject = new \DOMDocument();
			}
        }
        else if ( ! $dataObject ) 
        {
            /* 
             * This reference identifies the root node without a URI. This may include comments.
             */

            // Create a new document
            $xml = $refNode->ownerDocument->saveXML();
            $dataObject = new \DOMDocument();
            $dataObject->loadXML( $xml );
        }

        // If $dataObject is an element convert it to the document
        if ( ! $dataObject instanceof \DOMDocument )
            $dataObject = $dataObject->ownerDocument;

        $data = $this->processTransforms( $refNode, $dataObject, $includeCommentNodes, $dataFile );
        if ( ! $this->validateDigest( $refNode, $data ) )
        {
            return false;
        }

        if ($dataObject instanceof DOMNode)
        {
            /* Add this node to the list of validated nodes. */
            if ( ! empty( $identifier ) )
            {
                $this->validatedNodes[ $identifier ] = $dataObject;
            }
            else
            {
                $this->validatedNodes[] = $dataObject;
            }
        }

        return $data;
    }

    /**
     * @param \DOMElement $refNode
     * @return null
     */
    public function getRefNodeID($refNode)
    {
        if ( $uri = $refNode->getAttribute("URI") )
        {
            $arUrl = parse_url($uri);
            if ( empty( $arUrl['path'] ) )
            {
                if ( $identifier = $arUrl['fragment'] )
                {
                    return $identifier;
                }
            }
        }
        return null;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRefIDs()
    {
        $refids = array();

        $xpath = $this->getXPathObj();
        $query = "./". self::searchpfx . ":SignedInfo[1]/". self::searchpfx . ":Reference";
        $nodeset = $xpath->query( $query, $this->sigNode );
        if ( $nodeset->length == 0 )
        {
            throw new \Exception("Reference nodes not found");
        }
        foreach ( $nodeset AS $refNode )
        {
            $refids[] = $this->getRefNodeID( $refNode );
        }
        return $refids;
    }

    /**
	 * @param DataObjectFormat[] $dataObjectFormats (optional) The mime type of the signed document.  If not signed, XML is assumed
     * @return bool|string[]
     * @throws \Exception
     */
    public function validateReference( $dataObjectFormats = null )
    {
        $docElem = $this->sigNode->ownerDocument->documentElement;
        if ( ! $docElem->isSameNode( $this->sigNode ) )
        {
            if ( $this->sigNode->parentNode != null )
            {
                // $this->sigNode->parentNode->removeChild($this->sigNode);
            }
        }

        $xpath = $this->getXPathObj();
        $query = "./" . self::searchpfx . ":SignedInfo[1]/". self::searchpfx . ":Reference";
        $nodeset = $xpath->query( $query, $this->sigNode );
        if ( $nodeset->length == 0 )
        {
            throw new \Exception("Reference nodes not found");
        }

        /* Initialize/reset the list of validated nodes. */
        $this->validatedNodes = array();
        $datas = array();

        foreach ( $nodeset AS $refNode ) 
        {
			// Find the data object format associated with the reference
			$id = $refNode->getAttribute('Id');
			$type = $refNode->getAttribute('Type');
			$mimeType = XMLSecurityDSig::MimeTypeXML;

			if ( $dataObjectFormats )
			{
				foreach( $dataObjectFormats as $dataObjectFormat )
				{
					/** @var DataObjectFormat $dataObjectFormat */
					if ( $dataObjectFormat->objectReference == "#$id" )
					{
						if ( $dataObjectFormat->mimeType instanceof MimeType )
						{
							$mimeType = $dataObjectFormat->mimeType->text;
							break;
						}
					}
				}
			}

            $data = $this->processRefNode( $refNode, $type == XAdES::ReferenceType ? XMLSecurityDSig::MimeTypeXML : $mimeType );
            if ( $data === false )
            {
                /* Clear the list of validated nodes. */
                $this->validatedNodes = null;
                throw new \Exception("Reference validation failed: this means the data has been changed");
            }

            $datas[] = $data;
        }
        return $datas;
    }

    /**
     * @param DOMNode $sinfoNode
     * @param DOMDocument $node
     * @param string $algorithm
     * @param null|array $arTransforms
     * @param null|array $options
     */
    private function addRefInternal($sinfoNode, $node, $algorithm, $arTransforms=null, $options=null)
    {
        $prefix = null;
        $prefix_ns = null;
        $id_name = 'Id'; // This is the name of the attribute (id) of the 
        $overwrite_id  = true;
        $force_uri = false;
        $type = null; // Expected by XAdES to identify the <Reference> pointing to the XAdES <SignedProperties> 
        $id = null; // An optional id to add to the reference

        if ( is_array( $options ) )
        {
            $prefix = empty($options['prefix']) ? null : $options['prefix'];
            $prefix_ns = empty($options['prefix_ns']) ? null : $options['prefix_ns'];
            $id_name = empty($options['id_name']) ? 'Id' : $options['id_name'];
            $overwrite_id = !isset($options['overwrite']) ? true : (bool) $options['overwrite'];
            $force_uri = !isset($options['force_uri']) ? false : (bool) $options['force_uri'];
            $type = !isset($options['type']) ? null : $options['type'];
            $id = !isset($options['id']) ? null : $options['id'];
        }

        $attname = $id_name;
        if ( ! empty( $prefix ) )
        {
            $attname = $prefix.':'.$attname;
        }

        $refNode = $this->createNewSignNode('Reference');
        $sinfoNode->appendChild( $refNode );

        if ( $force_uri )
        {
            $refNode->setAttribute( 
                AttributeNames::URI,
                // If the caller has provided a URI then use it
                is_string( $options['force_uri'] ) 
                    ? $options['force_uri'] 
                    : '' );
        }
        else if ( ! $node instanceof DOMDocument )
        {
            $uri = null;
            if ( ! $overwrite_id )
            {
                $uri = $prefix_ns ? $node->getAttributeNS( $prefix_ns, $id_name ) : $node->getAttribute( $id_name );
            }

            if ( empty( $uri ) )
            {
                $uri = self::generateGUID();
                $node->setAttributeNS( $prefix_ns, $attname, $uri );
            }
            $refNode->setAttribute( AttributeNames::URI, '#'.$uri );
        }

        if ( $type )
            $refNode->setAttribute( AttributeNames::Type, $type );

        if ( $id )
            $refNode->setAttribute( AttributeNames::Id, $id );

        $transNodes = $this->createNewSignNode( ElementNames::Transforms );
        $refNode->appendChild( $transNodes );

        if ( is_array( $arTransforms ) )
        {
            foreach ($arTransforms AS $transform)
            {
                $transNode = $this->createNewSignNode( ElementNames::Transform );
                $transNodes->appendChild( $transNode );
                if ( is_array( $transform ) )
                {
                    if ( $transform[ self::CXPATH ] ?? false )
                    {
                        // This function has been changed because there can be multiple <XPath> instances
                        $transNode->setAttribute( AttributeNames::Algorithm, self::CXPATH );

                        if ( $transform[ self::CXPATH ]['query'] ?? false )
                        {
                            // Backwards compatibility
                            $transform[ self::CXPATH ] = array( array( $transform[ self::CXPATH ] ) );
                        }

                        foreach( $transform[ self::CXPATH ] as $xpaths )
                        {
                            foreach( $xpaths as $parts )
                            {
                                if ( $parts['query'] ?? false )
                                {
                                    $XPathNode = $this->createNewSignNode( ElementNames::XPath, $parts['query'] );
                                    $transNode->appendChild( $XPathNode );

                                    foreach( $parts['namespaces'] ?? array() as $prefix => $namespace )
                                    {
                                        $XPathNode->setAttribute( "xmlns:$prefix", $namespace );
                                    }
                                }
                            }
                        }
                    }

                    if ( $transform[ self::XPATH_FILTER2 ] ?? false )
                    {
                        // This function has been changed because there can be multiple <XPath> instances
                        $transNode->setAttribute( AttributeNames::Algorithm, self::XPATH_FILTER2 );

                        if ( $transform[ self::XPATH_FILTER2 ]['query'] ?? false )
                        {
                            // Backwards compatibility
                            $transform[ self::XPATH_FILTER2 ] = array( array( $transform[ self::XPATH_FILTER2 ] ) );
                        }

                        foreach( $transform[ self::XPATH_FILTER2 ] as $xpaths )
                        {
                            foreach( $xpaths as $parts )
                            {
                                if ( $parts['query'] ?? false )
                                {
                                    $XPathNode = $this->createNewSignNode( ElementNames::XPath, $parts['query'], self::XPATH_FILTER2 );
                                    $transNode->appendChild( $XPathNode );
                                    $XPathNode->setAttribute( AttributeNames::Filter, ( $parts['filter'] ?? XPathFilterName::intersect ) );

                                    foreach( $parts['namespaces'] ?? array() as $prefix => $namespace )
                                    {
                                        $XPathNode->setAttribute( "xmlns:$prefix", $namespace );
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $transNode->setAttribute( AttributeNames::Algorithm, $transform );
                }
            }
        }
        elseif ( ! empty( $this->canonicalMethod ) )
        {
            $transNode = $this->createNewSignNode( ElementNames::Transform );
            $transNodes->appendChild( $transNode );
            $transNode->setAttribute( AttributeNames::Algorithm, $this->canonicalMethod );
        }

        $canonicalData = $this->processTransforms( $refNode, $node, ! $force_uri );
        $digValue = $this->calculateDigest( $algorithm, $canonicalData );

        $digestMethod = $this->createNewSignNode( ElementNames::DigestMethod );
        $refNode->appendChild( $digestMethod );
        $digestMethod->setAttribute( AttributeNames::Algorithm, $algorithm );

        $digestValue = $this->createNewSignNode( ElementNames::DigestValue, $digValue );
        $refNode->appendChild( $digestValue );
    }

    /**
     * @param DOMDocument $node
     * @param string $algorithm
     * @param null|array $arTransforms
     * @param null|array $options
     */
    public function addReference( $node, $algorithm, $arTransforms=null, $options=null )
    {
        if ( $xpath = $this->getXPathObj() )
        {
            $query = "./". self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );
            if ( $sInfo = $nodeset->item(0) )
            {
                $this->addRefInternal( $sInfo, $node, $algorithm, $arTransforms, $options );
            }
        }
    }

    /**
     * @param array $arNodes
     * @param string $algorithm
     * @param null|array $arTransforms
     * @param null|array $options
     */
    public function addReferenceList( $arNodes, $algorithm, $arTransforms=null, $options=null )
    {
        if ( $xpath = $this->getXPathObj() )
        {
            $query = "./". self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );
            if ( $sInfo = $nodeset->item(0) )
            {
                foreach ( $arNodes AS $node )
                {
                    $this->addRefInternal( $sInfo, $node, $algorithm, $arTransforms, $options );
                }
            }
        }
    }

    /**
     * @param DOMElement|string $data
     * @param null|string $mimetype
     * @param null|string $encoding
     * @return DOMElement
     */
    public function addObject( $data, $mimetype=null, $encoding=null )
    {
        $objNode = $this->createNewSignNode('Object');
        $this->sigNode->appendChild( $objNode );
        if ( ! empty( $mimetype ) )
        {
            $objNode->setAttribute( 'MimeType', $mimetype );
        }
        if ( ! empty( $encoding ) )
        {
            $objNode->setAttribute( 'Encoding', $encoding );
        }

        if ($data instanceof DOMElement)
        {
            $this->sigNode->ownerDocument->createDocumentFragment();
            $newData = $this->sigNode->ownerDocument->importNode( $data, true );
        }
        else
        {
            $newData = $this->sigNode->ownerDocument->createTextNode( $data );
        }
        $objNode->appendChild($newData);

        return $objNode;
    }

    /**
     * Adds a timestamp of te form defined for xsd:dateTimeStamp (eg. 2021-05-12T12:35:00Z).
     * The timestamp is added as a <SignatureProperty>.
     * 
     * The class does not explicity support <SignatureProperty> but does support <Object> so
     * the necessary elements for a <SignatureProperty> are created and passed into an <Object>
     * 
     * @param string $timestamp xsd:dateTimeStamp (eg. 2021-05-12T12:35:00Z).
     * @param string $signatureId The id of <Signature> and isused as the property @Target
     * @param string $propertyId An id to use to identify the property.  The name is opaque and no meaning can be inferred.
     * @param string $tsaURL This is to be used by decendents
     * @return void
     */
    public function addTimestamp( $timestamp, $signatureId, $propertyId = 'timestamp', $tsaURL = null )
    {
        $propertiesXml = "<SignatureProperties xmlns=\"". self::XMLDSIGNS . "\">" .
            "<SignatureProperty Id=\"$propertyId\" Target=\"#$signatureId\">" .
            "     <xs:timestamp xmlns:xs=\"http://www.w3.org/2001/XMLSchema\">$timestamp</xs:timestamp> " .
            "  </SignatureProperty>" .
            "</SignatureProperties>";

        // Replace the prefix if one is provided
        if ( ! empty( $this->prefix ) )
        {
            $prefix = rtrim( $this->prefix, ':' );
            $search = array( "<S", "</S", "xmlns=" );
            $replace = array( "<{$prefix}:S", "</{$prefix}:S", "xmlns:{$prefix}=" );
            $propertiesXml = str_replace( $search, $replace, $propertiesXml );
        }

        $propertiesDom = new \DOMDocument();
        $propertiesDom->loadXML( $propertiesXml );
        $object = $this->addObject( $propertiesDom->documentElement );
        unset( $propertiesDom );

        $xpath = $this->getXPathObj();
        $xpath->registerNamespace( 'ds', self::XMLDSIGNS );
        $nodes = $xpath->query("./ds:SignatureProperties/ds:SignatureProperty[\"@Id=$propertyId\"]", $object );
        if ( $nodes->length == 1 )
        {
            $this->addReference(
                $nodes[0],
                XMLSecurityDSig::SHA256, 
                array( self::EXC_C14N ),
                array( 'overwrite' => false )
            );

            return $nodes[0];
        }
    
        return $object;
    }

    /**
     * Return the security key for the SignatureMethod/@Algorithm
     * @param DOMNode $node
     * @return XMLSecurityKey
     */
    public function locateKey( $node = null )
    {
        if ( empty( $node ) )
        {
            $node = $this->sigNode;
        }

        if ( ! $node instanceof DOMNode )
        {
            return null;
        }

        if ( $doc = $node->ownerDocument )
        {
            $xpath = new DOMXPath( $doc );
            $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
            $query = "string(./". self::searchpfx . ":SignedInfo/". self::searchpfx . ":SignatureMethod/@Algorithm)";
            $algorithm = $xpath->evaluate( $query, $node );
            if ( $algorithm )
            {
                try
                {
                    $securityKey = new XMLSecurityKey( $algorithm, array( 'type' => 'public' ) );
                } catch ( \Exception $e )
                {
                    return null;
                }
                return $securityKey;
            }
        }
        return null;
    }

    /**
     * Returns:
     *  Bool when verifying HMAC_SHA1;
     *  Int otherwise, with following meanings:
     *    1 on succesful signature verification,
     *    0 when signature verification failed,
     *   -1 if an error occurred during processing.
     *
     * NOTE: be very careful when checking the int return value, because in
     * PHP, -1 will be cast to True when in boolean context. Always check the
     * return value in a strictly typed way, e.g. "$obj->verify(...) === 1".
     *
     * @param XMLSecurityKey $securityKey
     * @return bool|int
     * @throws \Exception
     */
    public function verify( $securityKey )
    {
        $doc = $this->sigNode->ownerDocument;
        $xpath = new DOMXPath( $doc );
        $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
        $query = "string(./". self::searchpfx . ":SignatureValue)";
        $sigValue = $xpath->evaluate( $query, $this->sigNode );
        if ( empty( $sigValue ) )
        {
            throw new \Exception("Unable to locate SignatureValue");
        }
        return $securityKey->verifySignature( $this->signedInfo, base64_decode( $sigValue ) );
    }

    /**
     * @param XMLSecurityKey $securityKey
     * @param string $data
     * @return mixed|string
     */
    public function signData( $securityKey, $data )
    {
        return $securityKey->signData( $data );
    }

    /**
     * @param XMLSecurityKey $securityKey
     * @param null|DOMNode $appendToNode
     */
    public function sign( $securityKey, $appendToNode = null )
    {
        // If we have a parent node append it now so C14N works properly
        if ( $appendToNode != null ) 
        {
            $this->resetXPathObj();
            $this->appendSignature( $appendToNode );
            $this->sigNode = $appendToNode->lastChild;
        }

        if ( $xpath = $this->getXPathObj() ) 
        {
            // Get the SignedInfo node
            $query = "./" . self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );

            if ( $sInfo = $nodeset->item(0) )
            {
                // Get the hash algorithm
                $query = "./" . self::searchpfx . ":SignatureMethod";
                $nodeset = $xpath->query( $query, $sInfo );
                /** @var \DOMElement $sMethod */
                $sMethod = $nodeset->item(0);
                $sMethod->setAttribute( AttributeNames::Algorithm, $securityKey->type );

                // Compute the signature value
                /** @var \DOMElement $sInfo */
                $data = $this->canonicalizeData($sInfo, $this->canonicalMethod);
                $sigValue = base64_encode( $this->signData( $securityKey, $data ) );

                // Create a node for the  SignatureValue
                $sigValueNode = $this->createNewSignNode( 'SignatureValue', $sigValue );

                // And insert it in the right place
                if ($infoSibling = $sInfo->nextSibling)
                {
                    $infoSibling->parentNode->insertBefore( $sigValueNode, $infoSibling );
                } else
                {
                    $this->sigNode->appendChild( $sigValueNode );
                }
            }
        }
    }

    /**
     * Create the canonical version of the SignedInfo element
     * @param string $algorithm
     * @param null|DOMNode $appendToNode
     */
    public function getSignedInfoCanonicalized( $algorithm, $appendToNode = null )
    {
        // If we have a parent node append it now so C14N works properly
        if ( $appendToNode != null ) 
        {
            $this->resetXPathObj();
            $this->appendSignature( $appendToNode );
            $this->sigNode = $appendToNode->lastChild;
        }

        if ( $xpath = $this->getXPathObj() ) 
        {
            // Get the SignedInfo node
            $query = "./" . self::searchpfx . ":SignedInfo";
            $nodeset = $xpath->query( $query, $this->sigNode );

            if ( $sInfo = $nodeset->item(0) )
            {
                // Get the hash algorithm
                $query = "./" . self::searchpfx . ":SignatureMethod";
                $nodeset = $xpath->query( $query, $sInfo );
                /** @var \DOMElement $sMethod */
                $sMethod = $nodeset->item(0);
                $sMethod->setAttribute( AttributeNames::Algorithm, $algorithm );

                // Compute the signature value
                /** @var \DOMElement $sInfo */
                return $this->canonicalizeData($sInfo, $this->canonicalMethod);
            }
        }
    }


    public function appendCert()
    {

    }

    /**
     * @param XMLSecurityKey $securityKey
     * @param null|DOMNode $parent
     */
    public function appendKey($securityKey, $parent=null)
    {
        $securityKey->serializeKey($parent);
    }

    /**
     * This function inserts the signature element.
     *
     * The signature element will be appended to the element, unless $beforeNode is specified. If $beforeNode
     * is specified, the signature element will be inserted as the last element before $beforeNode.
     *
     * @param DOMNode $node       The node the signature element should be inserted into.
     * @param DOMNode $beforeNode The node the signature element should be located before.
     *
     * @return DOMNode The signature element node
     */
    public function insertSignature( $node, $beforeNode = null )
    {
        $document = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
        $signatureElement = $document->importNode($this->sigNode, true);

        if ($beforeNode == null)
        {
            return $node->insertBefore( $signatureElement );
        }
        else
        {
            return $node->insertBefore( $signatureElement, $beforeNode );
        }
    }

    /**
     * @param DOMNode $parentNode
     * @param bool $insertBefore
     * @return DOMNode
     */
    public function appendSignature( $parentNode, $insertBefore = false )
    {
        $beforeNode = $insertBefore ? $parentNode->firstChild : null;
        return $this->insertSignature( $parentNode, $beforeNode );
    }

    /**
     * @param string $cert
     * @param bool $isPEMFormat
     * @return string
     */
    public static function get509XCert( $cert, $isPEMFormat=true )
    {
        $certs = self::staticGet509XCerts( $cert, $isPEMFormat );
        if ( ! empty( $certs ) )
        {
            return $certs[0];
        }
        return '';
    }

    /**
     * @param string $certs
     * @param bool $isPEMFormat
     * @return array
     */
    public static function staticGet509XCerts( $certs, $isPEMFormat=true )
    {
        if ( $isPEMFormat )
        {
            $data = '';
            $certlist = array();
            $arCert = explode("\n", $certs);
            $inData = false;
            foreach ($arCert AS $curData)
            {
                if ( ! $inData )
                {
                    if ( strncmp( $curData, '-----BEGIN CERTIFICATE', 22 ) == 0 )
                    {
                        $inData = true;
                    }
                } 
                else
                {
                    if ( strncmp( $curData, '-----END CERTIFICATE', 20 ) == 0 )
                    {
                        $inData = false;
                        $certlist[] = $data;
                        $data = '';
                        continue;
                    }
                    $data .= trim( $curData );
                }
            }
            return $certlist;
        }
        else
        {
            return array($certs);
        }
    }

    /**
     * @param DOMElement $parentRef
     * @param string $cert
     * @param bool $isPEMFormat
     * @param bool $isURL
     * @param null|DOMXPath $xpath
     * @param null|array $options
     * @throws \Exception
     */
    public static function staticAdd509Cert( $parentRef, $cert, $isPEMFormat = true, $isURL = false, $xpath = null, $options = null )
    {
        if ( $isURL )
        {
            $cert = file_get_contents($cert);
        }

        if ( ! $parentRef instanceof DOMElement)
        {
            throw new \Exception('Invalid parent Node parameter');
        }

        $baseDoc = $parentRef->ownerDocument;

        if ( empty( $xpath ) )
        {
            $xpath = new DOMXPath($parentRef->ownerDocument);
            $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS) ;
        }

        $query = "./". self::searchpfx . ":KeyInfo";
        $nodeset = $xpath->query( $query, $parentRef );
        $keyInfo = $nodeset->item(0);
        $dsig_pfx = '';
        if ( ! $keyInfo ) 
        {
            $pfx = $parentRef->lookupPrefix( self::XMLDSIGNS );
            if ( ! empty( $pfx ) ) 
            {
                $dsig_pfx = $pfx . ":";
            }
            $inserted = false;
            $keyInfo = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx . 'KeyInfo' );

            $query = "./". self::searchpfx . ":Object";
            $nodeset = $xpath->query( $query, $parentRef );
            if ($sObject = $nodeset->item(0))
            {
                $sObject->parentNode->insertBefore( $keyInfo, $sObject );
                $inserted = true;
            }

            if (! $inserted)
            {
                $parentRef->appendChild( $keyInfo );
            }
        }
        else 
        {
            $pfx = $keyInfo->lookupPrefix( self::XMLDSIGNS );
            if ( ! empty( $pfx ) )
            {
                $dsig_pfx = $pfx . ":";
            }
        }

        // Add all certs if there are more than one
        $certs = self::staticGet509XCerts( $cert, $isPEMFormat );

        // Attach X509 data node
        $x509DataNode = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'X509Data' );
        $keyInfo->appendChild( $x509DataNode );

        $issuerSerial = false;
        $subjectName = false;
        if ( is_array( $options ) )
        {
            if ( ! empty( $options['issuerSerial'] ) )
            {
                $issuerSerial = true;
            }
            if ( ! empty( $options['subjectName'] ) )
            {
                $subjectName = true;
            }
        }

        // Attach all certificate nodes and any additional data
        foreach ($certs as $X509Cert)
        {
            if ( $issuerSerial || $subjectName )
            {
                if ( $certData = openssl_x509_parse( "-----BEGIN CERTIFICATE-----\n" . chunk_split( $X509Cert, 64, "\n" ) . "-----END CERTIFICATE-----\n" ) )
                {
                    if ( $subjectName && ! empty( $certData['subject'] ) )
                    {
                        if ( is_array( $certData['subject'] ) )
                        {
                            $parts = array();
                            foreach ( $certData['subject'] AS $key => $value )
                            {
                                if ( is_array( $value ) )
                                {
                                    foreach ($value as $valueElement)
                                    {
                                        array_unshift( $parts, "$key=$valueElement" );
                                    }
                                }
                                else
                                {
                                    array_unshift( $parts, "$key=$value" );
                                }
                            }
                            $subjectNameValue = implode( ',', $parts );
                        }
                        else
                        {
                            $subjectNameValue = $certData['subject'];
                        }
                        $x509SubjectNode = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx.'X509SubjectName', $subjectNameValue);
                        $x509DataNode->appendChild($x509SubjectNode);
                    }
                    if ( $issuerSerial && ! empty( $certData['issuer'] ) && ! empty( $certData['serialNumber'] ) )
                    {
                        if ( is_array($certData['issuer'] ) ) 
                        {
                            $parts = array();
                            foreach ($certData['issuer'] AS $key => $value)
                            {
                                array_unshift( $parts, "$key=$value" );
                            }
                            $issuerName = implode( ',', $parts );
                        }
                        else
                        {
                            $issuerName = $certData['issuer'];
                        }

                        $x509IssuerNode = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'X509IssuerSerial' );
                        $x509DataNode->appendChild( $x509IssuerNode );

                        $x509Node = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'X509IssuerName', $issuerName );
                        $x509IssuerNode->appendChild( $x509Node );
                        $x509Node = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'X509SerialNumber', $certData['serialNumber'] );
                        $x509IssuerNode->appendChild( $x509Node );
                    }
                }

            }
            $x509CertNode = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'X509Certificate', $X509Cert );
            $x509DataNode->appendChild( $x509CertNode );
        }
    }

    /**
     * @param string $cert
     * @param bool $isPEMFormat
     * @param bool $isURL
     * @param null|array $options
     */
    public function add509Cert($cert, $isPEMFormat=true, $isURL=false, $options=null)
    {
        if ( $xpath = $this->getXPathObj() )
        {
            self::staticAdd509Cert( $this->sigNode, $cert, $isPEMFormat, $isURL, $xpath, $options );
        }
    }

    /**
     * This function appends a node to the KeyInfo.
     *
     * The KeyInfo element will be created if one does not exist in the document.
     *
     * @param DOMNode $node The node to append to the KeyInfo.
     *
     * @return DOMNode The KeyInfo element node
     */
    public function appendToKeyInfo( $node )
    {
        $parentRef = $this->sigNode;
        $baseDoc = $parentRef->ownerDocument;

        $xpath = $this->getXPathObj();
        if ( empty( $xpath ) )
        {
            $xpath = new DOMXPath( $parentRef->ownerDocument );
            $xpath->registerNamespace( self::searchpfx, self::XMLDSIGNS );
        }

        $query = "./". self::searchpfx . ":KeyInfo";
        $nodeset = $xpath->query( $query, $parentRef );
        $keyInfo = $nodeset->item(0);
        if ( ! $keyInfo )
        {
            $dsig_pfx = '';
            $pfx = $parentRef->lookupPrefix( self::XMLDSIGNS );
            if ( ! empty( $pfx ) )
            {
                $dsig_pfx = $pfx.":";
            }

            $inserted = false;
            $keyInfo = $baseDoc->createElementNS( self::XMLDSIGNS, $dsig_pfx.'KeyInfo' );

            $query = "./". self::searchpfx . ":Object";
            $nodeset = $xpath->query( $query, $parentRef );
            if ( $sObject = $nodeset->item(0) )
            {
                $sObject->parentNode->insertBefore( $keyInfo, $sObject );
                $inserted = true;
            }

            if ( ! $inserted ) 
            {
                $parentRef->appendChild( $keyInfo );
            }
        }

        $keyInfo->appendChild( $node );

        return $keyInfo;
    }

    /**
     * This function retrieves an associative array of the validated nodes.
     *
     * The array will contain the id of the referenced node as the key and the node itself
     * as the value.
     *
     * Returns:
     *  An associative array of validated nodes or null if no nodes have been validated.
     *
     *  @return array Associative array of validated nodes
     */
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }

	/**
	 * Used to compute an absolute path for a resource ($target) with respect to a source.
	 * For example, the presentation linkbase file will be specified as relative to the
	 * location of the host schema.
	 * @param string $source The resource for the source
	 * @param string $target The resource for the target
	 * @return string
	 */
	public static function resolve_path( $source, $target )
	{
		// $target = urldecode( $target );

		$source = str_replace( '\\', '/', $source );
		// Remove any // instances as they confuse the path normalizer but take care to
		// not to remove ://
		$offset = 0;
		while ( true )
		{
			$pos = strpos( $source, "//", $offset );
			if ( $pos === false ) break;
			$offset = $pos + 2;
			// Ignore :// (eg https://)
			if ( $pos > 0 && $source[ $pos-1 ] == ":" ) continue;
			$source = str_replace( "//", "/", $source );
			$offset--;
		}

		// Using the extension to determine if the source is a file or directory reference is problematic unless it is always terminated with a /
		// This is because the source directory path may include a period such as x:/myroot/some.dir-in-a-path/
		$source = self::endsWith( $source, '/' ) || pathinfo( $source, PATHINFO_EXTENSION ) === "" //  || is_dir( $source )
			? $source
			: pathinfo( $source, PATHINFO_DIRNAME );

		$sourceIsUrl = filter_var( rawurlencode( $source ), FILTER_VALIDATE_URL );
		$targetIsUrl = filter_var( rawurlencode( $target ), FILTER_VALIDATE_URL );

		// Absolute
		if ( $target && ( filter_var( $target, FILTER_VALIDATE_URL ) || ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' && strlen( $target ) > 1 && ( $target[1] === ':' || substr( $target, 0, 2 ) === '\\\\' ) ) ) )
			$path = $target;

		// Relative to root
		elseif ( $target && ( $target[0] === '/' || $target[0] === '\\' ) )
		{
			$root = self::get_schema_root( $source );
			$path = $root . $target;
		}
		// Relative to source
		else
		{
			if ( self::endsWith( $source, ":" ) ) $source .= "/";
			$path =  $source . ( substr( $source, -1 ) == '/' ? '' : '/' ) . $target;
		}

		// Process the components
		// BMS 2018-06-06 By ignoring a leading slash the effect is to create relative paths on linux
		//				  However, its been done to handle http://xxx sources.  But this is not necessary (see below)
		$parts = explode( '/', $path );
		$safe = array();
		foreach ( $parts as $idx => $part )
		{
			// if ( empty( $part ) || ( '.' === $part ) )
			if ( '.' === $part )
			{
				continue;
			}
			elseif ( '..' === $part )
			{
				array_pop( $safe );
				continue;
			}
			else
			{
				$safe[] = $part;
			}
		}

		// BMS 2108-06-06 See above
		return implode( '/', $safe );

		// Return the "clean" path
		return $sourceIsUrl || $targetIsUrl
			? str_replace( ':/', '://', implode( '/', $safe ) )
			: implode( '/', $safe );
	}

	/**
	 * Find out if $haystack ends with $needle
	 * @param string $haystack
	 * @param string $needle
	 * @return boolean
	 */
	public static function endsWith( $haystack, $needle )
	{
		$strlen = strlen( $haystack );
		$testlen = strlen( $needle );
		if ( $testlen > $strlen ) return false;
		return substr_compare( $haystack, $needle, $strlen - $testlen, $testlen ) === 0;
	}

	/**
	 * Used by resolve_path to obtain the root element of a uri or file path.
	 * This is necessary because a schema or linkbase uri may be absolute but without a host.
	 *
	 * @param string The file
	 * @return string The root
	 */
	private static function get_schema_root( $file )
	{
		if ( filter_var( $file, FILTER_VALIDATE_URL ) === false )
		{
			// my else codes goes
			if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
			{
				// First case is c:\
				if ( strlen( $file ) > 1 && substr( $file, 1, 1 ) === ":" )
					$root = "{$file[0]}:";
				// Second case is a volume
				elseif ( strlen( $file ) > 1 && substr( $file, 0, 2 ) === "\\\\" )
				{
					$pos = strpos( $file, '\\', 2 );

					if ( $pos === false )
						$root = $file;
					else
						$root = substr( $file, 0, $pos );
				}
				// The catch all is that no root is provided
				else
					$root = pathinfo( $file, PATHINFO_EXTENSION ) === ""
						? $file
						: pathinfo( $file, PATHINFO_DIRNAME );
			}
		}
		else
		{
			$components = parse_url( $file );
			$root = "{$components['scheme']}://{$components['host']}";
		}

		return $root;
	}
}
