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
 
/**
 * Créé le XDS en fonction du CDA.
 */
class CXDSMappingCDA {

  public $cda;
  public $name_submission;
  public $name_document = array();
  public $id_classification;
  public $id_external;
  public $xpath;
  public $xcn_mediuser;
  public $xon_etablissement;
  public $oid = array();

  function __construct($cda) {
    $this->cda = $cda;
    $document_cda = new CCDADomDocument();
    $document_cda->loadXML($this->cda);
    $this->xpath = new CXDSXPath($document_cda);
    $this->xpath->registerNamespace("cda", "urn:hl7-org:v3");
    $this->id_classification = 0;
    $this->id_external = 0;
    $this->xcn_mediuser = CXDSTools::getXCNMediuser();
    $this->xon_etablissement = CXDSTools::getXONetablissement();
  }

  function generateXDS() {
    $id_registry  = "2.25.4896.5";
    $id_document  = "2.25.4896.4";
    $id_signature = "2.25.4896.3";

    $class = new CXDSRegistryObjectList();

    $registry = $this->createRegistryPackage($id_registry);
    $class->appendRegistryPackage($registry);
    $extrinsic = $this->createExtrinsicObject($id_document);
    $class->appendExtrinsicObject($extrinsic);

    $signature = $this->createSignature($id_signature);
    $class->appendExtrinsicObject($signature);

    $asso1 = $this->createAssociation("association01", $id_registry, $id_document);
    $asso2 = $this->createAssociation("association02", $id_registry, $id_signature);
    $asso3 = $this->createAssociation("association03", $id_signature, $id_registry, true);
    $class->appendAssociation($asso1);
    $class->appendAssociation($asso2);
    $class->appendAssociation($asso3);

    return $class->toXML();
  }

  function appendNameDocument($name) {
    array_push($this->name_document, $name);
  }

  function getIns () {
    $xpath = $this->xpath;
    //Récupération de l'insC ou de l'insA
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:id[@root='1.2.250.1.213.1.4.1']");
    if (!$node) {
      $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:id[@root='1.2.250.1.213.1.4.2']");
    }
    return $xpath->getIns($node);
  }

  function setClaId() {
    $this->id_classification++;
  }

  function setEiId() {
    $this->id_external++;
  }

  function createRegistryPackage($id) {
    $cla_id = &$this->id_classification;
    $ei_id = &$this->id_external;
    $ins = $this->getIns();
    $this->name_submission = $id;
    $registry = new CXDSRegistryPackage($id);
    //date de soumission
    $registry->setSubmissionTime(array(CXDSTools::getTimeUtc()));
    //PS qui envoie le document
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id, true);
    $this->setClaId();
    $document->setAuthorPerson(array($this->xcn_mediuser));
    //@todo: a faire
    $document->setAuthorSpecialty(array("G15_10/SM26^Médecin - Qualifié en Médecine Générale (SM)^1.2.250.1.213.1.1.4.5"));
    //Institution qui envoie le document
    $document->setAuthorInstitution(array($this->xon_etablissement));
    $registry->appendDocumentEntryAuthor($document);
    //type d'activité pour lequel on envoie les documents
    //@todo: a faire
    $content = new CXDSContentType("cla$cla_id", $id, "04");
    $this->setClaId();
    $content->setCodingScheme(array("1.2.250.1.213.2.2"));
    $content->setContentTypeCodeDisplayName("Hospitalisation de jour");
    $registry->setContentType($content);
    //spécification d'un Submissionset ou d'un folder, ici submissionset
    $registry->setSubmissionSet("cla$cla_id", $id, false);
    $this->setClaId();
    //patient du document
    $registry->setPatientId("ei$ei_id", $id, $ins);
    $this->setEiId();
    //OID de l'instance serveur
    //@todo: a faire
    $registry->setSourceId("ei$ei_id", $id, "1.2.250.1.999.1.1.7898");
    $this->setEiId();
    //OID unique  concat(oid.objet+id.doc+time)
    //@todo: a faire
    $this->oid["lot"] = "2.25.43911231647312014016.1";
    $registry->setUniqueId("ei$ei_id", $id, $this->oid["lot"]);
    $this->setEiId();

