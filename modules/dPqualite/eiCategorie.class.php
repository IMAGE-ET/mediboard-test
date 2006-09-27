<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CEiCategorie class
 */
class CEiCategorie extends CMbObject {
  // DB Table key
  var $ei_categorie_id  = null;
    
  // DB Fields
  var $nom              = null;

  // Object References
  var $_ref_items       = null;

  function CEiCategorie() {
    $this->CMbObject("ei_categories", "ei_categorie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "nom" => "str|maxLength|50|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
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
  
  function loadRefsBack() {
    $this->_ref_items = new CEiItem;
    $where = array();
    $where["ei_categorie_id"] = "= '$this->ei_categorie_id'";
    $order = "nom ASC";
    $this->_ref_items = $this->_ref_items->loadList($where, $order);
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "item(s)", 
      "name" => "ei_item", 
      "idfield" => "ei_item_id", 
      "joinfield" => "ei_categorie_id"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
}
?>