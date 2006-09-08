<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("mediusers", "functions"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

class CListeChoix extends CMbObject {
  // DB Table key
  var $liste_choix_id = null;

  // DB References
  var $chir_id     = null; // not null when associated to a user
  var $function_id = null; // not null when associated to a function

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
  var $_ref_modele   = null;

  function CListeChoix() {
    $this->CMbObject("liste_choix", "liste_choix_id");

    $this->_props["chir_id"]         = "ref";
    $this->_props["function_id"]     = "ref";
    $this->_props["nom"]             = "str|notNull";
    $this->_props["valeurs"]         = "str|confidential";
    $this->_props["compte_rendu_id"] = "ref";
  }
  
  function check() {
    if ($this->chir_id and $this->function_id) {
      return "Une liste ne peut pas appartenir à la fois à une fonction et un utilisateur";
    }
    if (!($this->chir_id or $this->function_id)) {
      return "Une liste doit appertenir à un utilisateur ou à une fonction";
    }
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
    $this->_ref_modele = new CCompteRendu;
    $this->_ref_modele->load($this->compte_rendu_id);
  }
  
  function updateFormFields() {
    if($this->valeurs != "")
      $this->_valeurs = explode("|", $this->valeurs);
    else
      $this->_valeurs = array();
    natcasesort($this->_valeurs);
  }
  
  function updateDBFields() {
    if($this->_new !== null) {
      $this->updateFormFields();
      $this->_valeurs[] = $this->_new;
      natcasesort($this->_valeurs);
      $this->valeurs = implode("|", $this->_valeurs);
    }
    if($this->_del !== null) {
      $this->updateFormFields();
      foreach($this->_valeurs as $key => $value) {
        if($this->_del == $value)
          unset($this->_valeurs[$key]);
      }
      $this->valeurs = implode("|", $this->_valeurs);
    }
  }
  
  function canRead() {
    $this->loadRefsFwd();
    $this->_canRead = ($this->_ref_chir->canRead() || $this->_ref_function->canRead()) && $this->_ref_modele->canRead();
    return $this->_canRead;
  }

  function canEdit() {
    $this->loadRefsFwd();
    $this->_canEdit = ($this->_ref_chir->canEdit() || $this->_ref_function->canEdit()) && $this->_ref_modele->canEdit();
    return $this->_canEdit;
  }
}

?>