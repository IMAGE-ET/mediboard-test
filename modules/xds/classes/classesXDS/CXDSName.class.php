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
 * classe correspondant à l?élément XML rim:Name, contenant le nom
 * (en clair) de l?objet du registre ;
 */
class CXDSName {

  public $name;
  /** @var \CXDSLocalizedString  */
  public $value;

  /**
   * Construction de l'instance
   *
   * @param String $value String
   */
  function __construct($value) {
    $this->name = "Name";
    $this->value = new CXDSLocalizedString($value);
  }

  /**
   * Génération du xml
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
