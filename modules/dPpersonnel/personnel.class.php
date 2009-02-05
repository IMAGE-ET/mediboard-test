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
    $specs["user_id"]     = "ref notNull class|CMediusers";
    $specs["emplacement"] = "enum notNull list|op|op_panseuse|reveil|service default|op";
    $specs["actif"]       = "bool notNull";
    
    $specs["_user_last_name" ] = "str";
    $specs["_user_first_name"] = "str";
    return $specs;
  }
  
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs['affectations'] = 'CAffectationPersonnel personnel_id';
	  return $backRefs;
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
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
		$ljoin["users_mediboard"] = "users_mediboard.user_id = personnel.user_id";
		$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["functions_mediboard.group_id"] = "= '$g->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
  
  static function loadListPers($emplacement, $actif = true){
    $personnel = new CPersonnel();
    $where["emplacement"] = "= '$emplacement'";
    
    // Could have been ambiguous with CMediusers.actif
    if ($actif) {
      $where[] = "personnel.actif = '1'";
    }
    
    $ljoin["users"] = "personnel.user_id = users.user_id";
    $order = "users.user_last_name";
    $listPers = $personnel->loadGroupList($where, $order, null, null, $ljoin);
    foreach($listPers as $pers){
      $pers->loadRefUser();
    }
    return $listPers;
  }
  
}
?>