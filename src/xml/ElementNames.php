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

	// XAdES
	const Any = "Any";
	const ByName = "ByName";
	const ByKey = "ByKey";
	const AttrAuthoritiesCertValues = "AttrAuthoritiesCertValues";
	const AttributeRevocationValues = "AttributeRevocationValues";
	const AttributeCertificateRefs = "AttributeCertificateRefs";
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
	const SignerRole = "SignerRole";
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
	const NoticeRef = "NoticeRef";
	const ExplicitText = "ExplicitText";
	const ClaimedRoles = "ClaimedRoles";
	const ClaimedRole = "ClaimedRole";
	const CertifiedRoles = "CertifiedRoles";
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
	const CompleteRevocationRefs = "CompleteRevocationRefs";
	const SigAndRefsTimeStamp = "SigAndRefsTimeStamp";
	const RefsOnlyTimeStamp = "RefsOnlyTimeStamp";
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
}
