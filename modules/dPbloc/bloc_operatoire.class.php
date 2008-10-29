<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Fabien Ménager
 */

/**
 * The CBlocOperatoire class
 */
class CBlocOperatoire extends CMbObject {
  // DB Table key
	var $bloc_operatoire_id = null;
  
  // DB references
  var $group_id   = null;
	
  // DB Fields
  var $nom        = null;
  
  var $_ref_group = null;
  
  // Object references
  var $_ref_salles = null;
  
  function getPerm($perm_type) {
    return true;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'bloc_operatoire';
    $spec->key   = 'bloc_operatoire_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["salles"] = "CSalle bloc_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["group_id"] = "notNull ref class|CGroups";
    $specs["nom"]      = "notNull str";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_group->_view.' - '.$this->nom;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = 'nom', $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["group_id"] = "= '$g->_id'";
    
    $list = $this->loadList($where, $order, $limit, $groupby, $ljoin);
    foreach ($list as &$bloc) {
    	$bloc->loadRefsSalles();
    }
    return $list;
  }
  
  function loadRefGroup(){
    $group = new CGroups;
    $this->_ref_group = $group->getCached($this->group_id);
  }
  
  function loadRefsFwd(){
    $this->loadRefGroup();
  }
  
  function loadRefsSalles() {
  	$this->_ref_salles = $this->loadBackRefs('salles', 'nom');
  }
  
  function loadRefsBack() {
    $this->loadRefsSalles();
  }
}
?>