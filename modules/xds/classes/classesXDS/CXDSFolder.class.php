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
 * Classe classification réprésentant un folder
 */
class CXDSFolder extends CXDSClassification {

  public $classificationNode;

  /**
   * Construction de l'instance
   *
   * @param String $id               String
   * @param String $classifiedObject String
   */
  function __construct($id, $classifiedObject) {
    parent::__construct($id);
    $this->classificationNode = "urn:uuid:d9d542f3-6cc4-48b6-8870-ea235fbc94c2";
    $this->classifiedObject = $classifiedObject;
  }

  /**
   * @see parent::toXML()
   */
  function toXML() {
    $xml = parent::toXML(true);
    return $xml;
  }
}