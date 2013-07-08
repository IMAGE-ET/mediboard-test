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
 * Correspond au documentEntry(fiche) dans XDS
 * Une fiche appartient à un registre et représente le document stocké dans l'entrepôt. Elle
 * contient les métadonnées décrivant les caractéristiques principales d'un document stocké
 * dans l'entrepôt dont l'index (uniqueId) pour pointer vers ce document.
 */
class CXDSExtrinsicObject extends CXDSRegistryObject {

  public $mimeType;
  /** @var  CXDSSlot */
  public $creationTime;
  /** @var  CXDSSlot */
  public $languageCode;
  /** @var  CXDSSlot */
  public $legalAuthenticator;
  /** @var  CXDSSlot */
  public $serviceStartTime;
  /** @var  CXDSSlot */
  public $serviceStopTime;
  /** @var  CXDSSlot */
  public $sourcePatientId;
  /** @var  CXDSSlot */
  public $sourcePatientInfo;
  /** @var  CXDSSlot */
  public $uri;
  /** @var  CXDSSlot */
  public $documentAvailability;
  /** @var  CXDSLocalizedString */
  public $title;
  /** @var  CXDSLocalizedString */
  public $comments;
  /** @var  CXDSDocumentEntryAuthor[] */
  public $documentEntryAuthor = array();
  /** @var  CXDSClass */
  public $class;
  /** @var  CXDSConfidentiality[] */
  public $confidentiality = array();
  /** @var  CXDSEventCodeList[] */
  public $eventCodeList = array();
  /** @var  CXDSFormat */
  public $format;
  /** @var  CXDSHealthcareFacilityType */
  public $healthcareFacilityType;
  /** @var  CXDSPracticeSetting */
  public $practiceSetting;
  /** @var  CXDSType */
  public $type;
  /** @var  CXDSPatientID */
  public $patientId;
  /** @var  CXDSUniqueID */
  public $uniqueId;

  /**
   * Construction de l'instance
   *
   * @param String $id       String
   * @param String $mimeType String
   * @param String $status   null
   * @param String $lid      null
   */
  function __construct($id, $mimeType, $status = null, $lid = null) {
    parent::__construct($id);
    $this->lid = $lid;
    $this->mimeType = $mimeType;
    $this->objectType = "urn:uuid:7edca82f-054d-47f2-a032-9b2a5b5186c1";
    $this->status = $status;
  }

  /**
   * Setter PatientId
   *
   * @param String $id             String
   * @param String $registryObject String
   * @param String $value          String
   *
   * @return void
   */
  function setPatientId($id, $registryObject, $value) {
    $this->patientId = new CXDSPatientID($id, $registryObject, $value);
  }

  /**
   * Setter UniqueId
   *
   * @param String $id             String
   * @param String $registryObject String
   * @param String $value          String
   *
   * @return void
   */
  function setUniqueId($id, $registryObject, $value) {
    $this->uniqueId = new CXDSUniqueId($id, $registryObject, $value);
  }

  /**
   * Setter comments
   *
   * @param String $comments String
   *
   * @return void
   */
  public function setComments($comments) {
    $this->comments = new CXDSDescription($comments);
  }

  /**
   * Setter Class
   *
   * @param CXDSClass $class CXDSClass
   *
   * @return void
   */
  function setClass($class) {
    $this->class = $class;
  }

  /**
   * Setter Format
   *
   * @param CXDSFormat $format CXDSFormat
   *
   * @return void
   */
  function setFormat($format) {
    $this->format = $format;
  }

  /**
   * Setter HealthcareFacilityType
   *
   * @param CXDSHealthcareFacilityType $health CXDSHealthcareFacilityType
   *
   * @return void
   */
  function setHealthcareFacilityType($health) {
    $this->healthcareFacilityType = $health;
  }

  /**
   * Setter PracticeSetting
   *
   * @param CXDSPracticeSetting $practice CXDSPracticeSetting
   *
   * @return void
   */
  function setPracticeSetting($practice) {
    $this->practiceSetting = $practice;
  }

  /**
   * Setter Type
   *
   * @param CXDSType $type CXDSType
   *
   * @return void
   */
  function setType($type) {
    $this->type = $type;
  }

  /**
   * Setter title
   *
   * @param String $title String
   *
   * @return void
   */
  public function setTitle($title) {
    $this->title = new CXDSName($title);
  }

  /**
   * Setter DocumentEntryAuthor
   *
   * @param CXDSDocumentEntryAuthor $documentEntry CXDSDocumentEntryAuthor
   *
   * @return void
   */
  function appendDocumentEntryAuthor($documentEntry) {
    array_push($this->documentEntryAuthor, $documentEntry);
  }

  /**
   * Setter Confidentiality
   *
   * @param CXDSConfidentiality $confidentiality CXDSConfidentiality
   *
   * @return void
   */
  function appendConfidentiality($confidentiality) {
    array_push($this->confidentiality, $confidentiality);
  }

  /**
   * Setter EventCodeList
   *
   * @param CXDSEventCodeList $event CXDSEventCodeList
   *
   * @return void
   */
  function appendEventCodeList($event) {
    array_push($this->eventCodeList, $event);
  }

  /**
   * Retourne les variables présent dans la classe
   *
   * @return array
   */
  function getPropertie() {
    $reflection = new ReflectionClass($this);
    $vars = array_keys($reflection->getdefaultProperties());
    $reflection = new ReflectionClass(get_parent_class($this));
    $parent_vars = array_keys($reflection->getdefaultProperties());

    $my_child_vars = array();
    foreach ($vars as $key) {
      if (!in_array($key, $parent_vars)) {
        $my_child_vars[] = $key;
      }
    }
    return $my_child_vars;
  }

  /**
   * Génération de XML de l'instance en cours
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createExtrinsicObjectRoot($this->id, $this->lid, $this->mimeType, $this->status);
    $base_xml = $xml->documentElement;
    $variables = $this->getPropertie();
    foreach ($variables as $_variable) {
      $class = $this->$_variable;
      if (!$class || $_variable === "mimeType") {
        continue;
      }
      if (is_array($this->$_variable)) {
        foreach ($this->$_variable as $_instance) {
          $xml->importDOMDocument($base_xml, $_instance->toXML());
        }
      }
      else {
        $xml->importDOMDocument($base_xml, $class->toXML());
      }
    }

    return $xml;
  }

}
