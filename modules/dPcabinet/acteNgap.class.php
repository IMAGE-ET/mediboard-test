<?php
  
/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
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
  
  function getSpecs() {
    $specs = parent::getSpecs();
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
    $this->_shortview.= "$this->code$this->coefficient";
    if ($this->demi) {
      $this->_shortview.= "/2";
    }
    
    $this->_view = "Acte NGAP $this->_shortview de $this->object_class:$this->object_id";
  }
  
  function loadExecution() {
    $this->loadTargetObject();
    $this->_ref_object->getActeExecution();
    $this->_execution = $this->_ref_object->_acte_execution;
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