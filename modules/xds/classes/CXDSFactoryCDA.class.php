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
class CXDSFactoryCDA extends CXDSFactory {

  /**
   * @see parent::extractData
   */
  function extractData() {
    /** @var CCDAFactory $factory */
    $factory                 = $this->mbObject;
    $this->document          = $factory->mbObject;
    $this->targetObject      = $factory->targetObject;
    $this->id_classification = 0;
    $this->id_external       = 0;

    $mediuser                = CMediusers::get();
    $specialty               = $mediuser->loadRefOtherSpec();
    $group                   = $mediuser->loadRefFunction()->loadRefGroup();
    $identifiant             = CXDSTools::getIdEtablissement(true, $group)."/$mediuser->_id";
    $this->specialty         = $specialty->code."^".$specialty->libelle."^".$specialty->oid;
    $this->xcn_mediuser      = CXDSTools::getXCNMediuser($identifiant, $mediuser->_p_last_name, $mediuser->_p_first_name);
    $this->xon_etablissement = CXDSTools::getXONetablissement($group->text, CXDSTools::getIdEtablissement(false, $group));

    $this->xpath = new CMbXPath($factory->dom_cda);
    $this->xpath->registerNamespace("cda", "urn:hl7-org:v3");

    $this->patient_id  = $this->getID($factory->patient, $factory->receiver);
    $this->ins_patient = $this->getIns($factory->patient);
    $uuid = CMbSecurity::generateUUID();
    $this->uuid["registry"]  = $uuid."1";
    $this->uuid["extrinsic"] = $uuid."2";
    $this->uuid["signature"] = $uuid."3";
  }

  /**
   * @see parent::createRegistryPackage
   */
  function createRegistryPackage($id) {
    /** @var CCDAFactory $factory */
    $factory               = $this->mbObject;
    $cla_id                = &$this->id_classification;
    $ei_id                 = &$this->id_external;
    $ins                   = $this->ins_patient;
    $this->name_submission = $id;
    $specialty             = $this->specialty;
    $object                = $this->targetObject;
    $registry = new CXDSRegistryPackage($id);
    //date de soumission
    $registry->setSubmissionTime(array(CXDSTools::getTimeUtc()));

    //PS qui envoie le document
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id, true);
    $this->setClaId();
    $document->setAuthorPerson(array($this->xcn_mediuser));
    $document->setAuthorSpecialty(array($specialty));
    //Institution qui envoie le document
    $document->setAuthorInstitution(array($this->xon_etablissement));
    $registry->appendDocumentEntryAuthor($document);

    //type d'activité pour lequel on envoie les documents
    $code = "";
    switch (get_class($object)) {
      case "COperation";
        $object = $object->loadRefSejour();
      case "CSejour":
        switch ($object->type) {
          case "comp":
            $code = "03";
            break;
          case "ambu":
            $code = "23";
            break;
          case "urg":
            $code = "10";
            break;
          default:
            $code = "07";
        }
        break;
      case "CConsultation";
        $code = "07";
        break;
      default:
    }

    $entry = CXDSTools::loadEntryJV("ASIP-SANTE_contentTypeCode.xml", $code);
    $content = new CXDSContentType("cla$cla_id", $id, $entry["id"]);
    $this->setClaId();
    $content->setCodingScheme(array($entry["oid"]));
    $content->setContentTypeCodeDisplayName($entry["name"]);
    $registry->setContentType($content);

    //spécification d'un SubmissionSet ou d'un folder, ici submissionSet
    $registry->setSubmissionSet("cla$cla_id", $id, false);
    $this->setClaId();

    //patient du document
    $registry->setPatientId("ei$ei_id", $id, $ins);
    $this->setEiId();
    $receiver = $factory->receiver;
    //OID de l'instance serveur
    $oid_instance = CMbOID::getOIDOfInstance($registry, $receiver);
    $registry->setSourceId("ei$ei_id", $id, $oid_instance);
    $this->setEiId();

    //OID unique
    $oid = CMbOID::getOIDFromClass($registry, $receiver);
    $cxds_submissionlot = new CXDSSubmissionLot();
    $cxds_submissionlot->date = "now";
    $cxds_submissionlot->type = $this->type;
    if ($msg = $cxds_submissionlot->store()) {
      throw new CMbException($msg);
    }

    $this->id_submission = $cxds_submissionlot->_id;
    $this->oid["lot"] = "$oid.$cxds_submissionlot->_id";
    $registry->setUniqueId("ei$ei_id", $id, $this->oid["lot"]);
    $this->setEiId();

