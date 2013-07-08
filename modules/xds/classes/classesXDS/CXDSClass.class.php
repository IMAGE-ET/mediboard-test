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
 * Classe classification représentant la variable class pour ExtrinsicObject
 * représente la classe du document (compte rendu, imagerie médicale, traitement, certificat,
 * etc.).
 */
class CXDSClass extends CXDSClassification {

  /** @var  CXDSName */
  public $name;
  /** @var  CXDSSlot */
  public $codingScheme;

  /**
   * Construction d'une instance
   *
   * @param String $id                 String
   * @param String $classifiedObject   String
   * @param String $nodeRepresentation String
   */
  function __construct($id, $classifiedObject, $nodeRepresentation) {
    parent::__construct($id);
    $this->classifiedObject = $classifiedObject;
    $this->classificationScheme = "urn:uuid:41a5887f-8865-4c09-adf7-e362475b143a";
    $this->nodeRepresentation = $nodeRepresentation;
  }

  /**
   * Création du nom avec une instance de CXDSName
   *
   * @param String $name String
   *
   * @return void
   */
  function setName($name) {
    $this->name = new CXDSName($name);
  }

  /**
   * Création du codingScheme avec un CXDSSlot
   *
   * @param String[] $codingScheme String[]
   *
   * @return void
   */
  function setCodingScheme($codingScheme) {
    $this->codingScheme = new CXDSSlot("codingScheme", $codingScheme);
  }

  /**
   * @see parent::toXML()
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = parent::toXML();
    $base_xml = $xml->documentElement;

    if ($this->name) {
      $xml->importDOMDocument($base_xml, $this->name->toXML());
    }

    if ($this->codingScheme) {
      $xml->importDOMDocument($base_xml, $this->codingScheme->toXML());
    }

    return $xml;
  }
}
