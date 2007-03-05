<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CCompteRendu extends CMbObject {
  // DB Table key
  var $compte_rendu_id   = null;

  // DB References
  var $chir_id           = null; // not null when is a template associated to a user
  var $function_id       = null; // not null when is a template associated to a function
  var $object_id         = null; // null when is a template, not null when a document

  // DB fields
  var $nom               = null;
  var $source            = null;
  var $object_class      = null;
  var $valide            = null;
  var $file_category_id  = null;
  
  /// Form fields
  var $_is_document      = false;
  var $_is_modele        = false;
  
  // Referenced objects
  var $_ref_chir         = null;
  var $_ref_category     = null;
  var $_ref_function     = null;
  var $_ref_object       = null;

  function CCompteRendu() {
    $this->CMbObject("compte_rendu", "compte_rendu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "chir_id"          => "ref xor|function_id class|CMediusers",
      "function_id"      => "ref class|CFunctions",
      "object_id"        => "ref",
      "nom"              => "notNull str",
      "source"           => "html",
      "object_class"     => "notNull enum list|CPatient|CConsultAnesth|COperation|CConsultation",
      "file_category_id" => "ref class|CFilesCategory",
      "valide"           => "numchar maxLength|1"
    );
  }

  function loadModeles($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!isset($where["object_id"])) {
      $where["object_id"] = "IS NULL";
    }

    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }

  function loadDocuments($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    if (!isset($where["object_id"])) {
      $where["object_id"] = "IS NOT NULL";
    }
    
    return parent::loadList($where, $order, $limit, $group, $leftjoin);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if($this->object_id == null)
      $this->_view = "Modèle : ";
    else
      $this->_view = "Document : ";
    $this->_view .= $this->nom;
  }

  function loadCategory(){
    // Categorie
    $this->_ref_category = new CFilesCategory;
    if($this->file_category_id){
      $this->_ref_category->load($this->file_category_id);
    }
  }

  function loadRefsFwd() {

    // Objet
    if (class_exists($this->object_class)) {
      $this->_ref_object = new $this->object_class;
      if ($this->object_id)
        $this->_ref_object->load($this->object_id);
        $this->_ref_object->loadRefsFwd();
    } else {
      trigger_error("Enable to create instance of '$this->object_class' class", E_USER_ERROR);
    }
    
    $this->loadCategory();
    
    // Chirurgien
    $this->_ref_chir = new CMediusers;
    if($this->chir_id) {
      $this->_ref_chir->load($this->chir_id);
    } elseif($this->object_id) {
      switch($this->object_class) {
        case "CConsultation" :
          $this->_ref_chir->load($this->_ref_object->_ref_plageconsult->chir_id);
          break;
        case "CConsultAnesth" :
          $this->_ref_object->_ref_consultation->loadRefsFwd();
          $this->_ref_chir->load($this->_ref_object->_ref_consultation->_ref_plageconsult->chir_id);
          break;
        case "COperation" :
          $this->_ref_chir->load($this->_ref_object->chir_id);
          break;
      }
    }

    // Fonction
    $this->_ref_function = new CFunctions;
    if($this->function_id)
      $this->_ref_function->load($this->function_id);
  }
  
  function loadModeleByCat($catName, $where1 = null, $order = "nom", $horsCat = null){
    $where = array();
    if(is_array($catName)) {
      $where = array_merge($where, $catName);
    }elseif(is_string($catName)){
      $where["nom"] = db_prepare("= %", $catName);
    }
    $category = new CFilesCategory;
    $resultCategory = $category->loadList($where);
    $documents = array();
    
    if(count($resultCategory) || $horsCat){
    	if($horsCat){
        $resultCategory[0] = "";
      }
      $where = array();
      $where["object_id"] = " IS NULL";
      $where["file_category_id"] = db_prepare_in(array_keys($resultCategory));
      if($where1){
        if(is_array($where1)) {
          $where = array_merge($where, $where1);
        }elseif(is_string($where1)){
          $where[] = $where1;
        }
      }
      $resultDoc = new CCompteRendu;$okiii = 1;
      $documents = $resultDoc->loadList($where,$order);
    }
    return $documents;
  }  
  
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }
    if($this->_ref_chir->_id) {
      $can = $this->_ref_chir->getPerm($permType);
    } else {
      $can = $this->_ref_function->getPerm($permType);
    }
    if($this->_ref_object->_id) {
      $can = $can && $this->_ref_object->getPerm($permType);
    }
    return $can;
  }
}

?>