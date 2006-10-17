<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CAffectation. 
 * @abstract Gère les affectation des séjours dans des lits
 */
class CAffectation extends CMbObject {
  // DB Table key
	var $affectation_id = null;
  
  // DB References
  var $lit_id    = null;
  var $sejour_id = null;

  // DB Fields
  var $entree   = null;
  var $sortie   = null;
  var $confirme = null;
  var $effectue = null;
  
  // Form Fields
  var $_entree_relative;
  var $_sortie_relative;
  
  // Object references
  var $_ref_lit    = null;
  var $_ref_sejour = null;
  var $_ref_prev   = null;
  var $_ref_next   = null;
  var $_no_synchro = null;

	function CAffectation() {
		$this->CMbObject("affectation", "affectation_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}
  
  function getSpecs() {
    return array (
      "lit_id"    => "ref|notNull",
      "sejour_id" => "ref|notNull",
      "entree"    => "dateTime|notNull",
      "sortie"    => "dateTime|notNull",
      "confirme"  => "enum|0|1",
      "effectue"  => "enum|0|1"
    );
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
    $changeSejour = 0;

    if($this->_no_synchro) {
      return $msg;
    }
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
    $this->loadRefsAffectations();
  }
  
  function loadRefsAffectations() {
    
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
  
  function getPerm($permType) {
    if(!$this->_ref_lit) {
      $this->loadRefLit();
    }
    if(!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    return ($this->_ref_lit->getPerm($permType) && $this->_ref_sejour->getPerm($permType));
  }
  
  function checkDaysRelative($date) {
    if ($this->entree and $this->sortie) {
      $this->_entree_relative = mbDaysRelative($date." 10:00:00", $this->entree);
      $this->_sortie_relative = mbDaysRelative($date." 10:00:00", $this->sortie);
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