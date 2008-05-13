<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescriptionLine class
 */
class CPrescriptionLine extends CMbObject {
  
  // DB Fields
  var $prescription_id = null;
  var $ald             = null;
  var $praticien_id    = null;
  var $signee           = null;
  
  var $debut            = null;
  var $duree            = null;
  var $unite_duree      = null;
  var $date_arret       = null;
    
  var $_ref_log_signee = null;
  var $_ref_log_date_arret = null;
  var $_ref_prises = null;
  var $_fin = null;
  var $_protocole = null;
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription cascade",
      "ald"             => "bool",
      "praticien_id"    => "ref class|CMediusers",
      "signee"          => "bool",
      "debut"           => "date",
      "duree"           => "num",
      "unite_duree"     => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "date_arret"      => "date",
      "_fin"            => "date"
    );
    return array_merge($specsParent, $specs);
  }
  
 function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prise_posologie"] = "CPrisePosologie object_id";
    return $backRefs;
  }
  
  function loadRefsPrises(){
    $this->_ref_prises = $this->loadBackRefs("prise_posologie");	
  }
  
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefPrescription();
    
    $this->_protocole = ($this->_ref_prescription->object_id) ? "0" : "1";
    
    if($this->duree && $this->debut){
    	if($this->unite_duree == "minute" || $this->unite_duree == "heure" || $this->unite_duree == "demi_journee"){
    		$this->_fin = $this->debut;
    	}
    	if($this->unite_duree == "jour"){
    		$_duree_temp = mbDate("+ $this->duree DAYS", $this->debut);
    		$this->_fin = mbDate(" -1 DAYS", $_duree_temp);	
    	}
    	if($this->unite_duree == "semaine"){
    		$_duree_temp = mbDate("+ $this->duree WEEKS", $this->debut);
    		$this->_fin = mbDate(" -1 DAYS", $_duree_temp);
    	}
      if($this->unite_duree == "quinzaine"){
      	$duree_temp = 2 * $this->duree;
    		$this->_fin = mbDate("+ $duree_temp WEEKS", $this->debut);
    	}
    	if($this->unite_duree == "mois"){
    		$this->_fin = mbDate("+ $this->duree MONTHS", $this->debut);	
    	}
    	if($this->unite_duree == "trimestre"){
    		$duree_temp = 3 * $this->duree;
    		$this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);	
    	}
      if($this->unite_duree == "semestre"){
    		$duree_temp = 6 * $this->duree;
    		$this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);	
    	}
    	if($this->unite_duree == "an"){
    		$this->_fin = mbDate("+ $this->duree YEARS", $this->debut);	
    	}
    }
  }
  
  
  function loadRefPrescription(){
  	$this->_ref_prescription = new CPrescription();
  	$this->_ref_prescription->load($this->prescription_id);
  }
  
  function loadRefPraticien(){
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadRefLogSignee(){
    $this->_ref_log_signee = $this->loadLastLogForField("signee");
  }
  
  
  function loadRefLogDateArret(){ 	
  	$this->_ref_log_date_arret = $this->loadLastLogForField("date_arret");
  }
  
  

}

?>