<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPersonnel class
 */
class CPersonnel extends CMbObject {
  // DB Table key
  var $personnel_id = null;
  
  // DB references
  var $user_id = null;
  
  // DB fields
  var $emplacement = null;
  
  // Form Field
  var $_user_last_name = null;
  var $_user_first_name = null;
  
  function CPersonnel() {
	$this->CMbObject("personnel", "personnel_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["user_id"] = "notNull ref class|CMediusers";
    $specs["emplacement"] = "notNull enum list|op|reveil|service default|op";
    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUser();
  }
  
  function loadRefUser(){
  	$this->_ref_user = new CMediusers();
  	$this->_ref_user->load($this->user_id);
  }
 
  function updateFormFields() {
    $this->_view = "Personnel $this->user_id";
  }
  
  function loadListPers($emplacement){
    $listPers = array();
    $personnel = new CPersonnel();
    $personnel->emplacement = $emplacement;
    $listPers  = $personnel->loadMatchingList();
    foreach($listPers as $key => $pers){
      $pers->loadRefUser();
    }
    return $listPers;
  }
  
}
?>