<?php
  
/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

CAppUI::requireModuleClass('dPccam', 'acte');

class CActeNGAP extends CActe {
  // DB Table key
  var $acte_ngap_id = null;
  
  // DB fields
  var $quantite    = null;
  var $code        = null;
  var $coefficient = null;
  var $demi        = null;
  var $complement  = null;
  
  // Form fields
  var $_short_view = null;
  
  // Distant fields
  var $_execution = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_ngap';
    $spec->key   = 'acte_ngap_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["code"]                = "str notNull maxLength|3";
    $specs["quantite"]            = "num notNull maxLength|2";
    $specs["coefficient"]         = "float notNull";
    $specs["demi"]                = "enum list|0|1 default|0";
    $specs["complement"]          = "enum list|N|F|U";

    $specs["_execution"]          = "dateTime";
    
    return $specs;
  }
 
  function updateFormFields() {
    parent::updateFormFields();
    
    // Vue codée
    $this->_shortview = $this->quantite > 1 ? "{$this->quantite}x" : "";
    $this->_shortview.= $this->code;
    if ($this->coefficient != 1) {
      $this->_shortview.= $this->coefficient;      
    }
    if ($this->demi) {
      $this->_shortview.= "/2";
    }
    
    $this->_view = "Acte NGAP $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    
    if ($this->code) {
      $this->code = strtoupper($this->code);
    }
  }
  
  function loadExecution() {
    $this->loadTargetObject();
    $this->_ref_object->getActeExecution();
    $this->_execution = $this->_ref_object->_acte_execution;
  }
  
  /**
   * CActe redefinition
   * @return string Serialised full code
   */
  function makeFullCode() {
      return $this->quantite.
        "-". $this->code.
        "-". $this->coefficient.
        "-". $this->montant_base.
        "-". str_replace("-","*", $this->montant_depassement).
        "-". $this->demi.
        "-". $this->complement; 
  }

  /**
   * CActe redefinition
   * @param string $code Serialised full code
   * @return void
   */
  function setFullCode($code){
    $details = explode("-", $code);
    $this->quantite    = $details[0];
    $this->code        = $details[1];
    $this->coefficient = $details[2];

    if (count($details) >= 4) {
      $this->montant_base = $details[3];
    }

    if (count($details) >= 5){
      $this->montant_depassement = str_replace("*","-",$details[4]);
    }
    
    if (count($details) >= 6){
    	$this->demi = $details[5];
    }

     if (count($details) >= 7){
    	$this->complement = $details[6];
    }
    
    $this->updateFormFields();
  }
  
  function getPrecodeReady() {
    return $this->quantite && $this->code && $this->coefficient;
  }
  
  function check(){
    if ($msg = $this->checkCoded()){
      return $msg;
    }
    
    return parent::check();
  }
 
  function canDeleteEx() {
    if ($msg = $this->checkCoded()){
      return $msg;
    }
    
    return parent::canDeleteEx();
  }
} 

?>