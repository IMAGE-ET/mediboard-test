<?php 

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$class = new CXDSRegistryObjectList();
$registry = new CXDSRegistryPackage("SubmissionSet01");
//date actuelle
$registry->setSubmissionTime(array("20111206110801"));
$registry->setTitle("Lot du document originel");
$registry->setComments("Compte rendu test");
//lien avec le PS du document
$document = new CXDSDocumentEntryAuthor("cla55", "SubmissionSet01", true);
$document->setAuthorPerson(array("00B1041553^MEDECIN4155-B1^PAUL^^^^^^&amp;1.2.250.1.71.4.2.1&amp;ISO^D^^^IDNPS"));
$document->setAuthorSpecialty(array("G15_10/SM26^Médecin - Qualifié en Médecine Générale (SM)^1.2.250.1.213.1.1.4.5"));
$document->setAuthorInstitution(array("Cabinet Dr MEDECIN2154-B1 PAUL^^^^^&amp;1.2.250.1.71.4.2.2&amp;ISO^IDNST^^^00B104155300"));
$registry->appendDocumentEntryAuthor($document);
//type d'activité pour lequel on envoie les documents
$content = new CXDSContentType("cla56", "SubmissionSet01", "04");
$content->setCodingScheme(array("1.2.250.1.213.2.2"));
$content->setContentTypeCodeDisplayName("Hospitalisation de jour");
$registry->setContentType($content);
//spécification d'un Submissionset ou d'un folder, ici submissionset
$registry->setSubmissionSet("cla57", "SubmissionSet01", true);
//patient du document
$registry->setPatientId("ei22", "SubmissionSet01", "1164485058822081751070^^^&amp;1.2.250.1.213.1.4.2&amp;ISO^INS-C^^20100522152212");
//OID de l'instance
$registry->setSourceId("ei23", "SubmissionSet01", "1.2.250.1.999.1.1.7898");
//OID unique
$registry->setUniqueId("ei24", "SubmissionSet01", "1.2.250.1.999.1.1.7898.1.20111206120801");
$class->appendRegistryPackage($registry);
//text/xml enfonction du type de contenu
$extrinsic = new CXDSExtrinsicObject("document01", "text/xml");
//effectiveTime en UTC pas GMT
$extrinsic->setSlot("creationTime", array("20100903114745"));
//languageCode
$extrinsic->setSlot("languageCode", array("fr-FR"));
//legalAuthenticator XCN
$extrinsic->setSlot("legalAuthenticator", array("00B1041553^MEDECIN4155-B1^PAUL^^^^^^&amp;1.2.250.1.71.4.2.1&amp;ISO^D^^^IDNPS"));
//documentationOf/serviceEvent/effectiveTime/low en UTC
$extrinsic->setSlot("serviceStartTime", array("20100319183244"));
//documentationOf/serviceEvent/effectiveTime/high en UTC
$extrinsic->setSlot("serviceStopTime", array("20100319183244"));
//recordTarget/patientRole/id
$extrinsic->setSlot("sourcePatientId", array("0456789999^^^&amp;1.2.250.1.999.1.1.7898.2&amp;ISO^PI"));
//recordTarget/patientRole/patient/name
$extrinsic->setSlot("sourcePatientInfo", array("PID-5|MARTINQUARANTESIX^Marie^^^^^L", "PID-7|19760414", "PID-8|F"));
//title
$extrinsic->setTitle("Document 3 (version originale)");
$extrinsic->setComments("commentaire sur document");
$document = new CXDSDocumentEntryAuthor("cla58", "Document01");
//author/assignedAuthor
//si
$document->setAuthorPerson(array("00B1041553^MEDECIN4155-B1^PAUL^^^^^^&amp;1.2.250.1.71.4.2.1&amp;ISO^D^^^IDNPS"));
//author/assignedAuthor/code
$document->setAuthorSpecialty(array("G15_10/SM26^Médecin - Qualifié en Médecine Générale (SM)^1.2.250.1.213.1.1.4.5"));
//author/assignedAuthor/representedOrganization - si absent, ne pas renseigner
//si nom pas présent - champ vide
//si id nullflavor alors 6-7-10 vide
$document->setAuthorInstitution(array("Cabinet Dr MEDECIN2154-B1 PAUL^^^^^&amp;1.2.250.1.71.4.2.2&amp;ISO^IDNST^^^00B104155300"));
/** Le role => author/functionCode*/
$extrinsic->appendDocumentEntryAuthor($document);
//code
$classification = new CXDSClass("cla59", "Document01", "10");
$classification->setCodingScheme(array("1.2.250.1.213.1.1.4.1"));
$classification->setName("Comptes rendus");
$extrinsic->setClass($classification);
//confidentialityCode
$confid = new CXDSConfidentiality("cla60", "document01", "N");
$confid->setCodingScheme(array("2.16.840.1.113883.5.25"));
$confid->setName("Normal");
$extrinsic->appendConfidentiality($confid);
//documentationOf/serviceEvent/code
$event = new CXDSEventCodeList("cla602", "document01", "18724-5");
$event->setCodingScheme(array("2.16.840.1.113883.6.1"));
$event->setName("HLA");
$extrinsic->appendEventCodeList($event);
//Enfonction d'un corps structuré
//urn:ihe:iti:xds-sd:pdf:2008
$format = new CXDSFormat("cla61", "document01", "urn:ihe:iti:xds-sd:text:2008");
//1.3.6.1.4.1.19376.1.2.3
$format->setCodingScheme(array("1.3.6.1.4.1.19376.1.2.3"));
//documents à corps non structuré en Pdf/A-1
$format->setName("documents à corps non structuré en texte brut");
$extrinsic->setFormat($format);
//componentOf/encompassingEncounter/ location/healthCareFacility/code
$healt = new CXDSHealthcareFacilityType("cla62", "document01", "SA01");
$healt->setCodingScheme(array("1.2.250.1.71.4.2.4"));
$healt->setName("Etablissement Public de Sante");
$extrinsic->setHealthcareFacilityType($healt);
//documentationOf/serviceEvent/performer/ assignedEntity/ representedOrganization/ standardIndustryClassCode
$pratice = new CXDSPracticeSetting("cla63", "document01", "ETABLISSEMENT");
$pratice->setCodingScheme(array("1.2.250.1.213.1.1.4.9"));
$pratice->setName("Etablissement de santé");
$extrinsic->setPracticeSetting($pratice);
//code
$type = new CXDSType("cla64", "documen01", "34874-8");
$type->setCodingScheme(array("2.16.840.1.113883.6.1"));
$type->setName("CR opératoire");
$extrinsic->setType($type);
//recordTarget/patientRole/id
$extrinsic->setPatientId("ei25", "document01", "1164485058822081751070^^^&amp;1.2.250.1.213.1.4.2&amp;ISO^INS-C^^20100522152212");
//id - root+extension
$extrinsic->setUniqueId("ei26", "document01", "1.2.250.1.999.1.1.7898.3.333.1");
$class->appendExtrinsicObject($extrinsic);

