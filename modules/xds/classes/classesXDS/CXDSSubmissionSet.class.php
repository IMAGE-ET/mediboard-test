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
 * Classe classification réprésentant une submissionSet
 */
class CXDSSubmissionSet extends CXDSClassification {

  public $classificationNode;

  /**
   * Construction de l'instance
   *
   * @param String $id               String
   * @param String $classifiedObject String
   */
  function __construct($id, $classifiedObject) {
    parent::__construct($id);
    $this->classificationNode = "urn:uuid:a54d6aa5-d40d-43f9-88c5-b4633d873bdd";
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