<?php
  
/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPccam", "acte"));

class CActeNGAP extends CActe {
  // DB Table key
  var $acte_ngap_id = null;
  
  // DB fields
  var $quantite    = null;
  var $code        = null;
  var $coefficient = null;
 
  function CActeNGAP() {
    $this->CMbObject("acte_ngap", "acte_ngap_id");
  
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["code"]                = "notNull str maxLength|3";
    $specs["quantite"]            = "notNull num maxLength|2";
    $specs["coefficient"]         = "notNull float";
    return $specs;
  }
 
  function check(){
    parent::check();
    if($msg = $this->checkCoded()){
      return $msg;
    }
  }
  
 
  function canDeleteEx(){
    parent::canDeleteEx();
    if($msg = $this->checkCoded()){
      return $msg;
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Acte NGAP de ".$this->object_class." : ".$this->object_id;
  }
} 
  
  
?>