    return $registry;
  }

  /**
   * @see parent::createExtrinsicObject
   */
  function createExtrinsicObject($id, $lid = null) {
    /** @var CCDAFactory $factory */
    $factory      = $this->mbObject;
    $cla_id       = &$this->id_classification;
    $ei_id        = &$this->id_external;
    $patient_id   = $this->patient_id;
    $ins          = $this->ins_patient;
    $hide_patient = $this->hide_patient;
    $hide_ps      = $this->hide_ps;
    $service      = $factory->service_event;
    $industry     = $factory->industry_code;
    $praticien    = $factory->practicien;
    $this->appendNameDocument($id);

    $extrinsic = new CXDSExtrinsicObject($id, "text/xml", $lid);

    //effectiveTime en UTC
    if ($factory->date_creation) {
      $extrinsic->setSlot("creationTime", array(CXDSTools::getTimeUtc($factory->date_creation)));
    }

    //languageCode
    $extrinsic->setSlot("languageCode", array($factory->langage));

    //legalAuthenticator XCN
    $legalAuthenticator = $this->getPerson($praticien);
    $extrinsic->setSlot("legalAuthenticator", array($legalAuthenticator));

    //documentationOf/serviceEvent/effectiveTime/low en UTC
    if ($service["time_start"]) {
      $extrinsic->setSlot("serviceStartTime", array(CXDSTools::getTimeUtc($service["time_start"])));
    }

    //documentationOf/serviceEvent/effectiveTime/high en UTC
    if ($service["time_stop"]) {
      $extrinsic->setSlot("serviceStopTime", array(CXDSTools::getTimeUtc($service["time_stop"])));
    }

    //recordTarget/patientRole/id
    $extrinsic->setSlot("sourcePatientId", array($patient_id));

    //recordtarget/patientRole
    $extrinsic->setSlot("sourcePatientInfo", $this->getSourcepatientInfo($factory->patient));

    //title
    $extrinsic->setTitle($factory->nom);

    //Auteur du document
    $document = new CXDSDocumentEntryAuthor("cla$cla_id", $id);
    $this->setClaId();

    //author/assignedAuthor
    $author = $this->getPerson($praticien);
    $document->setAuthorPerson(array($author));

    //author/assignedAuthor/code
    $spec = $praticien->loadRefOtherSpec();
    if ($spec->libelle) {
      $document->setAuthorSpecialty(array("$spec->code^$spec->libelle^$spec->oid"));
    }

    //author/assignedAuthor/representedOrganization - si absent, ne pas renseigner
    //si nom pas présent - champ vide
    //si id nullflavor alors 6-7-10 vide
    $author_organization = $praticien->loadRefFunction()->loadRefGroup();
    if ($author_organization->_id) {
      $institution = CXDSTools::getXONetablissement($author_organization->text, CXDSTools::getIdEtablissement(false, $author_organization));
      $document->setAuthorInstitution(array($institution));
    }
    $extrinsic->appendDocumentEntryAuthor($document);

    //confidentialityCode
    $confidentialite = $factory->confidentialite;
    $confid = new CXDSConfidentiality("cla$cla_id", $id, $confidentialite["code"]);
    $confid->setCodingScheme(array($confidentialite["codeSystem"]));
    $confid->setName($confidentialite["displayName"]);
    $extrinsic->appendConfidentiality($confid);

    if ($hide_ps) {
      $confid2 = CXDSConfidentiality::getMasquagePS("cla$cla_id", $id);
      $this->setClaId();
      $extrinsic->appendConfidentiality($confid2);
    }

    if ($hide_patient) {
      $confid3 = CXDSConfidentiality::getMasquagePatient("cla$cla_id", $id);
      $this->setClaId();
      $extrinsic->appendConfidentiality($confid3);
    }

    //documentationOf/serviceEvent/code - table de correspondance
    if (!$service["nullflavor"]) {
      $eventSystem = $service["oid"];
      $eventCode = $service["code"];
      switch ($service["type_code"]) {
        case "cim10":
          $cim10 = CCodeCIM10::get($eventCode);
          $libelle = $cim10->libelle;
          break;
        case "ccam":
          $ccam = CDatedCodeCCAM::get($eventCode);
          $libelle = $ccam->libelleCourt;
          break;
        default:
      }

      $event = new CXDSEventCodeList("cla$cla_id", $id, $eventCode);
      $this->setClaId();
      $event->setCodingScheme(array($eventSystem));
      $event->setName($libelle);
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
    $this->health_care_facility = $this->health_care_facility ? $this->health_care_facility : $healtcare;

    //documentationOf/serviceEvent/performer/assignedEntity/representedOrganization/standardIndustryClassCode
    $pratice    = new CXDSPracticeSetting("cla$cla_id", $id, $industry["code"]);
    $this->setClaId();
    $pratice  ->setCodingScheme(array($industry["codeSystem"]));
    $pratice  ->setName($industry["displayName"]);
    $this->practice_setting = $this->practice_setting ? $this->practice_setting : $industry;
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

}