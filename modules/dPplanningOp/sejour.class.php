<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Thomas Despoix
 */

require_once($AppUI->getModuleClass("mediusers") );
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPpatients", "patients"));

/**
 * Classe CSejour. 
 * @abstract Gère les séjours en établissement
 */
class CSejour extends CMbObject {
  // DB Table key
  var $sejour_id = null;
  
  // DB Réference
  var $patient_id = null; // remplace $op->pat_id
  var $praticien_id = null; // clone $op->chir_id
  
  // DB Fields
  var $type = null; // remplace $op->type_adm
  var $annule = null; // complète $op->annule
  var $chambre_seule = null; // remplace $op->chambre
  
  var $entree_prevue = null; // remplace $op->date_adm $op->time_adm, $op->_entree_adm
  var $sortie_prevue = null; // remplace $op->_sortie_adm
  var $entree_reelle = null; // remplace $op->entree_adm $op->admis
  var $sortie_reelle = null;

  var $venue_SHS = null; // remplace $op->venue_SHS
  var $saisi_SHS = null; // remplace $op->saisie
  var $modif_SHS = null; // remplace $op->modifiee

  // Object References  
  var $_ref_patient = null;
  var $_ref_praticien = null;
  var $_ref_operations = null;
  var $_ref_affectations = null;
  var $_ref_first_affectation = null;
  var $_ref_last_affectation = null; 

	function CSejour() {
		$this->CMbObject("sejour", "sejour_id");
    
    $this->_props["patient_id"]    = "ref|notNull";
    $this->_props["praticien_id"]    = "ref|notNull";
    
    $this->_props["type"] = "enum|comp|ambu|exte";
    $this->_props["annulee"] = "enum|0|1";
    $this->_props["chambre_seule"] = "enum|o|n";

    $this->_props["entree_prevue"] = "dateTime|notNull";
    $this->_props["sortie_prevue"] = "dateTime|notNull";
    $this->_props["entree_reelle"] = "dateTime";
    $this->_props["sortie_reelle"] = "dateTime";
    
    $this->_props["venue_SHS"] = "num|length|8|confidential";
    $this->_props["saisi_SHS"] = "enum|o|n";
    $this->_props["modif_SHS"] = "enum|0|1";
	}

  function check() {
    parent::check();
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "opérations", 
      "name" => "operation", 
      "idfield" => "operation_id", 
      "joinfield" => "sejour_id"
    );
    
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
  
  function updateDBFields() {
  }
  
  function loadRefsFwd() {
    $where = array (
      "patient_id" => "= '$this->patient_id'"
    );

    $this->_ref_patient = new CPatient;
    $this->_ref_patient->loadObject($where);

    $where = array (
      "user_id" => "= '$this->praticien_id'"
    );

    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->loadObject($where);
  }
  
  function loadRefsAffectations() {
    $where = array("sejour_id" => "= '$this->sejour_id'");
    $order = "sortie DESC";
    $this->_ref_affectations = new CAffectation();
    $this->_ref_affectations = $this->_ref_affectations->loadList($where, $order);

    if(count($this->_ref_affectations) > 0) {
      $this->_ref_first_affectation =& end($this->_ref_affectations);
      $this->_ref_last_affectation =& reset($this->_ref_affectations);
    } else {
      $this->_ref_first_affectation =& new CAffectation;
      $this->_ref_last_affectation =& new CAffectation;
    }
  }
  
  function loadRefsOperations() {
    $where = array (
      "sejour_id" => "= '$this->sejour_id'"
    );

    $operations = new COperation;
    $this->_ref_operations = $operations->loadList($where);
  }
  
  function loadRefsBack() {
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
  }
}
?>