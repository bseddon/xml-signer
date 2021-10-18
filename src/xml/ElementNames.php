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
 * Defines all the element names used by XAdES
 */
class ElementNames
{
	// XmlDSig
	const DigestMethod = "DigestMethod";
	const DigestValue = "DigestValue";
	const X509IssuerName = "X509IssuerName";
	const X509SerialNumber = "X509SerialNumber";
	const Transforms = "Transforms";
	const Transform = "Transform";
	const XPath = 'XPath';
	const CanonicalizationMethod = "CanonicalizationMethod";
	const Signature = "Signature";
	const SignedInfo = "SignedInfo"; 
	const SignatureValue = "SignatureValue"; 
	const KeyInfo = "KeyInfo"; 
	const Object = "Object";
	const SignatureMethod = "SignatureMethod";
	const KeyName = "KeyName";
	const KeyValue = "KeyValue";
	const RetrievalMethod = "RetrievalMethod";
	const X509Data = "X509Data";
	const PGPData = "PGPData";
	const SPKIData = "SPKIData";
	const MgmtData = "MgmtData";
	const X509Certificate = "X509Certificate";
	const Reference = "Reference";

	// XAdES
	const Any = "Any";
	const ByName = "ByName";
	const ByKey = "ByKey";
	const AttrAuthoritiesCertValues = "AttrAuthoritiesCertValues";
	const AttributeRevocationValues = "AttributeRevocationValues";
	const AttributeCertificateRefs = "AttributeCertificateRefs";
	const AttributeCertificateRefsV2 = "AttributeCertificateRefsV2";
	const AttributeRevocationRefs = "AttributeRevocationRefs";
	const QualifyingProperties = "QualifyingProperties";
	const QualifyingPropertiesReference = "QualifyingPropertiesReference";
	const SignedProperties = "SignedProperties";
	const SignedSignatureProperties = "SignedSignatureProperties";
	const SignedDataObjectProperties = "SignedDataObjectProperties";
	const UnsignedProperties = "UnsignedProperties";
	const UnsignedSignatureProperties = "UnsignedSignatureProperties";
	const UnsignedDataObjectProperties = "UnsignedDataObjectProperties";
	const UnsignedDataObjectProperty = "UnsignedDataObjectProperty";
	const SigningTime = "SigningTime";
	const SigningCertificate = "SigningCertificate";
	const SigningCertificateV2 = "SigningCertificateV2";
	const SignaturePolicyIdentifier = "SignaturePolicyIdentifier";
	const SignatureProductionPlace = "SignatureProductionPlace";
	const SignatureProductionPlaceV2 = "SignatureProductionPlaceV2";
	const SignerRole = "SignerRole";
	const SignerRoleV2 = "SignerRoleV2";
	const Cert = "Cert";
	const CertDigest = "CertDigest";
	const IssuerSerial = "IssuerSerial";
	const IssuerSerialV2 = "IssuerSerialV2";
	const DataObjectFormat = "DataObjectFormat";
	const CommitmentTypeIndication = "CommitmentTypeIndication";
	const AllDataObjectsTimeStamp = "AllDataObjectsTimeStamp";
	const IndividualDataObjectsTimeStamp = "IndividualDataObjectsTimeStamp";
	const HashDataInfo = "HashDataInfo";
	const EncapsulatedTimeStamp = "EncapsulatedTimeStamp";
	const XMLTimeStamp = "XMLTimeStamp";
	const XAdESTimeStamp = "XAdESTimeStamp";
	const OtherTimeStamp = "OtherTimeStamp";
	const Description = "Description";
	const ObjectIdentifier = "ObjectIdentifier";
	const MimeType = "MimeType";
	const Encoding = "Encoding";
	const Identifier = "Identifier";
	const DocumentationReferences = "DocumentationReferences";
	const DocumentationReference = "DocumentationReference";
	const CommitmentTypeId = "CommitmentTypeId";
	const ObjectReference = "ObjectReference";
	const CommitmentTypeQualifiers = "CommitmentTypeQualifiers";
	const AllSignedDataObjects = "AllSignedDataObjects";
	const CommitmentTypeQualifier = "CommitmentTypeQualifier";
	const SignaturePolicyId = "SignaturePolicyId";
	const SignaturePolicyImplied = "SignaturePolicyImplied";
	const SigPolicyId = "SigPolicyId";
	const SigPolicyHash = "SigPolicyHash";
	const SigPolicyQualifier = "SigPolicyQualifier";
	const SigPolicyQualifiers = "SigPolicyQualifiers";
	const SPURI = "SPURI";
	const SPUserNotice = "SPUserNotice";
	const SPDocSpecification = "SPDocSpecification";
	const SigPolDocLocalURI = "SigPolDocLocalURI";
	const SignaturePolicyDocument = "SignaturePolicyDocument";
	const NoticeRef = "NoticeRef";
	const ExplicitText = "ExplicitText";
	const ClaimedRoles = "ClaimedRoles";
	const ClaimedRole = "ClaimedRole";
	const CertifiedRoles = "CertifiedRoles";
	const CertifiedRolesV2 = "CertifiedRolesV2";
	const CertifiedRole = "CertifiedRole";
	const Organization = "Organization";
	const NoticeNumbers = "NoticeNumbers";
	const Int = "int";
	const City = "City";
	const PostalCode = "PostalCode";
	const StateOrProvince = "StateOrProvince";
	const CountryName = "CountryName";
	const CounterSignature = "CounterSignature";
	const SignatureTimeStamp = "SignatureTimeStamp";
	const CompleteCertificateRefs = "CompleteCertificateRefs";
	const CompleteCertificateRefsV2 = "CompleteCertificateRefsV2";
	const CompleteRevocationRefs = "CompleteRevocationRefs";
	const SigAndRefsTimeStamp = "SigAndRefsTimeStamp";
	const SigAndRefsTimeStampV2 = "SigAndRefsTimeStampV2";
	const RefsOnlyTimeStamp = "RefsOnlyTimeStamp";
	const RefsOnlyTimeStampV2 = "RefsOnlyTimeStampV2";
	const CertificateValues = "CertificateValues";
	const RevocationValues = "RevocationValues";
	const ArchiveTimeStamp = "ArchiveTimeStamp";
	const CertRefs = "CertRefs";
	const CRLRefs = "CRLRefs";
	const CRLRef = "CRLRef";
	const OCSPRefs = "OCSPRefs";
	const OtherRefs = "OtherRefs";
	const OtherRef = "OtherRef";
	const DigestAlgAndValue = "DigestAlgAndValue";
	const CRLIdentifier = "CRLIdentifier";
	const Issuer = "Issuer";
	const IssueTime = "IssueTime";
	const Number = "Number";
	const OCSPRef = "OCSPRef";
	const OCSPIdentifier = "OCSPIdentifier";
	const ResponderID = "ResponderID";
	const ProducedAt = "ProducedAt";
	const EncapsulatedX509Certificate = "EncapsulatedX509Certificate";
	const OtherCertificate = "OtherCertificate";
	const CRLValues = "CRLValues";
	const OCSPValues = "OCSPValues";
	const OtherValues = "OtherValues";
	const OtherValue = "OtherValue";
	const EncapsulatedCRLValue = "EncapsulatedCRLValue";
	const EncapsulatedOCSPValue = "EncapsulatedOCSPValue";
	const ReferenceInfo = "ReferenceInfo";
	const Include = "Include";
	const StreetAddress = "StreetAddress";
	const OtherAttributeCertificate = "OtherAttributeCertificate";
	const X509AttributeCertificate = "X509AttributeCertificate";
	const SignedAssertion = "SignedAssertion";
	const SignedAssertions = "SignedAssertions";
	const ReferenceInfoType = "ReferenceInfoType";
	const EncapsulatedPKIData = "EncapsulatedPKIData";
	const GenericTimeStamp = "GenericTimeStamp";
	const SignaturePolicyStore = "SignaturePolicyStore";
	const TimeStampValidationData = "TimeStampValidationData";
}
