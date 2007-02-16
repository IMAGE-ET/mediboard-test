<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

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
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "category_name" => "notNull str maxLength|50"
    );
  }
  
  function getSeeks() {
    return array (
      "category_name" => "like"
    );
  }
	
  function loadRefsBack(){
    $this->_ref_materiel = new CMateriel;
    $where = array();
    $where["category_id"]="= '$this->category_id'";
    $this->_ref_materiel = $this->_ref_materiel->loadList($where);
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