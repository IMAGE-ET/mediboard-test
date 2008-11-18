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
  var $object_class  = null;
  
  // Form fields
  var $_modeles      = null;
  var $_new          = null;
  var $_del          = null;
  var $_source       = null;
  var $_object_class = null;
  
  // Referenced objects
  var $_ref_chir     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack';
    $spec->key   = 'pack_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["chir_id"]      = "notNull ref class|CMediusers";
    $specs["nom"]          = "notNull str confidential";
    $specs["modeles"]      = "text";
    $specs["object_class"] = "notNull enum list|CPatient|CConsultAnesth|COperation|CConsultation|CSejour default|COperation";
    return $specs;
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  	$this->_modeles = array();
    $this->_source = "";
    if($this->modeles != "") {
      $modeles = explode("|", $this->modeles);
      foreach($modeles as $value) {
        $this->_modeles[$value] = new CCompteRendu;
        $this->_modeles[$value]->load($value);
        if($this->_object_class == null)
          $this->_object_class = $this->_modeles[$value]->object_class;
      }
      
      $this->_source = implode('<hr class="pagebreak" />', CMbArray::pluck($this->_modeles, "source"));   
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