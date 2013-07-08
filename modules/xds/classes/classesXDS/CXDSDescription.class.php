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
 * classe correspondant à l?élément XML rim:Description,
 * contenant la description textuelle de l?objet du registre ;
 */
class CXDSDescription {

  public $name;
  /** @var \CXDSLocalizedString  */
  public $value;

  /**
   * Construction de la classe
   *
   * @param String $value String
   */
  function __construct($value) {
    $this->name = "Description";
    $this->value = new CXDSLocalizedString($value);
  }

  /**
   * Génération du xml de l'instance en cours
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createNameDescriptionRoot($this->name);
    $xml->importDOMDocument($xml->documentElement, $this->value->toXML());

    return $xml;
  }
}
