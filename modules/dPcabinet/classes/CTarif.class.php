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
  var $codes_tarmed = null;
  var $codes_caisse = null;
  
  // Form fields
  var $_type       = null;
  var $_somme      = null;
  var $_codes_ngap = array();
  var $_codes_ccam = array();
  var $_codes_tarmed = array();
  var $_codes_caisse = array();
  var $_new_actes  = array();
  
  // Remote fields
  var $_precode_ready = null;
  var $_secteur1_uptodate = null;
  var $_has_mto = null;
  
  // Behaviour fields
  var $_add_mto = null;
	var $_update_montants = null;
  
  // Object References
  var $_ref_chir     = null;
  var $_ref_function = null;
  
  var $_bind_consult = null;
  var $_consult_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'tarifs';
    $spec->key   = 'tarif_id';
    $spec->xor["owner"] = array("function_id", "chir_id");
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]     = "ref class|CMediusers";
    $specs["function_id"] = "ref class|CFunctions";
    $specs["description"] = "str notNull confidential seekable";
    $specs["secteur1"]    = "currency notNull min|0";
    $specs["secteur2"]    = "currency";
    $specs["codes_ccam"]  = "str";
    $specs["codes_ngap"]  = "str";
    $specs["codes_tarmed"]= "str";
    $specs["codes_caisse"]= "str";
    $specs["_somme"]      = "currency";
    $specs["_type"]       = "";
    
    $specs["_precode_ready"] = "bool";
    $specs["_has_mto"]       = "bool";
    
    return $specs;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->description; 	 
    $this->_type = $this->chir_id == null ? "chir" : "function";
    $this->_codes_ngap = explode("|", $this->codes_ngap);
    $this->_codes_ccam = explode("|", $this->codes_ccam);
    $this->_codes_tarmed = explode("|", $this->codes_tarmed);
    $this->_codes_caisse = explode("|", $this->codes_caisse);
    CMbArray::removeValue("", $this->_codes_ngap);
    CMbArray::removeValue("", $this->_codes_ccam);
    CMbArray::removeValue("", $this->_codes_tarmed);
    CMbArray::removeValue("", $this->_codes_caisse);
    $this->_somme = $this->secteur1 + $this->secteur2;
  }
  
  function updatePlainFields() {
  	if ($this->_type !== null) {
  		$other_field = $this->_type == "chir" ? "function_id" : "chir_id";
      $this->$other_field = "";
  	}

    $this->updateMontants();
    $this->bindConsultation();
  }
  
  function bindConsultation() {
    if (!$this->_bind_consult){
      return;
    }

    $this->_bind_consult = false;
    
    // Chargement de la consultation
    $consult = new CConsultation();
    $consult->load($this->_consult_id);
    $consult->loadRefPlageConsult();
    $consult->loadRefsActesNGAP();
    $consult->loadRefsActesCCAM();
    $consult->loadRefsActesTarmed();
    $consult->loadRefsActesCaisse();
    
    // Affectation des valeurs au tarif
    $this->secteur1    = $consult->secteur1;
    $this->secteur2    = $consult->secteur2;
    $this->description = $consult->tarif;
    $this->codes_ccam  = $consult->_tokens_ccam;
    $this->codes_ngap  = $consult->_tokens_ngap;
    $this->codes_tarmed= $consult->_tokens_tarmed;
    $this->codes_caisse= $consult->_tokens_caisse;
    $this->chir_id     = $consult->_ref_chir->_id;
    $this->function_id = "";
  }
  
  function store() { 
    if ($this->_add_mto) {
      $this->completeField("codes_ngap");
      $this->codes_ngap .= "|1-MTO-1---0-";
    }
    	    
    return parent::store();
  }
	
	function updateMontants() {
		if (!$this->_update_montants) {
			return;
		}		

    $this->secteur1 = 0.00;
		$secteur2 = $this->secteur2;

    // Actes CCAM
    $this->completeField("codes_ccam");
    $this->_codes_ccam = explode("|", $this->codes_ccam);
    CMbArray::removeValue("", $this->_codes_ccam);
    foreach ($this->_codes_ccam as &$_code) {
      $acte = new CActeCCAM;
      $acte->setFullCode($_code);
      $this->secteur1 += $acte->updateMontantBase(); 

      // Affectation du secteur 2 au dépassement du premier acte trouvé
      $acte->montant_depassement = $secteur2 ? $secteur2 : 0;
      $secteur2 = 0;
			
      $_code = $acte->makeFullCode();
    }
    
		// Actes NGAP
    $this->completeField("codes_ngap");
    $this->_codes_ngap = explode("|", $this->codes_ngap);
    CMbArray::removeValue("", $this->_codes_ngap);
		foreach ($this->_codes_ngap as &$_code) {
	    $acte = new CActeNGAP;
	    $acte->setFullCode($_code);
      $this->secteur1 += $acte->updateMontantBase();	
			
      // Affectation du secteur 2  au dépassement du premier acte trouvé
      $acte->montant_depassement = $secteur2 ? $secteur2 : 0;
      $secteur2 = 0;

			$_code = $acte->makeFullCode();
    }
    $this->codes_ngap = implode("|", $this->_codes_ngap);
    
    if(CModule::getInstalled("tarmed")){
	    // Actes Tarmed 
	    $this->completeField("codes_tarmed");
	    $this->_codes_tarmed = explode("|", $this->codes_tarmed);
	    CMbArray::removeValue("", $this->_codes_tarmed);
			foreach ($this->_codes_tarmed as &$_code) {
		    $acte = new CActeTarmed;
		    $acte->setFullCode($_code);
	      $this->secteur1 += $acte->updateMontantBase();	
				
	      // Affectation du secteur 2  au dépassement du premier acte trouvé
	      $acte->montant_depassement = $secteur2 ? $secteur2 : 0;
	      $secteur2 = 0;
	
				$_code = $acte->makeFullCode();
	    }
	    $this->codes_tarmed = implode("|", $this->_codes_tarmed);
	    
	    // Actes Caisse
	    $this->completeField("codes_caisse");
	    $this->_codes_caisse = explode("|", $this->codes_caisse);
	    CMbArray::removeValue("", $this->_codes_caisse);
			foreach ($this->_codes_caisse as &$_code) {
		    $acte = new CActeCaisse;
		    $acte->setFullCode($_code);
	      $this->secteur1 += $acte->updateMontantBase();	
				
	      // Affectation du secteur 2  au dépassement du premier acte trouvé
	      $acte->montant_depassement = $secteur2 ? $secteur2 : 0;
	      $secteur2 = 0;
	
				$_code = $acte->makeFullCode();
	    }
	    $this->codes_caisse = implode("|", $this->_codes_caisse);
    }
		return $this->secteur1;
	}
	
	function getSecteur1Uptodate() {
		if ((!$this->codes_ngap && !$this->codes_ccam) || (!$this->codes_tarmed && !$this->codes_caisse)) {
			return $this->_secteur1_uptodate = "1";
		}
		
		// Backup ...
		$secteur1   = $this->secteur1;
    $codes_ccam = $this->_codes_ccam;
    $codes_ngap = $this->_codes_ngap;
    $codes_tarmed = $this->_codes_tarmed;
    $codes_caisse = $this->_codes_caisse;
    
		// Compute...
    $this->_update_montants = true;
		$new_secteur1 = $this->updateMontants();
    
		// ... and restore
    $this->secteur1 = $secteur1;
    $this->_codes_ccam = $codes_ccam;
    $this->_codes_ngap = $codes_ngap;
    $this->_codes_tarmed = $codes_tarmed;
    $this->_codes_caisse = $codes_caisse;

    return $this->_secteur1_uptodate = CFloatSpec::equals($secteur1, $new_secteur1, $this->_specs["secteur1"]) ? "1" : "0";
	}
  
  function getPrecodeReady() {
    $this->_has_mto = '0';
    $this->_new_actes = array();
    
    if (count($this->_codes_ccam) + count($this->_codes_ngap) + count($this->_codes_tarmed) + count($this->_codes_caisse) == 0) {
      return $this->_precode_ready = '0';
    }
    
    foreach ($this->_codes_ccam as $code) {
      $acte = new CActeCCAM();
      $acte->setFullCode($code);
      $this->_new_actes[$code] = $acte;
      if (!$acte->getPrecodeReady()) {
        return $this->_precode_ready = '0';
      }
    }

    foreach ($this->_codes_ngap as $code) {
      $acte = new CActeNGAP();
      $acte->setFullCode($code);
      $this->_new_actes["$code"] = $acte;
      if (!$acte->getPrecodeReady()) {
        return $this->_precode_ready = '0';
      }
      
      if (in_array($acte->code, array("MTO", "MPJ"))) {
        $this->_has_mto = '1';
      }
    }
    if(CModule::getInstalled("tarmed")){
	    foreach ($this->_codes_tarmed as $code) {
	      $acte = new CActeTarmed();
	      $acte->setFullCode($code);
	      $acte->loadRefTarmed(CTarmed::LITE);
	      $this->_new_actes["$code"] = $acte;
	      if (!$acte->getPrecodeReady()) {
	        return $this->_precode_ready = '0';
	      }
	    }
	    foreach ($this->_codes_caisse as $code) {
	      $acte = new CActeCaisse();
	      $acte->setFullCode($code);
	      $this->_new_actes["$code"] = $acte;
	      if (!$acte->getPrecodeReady()) {
	        return $this->_precode_ready = '0';
	      }
	    }
    }
    
    return $this->_precode_ready = '1';
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