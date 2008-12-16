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
  var $validation_auto = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_category';
    $spec->key   = 'file_category_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["compte_rendu"] = "CCompteRendu file_category_id";
    $backRefs["employes"] = "CEmployeCab function_id";
    $backRefs["files"] = "CFile file_category_id";
    return $backRefs;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"]   = "notNull str";
    $specs["class"] = "str";
    $specs["validation_auto"] = "bool";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom" => "like"
    );
  }
  
  static function loadListByClass() {
    $category = new CFilesCategory();
    $categories = $category->loadList(null, "nom");
    $catsByClass = array();
    foreach ($categories as $_category) {
      $catsByClass[$_category->class][$_category->_id] = $_category; 
    }
    unset($catsByClass[""]);
    return $catsByClass;
  }
    
  static function listCatClass($paramclass = null) {
    $instance = new CFilesCategory;
    $where = array();
    $where[] = $instance->_spec->ds->prepare("`class` IS NULL OR `class` = %", $paramclass);
    
    $listCat = new CFilesCategory;
    return $listCat->loadList($where);
    
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}
?>