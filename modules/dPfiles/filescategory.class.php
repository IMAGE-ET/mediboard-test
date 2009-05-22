<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPfiles
 *	@version $Revision$
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
  
  var $_count_documents = null;
  var $_count_files     = null;
  var $_count_doc_items = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_category';
    $spec->key   = 'file_category_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["categorized_documents"] = "CCompteRendu file_category_id";
    $backProps["categorized_files"]     = "CFile file_category_id";
    return $backProps;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]   = "str notNull seekable";
    $specs["class"] = "str";
    $specs["validation_auto"] = "bool";
    return $specs;
  }
  
  function countDocItems() {
    $this->_count_documents = $this->countBackRefs("categorized_documents");
    $this->_count_files     = $this->countBackRefs("categorized_files"    );
    
    $this->_count_doc_items = $this->_count_documents + $this->_count_files;
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