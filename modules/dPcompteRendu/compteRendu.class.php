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
  var $group_id          = null; // not null when is a template associated to a group
  
  // DB fields
  var $nom               = null;
  var $type              = null;
  var $source            = null;
  var $valide            = null;
  var $file_category_id  = null;
  var $header_id         = null;
  var $footer_id         = null;
  var $height            = null;
  
  /// Form fields
  var $_is_document      = false;
  var $_is_modele        = false;
  var $_owner            = null;
  
  // Referenced objects
  var $_ref_chir         = null;
  var $_ref_category     = null;
  var $_ref_function     = null;
  var $_ref_group        = null;
  var $_ref_header       = null;
  var $_ref_footer       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'compte_rendu';
    $spec->key   = 'compte_rendu_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["listes_choix"] = "CListeChoix compte_rendu_id";
    $backRefs["modeles_headed"] = "CCompteRendu header_id";
    $backRefs["modeles_footed"] = "CCompteRendu footer_id";
    return $backRefs;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["chir_id"]          = "ref class|CMediusers xor|function_id|group_id|object_id";
    $specs["function_id"]      = "ref class|CFunctions xor|chir_id|group_id|object_id";
    $specs["group_id"]         = "ref class|CGroups xor|chir_id|function_id|object_id";
    $specs["object_id"]        = "ref class|CMbObject meta|object_class xor|function_id|chir_id|group_id";
    $specs["object_class"]     = "enum notNull list|CPatient|CConsultation|CConsultAnesth|COperation|CSejour";
    $specs["nom"]              = "str notNull";
    $specs["type"]             = "enum list|header|body|footer default|body";
    $specs["source"]           = "html helped|object_class";
    $specs["file_category_id"] = "ref class|CFilesCategory";
    $specs["header_id"]        = "ref class|CCompteRendu";
    $specs["footer_id"]        = "ref class|CCompteRendu";
    $specs["height"]           = "float";
    $specs["valide"]           = "bool";

    $specs["_owner"]           = "enum list|prat|func|etab";

    return $specs;
  }

  function getHelpedFields(){
    return array(
      "source" => array("depend_value_1" => "object_class", "depend_value_2" => null),
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
    $this->_view = $this->object_id ? "" : "Modèle : ";
    $this->_view.= $this->nom;
    
    if ($this->chir_id    ) $this->_owner = "prat";
    if ($this->function_id) $this->_owner = "func";
    if ($this->group_id   ) $this->_owner = "etab";
  }

  function loadCategory(){
    // Categorie
    $this->_ref_category = new CFilesCategory;
    if($this->file_category_id){
      $this->_ref_category->load($this->file_category_id);
    }
  }
  
  function loadComponents() {
    if (!$this->_ref_header) {
	    $this->_ref_header = new CCompteRendu();
	    $this->_ref_header->load($this->header_id);
    }
    
    if (!$this->_ref_footer) {
	    $this->_ref_footer = new CCompteRendu();
	    $this->_ref_footer->load($this->footer_id);
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
      
    // Etablissement
    $this->_ref_group = new CGroups();
    if($this->group_id)
      $this->_ref_group->load($this->group_id);
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
   * Charge tous les modèles pour une classe d'objets associés à un utilisateur
   * @param $prat_id ref|CMediuser L'utilisateur concerné
   * @param $object_class string Nom de la classe d'objet, optionnel. Doit être un CMbObject
   * @param $type enum list|header|body|footer Type de composant, optionnel
   * @return array ("prat" => array<CCompteRendu>, "func" => array<CCompteRendu>, "etab" => array<CCompteRendu>)
   */
  static function loadAllModelesFor($id, $owner = 'prat', $object_class = null, $type = null) {
    $modeles = array(
      "prat" => array(),
      "func" => array(),
      "etab" => array(),
    );
    
    if (!$id) return $modeles;
    
    // Clauses de recherche
    $modele = new CCompteRendu();
    $where = array();
    $where["object_id"] = "IS NULL";
    
    if ($object_class) {  
      $where["object_class"] = "= '$object_class'";
    }
    
    if ($type) {
      $where["type"] = "= '$type'";
    }
    
    $order = "object_class, type, nom";

    switch ($owner) {
    	case 'prat': // Modèle du praticien
        $prat = new CMediusers();
        if (!$prat->load($id)) return $modeles;
        $prat->loadRefFunction();

		    $where["chir_id"]     = "= '$prat->_id'";
		    $where["function_id"] = "IS NULL";
		    $where["group_id"]    = "IS NULL";
		    $modeles["prat"] = $modele->loadlist($where, $order);
		    
    	case 'func': // Modèle de la fonction
    		if (isset($prat)) {
    			$func_id = $prat->function_id;
    		} else {
	        $func = new CFunctions();
	        if (!$func->load($id)) return $modeles;
	        
	        $func_id = $func->_id;
    		}
        
		    $where["chir_id"]     = "IS NULL";
		    $where["function_id"] = "= '$func_id'";
		    $where["group_id"]    = "IS NULL";
		    $modeles["func"] = $modele->loadlist($where, $order);
		    
    	case 'etab': // Modèle de l'établissement
    		$etab_id = CGroups::loadCurrent()->_id;
    		if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) return $modeles;
          
          $etab_id = $etab->_id;
    		}
        else if (isset($func)) {
          $etab_id = $func->group_id;
        } 
        else if(isset($func_id)) {
        	$func = new CFunctions();
        	$func->load($func_id);
        	
        	$etab_id = $func->group_id;
        }
        
		    $where["chir_id"]     = "IS NULL";
		    $where["function_id"] = "IS NULL";
		    $where["group_id"]    = " = '$etab_id'";
		    $modeles["etab"] = $modele->loadlist($where, $order);
    }
    
    return $modeles;
  }
    
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function) || !$this->_ref_object) {
      $this->loadRefsFwd();
    }
    if($this->_ref_object->_id){
      $can = $this->_ref_object->getPerm($permType);
    }elseif($this->_ref_chir->_id) {
      $can = $this->_ref_chir->getPerm($permType);
    } elseif($this->_ref_function->_id) {
      $can = $this->_ref_function->getPerm($permType);
    } else {
    	$can = $this->_ref_group->getPerm($permType);
    }
    return $can;
  }
}

?>