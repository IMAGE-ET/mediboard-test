<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPfiles
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

require_once( $AppUI->getSystemClass("mbobject"));

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

    $this->_props["file_category_id"]  = "ref";
    $this->_props["nom"]               = "str|maxLength|50|notNull";
    $this->_props["class"]             = "str|maxLength|30";
    
    $this->_seek["nom"] = "like";
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