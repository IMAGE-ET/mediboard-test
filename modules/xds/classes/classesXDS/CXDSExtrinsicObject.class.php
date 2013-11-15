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
class CXDSExtrinsicObject extends CXDSExtrinsicPackage {

  /** @var  CXDSSlot */
  public $hash;
  /** @var  CXDSSlot */
  public $size;
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
  public $lid;

  /**
   * Construction de l'instance
   *
   * @param String $id       String
   * @param String $mimeType String
   * @param String $lid      String
   */
  function __construct($id, $mimeType, $lid = null) {
    parent::__construct($id);
    $this->mimeType   = $mimeType;
    $this->objectType = "urn:uuid:7edca82f-054d-47f2-a032-9b2a5b5186c1";
    $this->lid = $lid;
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
   * @see parent::toXML()
   *
   * @return CXDSXmlDocument|void
   */
  function toXML() {
    return parent::toXML(false);
  }
}