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
  	$props = parent::getProps();
    $props["chir_id"]         = "ref class|CMediusers";
    $props["function_id"]     = "ref class|CFunctions";
    $props["group_id"]        = "ref class|CGroups";
    $props["nom"]             = "str notNull";
    $props["valeurs"]         = "text confidential";
    $props["compte_rendu_id"] = "ref class|CCompteRendu";
		
    $props["_owner"]           = "enum list|prat|func|etab";
    return $props;
  }
  
  function loadRefUser() {
    return $this->_ref_user = $this->loadFwdRef("chir_id", true);
  }
  
  function loadRefFunction() {
    return $this->_ref_function = $this->loadFwdRef("function_id", true);
  }
  
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
  
	function loadRefOwner() {
		return CValue::first(
		  $this->loadRefUser(),
			$this->loadRefFunction(),
			$this->loadRefGroup()
		);
	}
	
	function loadRefModele() {
		return $this->_ref_modele = $this->loadFwdRef("compte_rendu_id", true);
	}
	
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
    $this->_valeurs = $this->valeurs != "" ? explode("|", $this->valeurs) : array();
    natcasesort($this->_valeurs);

    if ($this->chir_id    ) $this->_owner = "prat";
    if ($this->function_id) $this->_owner = "func";
    if ($this->group_id   ) $this->_owner = "etab";

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
    $owner = $this->loadRefsOwner();
		return $owner->getPerm($permType);
  }
	
  static function loadAllFor($user_id) {
		$user = new CMediusers;
		$user->load($user_id);

    $listes = array();
		foreach ($user->getOwners() as $type => $owner) {
			$listes[$type] = $owner->loadBackRefs("listes_choix", "nom");
		}
		return $listes;
  }
}

?>