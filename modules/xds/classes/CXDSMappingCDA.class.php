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

  public $factory;
  public $name_submission;
  public $id_classification;
  public $id_external;
  public $xpath;
  public $xcn_mediuser;
  public $xon_etablissement;
  public $ins_patient;
  public $practice_setting;
  public $health_care_facility;
  public $uuid          = array();
  public $oid           = array();
  public $name_document = array();

  /**
   * Constructeur
   *
   * @param CCDAFactory $factory CDA factory
   */
  function __construct($factory) {
    $this->factory           = $factory;
    $this->id_classification = 0;
    $this->id_external       = 0;
    $this->xcn_mediuser      = CXDSTools::getXCNMediuser();
    $this->xon_etablissement = CXDSTools::getXONetablissement();

    $this->xpath = new CMbXPath($factory->dom_cda);
    $this->xpath->registerNamespace("cda", "urn:hl7-org:v3");

    $this->ins_patient = $this->getIns();
    $uuid = CMbSecurity::generateUUID();
    $this->uuid["registry"]  = $uuid."1";
    $this->uuid["extrinsic"] = $uuid."2";
    $this->uuid["signature"] = $uuid."3";
  }

  /**
   * Génération de la requête XDS57
   *
   * @param String $uuid          Identifiant du document dans le registre
   * @param Bool   $archivage     Archiage du document
   * @param Bool   $depublication Depublication du document
   *
   * @return CXDSXmlDocument
   */
  function generateXDS57($uuid, $archivage = null, $depublication = null) {
    $id_registry  = $this->uuid["registry"];

    $class = new CXDSRegistryObjectList();

    //Ajout du lot de soumission
    $registry = $this->createRegistryPackage($id_registry);
    $class->appendRegistryPackage($registry);
    $statusType = "";

    if ($depublication) {
      $statusType = "Deleted";
    }
    if ($archivage) {
      $statusType = "Archived";
    }

    $asso = new CXDSAssociation("association01", $id_registry, $uuid, "urn:ihe:iti:2010:AssociationType:UpdateAvailabilityStatus");
    $asso->setSlot("OriginalStatus", array("urn:oasis:names:tc:ebxml-regrep:StatusType:Approved"));
    $asso->setSlot("NewStatus", array("urn:asip:ci-sis:2010:StatusType:$statusType"));
    $class->appendAssociation($asso);

    return $class->toXML();
  }

  /**
   * Génère le corps XDS
   *
   * @return CXDSXmlDocument
   */
  function generateXDS41() {
    $id_registry  = $this->uuid["registry"];
    $id_document  = $this->uuid["extrinsic"];
    $id_signature = $this->uuid["signature"];

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
    $patient = $this->factory->patient;
    //@todo: faire l'INSA

    $comp5 = "INS-C";
    $comp4 = "1.2.250.1.213.1.4.2";
    $comp4 = "&$comp4&ISO";
    $comp1 = $patient->INSC;

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
    $ins                   = $this->ins_patient;
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
    //@todo : a faire
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
    $oid_instance = CMbOID::getOIDOfInstance($registry);
    $registry->setSourceId("ei$ei_id", $id, $oid_instance);
    $this->setEiId();

    //OID unique @todo : voir pour id du registre
    $oid = CMbOID::getOIDFromClass($registry);
    $this->oid["lot"] = $oid.".".CXDSTools::getTimeUtc();
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
    $factory = $this->factory;
    $cla_id = &$this->id_classification;
    $ei_id  = &$this->id_external;
    $xpath  = $this->xpath;
    $ins    = $this->ins_patient;
    $this->appendNameDocument($id);

    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");

    //effectiveTime en UTC
    $extrinsic->setSlot("creationTime", array(CXDSTools::getTimeUtc($factory->date_creation)));

    //languageCode
    $extrinsic->setSlot("languageCode", array($factory->langage));

    //legalAuthenticator XCN
    $legalAuthenticator = $this->getPerson($factory->practicien);
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
    $extrinsic->setTitle($factory->nom);

    //Auteur du document
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();

    //author/assignedAuthor
    $author = $this->getPerson($factory->practicien);
    $document->setAuthorPerson(array($author));

    //author/assignedAuthor/code
    $assigned_author = "/cda:ClinicalDocument/cda:author/cda:assignedAuthor";
    $node = $xpath->queryUniqueNode("$assigned_author/cda:code");
    if ($node) {
      $speciality = $this->getSpeciality($node);
      $document->setAuthorSpecialty(array($speciality));
    }

    //author/assignedAuthor/representedOrganization - si absent, ne pas renseigner
    //si nom pas présent - champ vide
    //si id nullflavor alors 6-7-10 vide
    $node = $xpath->queryUniqueNode("$assigned_author/cda:representedOrganization");

    if ($node && !$xpath->queryAttributNode(".", $node, "nullFlavor")) {
      $institution = $this->getOrganisation($node);
      $document->setAuthorInstitution(array($institution));
    }
    $extrinsic->appendDocumentEntryAuthor($document);

    //confidentialityCode
    $confidentialite = $factory->confidentialite;
    $confid = new CXDSConfidentiality("cla$cla_id", $id, $confidentialite["code"]);
    $confid->setCodingScheme(array($confidentialite["codeSystem"]));
    $confid->setName($confidentialite["displayName"]);
    $extrinsic->appendConfidentiality($confid);

    //documentationOf/serviceEvent/code - table de correspondance
    $eventCode = $xpath->queryAttributNode("$service_event/cda:code", null, "code");
    $eventName = $xpath->queryAttributNode("$service_event/cda:code", null, "displayName");
    $eventSystem = $xpath->queryAttributNode("$service_event/cda:code", null, "codeSystem");
    if ($eventCode) {
      $event = new CXDSEventCodeList("cla$cla_id", $id, $eventCode);
      $this->setClaId();
      $event->setCodingScheme(array($eventSystem));
      $event->setName($eventName);
      $extrinsic->appendEventCodeList($event);
    }

    //En fonction d'un corps structuré
    $type = $factory->mediaType;
    $codingScheme = "";
    $name = "";
    $formatCode = "";
    if ($type) {
      $entry = CXDSTools::loadEntryDocument("Document_non_structure.xml", $type);
      $codingScheme = $entry["codingScheme"];
      $name         = $entry["contenu"];
      $formatCode   = $entry["formatCode"];
    }
    else {
      $correspondance = new DOMDocument();
      $correspondance->load("modules/xds/resources/Document_structure.xml");
      $correspondanceXpath = new CMbXPath($correspondance);
      $type = $factory->templateId;
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
    $healtcare     = $factory->healt_care;
    $healt         = new CXDSHealthcareFacilityType("cla$cla_id", $id, $healtcare["code"]);
    $this->setClaId();
    $healt    ->setCodingScheme(array($healtcare["codeSystem"]));
    $healt    ->setName($healtcare["displayName"]);
    $extrinsic->setHealthcareFacilityType($healt);
    $this->health_care_facility = $healt;

    //documentationOf/serviceEvent/performer/assignedEntity/representedOrganization/standardIndustryClassCode
    $standard_industry = "$service_event/cda:performer/cda:assignedEntity/cda:representedOrganization/cda:standardIndustryClassCode";
    $prac       = $xpath->queryAttributNode($standard_industry, null, "code");
    $pracName   = $xpath->queryAttributNode($standard_industry, null, "displayName");
    $pracSystem = $xpath->queryAttributNode($standard_industry, null, "codeSystem");
    $pratice    = new CXDSPracticeSetting("cla$cla_id", $id, $prac);
    $this->setClaId();
    $pratice  ->setCodingScheme(array($pracSystem));
    $pratice  ->setName($pracName);
    $this->practice_setting = $pratice;
    $extrinsic->setPracticeSetting($pratice);

    //code
    $code = $factory->code;
    $type = new CXDSType("cla$cla_id", $id, $code["code"]);
    $this->setClaId();
    $type     ->setCodingScheme(array($code["codeSystem"]));
    $type     ->setName($code["displayName"]);
    $extrinsic->setType($type);

    //code - table de correspondance X04
    list($classCode, $oid, $name) = $this->getClassCodeFromCode($code["code"]);
    $classification = new CXDSClass("cla$cla_id", $id, $classCode);
    $this->setClaId();
    $classification->setCodingScheme(array($oid));
    $classification->setName($name);
    $extrinsic     ->setClass($classification);

    //recordTarget/patientRole/id
    $extrinsic->setPatientId("ei$ei_id", $id, $ins);
    $this->setEiId();

    //id - root
    $root = $factory->id_cda;
    $this->oid["extrinsic"] = $root;
    $extrinsic->setUniqueId("ei$ei_id", $id, $root);
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
    $ins    = $this->ins_patient;

    //Création du document
    $extrinsic = new CXDSExtrinsicObject($id, "text/xml");
    $extrinsic->setSlot("creationTime"      , array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("languageCode"      , array("art"));
    $extrinsic->setSlot("legalAuthenticator", array($this->xcn_mediuser));
    $extrinsic->setSlot("serviceStartTime"  , array(CXDSTools::getTimeUtc()));
    $extrinsic->setSlot("serviceStopTime"   , array(CXDSTools::getTimeUtc()));

    //patientId du lot de submission
    $extrinsic->setSlot("sourcePatientId", array($ins));
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

    $confid2 = CXDSConfidentiality::getMasquagePS("cla$cla_id", $id);
    $this->setClaId();
    $extrinsic->appendConfidentiality($confid2);

    $confid3 = CXDSConfidentiality::getMasquagePatient("cla$cla_id", $id);
    $this->setClaId();
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

    $healt = $this->health_care_facility;
    $extrinsic->setHealthcareFacilityType($healt);

    $pratice = $this->practice_setting;
    $extrinsic->setPracticeSetting($pratice);

    $type = new CXDSType("cla$cla_id", $id, "E1762");
    $this->setClaId();
    $type->setCodingScheme(array("ASTM"));
    $type->setName("Full Document");
    $extrinsic->setType($type);

    //identique au lot de submission
    $extrinsic->setPatientId("ei$ei_id", $id, $ins);
    $this->setEiId();

    //identifiant de la signature
    $this->oid["signature"] = $this->oid["lot"]."0";
    $extrinsic->setUniqueId("ei$ei_id", $id, $this->oid["signature"]);
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
   * Retourne l'oid, l'identifiant et le nom d'une classe selon le code
   *
   * @param String $code Code
   *
   * @return array
   */
  function getClassCodeFromCode($code) {
    $entry = CXDSTools::loadEntryJV("ASIP-SANTE_X04.xml", $code);
    $entry = CXDSTools::loadEntryJV("ASIP-SANTE_classCode.xml", $entry["name"]);
    $id   = $entry["id"];
    $oid  = $entry["oid"];
    $name = $entry["name"];

    return array($id, $oid, $name);
  }

  /**
   * Retourne la person
   *
   * @param CMediusers $praticien CMediusers
   *
   * @return string
   */
  function getPerson($praticien) {

    if (!$praticien->adeli && !$praticien->rpps) {
      return null;
    }
    $comp1 = "";
    $comp2 = $praticien->_p_last_name;
    $comp3 = $praticien->_p_first_name;
    $comp9 = "1.2.250.1.71.4.2.1";
    $comp10 = "D";

    if ($praticien->adeli) {
      $comp1 = "0$praticien->adeli";
    }

    if ($praticien->rpps) {
      $comp1 = "8$praticien->rpps";
    }
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