<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CPack extends CMbObject {
  // DB Table key
  var $pack_id       = null;

  // DB References
  var $chir_id       = null;
  var $function_id   = null;
  var $group_id      = null;

  // DB fields
  var $nom           = null;
  var $modeles       = null;
  var $object_class  = null;
  
  // Form fields
  var $_modeles      = null;
  var $_new          = null;
  var $_del          = null;
  var $_source       = null;
  var $_object_class = null;
  var $_owner        = null;
  
  // Referenced objects
  var $_ref_chir     = null;
  var $_ref_function = null;
  var $_ref_group    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack';
    $spec->key   = 'pack_id';
    $spec->xor["owner"] = array("chir_id", "function_id", "group_id");
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]      = "ref class|CMediusers";
    $specs["function_id"]  = "ref class|CFunctions";
    $specs["group_id"]     = "ref class|CGroups";
    $specs["nom"]          = "str notNull confidential";
    $specs["modeles"]      = "text";
    $specs["object_class"] = "enum notNull list|CPatient|CConsultAnesth|COperation|CConsultation|CSejour default|COperation";
    $specs["_owner"]       = "enum list|user|func|etab";
    return $specs;
  }
  
  function loadRefsFwd($cached = false) {
    $this->_ref_chir = $this->loadFwdRef("chir_id", $cached);
    $this->_ref_function = $this->loadFwdRef("function_id", $cached);
    $this->_ref_group = $this->loadFwdRef("group_id", $cached);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    
    if ($this->chir_id    ) $this->_owner = "user";
    if ($this->function_id) $this->_owner = "func";
    if ($this->group_id   ) $this->_owner = "etab";
    
  	$this->_modeles = array();
    $this->_source = "";
    if($this->modeles != "") {
      $modeles = explode("|", $this->modeles);
      foreach ($modeles as $value) {
        $this->_modeles[$value] = new CCompteRendu;
        $this->_modeles[$value]->load($value);
        if (!$this->_object_class)
          $this->_object_class = $this->_modeles[$value]->object_class;
      }
      
      $this->_source = implode('<hr class="pagebreak" />', CMbArray::pluck($this->_modeles, "source"));   
    }
    if (!$this->_object_class)
      $this->_object_class = "COperation";
  }
  
  function updateDBFields() {
    if ($this->_new !== null) {
      $this->updateFormFields();
      $this->_modeles[$this->_new] = new CCompteRendu;
      $this->_modeles[$this->_new]->load($this->_new);
      $this->modeles = implode("|", array_keys($this->_modeles));
    }
    if ($this->_del !== null) {
      $this->updateFormFields();
      unset($this->_modeles[$this->_del]);
      $this->modeles = implode("|", array_keys($this->_modeles));
    }
  }
  
  /**
   * @todo: refactor this to be in a super class
   * @param object $id
   * @param object $owner [optional]
   * @param object $object_class [optional]
   * @return 
   */
  static function loadAllPacksFor($id, $owner = 'user', $object_class = null) {
    $packs = array(
      "user" => array(), // warning: it not prat like in CCompteRendu
      "func" => array(),
      "etab" => array(),
    );
    
    if (!$id) return $packs;
    
    // Clauses de recherche
    $pack = new CPack();
    $where = array();
    
    if ($object_class) {  
      $where["object_class"] = "= '$object_class'";
    }
    
    $order = "object_class, nom";

    switch ($owner) {
      case 'user': // Modèle du praticien
        $user = new CMediusers();
        if (!$user->load($id)) return $packs;
        $user->loadRefFunction();

        $where["chir_id"]     = "= '$user->_id'";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = "IS NULL";
        $packs["user"] = $pack->loadlist($where, $order);
        
      case 'func': // Modèle de la fonction
        if (isset($user)) {
          $func_id = $user->function_id;
        } else {
          $func = new CFunctions();
          if (!$func->load($id)) return $packs;
          
          $func_id = $func->_id;
        }
        
        $where["chir_id"]     = "IS NULL";
        $where["function_id"] = "= '$func_id'";
        $where["group_id"]    = "IS NULL";
        $packs["func"] = $pack->loadlist($where, $order);
        
      case 'etab': // Modèle de l'établissement
        $etab_id = CGroups::loadCurrent()->_id;
        if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) return $packs;
          
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
        $packs["etab"] = $pack->loadlist($where, $order);
    }
    
    return $packs;
  }
  
  function getPerm($permType) {
    if(!$this->_ref_chir) {
      $this->loadRefsFwd();
    }
    return $this->_ref_chir->getPerm($permType);
  }
}

?>