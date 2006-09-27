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

    static $props = array (
      "nom" => "str|maxLength|50|notNull",
      "code_barre" => "num",
      "description" => "str",
      "category_id" => "ref|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
      "nom"         => "like",
      "description" => "like"
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
  
  function getPerm($permType) {
    if(!$this->_ref_category) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_category->getPerm($permType));
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