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

  /**
   * Retourne un type confidentialité pour masquer au PS
   *
   * @param String $id               Identifiant
   * @param String $classifiedObject ClassifiedObject
   *
   * @return CXDSConfidentiality
   */
  static function getMasquagePS($id, $classifiedObject) {
    $confidentiality = new CXDSConfidentiality($id, $classifiedObject, "MASQUE_PS");
    $confidentiality->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
    $confidentiality->setName("Document masqué aux professionnels de santé");
    return $confidentiality;
  }

  /**
   * Retourne un type confidentialité pour masquer au  patient
   *
   * @param String $id               Identifiant
   * @param String $classifiedObject ClassifiedObject
   *
   * @return CXDSConfidentiality
   */
  static function getMasquagePatient($id, $classifiedObject) {
    $confidentiality = new CXDSConfidentiality($id, $classifiedObject, "INVISIBLE_PATIENT");
    $confidentiality->setCodingScheme(array("1.2.250.1.213.1.1.4.13"));
    $confidentiality->setName("Document non visible par le patient");
    return $confidentiality;
  }
}