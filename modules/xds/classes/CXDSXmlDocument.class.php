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
    //$this->formatOutput = true;
  }

  /**
   * Création d'élément racine
   *
   * @param String $namespace String
   * @param String $name      String
   *
   * @return DOMElement
   */
  function createElemNS($namespace, $name) {
    $namespace = utf8_encode($namespace);
    $name = utf8_encode($name);
    return $this->createElementNS($namespace, $name);
  }

  /**
   * Création d'élément RIM
   *
   * @param String $name String
   *
   * @return DOMElement
   */
  function createRimRoot($name) {
    return $this->createElemNS("urn:oasis:names:tc:ebxml-regrep:xsd:rim:3.0", "rim:$name");
  }

  /**
   * Création d'élément racine lcm
   *
   * @param String $name String
   *
   * @return DOMElement
   */
  function createLcmRoot($name) {
    return $this->createElemNS("urn:oasis:names:tc:ebxml-regrep:xsd:lcm:3.0", "lcm:$name");
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
    $element = $this->createRimRoot("Slot");
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
  function createRegistryPackageRoot($id) {
    $id = utf8_encode($id);
    $element = $this->createRimRoot("RegistryPackage");
    $element->setAttribute("id", $id);
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
    $valueList = $this->createRimRoot("ValueList");
    foreach ($data as $_data) {
      $_data = utf8_encode($_data);
      $value = $this->createRimRoot("Value");
      $value->nodeValue = htmlspecialchars($_data);
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
    $element = $this->createRimRoot($name);
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
    $element = $this->createRimRoot("LocalizedString");
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
    $element = $this->createRimRoot("VersionInfo");
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
    $element = $this->createRimRoot("Classification");
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
    $element = $this->createRimRoot("ExternalIdentifier");
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
  function createExtrinsicObjectRoot($id, $mimeType, $objectType) {
    $id = utf8_encode($id);
    $mimeType = utf8_encode($mimeType);
    $objectType = utf8_encode($objectType);
    $element = $this->createRimRoot("ExtrinsicObject");
    $element->setAttribute("id", $id);
    $element->setAttribute("mimeType", $mimeType);
    $element->setAttribute("objectType", $objectType);
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
    $element = $this->createRimRoot("Classification");
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
  function createAssociationRoot($id, $type, $sourceObject, $targetObject, $objectType) {
    $id = utf8_encode($id);
    $sourceObject = utf8_encode($sourceObject);
    $targetObject = utf8_encode($targetObject);
    $objectType = utf8_encode($objectType);
    $element = $this->createRimRoot("Association");
    $element->setAttribute("id", $id);
    $element->setAttribute("associationType", $type);
    $element->setAttribute("objectType", $objectType);
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
    $this->appendChild($this->createRimRoot("RegistryObjectList"));
  }

  /**
   * Création de la racine XDS
   *
   * @return void
   */
  function createSubmitObjectsRequestRoot() {
    $element = $this->createLcmRoot("SubmitObjectsRequest");
    $element->appendChild($this->documentElement);
    $this->appendChild($element);
  }

  /**
   * Création d'un noeud pour l'entrepôt
   *
   * @param String $name String
   *
   * @return DOMElement
   */
  function createDocumentRepositoryElement($name) {
    return $this->createElemNS("urn:ihe:iti:xds-b:2007", "xds:$name");
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
