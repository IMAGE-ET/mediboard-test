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
 * Document xml du XDS
 */
class CXDSXmlDocument extends DOMDocument {

  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    $this->formatOutput = true;
  }

  /**
   * Création d'élément racine
   *
   * @param String $name String
   *
   * @return DOMElement
   */
  function createRoot($name) {
    $name = utf8_encode($name);
    return $this->createElementNS("urn:oasis:names:tc:ebxml-regrep:xsd:rim:3.0", $name);
  }

  /**
   * Création de la racine Slot
   *
   * @param String $name String
   *
   * @return void
   */
  function createSlotRoot($name) {
    $name = utf8_encode($name);
    $element = $this->createRoot("Slot");
    $element->setAttribute("name", $name);
    $this->appendChild($element);
  }

  /**
   * Création de la racine RegistryPackageRoot
   *
   * @param String $id     String
   * @param String $status String
   *
   * @return void
   */
  function createRegistryPackageRoot($id, $status) {
    $id = utf8_encode($id);
    $status = utf8_encode($status);
    $element = $this->createRoot("RegistryPackage");
    $element->setAttribute("id", $id);
    $element->setAttribute("status", $status);
    $this->appendChild($element);
  }

  /**
   * Création de valeurs Slot
   *
   * @param String[] $data String[]
   *
   * @return void
   */
  function createSlotValue($data) {
    $valueList = $this->createRoot("ValueList");
    foreach ($data as $_data) {
      $_data = utf8_encode($_data);
      $value = $this->createRoot("Value");
      $value->nodeValue = $_data;
      $valueList->appendChild($value);
    }
    $this->documentElement->appendChild($valueList);
  }

  /**
   * Création de la racine pour le Name et Description
   *
   * @param String $name String
   *
   * @return void
   */
  function createNameDescriptionRoot($name) {
    $element = $this->createRoot($name);
    $this->appendChild($element);
  }

  /**
   * Création du Localized
   *
   * @param String $value   String
   * @param String $charset String
   * @param String $lang    String
   *
   * @return void
   */
  function createLocalized($value, $charset, $lang) {
    $value = utf8_encode($value);
    $charset = utf8_encode($charset);
    $lang = utf8_encode($lang);
    $element = $this->createRoot("LocalizedString");
    $element->setAttribute("value", $value);
    $element->setAttribute("charset", $charset);
    $element->setAttribute("lang", $lang);
    $this->appendChild($element);
  }

  /**
   * Création du Version Info
   *
   * @param String $value String
   *
   * @return void
   */
  function createVersionInfo($value) {
    $value = utf8_encode($value);
    $element = $this->createRoot("VersionInfo");
    $element->setAttribute("VersionName", $value);
    $this->appendChild($element);
  }

  /**
   * Création de la racine de classification
   *
   * @param String $id                 String
   * @param String $classification     String
   * @param String $classified         String
   * @param String $nodeRepresentation String
   *
   * @return void
   */
  function createClassificationRoot($id, $classification, $classified, $nodeRepresentation) {
    $id = utf8_encode($id);
    $classification = utf8_encode($classification);
    $classified = utf8_encode($classified);
    $nodeRepresentation = utf8_encode($nodeRepresentation);
    $element = $this->createRoot("Classification");
    $element->setAttribute("id", $id);
    $element->setAttribute("classificationScheme", $classification);
    $element->setAttribute("classifiedObject", $classified);
    $element->setAttribute("nodeRepresentation", $nodeRepresentation);
    $this->appendChild($element);
  }

  /**
   * Création de la racine ExternalIdentifier
   *
   * @param String $id             String
   * @param String $identification String
   * @param String $registry       String
   * @param String $value          String
   *
   * @return void
   */
  function createExternalIdentifierRoot($id, $identification, $registry, $value) {
    $id = utf8_encode($id);
    $identification = utf8_encode($identification);
    $registry = utf8_encode($registry);
    $value = utf8_encode($value);
    $element = $this->createRoot("ExternalIdentifier");
    $element->setAttribute("id", $id);
    $element->setAttribute("identificationScheme", $identification);
    $element->setAttribute("registryObject", $registry);
    $element->setAttribute("value", $value);
    $this->appendChild($element);
  }

  /**
   * Création de la racine ExtrinsicObject
   *
   * @param String $id       String
   * @param String $lid      String
   * @param String $mimeType String
   * @param String $status   String
   *
   * @return void
   */
  function createExtrinsicObjectRoot($id, $lid, $mimeType, $status) {
    $id = utf8_encode($id);
    $lid = utf8_encode($lid);
    $mimeType = utf8_encode($mimeType);
    $status = utf8_encode($status);
    $element = $this->createRoot("ExtrinsicObject");
    $element->setAttribute("id", $id);
    $element->setAttribute("lid", $lid);
    $element->setAttribute("mimeType", $mimeType);
    $element->setAttribute("status", $status);
    $this->appendChild($element);
  }

  /**
   * Création de la racine Submission
   *
   * @param String $id                 String
   * @param String $classificationNode String
   * @param String $classifiedObject   String
   *
   * @return void
   */
  function createSubmissionRoot($id, $classificationNode, $classifiedObject) {
    $id = utf8_encode($id);
    $classifiedObject = utf8_encode($classifiedObject);
    $classificationNode = utf8_encode($classificationNode);
    $element = $this->createRoot("Classification");
    $element->setAttribute("id", $id);
    $element->setAttribute("classificationNode", $classificationNode);
    $element->setAttribute("classifiedObject", $classifiedObject);
    $this->appendChild($element);
  }

  /**
   * Création de la racine association
   *
   * @param String $id           String
   * @param String $status       String
   * @param String $type         String
   * @param String $sourceObject String
   * @param String $targetObject String
   *
   * @return void
   */
  function createAssociationRoot($id, $status, $type, $sourceObject, $targetObject) {
    $id = utf8_encode($id);
    $status = utf8_encode($status);
    $sourceObject = utf8_encode($sourceObject);
    $targetObject = utf8_encode($targetObject);
    $element = $this->createRoot("Association");
    $element->setAttribute("id", $id);
    $element->setAttribute("status", $status);
    $element->setAttribute("associationType", $type);
    $element->setAttribute("sourceObject", $sourceObject);
    $element->setAttribute("targetObject", $targetObject);
    $this->appendChild($element);
  }

  /**
   * Création de la racine ObjectList
   *
   * @return void
   */
  function createRegistryObjectListRoot() {
    $this->appendChild($this->createRoot("RegistryObjectList"));
  }

  /**
   * Création de la racine XDS
   *
   * @return void
   */
  function createSubmitObjectsRequestRoot() {
    $element = $this->createRoot("SubmitObjectsRequest");
    $element->appendChild($this->documentElement);
    $this->appendChild($element);
  }

  /**
   * Importe un DOMDocument à l'intérieur de l'élément spécifié
   *
   * @param DOMElement  $nodeParent  DOMElement
   * @param DOMDocument $domDocument DOMDocument
   *
   * @return void
   */
  function importDOMDocument($nodeParent, $domDocument) {
    $nodeParent->appendChild($this->importNode($domDocument->documentElement, true));
  }
}
