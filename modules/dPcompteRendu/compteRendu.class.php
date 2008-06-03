<?php /* $Id$ */
  
/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/
  
class CCompteRendu extends CMbMetaObject {
  // DB Table key
  var $compte_rendu_id   = null;
  
  // DB References
  var $chir_id           = null; // not null when is a template associated to a user
  var $function_id       = null; // not null when is a template associated to a function
  
  // DB fields
  var $nom               = null;
  var $source            = null;
  var $valide            = null;
  var $file_category_id  = null;
  
  /// Form fields
  var $_is_document      = false;
  var $_is_modele        = false;
  
  // Referenced objects
  var $_ref_chir         = null;
  var $_ref_category     = null;
  var $_ref_function     = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'compte_rendu';
    $spec->key   = 'compte_rendu_id';
    return $spec;
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["listes_choix"] = "CListeChoix compte_rendu_id";
     return $backRefs;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["chir_id"]          = "ref xor|function_id|object_id class|CMediusers";
    $specs["function_id"]      = "ref xor|chir_id|object_id class|CFunctions";
    $specs["object_id"]        = "ref xor|function_id|chir_id class|CMbObject meta|object_class";
    $specs["object_class"]     = "notNull enum list|CPatient|CConsultAnesth|COperation|CConsultation|CSejour";
    $specs["nom"]              = "notNull str";
    $specs["source"]           = "html";
    $specs["file_category_id"] = "ref class|CFilesCategory";
    $specs["valide"]           = "numchar maxLength|1";
    return $specs;
  }

  function getHelpedFields(){
    return array(
      "source" => "object_class"
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
	  parent::loadRefsFwd();

    $this->_ref_object->loadRefsFwd();
    
    $this->loadCategory();
    
    // Chirurgien
    $this->_ref_chir = new CMediusers;
    if($this->chir_id) {
      $this->_ref_chir->load($this->chir_id);
    } 
    elseif($this->object_id) {
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
  
  static function loadModeleByCat($catName, $where1 = null, $order = "nom", $horsCat = null){
    $ds = CSQLDataSource::get("std");
    $where = array();
    if(is_array($catName)) {
      $where = array_merge($where, $catName);
    }elseif(is_string($catName)){
      $where["nom"] = $ds->prepare("= %", $catName);
    }
    $category = new CFilesCategory;
    $resultCategory = $category->loadList($where);
    $documents = array();
    
    if(count($resultCategory) || $horsCat){
      $where = array();
    	if($horsCat){
        $resultCategory[0] = "";
    	  $where[] = "file_category_id IS NULL OR file_category_id ".$ds->prepareIn(array_keys($resultCategory));
      } else {
        $where["file_category_id"] = $ds->prepareIn(array_keys($resultCategory));
      }
      $where["object_id"] = " IS NULL";
      if($where1){
        if(is_array($where1)) {
          $where = array_merge($where, $where1);
        }elseif(is_string($where1)){
          $where[] = $where1;
        }
      }
      $resultDoc = new CCompteRendu;
      $documents = $resultDoc->loadList($where,$order);
    }
    return $documents;
  }
  
  /**
   * Charge les modèles pour une classe d'objets associés à un utilisateur
   * @param $object_class Nom de la classe d'objet. Doit être un CMbObject
   * @param $prat_id ref|CMediuser L'utilisateur concerné
   * @return array<CCompteRendu>
   */
  static function loadModelesForPrat($object_class, $prat_id) {
    $modele = new CCompteRendu();
    $where = array();
    $where["object_id"] = "IS NULL";
		$where["object_class"] = "= '$object_class'";
    $where["chir_id"] = "= '$prat_id'";
		$order = "nom";
    return $modele->loadlist($where, $order);
  }
  
  /**
   * Charge les modèles pour une classe d'objets associés à une fonction
   * @param $object_class Nom de la classe d'objet. Doit être un CMbObject
   * @param $function_id ref|CFonction L'utilisateur concerné
   * @return array<CCompteRendu>
   */
  static function loadModelesForFunc($object_class, $function_id) {
    $modele = new CCompteRendu();
    $where = array();
    $where["object_id"] = "IS NULL";
		$where["object_class"] = "= '$object_class'";
    $where["function_id"] = "= '$function_id'";
		$order = "nom";
    return $modele->loadlist($where, $order);
  }
  
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }
    if($this->_ref_object->_id){
      $can = $this->_ref_object->getPerm($permType);
    }elseif($this->_ref_chir->_id) {
      $can = $this->_ref_chir->getPerm($permType);
    } else {
      $can = $this->_ref_function->getPerm($permType);
    }
    return $can;
  }
}

?>