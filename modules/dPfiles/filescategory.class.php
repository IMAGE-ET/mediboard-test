<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPfiles
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CFilesCategory class
 */
class CFilesCategory extends CMbObject {
  // DB Table key
  var $file_category_id = null;	
  var $nom = null;
  var $class = null;
  
  
  function CFilesCategory() {
    $this->CMbObject("files_category", "file_category_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "file_category_id"  => "ref",
      "nom"               => "str|maxLength|50|notNull",
      "class"             => "str|maxLength|30"
    );
    $this->_props =& $props;

    static $seek = array (
      "nom" => "like"
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
	
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "fichier(s)", 
      "name"      => "files_mediboard", 
      "idfield"   => "file_category_id", 
      "joinfield" => "file_category_id"
    );
    
  return CMbObject::canDelete( $msg, $oid, $tables );	
  }
  
  function listCatClass($paramclass = null){
    $where = array();
    $where[] = "`class` IS NULL OR `class` = '$paramclass'";
    
    $listCat = new CFilesCategory;
    return $listCat->loadList($where);
    
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

}
?>