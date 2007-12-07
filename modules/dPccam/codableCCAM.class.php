<?php

class CCodableCCAM extends CMbObject {
	
  var $codes_ccam          = null;
  var $_codes_ccam         = null;
  var $_ref_actes_ccam     = null;
  var $_ext_codes_ccam     = null;
  var $_acte_execution     = null;
  var $_acte_depassement   = null;
  var $_datetime           = null;
  var $_praticien_id       = null;
  var $temp_operation      = null;
  var $_ref_anesth         = null;
  var $_anesth             = null;
  var $_coded              = null;
  var $_associationCodesActes = null;

  function updateFormFields() {
  	parent::updateFormFields();
    
    $this->codes_ccam = strtoupper($this->codes_ccam);
    $this->_codes_ccam = $this->codes_ccam ? 
      explode("|", $this->codes_ccam) : 
      array(); 
  }
  
  function getAssociationCodesActes() {
    $this->updateFormFields();
    $this->loadRefsActesCCAM();
    $this->_associationCodesActes = array();
    $listCodes = $this->_codes_ccam;
    $listActes = $this->_ref_actes_ccam;
    $i = 0;
    foreach($listCodes as $curr_code) {
      $code_complet = explode("-", $curr_code);
      $ccam     = $code_complet[0];
      $phase    = isset($code_complet[1]) ? $code_complet[1] : null;
      $activite = isset($code_complet[2]) ? $code_complet[2] : null;
      $this->_associationCodesActes[$i]["code"]    = $curr_code;
      $this->_associationCodesActes[$i]["nbActes"] = 0;
      $this->_associationCodesActes[$i]["ids"]     = "";
      foreach($listActes as $key_acte => $curr_acte) {
        $test = ($curr_acte->code_acte == $ccam);
        $test = $test && ($phase === null || $curr_acte->code_phase == $phase);
        $test = $test && ($activite === null || $curr_acte->code_activite == $activite);
        $test = $test && (!isset($this->_associationCodesActes[$i]["actes"][$curr_acte->code_phase][$curr_acte->code_activite]));
        if($test) {
          $this->_associationCodesActes[$i]["actes"][$curr_acte->code_phase][$curr_acte->code_activite] = $curr_acte;
          $this->_associationCodesActes[$i]["nbActes"]++;
          $this->_associationCodesActes[$i]["ids"] .= "$curr_acte->_id|";
          unset($listActes[$key_acte]);
        }
      }
      $i++;
    }
  }
  
  function updateDBCodesCCAMField() {
    if (null !== $this->_codes_ccam) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }
  }
  
  
  function updateMontants(){
    
  }
  
  function updateDBFields() {
    // Should update codes CCAM. Very sensible, test a lot before uncommenting
    // $this->updateDBCodesCCAMField();
  }
  
  function getSpecs() {
  	$specs["codes_ccam"] = "str";
  	return $specs;
  }
  
  function preparePossibleActes() {
  }
  
  function getExecutantId($code_activite) {
    return null;
  }
  
  function loadRefsCodesCCAM($full = 0) {
    $this->_ext_codes_ccam = array();
    if($this->_codes_ccam !== null) {
      foreach ($this->_codes_ccam as $code) {
        $ext_code_ccam = new CCodeCCAM($code);
        if($full) {
        	$ext_code_ccam->Load();
        } else {
          $ext_code_ccam->LoadLite();
        }
        $this->_ext_codes_ccam[] = $ext_code_ccam;
      }
    }
  }

  

  function getMaxCodagesActes() {
    if(!$this->_id || $this->codes_ccam === null) {
      return;
    }

    $oldObject = new $this->_class_name;
	  $oldObject->load($this->_id);
	  $oldObject->codes_ccam = $this->codes_ccam;
	  $oldObject->updateFormFields();
	    
	  $nb_codes_ccam_minimal = array();
	  $codes_ccam_minimal = array();
	  $nb_codes_ccam = array();
	    
	  $oldObject->loadRefsActesCCAM();
	    
	  // Creation du tableau minimal de codes ccam
	  foreach($oldObject->_ref_actes_ccam as $key => $acte){
	    @$codes_ccam_minimal[$acte->code_acte][$acte->code_activite][$acte->code_phase]++;
	  }
	  foreach($codes_ccam_minimal as $key => $acte){
	    $nb_codes_ccam_minimal[$key] = reset(max($acte));
	  }
	  foreach($nb_codes_ccam_minimal as $key => $acte) {
	    for($i = 0; $i < $acte; $i++){
	      $oldObject->_codes_ccam_minimal[] = $key;
	    }
	  }

	  // Transformation du tableau de codes ccam
	  foreach($oldObject->_codes_ccam as $key => $code) {
	    if(strlen($code) > 7){
	      // si le code est de la forme code-activite-phase
        $detailCode = explode("-", $code);
        $code = $detailCode[0];
	    }
	    @$nb_codes_ccam[$code]++;
	  }
	  
	  
	  // Test entre les deux tableaux
	  foreach($nb_codes_ccam_minimal as $code => $nb_code_minimal){
	    if($nb_code_minimal > @$nb_codes_ccam[$code]){
	      return "Impossible de supprimer le code";
	    }
	  }
  }
  
  
  
  function check(){
    $oldObject = new $this->_class_name;
    if($this->_id) {
      $oldObject->load($this->_id);
    }
    
    if($this->codes_ccam != $oldObject->codes_ccam){
      if ($msg = $this->getMaxCodagesActes()) {
        return $msg;
      }
    }   
    return parent::check();
  }
  
  function loadRefsActesCCAM() {
  	$acte = new CActeCCAM();
  	$acte->object_id = $this->_id;
  	$acte->object_class = $this->_class_name;
  	
  	$order = array();
  	$order[] = "code_association";
  	$order[] = "code_acte";
  	$order[] = "code_activite";
  	$order[] = "code_phase";
  	$order[] = "acte_id";
  	
  	$this->_ref_actes_ccam = $acte->loadMatchingList($order);
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
          
          
          $possible_acte->_anesth= ( $activite->numero == 4 ) ? true : false;
          
          
          $possible_acte->code_phase = $phase->phase;
          $possible_acte->execution = $this->_acte_execution;
          
          if (!$depassement_affecte and $possible_acte->code_activite == 1) {
            $depassement_affecte = true;     	
            $possible_acte->montant_depassement = $this->_acte_depassement;
          }
          
          $possible_acte->executant_id = $this->getExecutantId($possible_acte->code_activite);
          $possible_acte->updateFormFields();
          $possible_acte->loadRefs();
          $possible_acte->getAnesthAssocie();
                    
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
          
          $possible_acte->getTarif();
          
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