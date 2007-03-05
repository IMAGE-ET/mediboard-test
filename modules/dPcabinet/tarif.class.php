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
  
  // Form fields
  var $_type = null;

  // Object References
  var $_ref_chir     = null;
  var $_ref_function = null;

  function CTarif() {
    $this->CMbObject("tarifs", "tarif_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "chir_id"     => "ref class|CMediusers",
      "function_id" => "ref class|CFunctions",
      "description" => "notNull str confidential",
      "secteur1"    => "notNull currency min|0",
      "secteur2"    => "currency min|0"
    );
  }
  
  function getSeeks() {
    return array (
      "description" => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if($this->chir_id == null)
      $_type = "chir";
    else
      $_type = "function";
  }
  
  function updateDBFields() {
  	if($this->_type !== null) {
      if($this->_type == "chir")
        $this->function_id = "";
      else
        $this->chir_id = "";
  	}
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers();
    $this->_ref_chir->load($this->chir_id);
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir || !$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_chir->getPerm($permType) && $this->_ref_function->getPerm($permType));
  }
}

?>