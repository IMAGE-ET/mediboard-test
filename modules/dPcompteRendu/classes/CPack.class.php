<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Gestion de packs de documents
 */
class CPack extends CMbObject {
  // DB Table key
  var $pack_id       = null;

  // DB References
  var $user_id       = null;
  var $function_id   = null;
  var $group_id      = null;

  // DB fields
  var $nom           = null;
  var $object_class  = null;
  var $fast_edit     = null;
  var $fast_edit_pdf = null;
  var $merge_docs    = null;
  
  // Form fields
  var $_modeles      = null;
  var $_new          = null;
  var $_del          = null;
  var $_source       = null;
  var $_object_class = null;
  var $_owner        = null;
  
  // Referenced objects
  var $_ref_user     = null;
  var $_ref_function = null;
  var $_ref_group    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'pack';
    $spec->key   = 'pack_id';
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]       = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";
    $specs["group_id"]      = "ref class|CGroups";
    $specs["nom"]           = "str notNull seekable confidential";
    $specs["object_class"]  = "enum notNull list|CPatient|CConsultAnesth|COperation|CConsultation|CSejour default|COperation";
    $specs["fast_edit"]     = "bool default|0";
    $specs["fast_edit_pdf"] = "bool default|0";
    $specs["merge_docs"]    = "bool default|1";
    $specs["_owner"]        = "enum list|user|func|etab";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["modele_links"] = "CModeleToPack pack_id";
    return $backProps;
  }
  
  function loadRefsFwd($cached = false) {
    $this->_ref_user = $this->loadFwdRef("user_id", $cached);
    $this->_ref_function = $this->loadFwdRef("function_id", $cached);
    $this->_ref_group = $this->loadFwdRef("group_id", $cached);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    
    if ($this->user_id) {
      $this->_owner = "user";
    }
    if ($this->function_id) {
      $this->_owner = "func";
    }
    if ($this->group_id) {
      $this->_owner = "etab";
    }
    if (!$this->_object_class) {
      $this->_object_class = "COperation";
    }
  }
  
  /**
   * Réunit les contenus des modèles pour constituer la source html du pack
   * 
   * @return void
   */
  function loadContent() {
    $this->_source = "";
    $this->loadBackRefs("modele_links", "modele_to_pack_id");

    if (count($this->_back['modele_links']) > 0) {
      $last_key = end(array_keys($this->_back['modele_links']));
      foreach ($this->_back['modele_links'] as $key => $_modeletopack) {
        $modele = $_modeletopack->_ref_modele;
        $modele->loadContent();
        $modele->loadIntroConclusion();
        
        if (!$this->_object_class) {
          $this->_object_class = $modele->object_class;
        }
        
        if ($modele->_ref_preface->_id) {
          $preface = $modele->_ref_preface;
          $preface->loadContent();
          $modele->_source = $preface->_source . "<br />" . $modele->_source;
        }
        
        if ($modele->_ref_ending->_id) {
          $ending = $modele->_ref_ending;
          $ending->loadContent();
          $modele->_source .= "<br />" . $ending->_source;
        }
        
        $this->_source .= $modele->_source;
        
        // Si on est au dernier modèle, pas de page break
        if ($key === $last_key) {
          break;
        }
        $this->_source .= '<hr class="pagebreak" />';
      }
    }
  }
  
  /**
   * Charge les packs pour un propriétaire donné
   * 
   * @param object $id           identifiant du propriétaire
   * @param object $owner        [optional]
   * @param object $object_class [optional]
   * 
   * @todo: refactor this to be in a super class
   * 
   * @return array
   */
  static function loadAllPacksFor($id, $owner = 'user', $object_class = null) {
    $packs = array(
      "user" => array(), // warning: it's not prat like in CCompteRendu
      "func" => array(),
      "etab" => array(),
    );
    
    if (!$id) {
      return $packs;
    }
    
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
        if (!$user->load($id)) {
          return $packs;
        }
        $user->loadRefFunction();

        $where["user_id"]     = "= '$user->_id'";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = "IS NULL";
        $packs["user"] = $pack->loadlist($where, $order);
        
      case 'func': // Modèle de la fonction
        if (isset($user)) {
          $func_id = $user->function_id;
        }
        else {
          $func = new CFunctions();
          if (!$func->load($id)) {
            return $packs;
          }
          
          $func_id = $func->_id;
        }
        
        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "= '$func_id'";
        $where["group_id"]    = "IS NULL";
        $packs["func"] = $pack->loadlist($where, $order);
        
      case 'etab': // Modèle de l'établissement
        $etab_id = CGroups::loadCurrent()->_id;
        if ($owner == 'etab') {
          $etab = new CGroups();
          if (!$etab->load($id)) {
            return $packs;
          }
          
          $etab_id = $etab->_id;
        }
        else if (isset($func)) {
          $etab_id = $func->group_id;
        }
        else if (isset($func_id)) {
          $func = new CFunctions();
          $func->load($func_id);
          
          $etab_id = $func->group_id;
        }
        
        $where["user_id"]     = "IS NULL";
        $where["function_id"] = "IS NULL";
        $where["group_id"]    = " = '$etab_id'";
        $packs["etab"] = $pack->loadlist($where, $order);
    }
    
    return $packs;
  }
  
  function getPerm($permType) {
    if (!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return $this->_ref_user->getPerm($permType);
  }
}

?>