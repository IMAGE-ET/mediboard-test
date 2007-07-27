<?php

class CCodableCCAM extends CMbObject {
	
  var $codes_ccam         = null;
  var $_codes_ccam        = null;
  var $_ref_actes_ccam    = null;
  var $_ext_codes_ccam    = null;
  var $_acte_execution    = null;
  var $_acte_depassement  = null;
  var $_datetime          = null;
  var $_praticien_id      = null;
  var $temp_operation     = null;
  var $_ref_anesth        = null;
  var $_anesth = null;

  function updateFormFields() {
  	parent::updateFormFields();
    
    $this->codes_ccam = strtoupper($this->codes_ccam);
    $this->_codes_ccam = array();
    if($this->codes_ccam)
      $this->_codes_ccam = explode("|", $this->codes_ccam);
  }
    
  function getSpecs() {
  	$specs["codes_ccam"] = "str";
  	return $specs;
  }
  
  function preparePossibleActes() {
  }
  
  function loadRefsCodesCCAM() {
    $this->_ext_codes_ccam = array();
    if($this->_codes_ccam !== null) {
      foreach ($this->_codes_ccam as $code) {
        $ext_code_ccam = new CCodeCCAM($code);
        $ext_code_ccam->LoadLite();
        $this->_ext_codes_ccam[] = $ext_code_ccam;
      }
    }
  }

  function loadRefsActesCCAM() {
  	$acte = new CActeCCAM();
  	$acte->object_id = $this->_id;
  	$acte->object_class = $this->_class_name;
  	
  	$this->_ref_actes_ccam = $acte->loadMatchingList();
  }
  
  function loadPossibleActes () {
  	$this->preparePossibleActes();
    $depassement_affecte = false;
    // existing acts may only be affected once to possible acts
    $used_actes = array();
    foreach ($this->_ext_codes_ccam as $codeKey => $codeValue) {
      $code =& $this->_ext_codes_ccam[$codeKey];
      $code->load($code->code);
     
      foreach ($code->activites as $activiteKey => $activiteValue) {
        $activite =& $code->activites[$activiteKey];
        foreach ($activite->phases as $phaseKey => $phaseValue) {
          $phase =& $activite->phases[$phaseKey];     
          $possible_acte = new CActeCCAM;
          $possible_acte->montant_depassement = 0;
          $possible_acte->code_acte = $code->code;
          $_code = $possible_acte->code_activite = $activite->numero;
          
          
          $possible_acte->_anesth=($activite->numero==4)?true:false;
          
          
          $possible_acte->code_phase = $phase->phase;
          $possible_acte->execution = $this->_acte_execution;
          
          if (!$depassement_affecte and $possible_acte->code_activite == 1) {
            $depassement_affecte = true;     	
            $possible_acte->montant_depassement = $this->_acte_depassement;
          }
          
          $possible_acte->executant_id = $this->getExecutant_id($possible_acte->code_activite);
          $possible_acte->updateFormFields();
          $possible_acte->loadRefs();
                    
          // Affect a loaded acte if exists
          foreach ($this->_ref_actes_ccam as $curr_acte) {
            if ($curr_acte->code_acte == $possible_acte->code_acte 
            and $curr_acte->code_activite == $possible_acte->code_activite 
            and $curr_acte->code_phase == $possible_acte->code_phase) {
              if (!isset($used_actes[$curr_acte->acte_id])) {
                $possible_acte = $curr_acte;
                $used_actes[$curr_acte->acte_id] = true;
                break;
              }
            }
          }
          
          $phase->_connected_acte = $possible_acte;
          
          foreach ($phase->_modificateurs as $modificateurKey => $modificateurValue) {
            $modificateur =& $phase->_modificateurs[$modificateurKey];
            if (strpos($phase->_connected_acte->modificateurs, $modificateur->code) !== false) {
              $modificateur->_value = $modificateur->code;
            } else {
              $modificateur->_value = "";              
            }
          }
        }
      }
    } 
  }
  
}
?>