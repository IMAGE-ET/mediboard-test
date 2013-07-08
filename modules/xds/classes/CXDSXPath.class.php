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
 * Description
 */
class CXDSXPath extends CMbXPath {

  /**
   * Retourne la valeur du noeud cherché, null si le noeud n'est pas présent
   *
   * @param String $xpath String
   *
   * @return null|string
   */
  function getNodeValue($xpath) {

    $result = $this->queryUniqueNode($xpath);

    if ($result) {
      $result = $result->nodeValue;
    }

    return $result;
  }

  /**
   * Retourne la valeur de l'attribut
   *
   * @param DOMElement $node    DOMElement
   * @param string     $attName String
   *
   * @return string
   */
  function getValueAttributNode($node, $attName) {
    return $node->getAttribute($attName);
  }

  /**
   * Retourne le codingScheme
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getCodingScheme($node) {
    $xds = $node->getElementsByTagName("xds")->item(0);
    $result = $this->getValueAttributNode($xds, "codingScheme");
    return $result;
  }

  /**
   * Retourne le contenu
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getContenu($node) {
    $mediaType = $node->getElementsByTagName("mediaType")->item(0);
    $result = $this->getValueAttributNode($mediaType, "contenu");
    return $result;
  }

  /**
   * Retourne le formatCode
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getformatCode($node) {
    $xds = $node->getElementsByTagName("xds")->item(0);
    $result = $this->getValueAttributNode($xds, "formatCode");
    return $result;
  }

  /**
   * Retourne la person
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getPerson($node) {
    $comp10 = "D";
    $id = $node->getElementsByTagName("id")->item(0);
    $person = $node->getElementsByTagName("assignedPerson");
    $person = $person->item(0);
    /** @var DOMElement $person */
    $comp2 = $person->getElementsByTagName("family")->item(0)->nodeValue;
    $comp3 = $person->getElementsByTagName("given")->item(0)->nodeValue;
    $comp1 = $this->getValueAttributNode($id, "extension");
    $comp9 = $this->getValueAttributNode($id, "root");
    $comp13 = $this->getTypeId($comp1);
    $result = "$comp1^$comp2^$comp3^^^^^^^&amp;$comp9&amp;ISO^$comp10^^^$comp13";
    return $result;
  }

  /**
   * Vérifie si l'objet possèdent un NullFlavor
   *
   * @param DOMElement $node DOMElement
   *
   * @return bool
   */
  function isNullFlavor($node) {
    if ($node->getAttribute("nullFlavor")) {
      return true;
    }

    return false;
  }

  /**
   * Retourne l'organisation
   *
   * @param DOMElement $node DOMElement
   *
   * @return String
   */
  function getOrganisation($node) {
    $comp1  = "";
    $comp6  = "";
    $comp7  = "";
    $comp10 = "";
    $id = $node->getElementsByTagName("id")->item(0);
    $name = $node->getElementsByTagName("name")->item(0);
    if (!$this->isNullFlavor($name)) {
      $comp1 = $name->nodeValue;
    }
    if (!$this->isNullFlavor($id)) {
      $comp7 = "DNST";
      $comp6 = $this->getValueAttributNode($id, "root");
      $comp6 = "&amp;$comp6&ampISO";
      $comp10 = $this->getValueAttributNode($id, "extension");
    }

    return "$comp1^^^^^^$comp6^$comp7^^^$comp10";
  }

  /**
   * Retourne la speciality
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getSpeciality($node) {
    $comp1 = $this->getValueAttributNode($node, "code");
    $comp2 = $this->getValueAttributNode($node, "displayName");
    $comp3 = $this->getValueAttributNode($node, "codeSystem");
    return "$comp1^$comp2^$comp3";
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
   * Retourne l'INSC
   *
   * @param DOMElement $node DOMElement
   *
   * @return string
   */
  function getIns($node) {
    //@todo: récupérer la date pour l'INS-C
    $comp5 = "INS-C";
    $comp4 = $this->getValueAttributNode($node, "root");
    $comp1 = $this->getValueAttributNode($node, "extension");
    if ($comp4 === "1.2.250.1.213.1.4.1") {
      $comp5 = "INS-A";
    }
    $result = "$comp1^^^&amp;$comp4&amp;ISO^$comp5";
    return $result;
  }
}