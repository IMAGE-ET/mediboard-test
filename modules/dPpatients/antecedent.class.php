<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

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
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "patient_id" => "ref|notNull",
      "type"       => "enum|med|alle|trans|obst|chir|fam|anesth|notNull",
      "date"       => "date",
      "rques"      => "text"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }
}

?>