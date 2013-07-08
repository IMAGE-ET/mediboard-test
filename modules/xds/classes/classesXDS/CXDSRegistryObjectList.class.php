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
 * Classe représente la racine de l?échange des métadonnées XDS
 */
class CXDSRegistryObjectList {
  /** @var CXDSAssociation[] */
  public $association = array();
  /** @var CXDSRegistryPackage[] */
  public $registryPackage = array();
  /** @var CXDSExtrinsicObject[] */
  public $extrinsicObject = array();

  /**
   * Setter Association
   *
   * @param CXDSAssociation $association CXDSAssociation
   *
   * @return void
   */
  function appendAssociation($association) {
    array_push($this->association, $association);
  }

  /**
   * Setter RegistryPackage
   *
   * @param CXDSRegistryPackage $registry CXDSRegistryPackage
   *
   * @return void
   */
  function appendRegistryPackage($registry) {
    array_push($this->registryPackage, $registry);
  }

  /**
   * Setter ExtrinsicObject
   *
   * @param CXDSExtrinsicObject $extrinsic CXDSExtrinsicObject
   *
   * @return void
   */
  function appendExtrinsicObject($extrinsic) {
    array_push($this->extrinsicObject, $extrinsic);
  }

  /**
   * Génération du xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createRegistryObjectListRoot();
    $base_xml = $xml->documentElement;
    foreach ($this->registryPackage as $_registryPackage) {
      $xml->importDOMDocument($base_xml, $_registryPackage->toXML());
    }

    foreach ($this->extrinsicObject as $_extrinsicObject) {
      $xml->importDOMDocument($base_xml, $_extrinsicObject->toXML());
    }

    foreach ($this->association as $_association) {
      $xml->importDOMDocument($base_xml, $_association->toXML());
    }

    $xml->createSubmitObjectsRequestRoot();

    return $xml;
  }
}
