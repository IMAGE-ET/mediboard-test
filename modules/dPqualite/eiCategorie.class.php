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
    
    $this->_props["nom"] = "str|maxLength|50|notNull";
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