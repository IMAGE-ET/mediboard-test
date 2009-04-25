<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision$
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
	
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]     = "ref notNull class|CMediusers";
    $specs["emplacement"] = "enum notNull list|op|op_panseuse|reveil|service default|op";
    $specs["actif"]       = "bool notNull";
    
    $specs["_user_last_name" ] = "str";
    $specs["_user_first_name"] = "str";
    return $specs;
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps['affectations'] = 'CAffectationPersonnel personnel_id';
	  return $backProps;
	}

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefUser();
  }
  
  function loadRefUser() {
    $this->_ref_user = $this->loadFwdRef("user_id");
  	$this->_view = $this->getFormattedValue("emplacement") . ": " . $this->_ref_user->_view;
  	
  }
 
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->getFormattedValue("emplacement") . ": " . $this->user_id;
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
  
  /**
   * Charge le personnel pour l'établissement courant
   * @param string $emplacement filter
   * @param bool $actif
   * @return array[CPersonnel] 
   */
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