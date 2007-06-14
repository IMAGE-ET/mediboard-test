<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CPrescriptionLabo extends CMbObject {
  
  // Status const
  const VIERGE       = 16;
  const PRELEVEMENTS = 32;
  const VEROUILLEE   = 48;
  const TRANSMISE    = 64;
  const SAISIE       = 80;
  const VALIDEE      = 96;
  const FERMEE       = 112;
  
  // DB Table key
  var $prescription_labo_id = null;
  
  // DB Fields
  var $date       = null;
  var $verouillee = null;
  
  // DB references
  var $patient_id   = null;
  var $praticien_id = null;
  
  // Form Fields
  var $_status = null;
  
  // Forward references
  var $_ref_patient   = null;
  var $_ref_praticien = null;
  
  // Back references
  var $_ref_prescription_items = null;
  
  // Distant references
  var $_ref_examens = null;
  var $_ref_classification_roots = null;
  
  function CPrescriptionLabo() {
    $this->CMbObject("prescription_labo", "prescription_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "patient_id"   => "ref class|CPatient notNull",
      "praticien_id" => "ref class|CMediusers notNull",
      "date"         => "dateTime",
      "verouillee"   => "bool"
    );
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_labo_examen"] = "CPrescriptionLaboExamen prescription_labo_id";
    return $backRefs;
  }
  
  function getSeeks() {
    return array( "patient_id" => "ref|CPatient" );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->date;
    $this->_view      = "Prescription du ".mbTranformTime(null, $this->date, "%d/%m/%Y %Hh%M");
    $this->getStatus();
  }
  
  function loadRefsFwd() {
    if (!$this->_ref_patient) {
      $this->_ref_patient = new CPatient();
      $this->_ref_patient->load($this->patient_id);
    }
    
    if (!$this->_ref_praticien) {
      $this->_ref_praticien = new CMediusers();
      $this->_ref_praticien->load($this->praticien_id);
    }
  }
  
  function loadRefsBack() {
    if (!$this->_ref_prescription_items) {
      $item = new CPrescriptionLaboExamen;
      $item->prescription_labo_id = $this->_id;
      $this->_ref_prescription_items = $item->loadMatchingList();
      $this->_ref_examens = array();
      foreach ($this->_ref_prescription_items as &$_item) {
        $_item->_ref_prescription_labo =& $this;
        $_item->loadRefsFwd();
        $examen =& $_item->_ref_examen_labo;
        $this->_ref_examens[$examen->_id] =& $examen; 
      }
    }
  }
  
  function getStatus() {
    if($this->getNumFiles()) {
      $this->_status = self::TRANSMISE;
      return $this->_status;
    }
    if($this->verouillee) {
      $this->_status = self::VEROUILLEE;
      return $this->_status;
    }
    if($this->countBackRefs("prescription_labo_examen")) {
      $this->_status = self::PRELEVEMENTS;
      return $this->_status;
    }
    $this->_status = self::VIERGE;
    return $this->_status;
  }

  /**
   * Load minimal catalogue classification to cover the prescription analyses
   */
  function loadClassification() {
    $catalogues = array();
    
    // Load needed catalogues
    foreach ($this->_ref_examens as $examen) {
      $catalogue_id = $examen->catalogue_labo_id;
      if (!array_key_exists($catalogue_id, $catalogues)) {
        $catalogue = new CCatalogueLabo;
        $catalogue->load($catalogue_id);
        $catalogue->_ref_catalogues_labo = array();
        $catalogues[$catalogue->_id] = $catalogue;
      }
    }

    // Complete catalogue hierarchy
    foreach ($catalogues as $_catalogue) {
      $child_catalogue = $_catalogue;
      while ($child_catalogue->pere_id && !array_key_exists($child_catalogue->pere_id, $catalogues)) {
        $catalogue = new CCatalogueLabo;
        $catalogue->load($child_catalogue->pere_id);
        $catalogues[$catalogue->_id] = $catalogue;
        $child_catalogue = $catalogue;
      }
    }

    // Prepare catalogues collections
    foreach ($catalogues as &$ref_catalogue) {
      $ref_catalogue->_ref_catalogues_labo = array();
      $ref_catalogue->_ref_prescription_items = array();
    }

    // Feed prescription items
    foreach ($this->_ref_prescription_items as $_item) {
      $catalogue_id = $_item->_ref_examen_labo->catalogue_labo_id;
      $catalogues[$catalogue_id]->_ref_prescription_items[$_item->_id] = $_item;
    }

    // Link catalogue hierarchy
    foreach ($catalogues as &$link_catalogue) {
      if ($parent_id = $link_catalogue->pere_id) {
        $parent_catalogue =& $catalogues[$parent_id];
        $parent_catalogue->_ref_catalogues_labo[$link_catalogue->_id] =& $link_catalogue;
        $link_catalogue->_ref_pere =& $parent_catalogue;
      } 
    }
    
    // Find classifications roots
    foreach ($catalogues as &$root_catalogue) {
      if ($root_catalogue->computeLevel() == 0) {
        $this->_ref_classification_roots[$root_catalogue->_id] =& $root_catalogue;
      }
    }
  } 
   
  function getPerm($permType) {
    $this->loadRefsFwd();
    return $this->_ref_praticien->getPerm($permType);
  }
}

?>