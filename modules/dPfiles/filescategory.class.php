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
    
  function listCatClass($paramclass = null){
    $where = array();
    $where[] = $this->_spec->ds->prepare("`class` IS NULL OR `class` = %", $paramclass);
    
    $listCat = new CFilesCategory;
    return $listCat->loadList($where);
    
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

}
?>