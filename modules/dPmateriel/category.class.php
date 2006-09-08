<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("dPmateriel", "materiel"));

/**
 * The CCategory class
 */
class CCategory extends CMbObject {
  // DB Table key
  var $category_id   = null;	
  var $category_name = null;
  
  // Referencies
  var $_ref_materiel = null;
  
  function CCategory() {
    $this->CMbObject("materiel_category", "category_id");

    $this->_props["category_name"] = "str|maxLength|50|notNull";
    
    $this->_seek["category_name"] = "like";
  }
	
  function loadRefsBack(){
    $this->_ref_materiel = new CMateriel;
    $where = array();
    $where["category_id"]="= '$this->category_id'";
    $this->_ref_materiel = $this->_ref_materiel->loadList($where);
  }
  
  function canRead($withRefs = true) {
    $this->_canRead = isMbAllowed(PERM_READ, "dPmateriel", $this->category_id);
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    $this->_canEdit = isMbAllowed(PERM_EDIT, "dPmateriel", $this->category_id);
    return $this->_canEdit;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "matériel(s)", 
      "name"      => "materiel", 
      "idfield"   => "materiel_id", 
      "joinfield" => "category_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );	
  }

}
?>