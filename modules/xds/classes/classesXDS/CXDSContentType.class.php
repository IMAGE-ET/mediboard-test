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
 * Classe classification représentant la variable ContentType de RegistryPackage
 */
class CXDSContentType extends CXDSClassification {

  /** @var CXDSName */
  public $contentTypeCodeDisplayName;
  /** @var CXDSSlot */
  public $codingScheme;

  /**
   * Génération de l'instance
   *
   * @param String $id               String
   * @param String $classifiedObject String
   * @param String $contentType      String
   */
  function __construct($id, $classifiedObject, $contentType) {
    parent::__construct($id);
    $this->classificationScheme = "urn:uuid:aa543740-bdda-424e-8c96-df4873be8500";
    $this->classifiedObject = $classifiedObject;
    $this->nodeRepresentation = $contentType;
  }

  /**
   * Setter ContentTypeCodeDisplayName
   *
   * @param String $value String
   *
   * @return void
   */
  function setContentTypeCodeDisplayName($value) {
    $this->contentTypeCodeDisplayName = new CXDSName($value);
  }

  /**
   * Setter CodingScheme
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setCodingScheme($value) {
    $this->codingScheme = new CXDSSlot("codingScheme", $value);
  }

  /**
   * @see parent::toXML()
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = parent::toXML();

    if ($this->contentTypeCodeDisplayName) {
      $xml->importDOMDocument($xml->documentElement, $this->contentTypeCodeDisplayName->toXML());
    }

    if ($this->codingScheme) {
      $xml->importDOMDocument($xml->documentElement, $this->codingScheme->toXML());
    }

    return $xml;
  }

}
