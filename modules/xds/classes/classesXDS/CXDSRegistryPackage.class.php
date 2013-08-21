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
 * Les classes XDS SubmissionSet et Folder ont pour équivalent le même élément
 * rim:RegistryPackage. La distinction entre les deux classes est effectuée par l?ajout de
 * rim:Classification à rim:RegistryPackage.
 */
class CXDSRegistryPackage extends CXDSExtrinsicPackage {

  /** @var  CXDSSlot */
  public $submissionTime;
  /** @var  CXDSFolder|CXDSSubmissionSet */
  public $submissionSet;
  /** @var  CXDSContentType */
  public $contentType;
  /** @var  CXDSSourceId */
  public $sourceId;

  /**
   * Construction de l'instance
   *
   * @param String $id String
   */
  function __construct($id) {
    parent::__construct($id);
  }

  /**
   * Setter SubmissionTime
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setSubmissionTime($value) {
    $this->submissionTime = new CXDSSlot("submissionTime", $value);
  }

  /**
   * Setter SourceId
   *
   * @param String $id             String
   * @param String $registryObject String
   * @param String $value          String
   *
   * @return void
   */
  function setSourceId($id, $registryObject, $value) {
    $this->sourceId = new CXDSSourceId($id, $registryObject, $value);
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
    $this->uniqueId = new CXDSUniqueId($id, $registryObject, $value, true);
  }

  /**
   * Setter ContentType
   *
   * @param CXDSContentType $contentType CXDSContentType
   *
   * @return void
   */
  function setContentType($contentType) {
    $this->contentType = $contentType;
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
    $this->patientId = new CXDSPatientID($id, $registryObject, $value, true);
  }

  /**
   * Setter SubmissionSet
   *
   * @param String $id                 String
   * @param String $classifiedObject   String
   * @param bool   $classificationNode bool
   *
   * @return void
   */
  function setSubmissionSet($id, $classifiedObject, $classificationNode) {
    if ($classificationNode) {
      $this->submissionSet = new CXDSFolder($id, $classifiedObject);
      return;
    }
    $this->submissionSet = new CXDSSubmissionSet($id, $classifiedObject);
  }

  /**
   * @see parent::toXML()
   *
   * @return CXDSXmlDocument|void
   */
  function toXML() {
    return parent::toXML(true);
  }
}