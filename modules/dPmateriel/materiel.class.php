<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPmateriel
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CMateriel class
 */
class CMateriel extends CMbObject {
  // DB Table key
  var $materiel_id = null;
  
  // DB Fields
  var $nom        = null;
  var $code_barre = null;
  var $description = null;
  var $category_id = null;

  // Object References
  var $_ref_stock       = null;
  var $_ref_refMateriel = null;
  var $_ref_category    = null;
  
  function CMateriel() {
    $this->CMbObject("materiel", "materiel_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["nom"] = "str|maxLength|50|notNull";
    $this->_props["code_barre"] = "num";
    $this->_props["description"] = "str";
    $this->_props["category_id"] = "ref|notNull";
    
    $this->_seek["nom"]         = "like";
    $this->_seek["description"] = "like";
  }
  
  function loadRefsBack(){
    $this->_ref_stock = new CStock;
    $where = array();
    $where["materiel_id"] = "= '$this->materiel_id'";
    $this->_ref_stock = $this->_ref_stock->loadList($where);
      
    $this->_ref_refMateriel = new CRefMateriel;
    $where = array();
    $where["materiel_id"] = "= '$this->materiel_id'";
    $this->_ref_refMateriel = $this->_ref_refMateriel->loadList($where);
  } 
  
  function loadRefsFwd(){ 
    $this->_ref_category = new CCategory;
    $this->_ref_category->load($this->category_id);  
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canRead = $this->_ref_category->canRead();
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canEdit = $this->_ref_category->canEdit();
    return $this->_canEdit;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "stock(s)", 
      "name"      => "stock", 
      "idfield"   => "stock_id", 
      "joinfield" => "materiel_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>