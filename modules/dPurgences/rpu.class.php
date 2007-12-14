<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPurgences
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CRPU class
 * Résumé de Passage aux Urgences
 */
class CRPU extends CMbObject {
  // DB Table key
  var $rpu_id = null;
    
  // DB Fields
  var $sejour_id       = null;
  var $diag_infirmier  = null;
  var $mode_entree     = null;
  var $provenance      = null;
  var $transport       = null;
  var $prise_en_charge = null;
  var $motif           = null;
  var $ccmu            = null;
  var $sortie          = null;
  var $mode_sortie     = null;
  var $destination     = null;
  var $orientation     = null;
  
  
  // Distant Fields
  // Patient
  var $_patient_id = null;
  var $_cp         = null;
  var $_ville      = null;
  var $_naissance  = null;
  // Sejour
  var $_responsable_id = null;
  var $_entree         = null;
  var $_DP             = null;
  var $_DAS            = null;
  var $_ref_actes_ccam = null;
  
  // Object References
  var $_ref_sejour = null;

  function CRPU() {
    $this->CMbObject("rpu", "rpu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "sejour_id"       => "notNull ref class|CSejour",
      "diag_infirmier"  => "text",
      "mode_entree"     => "enum list|6|7|8",
      "provenance"      => "enum list|1|2|3|4|5|8",
      "transport"       => "enum list|perso|ambu|vsab|smur|heli|fo",
      "prise_en_charge" => "enum list|med|paramed|aucun",
      "motif"           => "text",
      "ccmu"            => "notNull enum list|1|2|3|4|5|P|D",
      "_responsable_id" => "notNull ref class|CMediusers",
      "_entree"         => "dateTime",
      "sortie"          => "dateTime",
      "mode_sortie"     => "enum list|6|7|8|9",
      "destination"     => "enum list|1|2|3|4|6|7",
      "orientation"     => "enum list|HDT|HO|SC|SI|REA|UHCD|MED|CHIR|OBST|FUGUE|SCAM|PSA|REO"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    $this->_patient_id = $this->_ref_sejour->_ref_patient->_id;
    $this->_cp         = $this->_ref_sejour->_ref_patient->cp;
    $this->_ville      = $this->_ref_sejour->_ref_patient->ville;
    $this->_naissance  = $this->_ref_sejour->_ref_patient->naissance;
    
    $this->_responsable_id = $this->_ref_sejour->praticien_id;
    $this->_entree         = $this->_ref_sejour->_entree;
    $this->_DP             = $this->_ref_sejour->DP;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_sejour->loadRefsFwd();
  }
  
  function store() {
    global $g;
    $this->loadRefsFwd();
    $this->_ref_sejour->patient_id = $this->_patient_id;
    $this->_ref_sejour->group_id = $g;
    $this->_ref_sejour->praticien_id = $this->_responsable_id;
    $this->_ref_sejour->type = "urg";
    $this->_ref_sejour->entree_prevue = $this->_entree;
    $this->_ref_sejour->entree_reelle = $this->_entree;
    $this->_ref_sejour->sortie_prevue = mbDate(null, $this->_entree)." 23:59:59";
    if($msg = $this->_ref_sejour->store()) {
      return $msg;
    }
    $this->sejour_id = $this->_ref_sejour->_id;
    $msg = parent::store();
    return $msg;
  }
  
  function delete() {
    $this->loadRefsFwd();
    if($msg = parent::delete()) {
      return $msg;
    }
    $msg = $this->_ref_sejour->delete();
    return $msg;
  }
}
?>