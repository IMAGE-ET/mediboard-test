<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Xpath class for CDA
 */
class CCDAXPath extends CMbXPath {

  /**
   * @see parent::__construct()
   */
  function __construct($xml) {
    parent::__construct($xml);
    $this->registerNamespace("cda", "urn:hl7-org:v3");
  }

  /**
   * Return the name of the document
   *
   * @return string
   */
  function getTitle() {
    return $this->queryTextNode("//cda:title");
  }

  /**
   * Return the create date of the document
   *
   * @return datetime|Null
   */
  function getCreateDate() {
    $create_date = $this->queryAttributNode("/cda:ClinicalDocument/cda:effectiveTime", null, "value");
    if ($create_date) {
      $create_date = CMbDT::dateTime($create_date);
    }

    return $create_date;
  }

  /**
   * Return the custodian organization node
   *
   * @return DOMElement
   */
  function getCustodianOrganization() {
    return $this->queryUniqueNode("//cda:custodian/cda:assignedCustodian/cda:representedCustodianOrganization");
  }

  /**
   * Return the Author of the document(legal author or the first author)
   *
   * @return DOMNode
   */
  function getAuthor() {
    $auteur = $this->queryUniqueNode("//cda:legalAuthenticator/cda:assignedEntity");
    if (!$auteur) {
      $auteur = $this->query("//cda:author/cda:assignedAuthor")->item(0);
    }

    return $auteur;
  }

  /**
   * Return the list of patients
   *
   * @return DOMNodeList
   */
  function getPatients() {
    return $this->query("//cda:recordTarget");
  }

  /**
   * Return the name of the patient
   *
   * @param DOMNode $patient Patient Node
   *
   * @return string
   */
  function getPatientName($patient) {
    $xpath = "./cda:patientRole/cda:patient/cda:name";
    $name = $this->queryTextNode("$xpath/cda:family[not(@*)]", $patient);
    if (!$name) {
      $name = $this->queryTextNode("$xpath/cda:family[@qualifier='SP']", $patient);
    }

    return $name;
  }

  /**
   * return the birthDate of the patient
   *
   * @param DOMNode $patient Patient Node
   *
   * @return date|Null
   */
  function getPatientBirthDate($patient) {
    $birthdate = $this->queryAttributNode("./cda:patientRole/cda:patient/cda:birthTime", $patient, "value");
    if ($birthdate) {
      $birthdate = CMbDT::date($birthdate);
    }

    return $birthdate;
  }

  /**
   * Return the identifier of the object
   *
   * @param DOMNode $node node
   *
   * @return string
   */
  function getIdentifierII($node) {
    $root      = $this->queryAttributNode("./cda:id", $node, "root");
    $extension = $this->queryAttributNode("./cda:id", $node, "extension");
    if ($extension) {
      $root .= "@$extension";
    }

    return $root;
  }

  /**
   * Return the code of the node
   * By default the code of the document is return
   *
   * @param DomNode $node Node
   *
   * @return string
   */
  function getCodeCE($node = null) {
    $codeSystem = $this->queryAttributNode("./cda:code", $node, "codeSystem");
    $code       = $this->queryAttributNode("./cda:code", $node, "code");

    return "$codeSystem^$code";
  }

  /**
   * Return the document
   *
   * @return string
   */
  function getDocument() {
    $body           = $this->queryUniqueNode("cda:component/cda:nonXMLBody/cda:text");
    $representation = $this->queryAttributNode(".", $body, "representation");
    $file_cda       = $this->queryTextNode(".", $body);

    if ($representation == "B64") {
      $file_cda = base64_decode($file_cda);
    }

    return $file_cda;
  }
}