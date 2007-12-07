<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/
  
  
class CActeNGAP extends CMbObject {
  // DB Table key
  var $acte_ngap_id = null;
  
  // DB fields
  var $quantite    = null;
  var $code        = null;
  var $coefficient = null;
  var $consultation_id = null;
  var $montant_depassement = null;
  var $montant_base = null;
  var $_preserve_montant = null;
  
  // Form Fields
  var $_montant_facture = null;
  
  
  function CActeNGAP() {
    $this->CMbObject("acte_ngap", "acte_ngap_id");
  
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "code"                => "notNull str maxLength|3",
      "quantite"            => "notNull num maxLength|2",
      "coefficient"         => "notNull float",
      "consultation_id"     => "notNull ref class|CConsultation",
      "montant_depassement" => "currency min|0",
      "montant_base"        => "currency"
    );
  }
 
  
  function checkCoded(){
    $consult = new CConsultation();
    $consult->load($this->consultation_id);
    if($consult->_coded == "1"){
      return "Consultation déjà validée";
    }    
  }
  
  
  function check(){
    parent::check();

    if($msg = $this->checkCoded()){
      return $msg;
    }
    
  }
  
  function store(){
    if($msg = parent::store()){
      return $msg;
    }
    
    if(!$this->_preserve_montant){
	    // Lancement du store de la consult pour mettre a jour secteur1 et secteur2
	    $consult = new CConsultation();
	    $consult->load($this->consultation_id);
	    $consult->updateMontants();  
	  }
  }
  
  
  function canDeleteEx(){
    parent::canDeleteEx();

    if($msg = $this->checkCoded()){
      return $msg;
    }
  }
  
  function delete(){
    if($msg = parent::delete()){
      return $msg;
    }
    
    if(!$this->_preserve_montant){
	    // Lancement du store de la consult pour mettre a jour secteur1 et secteur2
	    $consult = new CConsultation();
	    $consult->load($this->consultation_id);
	    $consult->updateMontants();   
	  } 
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Acte NGAP de la consultation".$this->consultation_id;
    $this->_montant_facture = $this->montant_base + $this->montant_depassement;
  }
}


?>