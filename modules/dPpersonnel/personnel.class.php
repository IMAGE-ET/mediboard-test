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
  var $actif       = null;
  
  // Form Field
  var $_user_last_name = null;
  var $_user_first_name = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'personnel';
    $spec->key   = 'personnel_id';
    return $spec;
  }
	
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["user_id"]     = "notNull ref class|CMediusers";
    $specs["emplacement"] = "notNull enum list|op|op_panseuse|reveil|service default|op";
    $specs["actif"]       = "notNull bool";
    
    $specs["_user_last_name" ] = "str";
    $specs["_user_first_name"] = "str";
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
  
  static function loadListPers($emplacement, $actif = true){
    $personnel = new CPersonnel();
    $personnel->emplacement = $emplacement;
    if($actif) {
      $personnel->actif = 1;
    }
    $ljoin["users"] = "personnel.user_id = users.user_id";
    $order = "users.user_last_name";
    $listPers = $personnel->loadMatchingList($order, null, null, $ljoin);
    foreach($listPers as $pers){
      $pers->loadRefUser();
    }
    return $listPers;
  }
  
}
?>