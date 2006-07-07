<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */


require_once( $AppUI->getSystemClass('mbobject'));
require_once( $AppUI->getModuleClass("dPmateriel", "stock") );
require_once($AppUI->getModuleClass("dPmateriel", "category"));

/**
 * The CMateriel class
 */
class CMateriel extends CMbObject {
  // DB Table key
	var $materiel_id = null;
	
  // DB Fields
  var $nom = null;
  var $code_barre = null;
  var $description = null;
  var $category_id = null;

  // Object References
  var $_refs_stock = null;
  var $_ref_category = null;
  
	function CMateriel() {
      $this->CMbObject( 'materiel', 'materiel_id' );

      $this->_props["nom"] = "str|maxLength|50|notNull";
      $this->_props["code_barre"] = "num";
      $this->_props["description"] = "str";
      $this->_props["category_id"] = "num|notNull";
	}
	
	function LoadRefsBack(){
	  $this->_refs_stock = new CStock;
	  $where = array();
	  $where["materiel_id"] = "= '$this->materiel_id'";
      $this->_refs_stock = $this->_refs_stock->loadList($where);
      foreach($this->_refs_stock as $key => $value) {
        $this->_refs_stock[$key]->loadRefsFwd();
        $this->_refs_stock[$key]->_ref_group->loadRefsFwd();
      }
	} 
	
	function LoadRefsFwd(){
	  // Forward references    
      $this->_ref_category = new CCategory;
      $this->_ref_category->load($this->category_id);	
	}
	
	function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      'label' => 'stock(s)', 
      'name' => 'stock', 
      'idfield' => 'stock_id', 
      'joinfield' => 'materiel_id'
    );
    
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
}
?>