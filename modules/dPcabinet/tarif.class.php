<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CTarif extends CMbObject {
  // DB Table key
  var $tarif_id = null;

  // DB References
  var $chir_id     = null;
  var $function_id = null;

  // DB fields
  var $description = null;
  var $secteur1    = null;
  var $secteur2    = null;
  var $codes_ccam  = null;
  var $codes_ngap  = null;
  
  // Form fields
  var $_type = null;
  var $_somme = null;
  var $_codes_ngap = array();
  var $_codes_ccam = array();
  
  // Remote fields
  var $_precode_ready = null;
  
  // Object References
  var $_ref_chir     = null;
  var $_ref_function = null;
  
  var $_bind_consult = null;
  var $_consult_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'tarifs';
    $spec->key   = 'tarif_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]     = "ref class|CMediusers xor|function_id";
    $specs["function_id"] = "ref class|CFunctions";
    $specs["description"] = "str notNull confidential seekable";
    $specs["secteur1"]    = "currency notNull min|0";
    $specs["secteur2"]    = "currency";
    $specs["codes_ccam"]  = "str";
    $specs["codes_ngap"]  = "str";
    $specs["_somme"]      = "currency";
    $specs["_type"]       = "";
    return $specs;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->description; 	 
    $this->_type = $this->chir_id == null ? "chir" : "function";
    $this->_codes_ngap = explode("|", $this->codes_ngap);
    $this->_codes_ccam = explode("|", $this->codes_ccam);
    CMbArray::removeValue("", $this->_codes_ngap);
    CMbArray::removeValue("", $this->_codes_ccam);
  }
  
  function updateDBFields() {
  	if ($this->_type !== null) {
      if($this->_type == "chir")
        $this->function_id = "";
      else
        $this->chir_id = "";
  	}
  }
  
  
  function bindConsultation(){
    $this->_bind_consult = false;
    
    // Chargement de la consultation
    $consult = new CConsultation();
    $consult->load($this->_consult_id);
    $consult->loadRefPlageConsult();
    $consult->loadRefsActesNGAP();
    $consult->loadRefsActesCCAM();
    
    // Affectation des valeurs au tarif
    $this->secteur1    = $consult->secteur1;
    $this->secteur2    = $consult->secteur2;
    $this->description = $consult->tarif;
    $this->codes_ccam  = $consult->_tokens_ccam;
    $this->codes_ngap  = $consult->_tokens_ngap;
    $this->chir_id     = $consult->_ref_chir->_id;
    $this->function_id = "";
  }
  
  
  function store(){ 
    if ($this->_bind_consult){
      if($msg = $this->bindConsultation()){
        return $msg;
      }
    }
    
    return parent::store();
  }
  
  function getPrecodeReady() {
    if (count($this->_codes_ccam) + count($this->_codes_ngap) == 0) {
      return $this->_precode_ready = false;
    }
    
    foreach ($this->_codes_ccam as $code) {
      $acte = new CActeCCAM();
      $acte->setCodeComplet($code);
      if (!$acte->getPrecodeReady()) {
        return $this->_precode_ready = false;
      }
    }

    foreach ($this->_codes_ngap as $code) {
      $acte = new CActeNGAP();
      $acte->setCodeComplet($code);
      if (!$acte->getPrecodeReady()) {
        return $this->_precode_ready = false;
      }
    }
    
    return $this->_precode_ready = true;
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers();
    $this->_ref_chir->load($this->chir_id);
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
    
    $this->getPrecodeReady();
  }
  
  function getPerm($permType) {
    if (!$this->_ref_chir || !$this->_ref_function) {
      $this->loadRefsFwd();
    }
    
    return 
      $this->_ref_chir->getPerm($permType) || 
      $this->_ref_function->getPerm($permType);
  }
}

?>