/**
 * signature du practicien
 */
$extrinsic = new CXDSExtrinsicObject("Signature01", "text/xml");
$extrinsic->setSlot("creationTime", array("20111206110801"));
$extrinsic->setSlot("languageCode", array("art"));
$extrinsic->setSlot("legalAuthenticator", array("00B1041553^MEDECIN4155-B1^PAUL^^^^^^&amp;1.2.250.1.71.4.2.1&amp;ISO^D^^^IDNPS"));
$extrinsic->setSlot("serviceStartTime", array("20111206110801"));
$extrinsic->setSlot("serviceStopTime", array("20111206110801"));
$extrinsic->setSlot("sourcePatientId", array("1164485058822081751070^^^&amp;1.2.250.1.213.1.4.2&amp;ISO^INS-C^^20100522152212"));
$extrinsic->setTitle("Source");
$document = new CXDSDocumentEntryAuthor("cla65", "Signature01");
$document->setAuthorPerson(array("00B1041553^MEDECIN4155-B1^PAUL^^^^^^&amp;1.2.250.1.71.4.2.1&amp;ISO^D^^^IDNPS"));
$document->setAuthorSpecialty(array("G15_10/SM26^Médecin - Qualifié en Médecine Générale (SM)^1.2.250.1.213.1.1.4.5"));
$document->setAuthorInstitution(array("Cabinet Dr MEDECIN2154-B1 PAUL^^^^^&amp;1.2.250.1.71.4.2.2&amp;ISO^IDNST^^^00B104155300"));
$document->appendAuthorRole(array(""));
$extrinsic->appendDocumentEntryAuthor($document);
$classification = new CXDSClass("cla66", "Signature01", "urn:oid:1.3.6.1.4.1.19376.1.2.1.1.1");
$classification->setCodingScheme(array("URN"));
$classification->setName("Digital Signature");
$extrinsic->setClass($classification);
$confid = new CXDSConfidentiality("cla67", "Signature01", "N");
$confid->setCodingScheme(array("2.16.840.1.113883.5.25"));
$confid->setName("Normal");
$extrinsic->appendConfidentiality($confid);
$confid2 = new CXDSConfidentiality("cla671", "Signature01", "MASQUE_PS");
$confid2->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
$confid2->setName("Document masqué aux professionnels de santé");
$extrinsic->appendConfidentiality($confid2);
$confid3 = new CXDSConfidentiality("cla672", "Signature01", "INVISIBLE_PATIENT");
$confid3->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
$confid3->setName("Document non visible par le patient");
$extrinsic->appendConfidentiality($confid3);
$event = new CXDSEventCodeList("cla68", "Signature01", "1.2.840.10065.1.12.1.14");
$event->setCodingScheme(array("1.2.840.10065.1.12"));
$event->setName("Source");
$extrinsic->appendEventCodeList($event);
$format = new CXDSFormat("cla69", "Signature01", "http://www.w3.org/2000/09/xmldsig#");
$format->setCodingScheme(array("URN"));
$format->setName("Default Signature Style");
$extrinsic->setFormat($format);
$healt = new CXDSHealthcareFacilityType("cla70", "Signature01", "SA01");
$healt->setCodingScheme(array("1.2.250.1.71.4.2.4"));
$healt->setName("Etablissement Public de Sante");
$extrinsic->setHealthcareFacilityType($healt);
$pratice = new CXDSPracticeSetting("cla71", "Signature01", "ETABLISSEMENT");
$pratice->setCodingScheme(array("1.2.250.1.213.1.1.4.9"));
$pratice->setName("Etablissement de santé");
$extrinsic->setPracticeSetting($pratice);
$type = new CXDSType("cla72", "Signature01", "E1762");
$type->setCodingScheme(array("ASTM"));
$type->setName("Full Document");
$extrinsic->setType($type);
$extrinsic->setPatientId("ei27", "Signature01", "1164485058822081751070^^^&amp;1.2.250.1.213.1.4.2&amp;ISO^INS-C^^20100522152212");
$extrinsic->setUniqueId("ei28", "Signature01", "1.2.250.1.999.1.1.7898.3.20111206120801.0");
$class->appendExtrinsicObject($extrinsic);

/**
 * si relatedDocument/parentDocument/id => association RPLC
 */
//association du registry avec l'extrinsic
$hasmember = new CXDSHasMemberAssociation("association1", "SubmissionSet01", "document01");
$hasmember->setSubmissionSetStatus(array("Original"));
$class->appendAssociation($hasmember);
//association du registry avec la signature
$hasmember = new CXDSHasMemberAssociation("association2", "SubmissionSet01", "Signature01");
$hasmember->setSubmissionSetStatus(array("Original"));
$class->appendAssociation($hasmember);
//association de signature de document
$hasmember = new CXDSHasMemberAssociation("association3", "Signature01", "SubmissionSet01", true);
$hasmember->setSubmissionSetStatus(array("Original"));
$class->appendAssociation($hasmember);

$xml = $class->toXML();

mbTrace($xml->saveXML());