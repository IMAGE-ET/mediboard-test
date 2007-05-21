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
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["compte_rendu"] = "CCompteRendu file_category_id";
      $backRefs["employes"] = "CEmployeCab function_id";
      $backRefs["files"] = "CFile file_category_id";
     return $backRefs;
  }

  function getSpecs() {
    return array (
      "nom"               => "notNull str",
      "class"             => "str"
    );
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "fichier(s)", 
      "name"      => "files_mediboard", 
      "idfield"   => "file_category_id", 
      "joinfield" => "file_category_id"
    );
    
    $tables[] = array (
      "label"     => "compte(s) rendu(s)", 
      "name"      => "compte_rendu", 
      "idfield"   => "compte_rendu_id", 
      "joinfield" => "file_category_id"
    );
  return CMbObject::canDelete( $msg, $oid, $tables );	
  }
  
  function listCatClass($paramclass = null){
    $where = array();
    $where[] = db_prepare("`class` IS NULL OR `class` = %", $paramclass);
    
    $listCat = new CFilesCategory;
    return $listCat->loadList($where);
    
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

}
?>