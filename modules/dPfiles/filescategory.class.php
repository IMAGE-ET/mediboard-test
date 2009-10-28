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
  var $send_auto = null;
  
  var $_count_documents = null;
  var $_count_files     = null;
  var $_count_doc_items = null;
  
  var $_count_unsent_documents = null;
  var $_count_unsent_files     = null;
  var $_count_unsent_doc_items = null;
  
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
    $specs["send_auto"] = "bool";
    return $specs;
  }
  
  function countDocItems() {
    $this->_count_documents = $this->countBackRefs("categorized_documents");
    $this->_count_files     = $this->countBackRefs("categorized_files"    );
    $this->_count_doc_items = $this->_count_documents + $this->_count_files;
  }
  
  function countUnsentDocItems() {
    $where["file_category_id"] = "= '$this->_id'";
    $where["etat_envoi"      ] = "!= 'oui'";
    $where["object_id"       ] = "IS NOT NULL";
    
    $file = new CFile();
    $this->_count_unsent_files = $file->countList($where);;
    
    $document = new CCompteRendu();
    $this->_count_unsent_documents = $document->countList($where);
    $this->_count_unsent_doc_items = $this->_count_unsent_documents + $this->_count_unsent_files;
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
    
  static function listCatClass($class = null) {
    $instance = new CFilesCategory;
    $where = array(
      $instance->_spec->ds->prepare("`class` IS NULL OR `class` = %", $class)
    );
    return $instance->loadList($where);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}
?>