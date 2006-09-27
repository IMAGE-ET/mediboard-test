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

    static $props = array (
      "category_name" => "str|maxLength|50|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
      "category_name" => "like"
    );
    $this->_seek =& $seek;



    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
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