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
  var $files_category_id = null;	
  var $nom = null;
  var $class = null;
  
  
  function CFilesCategory() {
    $this->CMbObject( "files_category", "files_category_id" );

    $this->_props["files_category_id"] = "ref";
    $this->_props["nom"]               = "str|maxLength|50|notNull";
    $this->_props["class"]             = "str|maxLength|30";
  }
	
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "fichier(s)", 
      "name" => "files_mediboard", 
      "idfield" => "files_category_id", 
      "joinfield" => "files_category_id"
    );
    
  return CDpObject::canDelete( $msg, $oid, $tables );	
  }
  
  function lstCatClass($paramclass = null){
    $where = array();
    $where[] = "`class` IS NULL OR `class` = '$paramclass'";
    
    $listCat = new CFilesCategory;
    return $listCat->loadList($where);
    
  }
  
  function updateFormFields(){
    $this->_view = $this->nom;
  }

}
?>