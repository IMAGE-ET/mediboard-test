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
 * classe correspondant à l?élément XML rim:Slot, contenant l?insertion
 * d?une liste variable d?attributs supplémentaires à un objet du registre ;
 */
class CXDSSlot {

  public $name;
  public $data = array();

  /**
   * Création d'une instance
   *
   * @param String   $name String
   * @param String[] $data String[]
   */
  function __construct($name, $data) {
    $this->name = $name;
    $this->data = $data;
  }

  /**
   * Génération de xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createSlotRoot($this->name);
    $xml->createSlotValue($this->data);

    return $xml;
  }
}
