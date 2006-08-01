<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("dPhospi"     , "lit"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPplanningOp", "sejour"));

/**
 * Classe CAffectation. 
 * @abstract Gère les affectation des séjours dans des lits
 */
class CAffectation extends CMbObject {
  // DB Table key
	var $affectation_id = null;
  
  // DB References
  var $lit_id       = null;
  var $operation_id = null;
  var $sejour_id    = null;

  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $confirme = null;
  var $effectue = null;
  
  // Form Fields
  var $_entree_relative;
  var $_sortie_relative;
  
  // Object references
  var $_ref_lit       = null;
  var $_ref_operation = null;
  var $_ref_sejour    = null;
  var $_ref_prev      = null;
  var $_ref_next      = null;
  var $_no_synchro    = null;

	function CAffectation() {
		$this->CMbObject("affectation", "affectation_id");
    
    $this->_props["lit_id"]       = "ref|notNull";
    $this->_props["operation_id"] = "ref|notNull";
    $this->_props["sejour_id"]    = "ref|notNull";
    $this->_props["entree"]       = "dateTime|notNull";
    $this->_props["sortie"]       = "dateTime|notNull";
    $this->_props["confirme"]     = "enum|0|1";
    $this->_props["effectue"]     = "enum|0|1";
	}

  function check() {
    if($msg = parent::check()) {
      return $msg;
    }
    if(!$this->affectation_id) {
      return null;
    }
  	$obj = new CAffectation();
  	$obj->load($this->affectation_id);
  	$obj->loadRefsFwd();
  	if(!$this->entree && $obj->affectation_id)
  	  $this->entree = $obj->entree;
  	if(!$this->sortie && $obj->affectation_id)
  	  $this->sortie = $obj->sortie;
  	if($obj->_ref_next->affectation_id && ($this->sortie != $obj->sortie))
  	  return "Le patient a subi un déplacement";
  	if($obj->_ref_prev->affectation_id && ($this->entree != $obj->entree))
  	  return "Le patient a subi un déplacement";
    if ($this->sortie <= $this->entree) {
      return "La date de sortie doit être supérieure à la date d'entrée";
    }
    return null;
  }
  
  function delete() {
  	$this->load();
    $msg = null;
	  if (!$this->canDelete( $msg )) {
		  return $msg;
	  }
	  $sql = "DELETE FROM `affectation` WHERE `sejour_id` = '".$this->sejour_id."'";
	  if (!db_exec( $sql )) {
      return db_error();
	  } else {
		  return null;
	  }
  }
  
  function store() {
    if($msg = parent::store()) {
      return $msg;
    }
    // Modification de la date d'admission et de la durée de l'hospi
    $this->load($this->affectation_id);
    $this->loadRefsFwd();

    if($this->_no_synchro) {
      return $msg;
    }

    $changeSejour = 0;
    if(!$this->_ref_prev->affectation_id) {
      if($this->entree != $this->_ref_sejour->entree_prevue) {
        $this->_ref_sejour->entree_prevue = $this->entree;
        $changeSejour = 1;
      }
    }
    if(!$this->_ref_next->affectation_id) {
      if($this->sortie != $this->_ref_sejour->sortie_prevue) {
        $this->_ref_sejour->sortie_prevue = $this->sortie;
        $changeSejour = 1;
      }
    }
    if($changeSejour) {
      $this->_ref_sejour->_date_entree_prevue = null;
      $this->_ref_sejour->_date_sortie_prevue = null;
      $this->_ref_sejour->_hour_entree_prevue = null;
      $this->_ref_sejour->_hour_sortie_prevue = null;
      $this->_ref_sejour->_min_entree_prevue  = null;
      $this->_ref_sejour->_min_sortie_prevue  = null;
      $this->_ref_sejour->store();
    }
    return $msg;
  }
  
  function updateDBFields() {

  	$this->loadRefsFwd();
    
    $flag = !$this->_ref_next->affectation_id && !$this->_ref_prev->affectation_id && !$this->affectation_id;
    $flagComp = $flag && ($this->_ref_sejour->type == "comp");
    $flagAmbu = $flag && ($this->_ref_sejour->type == "ambu");
    
    if($flagComp) {
      $this->sortie = mbDate("", $this->sortie)." 10:00:00";
    }
    if($flagAmbu) {
      $this->sortie = mbDate("", $this->sortie)." "."18:00:00";
      /*
      if($this->_ref_sejour->_ref_operation->time_operation != "00:00:00") {
        if($this->_ref_operation->time_operation >= "18:00:00")
          $this->sortie = mbDate("", $this->sortie)." "."23:59:00";
        else
          $this->sortie = mbDate("", $this->sortie)." ".mbTime("+ 6 hours", $this->_ref_operation->time_operation);
      } else
        $this->sortie = mbDate("", $this->sortie)." "."18:00:00";
      */
    }
  }
  
  function loadRefLit() {
    $where = array (
      "lit_id" => "= '$this->lit_id'"
    );

    $this->_ref_lit = new CLit;
    $this->_ref_lit->loadObject($where);
  }
  
  function loadRefSejour() {
    $where = array (
      "sejour_id" => "= '$this->sejour_id'"
    );
    
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->loadObject($where);
  }
  
  function loadRefsFwd() {
    $this->loadRefLit();
    $this->loadRefSejour();
    
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "sortie" => "= '$this->entree'"
    );
    
    $this->_ref_prev = new CAffectation;
    $this->_ref_prev->loadObject($where);
    
    $where = array (
      "affectation_id" => "!= '$this->affectation_id'",
      "sejour_id" => "= '$this->sejour_id'",
      "entree" => "= '$this->sortie'"
    );
    
    $this->_ref_next = new CAffectation;
    $this->_ref_next->loadObject($where);
  }
  
  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = mbDaysRelative($date, $this->entree);
      $this->_sortie_relative = mbDaysRelative($date, $this->sortie);
    }
  }
  
  function colide($aff) {
  	if (($aff->entree < $this->sortie and $aff->sortie > $this->sortie)
            or ($aff->entree < $this->entree and $aff->sortie > $this->entree)
            or ($aff->entree >= $this->entree and $aff->sortie <= $this->sortie))
      return true;
    return false;
  }
}
?>