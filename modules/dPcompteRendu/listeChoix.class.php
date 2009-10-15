<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CListeChoix extends CMbObject {
  // DB Table key
  var $liste_choix_id = null;

  // DB References
  var $chir_id     = null; // not null when associated to a user
  var $function_id = null; // not null when associated to a function
  var $group_id    = null; // not null when associated to a group

  // DB fields
  var $nom             = null;
  var $valeurs         = null;
  var $compte_rendu_id = null;
  
  // Form fields
  var $_valeurs = null;
  var $_new     = null;
  var $_del     = null;
  
  // Referenced objects
  var $_ref_chir     = null;
  var $_ref_function = null;
  var $_ref_group    = null;
  var $_ref_modele   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'liste_choix';
    $spec->key   = 'liste_choix_id';
    $spec->xor["owner"] = array("chir_id", "function_id", "group_id");
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["chir_id"]         = "ref class|CMediusers";
    $specs["function_id"]     = "ref class|CFunctions";
    $specs["group_id"]        = "ref class|CGroups";
    $specs["nom"]             = "str notNull";
    $specs["valeurs"]         = "text confidential";
    $specs["compte_rendu_id"] = "ref class|CCompteRendu";
    return $specs;
  }
  
  function check() {
    if ($this->chir_id and $this->function_id and $this->group_id) {
      return "Une liste ne peut pas appartenir qu'a un utilisateur, une fonction ou un établissement";
    }
    
    if (!($this->chir_id or $this->function_id or $this->group_id)) {
      return "Une liste doit appartenir à un utilisateur, une fonction ou un établissement";
    }
    return parent::check();
  }
  
  function loadRefsFwd() {
    // Owner
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
    
    $this->_ref_modele = new CCompteRendu;
    $this->_ref_modele->load($this->compte_rendu_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_valeurs = $this->valeurs != "" ? explode("|", $this->valeurs) : array();
    natcasesort($this->_valeurs);
  }
  
  function updateDBFields() {
    if($this->_new !== null) {
      $this->updateFormFields();
      $this->_valeurs[] = trim($this->_new);
      natcasesort($this->_valeurs);
      $this->valeurs = implode("|", $this->_valeurs);
    }
    if($this->_del !== null) {
      $this->updateFormFields();
      foreach($this->_valeurs as $key => $value) {
        if(trim($this->_del) == trim($value))
          unset($this->_valeurs[$key]);
      }
      $this->valeurs = implode("|", $this->_valeurs);
    }
  }
  
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function || $this->_ref_group)) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_chir->_id) {
      return $this->_ref_chir->getPerm($permType);
    } else if ($this->_ref_function->_id) {
      return $this->_ref_function->getPerm($permType);
    } else {
      return $this->_ref_group->getPerm($permType);
    }
  }
}

?>