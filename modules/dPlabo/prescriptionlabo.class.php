<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CPrescriptionLabo extends CMbObject {
  // DB Table key
  var $prescription_labo_id = null;
  
  // DB references
  var $consultation_id = null;
  
  // Back references
  var $_ref_prescription_labo_examens = null;
  
  function CPrescriptionLabo() {
    $this->CMbObject("prescription_labo", "prescription_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "consultation_id" => "ref class|CConsultation notNull"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_labo_examen"] = "CPrescriptionLaboExamen prescription_labo_id";
    return $backRefs;
  }

  function updateFormFields() {
    $this->_shortview = $this->libelle;
    $this->_view      = $this->libelle;
  }
  
  function loadRefsFwd() {
    $this->_ref_consultation = new CConsultation;
    $this->_ref_consultation->load($this->consultation_id);
  }
  
  function loadRefsBack() {
    $item = new CPrescriptionLaboExamen;
    
    $where = array("prescription_labo_id" => "= $this->prescription_labo_id");
    $this->_ref_prescription_labo_examen = $item->loadList($where);
    foreach($this->_ref_prescription_labo_examen as $key => $curr_item) {
      $this->_ref_prescription_labo_examen[$key]->loadRefsFwd();
    }
  }
}

?>