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
 * Permet de générer le XDs en fonction des champs remplis
 */
class CXDSFactory {

  /** @var CMbObject  */
  public $mbObject;
  /** @var CMbObject  */
  public $targetObject;
  public $document;
  public $hide_patient;
  public $hide_ps;
  public $name_submission;
  public $id_classification;
  public $id_external;
  public $patient_id;
  public $xpath;
  public $xcn_mediuser;
  public $xon_etablissement;
  public $specialty;
  public $ins_patient;
  public $practice_setting;
  public $health_care_facility;
  public $iti57;
  public $size;
  public $hash;
  public $repository;
  public $doc_uuid;
  public $id_submission;
  public $type;
  public $uuid          = array();
  public $oid           = array();
  public $name_document = array();


  /**
   * Création de la classe en fonction de l'objet passé
   *
   * @param CMbObject|CCDAFactory $mbObject objet mediboard
   *
   * @return CXDSFactory
   */
  static function factory($mbObject) {
    switch (get_class($mbObject)) {
      case "CCDAFactoryDocItem":
        $class = new CXDSFactoryCDA($mbObject);
        break;
      default:
        $class = new self($mbObject);
    }

    return $class;
  }

  /**
   * Constructeur
   *
   * @param CMbObject $mbObject mediboard object
   */
  function __construct($mbObject) {
    $this->mbObject = $mbObject;
  }

  /**
   * Extrait les données de l'objet nécessaire au XDS
   *
   * @return void
   */
  function extractData() {
  }

