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
  
  // DB Table key
  var $patient_id = null;
  
  // DB Fields
  var $entree_prevue = null;
  var $sortie_prevue = null;
  var $entree_reelle = null;
  var $sortie_reelle = null;

  // Object References  
  var $_ref_patient = null;
  var $_ref_operations = null;

	function CSejour() {
		$this->CMbObject("sejour", "sejour_id");
    
    $this->_props["patient_id"]    = "ref|notNull";
    $this->_props["entree_prevue"] = "dateTime|notNull";
    $this->_props["sortie_prevue"] = "dateTime|notNull";
    $this->_props["entree_reelle"] = "dateTime";
    $this->_props["sortie_reelle"] = "dateTime";
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