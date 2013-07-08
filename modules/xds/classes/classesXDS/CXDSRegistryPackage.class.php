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
class CXDSRegistryPackage extends CXDSRegistryObject {

  public $submissionTime;
  public $title;
  public $comments;
  public $submissionSet;
  public $contentType;
  public $documentEntryAuthor = array();
  public $sourceId;
  public $uniqueId;
  public $patientId;


  /**
   * Construction de l'instance
   *
   * @param String $id     String
   * @param String $status null
   */
  function __construct($id, $status = null) {
    parent::__construct($id);
    $this->status = $status;
  }

  /**
   * Setter SubmissionTime
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setSubmissionTime($value) {
    $this->submissionTime = new CXDSSlot("CreationTime", $value);
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
   * Setter DocumentEntryAuthor
   *
   * @param CXDSDocumentEntryAuthor $entry CXDSDocumentEntryAuthor
   *
   * @return void
   */
  function appendDocumentEntryAuthor($entry) {
    array_push($this->documentEntryAuthor, $entry);
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
   * Retourne les variables de la classe
   *
   * @return String[]
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
   * Génère le XML
   *
   * @return CXDSXmlDocument|void
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createRegistryPackageRoot($this->id, $this->status);
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