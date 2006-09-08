<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("dPpatients", "patients"));

class CAntecedent extends CMbObject {
  // DB Table key
  var $antecedent_id = null;

  // DB References
  var $patient_id = null;

  // DB fields
  var $type  = null;
  var $date  = null;
  var $rques = null;
  
  // Object References
  var $_ref_patient = null;

  function CAntecedent() {
    $this->CMbObject("antecedent", "antecedent_id");

    $this->_props["patient_id"] = "ref|notNull";
    $this->_props["type"]       = "enum|alle|trans|obst|chir|med|fam|notNull";
    $this->_props["date"]       = "date";
    $this->_props["rques"]      = "text";
  }
  
  function loadRefsFwd() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>