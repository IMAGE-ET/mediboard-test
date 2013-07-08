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
 * Classe classification représentant la variable confidentiality d"ExtrinsicObject
 * Ensemble de métadonnées contenant les informations définissant le niveau de confidentialité d?un
 * document déposé dans l?entrepôt.
 */
class CXDSConfidentiality extends CXDSClass {

  /**
   * Construction de la classe
   *
   * @param String $id                 String
   * @param String $classifiedObject   String
   * @param String $nodeRepresentation String
   */
  function __construct($id, $classifiedObject, $nodeRepresentation) {
    parent::__construct($id, $classifiedObject, $nodeRepresentation);
    $this->classificationScheme = "urn:uuid:f4f85eac-e6cb-4883-b524-f2705394840f";
  }
}