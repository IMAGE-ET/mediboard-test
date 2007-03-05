<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CPack extends CMbObject {
  // DB Table key
  var $pack_id       = null;

  // DB References
  var $chir_id       = null;

  // DB fields
  var $nom           = null;
  var $modeles       = null;
  
  // Form fields
  var $_modeles      = null;
  var $_new          = null;
  var $_del          = null;
  var $_source       = null;
  var $_object_class = null;
  
  // Referenced objects
  var $_ref_chir     = null;

  function CPack() {
    $this->CMbObject("pack", "pack_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "chir_id" => "notNull ref class|CMediusers",
      "nom"     => "notNull str confidential",
      "modeles" => "text"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  	$this->_modeles = array();
    $this->_source = "";
    if($this->modeles != "") {
      $modeles = explode("|", $this->modeles);
      foreach($modeles as $key => $value) {
        $this->_modeles[$value] = new CCompteRendu;
        $this->_modeles[$value]->load($value);
        $this->_source .= $this->_modeles[$value]->source."<br style='page-break-after:always' />";        
        if($this->_object_class == null)
          $this->_object_class = $this->_modeles[$value]->object_class;
      }
    }
    if($this->_object_class == null)
      $this->_object_class = "COperation";
  }
  
  function updateDBFields() {
    if($this->_new !== null) {
      $this->updateFormFields();
      $this->_modeles[$this->_new] = new CCompteRendu;
      $this->_modeles[$this->_new]->load($this->_new);
      $this->modeles = "";
      foreach($this->_modeles as $key => $value)
        $this->modeles .= "|$key";
      $this->modeles = substr($this->modeles, 1);
    }
    if($this->_del !== null) {
      $this->updateFormFields();
      foreach($this->_modeles as $key => $value) {
        if($this->_del == $key)
          unset($this->_modeles[$key]);
      }
      $this->modeles = "";
      foreach($this->_modeles as $key => $value)
        $this->modeles .= "|$key";
      $this->modeles = substr($this->modeles, 1);
    }
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_chir->getPerm($permType));
  }
}

?>