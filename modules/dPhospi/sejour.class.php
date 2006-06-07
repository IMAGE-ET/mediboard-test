<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Thomas Despoix
 */

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
  var $entree_prevue = null; // remplace $op->date_adm $op->time_adm, $op->_entree_adm
  var $sortie_prevue = null; // remplace $op->_sortie_adm
  var $entree_reelle = null; // remplace $op->entree_adm $op->admis
  var $sortie_reelle = null;
  var $chambre_seule = null; // remplace $op->chambre

  // Object References  
  var $_ref_patient = null;
  var $_ref_praticien = null;
  var $_ref_operations = null;

	function CSejour() {
		$this->CMbObject("sejour", "sejour_id");
    
    $this->_props["patient_id"]    = "ref|notNull";
    $this->_props["entree_prevue"] = "dateTime|notNull";
    $this->_props["sortie_prevue"] = "dateTime|notNull";
    $this->_props["entree_reelle"] = "dateTime";
    $this->_props["sortie_reelle"] = "dateTime";
    $this->_props["chambre"] = "enum|o|n";
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

    $this->_ref_praticien = new CMediuser;
    $this->_ref_praticien->loadObject($where);
  }
  
  function loadRefsBack() {
    $where = array (
      "sejour_id" => "= '$this->sejour_id'"
    );

    $operations = new COperations;
    $this->_ref_operations = $operations->loadList($where);
  }
}
?>