  /**
   * Génération de la requête XDS57 concernant le dépubliage et l'archivage
   *
   * @param String $uuid   Identifiant du document dans le registre
   * @param Bool   $action Action fait sur le document
   *
   * @return CXDSXmlDocument
   */
  function generateXDS57($uuid, $action = null) {
    $id_registry  = $this->uuid["registry"];

    $class = new CXDSRegistryObjectList();

    //Ajout du lot de soumission
    $registry = $this->createRegistryPackage($id_registry);
    $class->appendRegistryPackage($registry);
    $statusType = "";

    switch ($action) {
      case "unpublished":
        $statusType = "Deleted";
        break;
      case "archived":
        $statusType = "Archived";
        break;
      default:
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
   * @throws CMbException
   * @return CXDSXmlDocument
   */
  function generateXDS41() {
    $id_registry  = $this->uuid["registry"];
    $id_document  = $this->uuid["extrinsic"];
    $doc_uuid     = $this->doc_uuid;

    //Ajout du lot de soumission
    $class = new CXDSRegistryObjectList();

    //Métadonnée du lot de soumission
    $registry = $this->createRegistryPackage($id_registry);
    $class->appendRegistryPackage($registry);

    //Ajout d'un document
    $extrinsic = $this->createExtrinsicObject($id_document);
    $class->appendExtrinsicObject($extrinsic);

    //Ajout des associations
    $asso1 = $this->createAssociation("association01", $id_registry, $id_document);
    $class->appendAssociation($asso1);

    //si le document est déjà existant
    if ($doc_uuid) {
      $asso4 = $this->createAssociation("association02", $id_document, $doc_uuid, false, true);
      $class->appendAssociation($asso4);
    }

    //Création dans mediboard du lot de soumission
    $cxds_submissionlot_document = new CXDSSubmissionLotToDocument();
    $cxds_submissionlot_document->submissionlot_id = $this->id_submission;
    $cxds_submissionlot_document->setObject($this->document);
    if ($msg = $cxds_submissionlot_document->store()) {
      throw new CMbException($msg);
    }

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
   * @param CPatient $patient Patient
   *
   * @return string
   */
  function getIns ($patient) {
    $ins = null;
    //@todo: faire l'INSA
    $last_ins = $patient->_ref_last_ins;
    if ($last_ins) {
      $ins = $last_ins->ins;
    }
    $comp5 = "INS-C";
    $comp4 = "1.2.250.1.213.1.4.2";
    $comp4 = "&$comp4&ISO";
    $comp1 = $ins;

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
   * @throws CMbException
   * @return CXDSRegistryPackage
   */
  function createRegistryPackage($id) {
  }

  /**
   * Création  d'un document
   *
   * @param String $id  Identifiant
   * @param String $lid Lid
   *
   * @return CXDSExtrinsicObject
   */
  function createExtrinsicObject($id, $lid = null) {
  }

  /**
   * Création du document de la signature
   *
   * @param String $id Identifiant
   *
   * @return CXDSExtrinsicObject
   */
  function createSignature($id) {
    $cla_id    = &$this->id_classification;
    $ei_id     = &$this->id_external;
    $ins       = $this->ins_patient;
    $specialty = $this->specialty;

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

    //identique à celui qui envoie
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();
    $document->setAuthorPerson(array($this->xcn_mediuser));
    $document->setAuthorSpecialty(array($specialty));
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

    $healtcare = $this->health_care_facility;
    $healt = new CXDSHealthcareFacilityType("cla$cla_id", $id, $healtcare["code"]);
    $this->setClaId();
    $healt    ->setCodingScheme(array($healtcare["codeSystem"]));
    $healt    ->setName($healtcare["displayName"]);
    $extrinsic->setHealthcareFacilityType($healt);

    $industry = $this->practice_setting;
    $pratice  = new CXDSPracticeSetting("cla$cla_id", $id, $industry["code"]);
    $this->setClaId();
    $pratice  ->setCodingScheme(array($industry["codeSystem"]));
    $pratice  ->setName($industry["displayName"]);
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
   * @param bool   $rplc   Remplacement
   *
   * @return CXDSHasMemberAssociation
   */
  function createAssociation($id, $source, $target, $sign = false, $rplc = false) {
    $hasmember = new CXDSHasMemberAssociation($id, $source, $target, $sign, $rplc);
    if (!$sign || !$rplc) {
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
  function getPerson(CMediusers $praticien) {
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
    /*if (strlen($id) === 12) {
      $result = "INS-A";
    }*/
    return $result;
  }

  /**
   * Transforme une chaine date au format time CDA
   *
   * @param String $date      String
   * @param bool   $naissance false
   *
   * @return string
   */
  function getTimeToUtc($date, $naissance = false) {
    if (!$date) {
      return null;
    }
    if ($naissance) {
      $date = Datetime::createFromFormat("Y-m-d", $date);
      return $date->format("Ymd");
    }
    $timezone = new DateTimeZone(CAppUI::conf("timezone"));
    $date     = new DateTime($date, $timezone);

    return $date->format("YmdHisO");
  }

  /**
   * Retourne le sourcepatientinfo
   *
   * @param CPatient $patient patient
   *
   * @return String[]
   */
  function getSourcepatientInfo($patient) {
    $source_info = array();
    $pid5 = "PID-5|$patient->_p_last_name^$patient->_p_first_name^^^^^D";
    $source_info[] = $pid5;
    if ($patient->_p_maiden_name) {
      $pid5_2 = "PID-5|$patient->_p_maiden_name^^^^^^^L";
      $source_info[] = $pid5_2;
    }
    $date = $this->getTimeToUtc($patient->_p_birth_date, true);
    $pid7 = "PID-7|$date";
    $source_info[] = $pid7;
    $sexe = mb_strtoupper($patient->sexe);
    $pid8 = "PID-8|$sexe";
    $source_info[] = $pid8;
    if ($patient->_p_street_address || $patient->_p_city || $patient->_p_postal_code) {
      $addresses = preg_replace("#[\t\n\v\f\r]+#", " ", $patient->_p_street_address, PREG_SPLIT_NO_EMPTY);
      $pid11 = "PID-11|$addresses^^$patient->_p_city^^$patient->_p_postal_code";
      $source_info[] = $pid11;
    }
    if ($patient->_p_phone_number) {
      $pid13 = "PID-13|$patient->_p_phone_number";
      $source_info[] = $pid13;
    }
    if ($patient->_p_mobile_phone_number) {
      $pid14 = "PID-14|$patient->_p_mobile_phone_number";
      $source_info[] = $pid14;
    }
    $pid16 = "PID-16|{$this->getMaritalStatus($patient->situation_famille)}";
    $source_info[] = $pid16;

    return $source_info;
  }

  /**
   * Return the Marital Status
   *
   * @param String $status mediboard status
   *
   * @return string
   */
  function getMaritalStatus($status) {
    switch ($status) {
      case "S":
        $ce = "S";
        break;
      case "M":
        $ce = "M";
        break;
      case "G":
        $ce = "G";
        break;
      case "D":
        $ce = "D";
        break;
      case "W":
        $ce = "W";
        break;
      case "A":
        $ce = "A";
        break;
      case "P":
        $ce = "P";
        break;
      default:
        $ce = "U";
    }
    return $ce;
  }

  /**
   * Retourne l'OID du patient
   *
   * @param CPatient         $patient  Patient
   * @param CInteropReceiver $receiver Receiver
   *
   * @return string
   */
  function getID ($patient, $receiver) {
    $oid = CMbOID::getOIDOfInstance($patient, $receiver);

    $comp4 = $oid;
    $comp4 = "&$comp4&ISO";
    $comp1 = $patient->_IPP ? $patient->_IPP : $patient->_id;
    $comp5 = "PI";

    $result = "$comp1^^^$comp4^$comp5";
    return $result;
  }
}