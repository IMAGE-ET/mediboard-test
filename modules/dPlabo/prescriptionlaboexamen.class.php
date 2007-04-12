<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CPrescriptionLaboExamen extends CMbObject {
  // DB Table key
  var $prescription_labo_examen_id = null;
  
  // DB references
  var $prescription_labo_id = null;
  var $examen_labo_id       = null;
  
  // Forward references
  var $_ref_prescription_labo = null;
  var $_ref_examen_labo       = null;
  
  function CPrescriptionLaboExamen() {
    $this->CMbObject("prescription_labo_examen", "prescription_labo_examen_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "prescription_labo_id" => "ref class|CPrescriptionLabo notNull",
      "examen_labo_id"       => "ref class|CExamenLabo notNull"
    );
  }
  
  function updateFormFields() {
    $this->loadRefsFwd();
    $this->_shortview = $this->_ref_examen_labo->_shortview;
    $this->_view      = $this->_ref_examen_labo->_view;
  }
  
  function loadRefPrescription() {
    $this->_ref_prescription_labo = new CPrescriptionLabo;
    $this->_ref_prescription_labo->load($this->prescription_labo_id);
  }

  function loadRefExamen() {
    $this->_ref_examen_labo = new CExamenLabo;
    $this->_ref_examen_labo->load($this->examen_labo_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefPrescription();
    $this->loadRefExamen();
  }
}

?>