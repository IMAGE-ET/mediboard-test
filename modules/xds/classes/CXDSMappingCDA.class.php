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
  public $id_classification;
  public $id_external;
  public $xpath;
  public $xcn_mediuser;
  public $xon_etablissement;
  public $oid = array();
  public $name_document = array();

  /**
   * Constructeur
   *
   * @param String $cda Document CDA
   */
  function __construct($cda) {
    $this->cda               = $cda;
    $this->id_classification = 0;
    $this->id_external       = 0;
    $this->xcn_mediuser      = CXDSTools::getXCNMediuser();
    $this->xon_etablissement = CXDSTools::getXONetablissement();

    $document_cda = new CCDADomDocument();
    $document_cda->loadXML($cda);

    $this->xpath = new CMbXPath($document_cda);
    $this->xpath->registerNamespace("cda", "urn:hl7-org:v3");
  }

  /**
   * Génère le corps XDS
   *
   * @return CXDSXmlDocument
   */
  function generateXDS() {
    $id_registry  = "2.25.4896.5";
    $id_document  = "2.25.4896.4";
    $id_signature = "2.25.4896.3";

    $class = new CXDSRegistryObjectList();

    //Ajout du lot de soumission
    $registry = $this->createRegistryPackage($id_registry);
    $class->appendRegistryPackage($registry);

    //Ajout d'un document
    $extrinsic = $this->createExtrinsicObject($id_document);
    $class->appendExtrinsicObject($extrinsic);

    //Ajout du document de signature
    $signature = $this->createSignature($id_signature);
    $class->appendExtrinsicObject($signature);

    //Ajout des associations
    $asso1 = $this->createAssociation("association01", $id_registry, $id_document);
    $asso2 = $this->createAssociation("association02", $id_registry, $id_signature);
    $asso3 = $this->createAssociation("association03", $id_signature, $id_registry, true);
    $class->appendAssociation($asso1);
    $class->appendAssociation($asso2);
    $class->appendAssociation($asso3);

    return $class->toXML();
  }

  /**
   * Garde en mémoire le nom des documents
   *
   * @param String $name Nom du document
   *
   * @return void
   */
  function appendNameDocument($name) {
    array_push($this->name_document, $name);
  }

  /**
   * Retourne l'INS présent dans le CDA
   *
   * @return string
   */
  function getIns () {
    $xpath = $this->xpath;

    //Récupération de l'INSA
    $patientRole = "/cda:ClinicalDocument/cda:recordTarget/cda:patientRole";
    $node = $xpath->queryUniqueNode("$patientRole/cda:id[@root='1.2.250.1.213.1.4.1']");
    $comp5 = "INS-A";

    if (!$node) {
      //Récupération de l'INSC
      $node = $xpath->queryUniqueNode("$patientRole/cda:id[@root='1.2.250.1.213.1.4.2']");
      $comp5 = "INS-C";
    }

    //@todo: récupérer la date pour l'INS-C
    $comp4 = $xpath->queryAttributNode(".", $node, "root");
    $comp4 = "&$comp4&ISO";
    $comp1 = $xpath->queryAttributNode(".", $node, "extension");

    $result = "$comp1^^^$comp4^$comp5";
    return $result;
  }

  /**
   * Incrémente l'identifiant des classifications
   *
   * @return void
   */
  function setClaId() {
    $this->id_classification++;
  }

  /**
   * Incrémente l'identifiant des externals
   *
   * @return void
   */
  function setEiId() {
    $this->id_external++;
  }

  /**
   * Création du lot de soumission
   *
   * @param String $id Identifiant du lot de soumission
   *
   * @return CXDSRegistryPackage
   */
  function createRegistryPackage($id) {
    $cla_id                = &$this->id_classification;
    $ei_id                 = &$this->id_external;
    $ins                   = $this->getIns();
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

  /**
   * Création  d'un document
   *
   * @param String $id Identifiant
   *
   * @return CXDSExtrinsicObject
   */
  function createExtrinsicObject($id) {
    $cla_id = &$this->id_classification;
    $ei_id  = &$this->id_external;
    $xpath  = $this->xpath;
    $ins    = $this->getIns();
    $this->appendNameDocument($id);

    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");

    //effectiveTime en UTC
    $time = $xpath->queryAttributNode("/cda:ClinicalDocument/cda:effectiveTime", null, "value");
    $extrinsic->setSlot("creationTime", array(CXDSTools::getTimeUtc($time)));

    //languageCode
    $languageCode = $xpath->queryAttributNode("/cda:ClinicalDocument/cda:languageCode", null, "code");
    $extrinsic->setSlot("languageCode", array($languageCode));

    //legalAuthenticator XCN
    $node = $xpath->queryUniqueNode("/cda:ClinicalDocument/cda:legalAuthenticator/cda:assignedEntity");
    $legalAuthenticator = $this->getPerson($node);
    $extrinsic->setSlot("legalAuthenticator", array($legalAuthenticator));

    //documentationOf/serviceEvent/effectiveTime/low en UTC
    $service_event = "/cda:ClinicalDocument/cda:documentationOf/cda:serviceEvent";
    $serviceStart = $xpath->queryAttributNode("$service_event/cda:effectiveTime/cda:low", null, "value");
    $extrinsic->setSlot("serviceStartTime", array(CXDSTools::getTimeUtc($serviceStart)));

    //documentationOf/serviceEvent/effectiveTime/high en UTC
    $serviceStop = $xpath->queryAttributNode("$service_event/cda:effectiveTime/cda:high", null, "value");
    $extrinsic->setSlot("serviceStopTime", array(CXDSTools::getTimeUtc($serviceStop)));

    //recordTarget/patientRole/id
    $extrinsic->setSlot("sourcePatientId", array($ins));

    //title
    $title = $xpath->queryTextNode("/cda:ClinicalDocument/cda:title");
    $extrinsic->setTitle($title);

    //Auteur du document
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();

    //author/assignedAuthor
    $assigned_author = "/cda:ClinicalDocument/cda:author/cda:assignedAuthor";
    $node = $xpath->queryUniqueNode($assigned_author);
    $author = $this->getPerson($node);
    $document->setAuthorPerson(array($author));

    //author/assignedAuthor/code
    $node = $xpath->queryUniqueNode("$assigned_author/cda:code");
    $speciality = $this->getSpeciality($node);
    $document->setAuthorSpecialty(array($speciality));

    //author/assignedAuthor/representedOrganization - si absent, ne pas renseigner
    //si nom pas présent - champ vide
    //si id nullflavor alors 6-7-10 vide
    $node = $xpath->queryUniqueNode("$assigned_author/cda:representedOrganization");
    if (!$xpath->queryAttributNode(".", $node, "nullFlavor")) {
      $institution = $this->getOrganisation($node);
      $document->setAuthorInstitution(array($institution));
    }
    $extrinsic->appendDocumentEntryAuthor($document);

    //confidentialityCode
    $confidentiality_code = "/cda:ClinicalDocument/cda:confidentialityCode";
    $confidentiality = $xpath->queryAttributNode($confidentiality_code, null, "code");
    $confid = new CXDSConfidentiality("cla$cla_id", $id, $confidentiality);
    $this->setClaId();
    $confidentialityCode = $xpath->queryAttributNode($confidentiality_code, null, "codeSystem");
    $confid->setCodingScheme(array($confidentialityCode));
    $confidentialityName = $xpath->queryAttributNode($confidentiality_code, null, "displayName");
    $confid->setName($confidentialityName);
    $extrinsic->appendConfidentiality($confid);

    //documentationOf/serviceEvent/code - table de correspondance
    $eventCode = $xpath->queryAttributNode("$service_event/cda:code", null, "code");
    $eventName = $xpath->queryAttributNode("$service_event/cda:code", null, "displayName");
    $eventSystem = $xpath->queryAttributNode("$service_event/cda:code", null, "codeSystem");
    $event = new CXDSEventCodeList("cla$cla_id", $id, $eventCode);
    $this->setClaId();
    $event->setCodingScheme(array($eventSystem));
    $event->setName($eventName);
    $extrinsic->appendEventCodeList($event);

    //En fonction d'un corps structuré
    $type = $xpath->queryAttributNode("/cda:ClinicalDocument/cda:component/cda:nonXMLBody/cda:text", null, "mediaType");
    $codingScheme = "";
    $name = "";
    $formatCode = "";
    if ($type) {
      $correspondance = new DOMDocument();
      $correspondance->load("modules/xds/resources/Document_non_structure.xml");
      $correspondanceXpath = new CMbXPath($correspondance);
      $node         = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='$type']");
      $codingScheme = $correspondanceXpath->queryAttributNode("./xds", $node, "codingScheme");
      $name         = $correspondanceXpath->queryAttributNode("./mediaType", $node, "contenu");
      $formatCode   = $correspondanceXpath->queryAttributNode("./xds", $node, "formatCode");
    }
    else {
      $correspondance = new DOMDocument();
      $correspondance->load("modules/xds/resources/Document_structure.xml");
      $correspondanceXpath = new CMbXPath($correspondance);
      $type = $xpath->query("/cda:ClinicalDocument/cda:templateId");
      foreach ($type as $_type) {
        $type_id = $correspondanceXpath->queryAttributNode(".", $_type, "root");
        $node = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='$type_id']");
        if (!$node) {
          continue;
        }
        $codingScheme = $correspondanceXpath->queryAttributNode("./xds", $node, "codingScheme");
        $name         = $correspondanceXpath->queryAttributNode("./mediaType", $node, "contenu");
        $formatCode   = $correspondanceXpath->queryAttributNode("./xds", $node, "formatCode");
      }
      if (!$codingScheme) {
        $node = $correspondanceXpath->queryUniqueNode("/mappage/line[@id='*']");
        $codingScheme = $correspondanceXpath->queryAttributNode("./xds", $node, "codingScheme");
        $name         = $correspondanceXpath->queryAttributNode("./mediaType", $node, "contenu");
        $formatCode   = $correspondanceXpath->queryAttributNode("./xds", $node, "formatCode");;
      }
    }

    $format = new CXDSFormat("cla$cla_id", $id, $formatCode);
    $this->setClaId();
    $format->setCodingScheme(array($codingScheme));
    $format->setName($name);
    $extrinsic->setFormat($format);

    //componentOf/encompassingEncounter/location/healthCareFacility/code
    $healtcare_facility = "/cda:ClinicalDocument/cda:componentOf/cda:encompassingEncounter/cda:location/cda:healthCareFacility";
    $healtcare     = $xpath->queryAttributNode("$healtcare_facility/cda:code", null, "code");
    $healtcareName = $xpath->queryAttributNode("$healtcare_facility/cda:code", null, "displayName");
    $healtcareCode = $xpath->queryAttributNode("$healtcare_facility/cda:code", null, "codeSystem");
    $healt         = new CXDSHealthcareFacilityType("cla$cla_id", $id, $healtcare);
    $this->setClaId();
    $healt    ->setCodingScheme(array($healtcareCode));
    $healt    ->setName($healtcareName);
    $extrinsic->setHealthcareFacilityType($healt);

    //documentationOf/serviceEvent/performer/assignedEntity/representedOrganization/standardIndustryClassCode
    $standard_industry = "$service_event/cda:performer/cda:assignedEntity/cda:representedOrganization/cda:standardIndustryClassCode";
    $prac       = $xpath->queryAttributNode($standard_industry, null, "code");
    $pracName   = $xpath->queryAttributNode($standard_industry, null, "displayName");
    $pracSystem = $xpath->queryAttributNode($standard_industry, null, "codeSystem");
    $pratice    = new CXDSPracticeSetting("cla$cla_id", $id, $prac);
    $this->setClaId();
    $pratice  ->setCodingScheme(array($pracSystem));
    $pratice  ->setName($pracName);
    $extrinsic->setPracticeSetting($pratice);

    //code
    $code_xpath = "/cda:ClinicalDocument/cda:code";
    $code       = $xpath->queryAttributNode($code_xpath, null, "code");
    $codeName   = $xpath->queryAttributNode($code_xpath, null, "displayName");
    $codeSystem = $xpath->queryAttributNode($code_xpath, null, "codeSystem");
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
    //@todo : voir pour extension
    $id_xpath  = "/cda:ClinicalDocument/cda:id";
    $root      = $xpath->queryAttributNode($id_xpath, null, "root");
    $extension = $xpath->queryAttributNode($id_xpath, null, "extension");
    $unique_id = $this->getUniqueId($root, $extension);
    $this->oid["extrinsic"] = $unique_id;
    $extrinsic->setUniqueId("ei$ei_id", $id, $unique_id);
    $this->setEiId();

    return $extrinsic;
  }

  /**
   * Création du document de la signature
   *
   * @param String $id Identifiant
   *
   * @return CXDSExtrinsicObject
   */
  function createSignature($id) {
    $cla_id = &$this->id_classification;
    $ei_id  = &$this->id_external;

    //Création du document
    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");
    $extrinsic->setSlot("creationTime"      , array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("languageCode"      , array("art"));
    $extrinsic->setSlot("legalAuthenticator", array($this->xcn_mediuser));
    $extrinsic->setSlot("serviceStartTime"  , array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("serviceStopTime"   , array(CXDSTools::getTimeUtc()));

    //@todo: a faire
    //patientId du lot de submission
    $extrinsic->setSlot("sourcePatientId", array("0887177831579788841339^^^&1.2.250.1.213.1.4.2&ISO^INS-C"));
    $extrinsic->setTitle("Source");

    //@todo: a faire
    //identique à celui qui envoie
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();
    $document->setAuthorPerson(array($this->xcn_mediuser));
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

  /**
   * Création des associations
   *
   * @param String $id     Identifiant
   * @param String $source Source
   * @param String $target Cible
   * @param bool   $sign   Association de type signature
   *
   * @return CXDSHasMemberAssociation
   */
  function createAssociation($id, $source, $target, $sign = false) {
    //@todo: a faire
    /**
     * si relatedDocument/parentDocument/id => association RPLC
     */
    $hasmember = new CXDSHasMemberAssociation($id, $source, $target, $sign);
    if (!$sign) {
      $hasmember->setSubmissionSetStatus(array("Original"));
    }

    return $hasmember;
  }

  /**
   * Concatène le root et l'extension
   *
   * @param String $root      Racine
   * @param String $extension Extension
   *
   * @return string
   */
  function getUniqueId($root, $extension) {
    if ($extension) {
      $extension = "^$extension";
    }
    return $root.$extension;
  }

  /**
   * Retourne l'oid, l'identifiant et le nom d'une classe selon le code
   *
   * @param String $code Code
   *
   * @return array
   */
  function getClassCodeFromCode($code) {
    $xml_jv_x04 = new DOMDocument();
    $xml_jv_x04->load("modules/xds/resources/jeux_de_valeurs/ASIP-SANTE_X04.xml");

    $xpath_x04 = new CMbXPath($xml_jv_x04);
    $node      = $xpath_x04->queryUniqueNode("/jeuxValeurs/line[@id='$code']");
    $id        = $node->getAttribute("name");

    $xml_classCode = new DOMDocument();
    $xml_classCode->load("modules/xds/resources/jeux_de_valeurs/ASIP-SANTE_classCode.xml");

    $xpath_classCode = new CMbXPath($xml_classCode);
    $node            = $xpath_classCode->queryUniqueNode("/jeuxValeurs/line[@id='$id']");
    $oid             = $node->getAttribute("oid");
    $name            = $node->getAttribute("name");

    return array($id, $oid, $name);
  }

  /**
   * Retourne la person
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getPerson($node) {
    $xpath = $this->xpath;
    $comp10 = "D";
    $id = $node->getElementsByTagName("id")->item(0);

    $person = $node->getElementsByTagName("assignedPerson");
    $person = $person->item(0);
    /** @var DOMElement $person */
    $comp2 = $person->getElementsByTagName("family")->item(0)->nodeValue;
    $comp3 = $person->getElementsByTagName("given")->item(0)->nodeValue;
    $comp1 = $xpath->queryAttributNode(".", $id, "extension");
    $comp9 = $xpath->queryAttributNode(".", $id, "root");
    $comp13 = $this->getTypeId($comp1);
    $result = "$comp1^$comp2^$comp3^^^^^^&$comp9&ISO^$comp10^^^$comp13";
    return $result;
  }

  /**
   * Retourne le type d'id passé en paramètre
   *
   * @param String $id String
   *
   * @return string
   */
  function getTypeId($id) {
    $result = "IDNPS";
    if (strpos("/", $id) !== false) {
      $result = "EI";
    }
    if (strlen($id) === 22) {
      $result = "INS-C";
    }
    //todo : Faire l'INS-A
    return $result;
  }

  /**
   * Retourne l'organisation
   *
   * @param DOMElement $node DOMElement
   *
   * @return String
   */
  function getOrganisation($node) {
    $xpath = $this->xpath;
    $comp1  = "";
    $comp6  = "";
    $comp7  = "";
    $comp10 = "";
    $id = $node->getElementsByTagName("id")->item(0);
    $name = $node->getElementsByTagName("name")->item(0);

    if (!$xpath->queryAttributNode(".", $name, "nullFlavor")) {
      $comp1 = $name->nodeValue;
    }

    if (!$xpath->queryAttributNode(".", $id, "nullFlavor")) {
      $comp7 = "IDNST";
      $comp6 = $xpath->queryAttributNode(".", $id, "root");
      $comp6 = "&$comp6&ISO";
      $comp10 = $xpath->queryAttributNode(".", $id, "extension");
    }

    return "$comp1^^^^^$comp6^$comp7^^^$comp10";
  }

  /**
   * Retourne la speciality
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getSpeciality($node) {
    $xpath = $this->xpath;
    $comp1 = $xpath->queryAttributNode(".", $node, "code");
    $comp2 = $xpath->queryAttributNode(".", $node, "displayName");
    $comp3 = $xpath->queryAttributNode(".", $node, "codeSystem");
    return "$comp1^$comp2^$comp3";
  }
}