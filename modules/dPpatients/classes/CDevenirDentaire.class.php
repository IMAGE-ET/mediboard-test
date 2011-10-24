<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDevenirDentaire extends CMbObject {
  // DB Table key
  var $devenir_dentaire_id  = null;

  // DB Fields
  var $patient_id           = null;

  // Back references
  var $_ref_actes_dentaires = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'devenir_dentaire';
    $spec->key   = 'devenir_dentaire_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["patient_id"]  = "ref notNull class|CPatient";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_dentaires"] = "CActeDentaire devenir_dentaire_id";
    return $backProps;
  }
  
  function loadRefsActesDentaires() {
    return $this->_ref_actes_dentaires = $this->loadBackRefs("actes_dentaires");
  }
  
  /**
   * Identifiant du devenir dentaire du patient. 
   * Crée le devenir dentaire si nécessaire si nécessaire
   * @param $patient_id ref Identifiant du patient
   * @return ref|CDossierMedical
   */
  static function devenirDentairelId($patient_id) {
    $devenir = new CDevenirDentaire();
    $devenir->patient_id    = $patient_id;
    $devenir->loadMatchingObject();
    if(!$devenir->_id) {
      $devenir->store();
    }
    return $devenir->_id;
  }
  
}