    return $registry;
  }

  function createExtrinsicObject($id) {
    $cla_id = &$this->id_classification;
    $ei_id = &$this->id_external;
    $this->appendNameDocument($id);
    $xpath = $this->xpath;
    $ins = $this->getIns();

    //table de correspondance
    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");
    //effectiveTime en UTC
    $time = $xpath->getNodeValue("/cda:ClinicalDocument/cda:effectiveTime/@value");
    $extrinsic->setSlot("creationTime", array(CXDSTools::getTimeUtc($time)));
    //languageCode
    $languageCode = $xpath->getNodeValue("/cda:ClinicalDocument/cda:languageCode/@code");
    $extrinsic->setSlot("languageCode", array($languageCode));
    //legalAuthenticator XCN
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:legalAuthenticator/cda:assignedEntity");
    $legalAuthenticator = $xpath->getPerson($node);
    $extrinsic->setSlot("legalAuthenticator", array($legalAuthenticator));
    //documentationOf/serviceEvent/effectiveTime/low en UTC
    $serviceStart = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:effectiveTime/cda:low/@value");
    $extrinsic->setSlot("serviceStartTime", array(CXDSTools::getTimeUtc($serviceStart)));
    //documentationOf/serviceEvent/effectiveTime/high en UTC
    $serviceStop = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:effectiveTime/cda:high/@value");
    $extrinsic->setSlot("serviceStopTime", array(CXDSTools::getTimeUtc($serviceStop)));
    //recordTarget/patientRole/id
    $extrinsic->setSlot("sourcePatientId", array($ins));
    //title
    $title = $xpath->getNodeValue("/cda:ClinicalDocument/cda:title");
    $extrinsic->setTitle($title);

    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();
    //author/assignedAuthor
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:author/cda:assignedAuthor");
    $author = $xpath->getPerson($node);
    $document->setAuthorPerson(array($author));
    //author/assignedAuthor/code
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:author/cda:assignedAuthor/cda:code");
    $speciality = $xpath->getSpeciality($node);
    $document->setAuthorSpecialty(array($speciality));
    //author/assignedAuthor/representedOrganization - si absent, ne pas renseigner
    //si nom pas présent - champ vide
    //si id nullflavor alors 6-7-10 vide
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:author/cda:assignedAuthor/cda:representedOrganization");
    if (!$xpath->isNullFlavor($node)) {
      $institution = $xpath->getOrganisation($node);
      $document->setAuthorInstitution(array($institution));
    }
        /** Le role => author/functionCode*/
    $extrinsic->appendDocumentEntryAuthor($document);

    //confidentialityCode
    $confidentiality = $xpath->getNodeValue("/cda:ClinicalDocument/cda:confidentialityCode/@code");
    $confid = new CXDSConfidentiality("cla$cla_id", $id, $confidentiality);
    $this->setClaId();
    $confidentialityCode = $xpath->getNodeValue("/cda:ClinicalDocument/cda:confidentialityCode/@codeSystem");
    $confid->setCodingScheme(array($confidentialityCode));
    $confidentialityName = $xpath->getNodeValue("/cda:ClinicalDocument/cda:confidentialityCode/@displayName");
    $confid->setName($confidentialityName);
    $extrinsic->appendConfidentiality($confid);

    //documentationOf/serviceEvent/code - table de correspondance
    $eventCode = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:code/@code");
    $eventName = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:code/@displayName");
    $eventSystem = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:code/@codeSystem");
    $event = new CXDSEventCodeList("cla$cla_id", $id, $eventCode);
    $this->setClaId();
    $event->setCodingScheme(array($eventSystem));
    $event->setName($eventName);
    $extrinsic->appendEventCodeList($event);

    //En fonction d'un corps structuré
    $type = $xpath->getNodeValue("/cda:ClinicalDocument/cda:component/cda:nonXMLBody/cda:text/@mediaType");
    $codingScheme = "";
    $name = "";
    $formatCode = "";
    if ($type) {
      $correspondance = new DOMDocument();
      $correspondance->load("modules/xds/resources/Document_non_structure.xml");
      $correspondanceXpath = new CXDSXPath($correspondance);
      $node         = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='$type']");
      $codingScheme = $correspondanceXpath->getCodingScheme($node);
      $name         = $correspondanceXpath->getContenu($node);
      $formatCode   = $correspondanceXpath->getformatCode($node);
    }
    else {
      $correspondance = new DOMDocument();
      $correspondance->load("modules/xds/resources/Document_structure.xml");
      $correspondanceXpath = new CXDSXPath($correspondance);
      $type = $xpath->query("/cda:ClinicalDocument/cda:templateId");
      foreach ($type as $_type) {
        $type_id = $correspondanceXpath->getValueAttributNode($_type, "root");
        $node = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='$type_id']");
        if (!$node) {

          continue;
        }
        $codingScheme = $correspondanceXpath->getCodingScheme($node);
        $name         = $correspondanceXpath->getContenu($node);
        $formatCode   = $correspondanceXpath->getformatCode($node);
      }
      if (!$codingScheme) {
        $node = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='*']");
        $codingScheme = $correspondanceXpath->getCodingScheme($node);
        $name         = $correspondanceXpath->getContenu($node);
        $formatCode   = $correspondanceXpath->getformatCode($node);
      }
    }

    $format = new CXDSFormat("cla$cla_id", $id, $formatCode);
    $this->setClaId();
    $format->setCodingScheme(array($codingScheme));
    $format->setName($name);
    $extrinsic->setFormat($format);

    //componentOf/encompassingEncounter/location/healthCareFacility/code
    $healtcare     = $xpath->getNodeValue("/cda:ClinicalDocument/cda:componentOf/cda:encompassingEncounter/cda:location/cda:healthCareFacility/cda:code/@code");
    $healtcareName = $xpath->getNodeValue("/cda:ClinicalDocument/cda:componentOf/cda:encompassingEncounter/cda:location/cda:healthCareFacility/cda:code/@displayName");
    $healtcareCode = $xpath->getNodeValue("/cda:ClinicalDocument/cda:componentOf/cda:encompassingEncounter/cda:location/cda:healthCareFacility/cda:code/@codeSystem");
    $healt         = new CXDSHealthcareFacilityType("cla$cla_id", $id, $healtcare);
    $this->setClaId();
    $healt    ->setCodingScheme(array($healtcareCode));
    $healt    ->setName($healtcareName);
    $extrinsic->setHealthcareFacilityType($healt);

    //documentationOf/serviceEvent/performer/assignedEntity/representedOrganization/standardIndustryClassCode
    $prac       = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:performer/cda:assignedEntity/cda:representedOrganization/cda:standardIndustryClassCode/@code");
    $pracName   = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:performer/cda:assignedEntity/cda:representedOrganization/cda:standardIndustryClassCode/@displayName");
    $pracSystem = $xpath->getNodeValue("/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent/cda:performer/cda:assignedEntity/cda:representedOrganization/cda:standardIndustryClassCode/@codeSystem");
    $pratice    = new CXDSPracticeSetting("cla$cla_id", $id, $prac);
    $this->setClaId();
    $pratice  ->setCodingScheme(array($pracSystem));
    $pratice  ->setName($pracName);
    $extrinsic->setPracticeSetting($pratice);

    //code
    $code       = $xpath->getNodeValue("/cda:ClinicalDocument/cda:code/@code");
    $codeName   = $xpath->getNodeValue("/cda:ClinicalDocument/cda:code/@displayName");
    $codeSystem = $xpath->getNodeValue("/cda:ClinicalDocument/cda:code/@codeSystem");
    $type       = new CXDSType("cla$cla_id", $id, $code);
    $this->setClaId();
    $type     ->setCodingScheme(array($codeSystem));
    $type     ->setName($codeName);
    $extrinsic->setType($type);

    //code - table de correspondance X04
    list($classCode, $oid, $name) = $this->getClassCodeFromCode($code);
    $classification = new CXDSClass("cla$cla_id", $id, $classCode);
    $this->setClaId();
    $classification->setCodingScheme(array($oid));
    $classification->setName($name);
    $extrinsic     ->setClass($classification);

    //recordTarget/patientRole/id
    $extrinsic->setPatientId("ei$ei_id", $id, $ins);
    $this->setEiId();

    //id - root+extension
    $root = $xpath->getNodeValue("/cda:ClinicalDocument/cda:id/@root");
    $extension = $xpath->getNodeValue("/cda:ClinicalDocument/cda:id/@extension");
    $unique_id = $this->getUniqueId($root, $extension);
    $this->oid["extrinsic"] = $unique_id;
    $extrinsic->setUniqueId("ei$ei_id", $id, $unique_id);
    $this->setEiId();

    return $extrinsic;
  }

  function createSignature($id) {
    $cla_id = &$this->id_classification;
    $ei_id = &$this->id_external;

    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");
    $extrinsic->setSlot("creationTime", array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("languageCode", array("art"));

    $extrinsic->setSlot("legalAuthenticator", array($this->xcn_mediuser));
    $extrinsic->setSlot("serviceStartTime", array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("serviceStopTime", array(CXDSTools::getTimeUtc()));
    //@todo: a faire
    //patientId du lot de submission
    $extrinsic->setSlot("sourcePatientId", array("0887177831579788841339^^^&1.2.250.1.213.1.4.2&ISO^INS-C"));
    $extrinsic->setTitle("Source");

    //identique à celui qui envoie
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();
    $document->setAuthorPerson(array($this->xcn_mediuser));
    //@todo: a faire
    $document->setAuthorSpecialty(array("G15_10/SM26^Médecin - Qualifié en Médecine Générale (SM)^1.2.250.1.213.1.1.4.5"));
    $document->setAuthorInstitution(array($this->xon_etablissement));

    $extrinsic->appendDocumentEntryAuthor($document);

    $classification = new CXDSClass("cla$cla_id", $id, "urn:oid:1.3.6.1.4.1.19376.1.2.1.1.1");
    $this->setClaId();
    $classification->setCodingScheme(array("URN"));
    $classification->setName("Digital Signature");
    $extrinsic->setClass($classification);

    $confid = new CXDSConfidentiality("cla$cla_id", $id, "N");
    $this->setClaId();
    $confid->setCodingScheme(array("2.16.840.1.113883.5.25"));
    $confid->setName("Normal");
    $extrinsic->appendConfidentiality($confid);

    $confid2 = new CXDSConfidentiality("cla$cla_id", $id, "MASQUE_PS");
    $this->setClaId();
    $confid2->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
    $confid2->setName("Document masqué aux professionnels de santé");
    $extrinsic->appendConfidentiality($confid2);

    $confid3 = new CXDSConfidentiality("cla$cla_id", $id, "INVISIBLE_PATIENT");
    $this->setClaId();
    $confid3->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
    $confid3->setName("Document non visible par le patient");
    $extrinsic->appendConfidentiality($confid3);

    $event = new CXDSEventCodeList("cla$cla_id", $id, "1.2.840.10065.1.12.1.14");
    $this->setClaId();
    $event->setCodingScheme(array("1.2.840.10065.1.12"));
    $event->setName("Source");
    $extrinsic->appendEventCodeList($event);

    $format = new CXDSFormat("cla$cla_id", $id, "http://www.w3.org/2000/09/xmldsig#");
    $this->setClaId();
    $format->setCodingScheme(array("URN"));
    $format->setName("Default Signature Style");
    $extrinsic->setFormat($format);

    //@todo: a faire
    $healt = new CXDSHealthcareFacilityType("cla$cla_id", $id, "SA01");
    $this->setClaId();
    $healt->setCodingScheme(array("1.2.250.1.71.4.2.4"));
    $healt->setName("Etablissement Public de santé");
    $extrinsic->setHealthcareFacilityType($healt);

    //@todo: a faire
    $pratice = new CXDSPracticeSetting("cla$cla_id", $id, "ETABLISSEMENT");
    $this->setClaId();
    $pratice->setCodingScheme(array("1.2.250.1.213.1.1.4.9"));
    $pratice->setName("Etablissement de santé");
    $extrinsic->setPracticeSetting($pratice);

    $type = new CXDSType("cla$cla_id", $id, "E1762");
    $this->setClaId();
    $type->setCodingScheme(array("ASTM"));
    $type->setName("Full Document");
    $extrinsic->setType($type);

    //@todo: a faire
    //identique au lot de submission
    $extrinsic->setPatientId("ei$ei_id", $id, "0887177831579788841339^^^&1.2.250.1.213.1.4.2&ISO^INS-C^^20100522152212");
    $this->setEiId();
    //@todo: a faire
    $this->oid["signature"] = "1.2.250.1.999.1.1.7898.3.20111206120801.0";
    $extrinsic->setUniqueId("ei$ei_id", $id, "1.2.250.1.999.1.1.7898.3.20111206120801.0");
    $this->setEiId();

    return $extrinsic;
  }

  function createAssociation($id, $source, $target, $sign = false) {
    /**
     * si relatedDocument/parentDocument/id => association RPLC
     */
    $hasmember = new CXDSHasMemberAssociation($id, $source, $target, $sign);
    if (!$sign) {
      $hasmember->setSubmissionSetStatus(array("Original"));
    }

    return $hasmember;
  }

  function getUniqueId($root, $extension) {
    if ($extension) {
      $extension = "^$extension";
    }
    return $root.$extension;
  }

  function getClassCodeFromCode($code) {
    $xml_jv_x04 = new DOMDocument();
    $xml_jv_x04->load("modules/xds/resources/jeux_de_valeurs/ASIP-SANTE_X04.xml");
    $xpath_x04 = new CMbXPath($xml_jv_x04);
    $node = $xpath_x04->queryUniqueNode("/jeuxValeurs/line[@id='$code']");
    $id = $node->getAttribute("name");

    $xml_classCode = new DOMDocument();
    $xml_classCode->load("modules/xds/resources/jeux_de_valeurs/ASIP-SANTE_classCode.xml");
    $xpath_classCode = new CMbXPath($xml_classCode);
    $node = $xpath_classCode->queryUniqueNode("/jeuxValeurs/line[@id='$id']");
    $oid = $node->getAttribute("oid");
    $name = $node->getAttribute("name");

    return array($id, $oid, $name);
  }
}
