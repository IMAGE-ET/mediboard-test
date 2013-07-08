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
 * classe correspondant à l?élément XML
 * rim:ExternalIdentifier, contenant un identifiant externe alloué à un objet du registre tel
 * qu?une fiche, un lot de soumission ou un classeur.
 */
class CXDSExternalIdentifier extends CXDSRegistryObject {

  public $registryObject;
  public $identificationScheme;
  public $value;
  /** @var  CXDSName */
  public $name;

  /**
   * Construction de l'instance
   *
   * @param String $id             String
   * @param String $registryObject String
   * @param String $value          String
   */
  function __construct($id, $registryObject, $value) {
    parent::__construct($id);
    $this->registryObject = $registryObject;
    $this->value = $value;
    $this->objectType = "urn:oasis:names:tc:ebxmlregrep:ObjectType:RegistryObject:ExternalIdentifier";
  }

  /**
   * Setter Name
   *
   * @param String $value String
   *
   * @return void
   */
  function setName($value) {
    $this->name = new CXDSName($value);
  }

  /**
   * Génération du XML pour l'instance en cours
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createExternalIdentifierRoot($this->id, $this->identificationScheme, $this->registryObject, $this->value);
    if ($this->name) {
      $xml->importDOMDocument($xml->documentElement, $this->name->toXML());
    }

    return $xml;
  }

}
