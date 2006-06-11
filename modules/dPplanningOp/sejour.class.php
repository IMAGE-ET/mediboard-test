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
  
  var $rques = null;

  // Object References  
  var $_ref_patient = null;
  var $_ref_praticien = null;
  var $_ref_operations = null;
  var $_ref_affectations = null;
  var $_ref_first_affectation = null;
  var $_ref_last_affectation = null;
  
  // Form Fields
  var $_duree_prevue = null;

	function CSejour() {
		$this->CMbObject("sejour", "sejour_id");
    
    $this->_props["patient_id"]    = "ref|notNull";
    $this->_props["praticien_id"]    = "ref|notNull";
    
    $this->_props["type"] = "enum|comp|ambu|exte";
    $this->_props["annule"] = "enum|0|1";
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
  
  function bindToOp($operation_id) {
    $operation = new COperation;
    $operation->load($operation_id);
    $this->load($operation->sejour_id);
    $this->patient_id    = $operation->pat_id;
    $this->praticien_id  = $operation->chir_id;
    $this->type          = $operation->type_adm;
    $this->annule        = $operation->annulee;
    $this->chambre_seule = $operation->chambre;
    $this->entree_prevue = $operation->date_adm." ".$operation->time_adm;
    $this->sortie_prevue = mbAddDateTime("+".$operation->duree_hospi." DAYS", $this->entree_prevue);
    if($operation->admis == 'o')
      $this->entree_reelle = $operation->entree_adm;
    else
      $this->entree_reelle = '';
    $this->sortie_reelle = '';
    $this->venue_SHS     = $operation->venue_SHS;
    $this->saisi_SHS     = $operation->saisie;
    $this->modif_SHS     = $operation->modifiee;
  }
  
  function store() {
    if(!($msg = parent::store())) {
      $this->load($this->sejour_id);
      if($this->annule) {
        $this->delAffectations();
      }
      $this->loadRefsOperations();
      foreach($this->_ref_operations as $keyOp => $operation) {
        $this->_ref_operations[$keyOp]->pat_id = $this->patient_id;
        $this->_ref_operations[$keyOp]->chir_id = $this->praticien_id;
        $this->_ref_operations[$keyOp]->type_adm = $this->type;
        $this->_ref_operations[$keyOp]->annulee = $this->annule;
        $this->_ref_operations[$keyOp]->chambre = $this->chambre_seule;
        $this->_ref_operations[$keyOp]->date_adm = $this->entree_prevue;
        $this->_ref_operations[$keyOp]->time_adm = $operation->date_adm;
        $this->_ref_operations[$keyOp]->duree_hospi = mbDaysRelative($this->entree_prevue, $this->sortie_prevue);
        if($this->entree_reelle) {
          $this->_ref_operations[$keyOp]->admis = 'o';
        } else {
          $this->_ref_operations[$keyOp]->admis = 'n';
        }
        $this->_ref_operations[$keyOp]->entree_adm = $this->entree_reelle;
        $this->_ref_operations[$keyOp]->venue_SHS = $this->venue_SHS;
        $this->_ref_operations[$keyOp]->saisie = $this->saisi_SHS;
        $this->_ref_operations[$keyOp]->modifiee = $this->modif_SHS;
        $msgOp = $this->_ref_operations[$keyOp]->store();
      }
    } else {
      return $msg;
    }
    return null;
  }
  
  function delete() {
    $msg = parent::delete();
    if($msg == null) {
      //suppression des affectations
      $this->delAffectations();
    }
    return $msg;
  }
  
  function delAffectations() {
    $this->loadRefsAffectations();
    foreach($this->_ref_affectations as $key => $value) {
      $this->_ref_affectations[$key]->delete();
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree_prevue = mbDaysRelative($this->entree_prevue, $this->sortie_prevue);
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