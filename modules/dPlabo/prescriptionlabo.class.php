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
  
  // DB Fields
  var $date = null;
  
  // DB references
  var $patient_id   = null;
  var $praticien_id = null;
  
  // Forward references
  var $_ref_patient   = null;
  var $_ref_praticien = null;
  
  // Back references
  var $_ref_prescription_labo_examens = null;
  
  function CPrescriptionLabo() {
    $this->CMbObject("prescription_labo", "prescription_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "patient_id"   => "ref class|CPatient notNull",
      "praticien_id" => "ref class|CMediusers notNull",
      "date"         => "dateTime"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_labo_examen"] = "CPrescriptionLaboExamen prescription_labo_id";
    return $backRefs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->date;
    $this->_view      = "Prescription du ".mbTranformTime(null, $this->date, "%d/%m/%Y %Hh%M");
  }
  
  function loadRefsFwd() {
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->patient_id);
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadRefsBack() {
    $examen = new CPrescriptionLaboExamen;
    
    $where = array("prescription_labo_id" => "= $this->prescription_labo_id");
    $this->_ref_prescription_labo_examens = $examen->loadList($where);
    mbTrace(array_keys($this->_ref_prescription_labo_examens), "examens items");
    foreach($this->_ref_prescription_labo_examens as &$curr_examen) {
      $curr_examen->loadRefsFwd();
    }
  }
  
  function getPerm($permType) {
    $this->loadRefsFwd();
    return $this->_ref_praticien->getPerm($permType);
  }
}

?>