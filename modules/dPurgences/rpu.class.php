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
  var $radio_debut     = null;
  var $radio_fin       = null;
  
  // Distant Fields
  var $_count_consultations = null;

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
  var $_ref_consult = null;
  
  // Bind
  var $_bind_sejour = null;

  function CRPU() {
    $this->CMbObject("rpu", "rpu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "sejour_id"       => "notNull ref class|CSejour",
      "diag_infirmier"  => "text",
      "mode_entree"     => "enum list|6|7|8",
      "provenance"      => "enum list|1|2|3|4|5|8",
      "transport"       => "enum list|perso|ambu|vsab|smur|heli|fo",
      "prise_en_charge" => "enum list|med|paramed|aucun",
      "motif"           => "text",
      "ccmu"            => "enum list|1|P|2|3|4|5|D",
      "_responsable_id" => "notNull ref class|CMediusers",
      "_entree"         => "dateTime",
      "sortie"          => "dateTime",
      "mode_sortie"     => "enum list|6|7|8|9 default|8",
      "destination"     => "enum list|1|2|3|4|6|7",
      "orientation"     => "enum list|HDT|HO|SC|SI|REA|UHCD|MED|CHIR|OBST|FUGUE|SCAM|PSA|REO",
      "radio_debut"     => "dateTime",
      "radio_fin"       => "dateTime"
     );
    return array_merge($specsParent, $specs);
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
    
    $this->_view = $this->_ref_sejour->_ref_patient->_view;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
  }
  
  function getHelpedFields(){
    return array(
      "motif"          => null,
      "diag_infirmier" => null
    );
  }
  
  
  function loadRefSejour() {
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_sejour->loadRefsFwd();

    // Chargement de la consultation ATU
    $this->_count_consultations = $this->_ref_sejour->countBackRefs("consultations");
    $this->_ref_consult = $this->_ref_sejour->loadUniqueBackRef("consultations");
    if ($this->_ref_consult->_id) {
      $this->_ref_consult->loadRefPlageConsult();
      $this->_ref_consult->_ref_plageconsult->_ref_chir->loadRefFunction();
      $this->_ref_consult->getNumDocsAndFiles();
    }
  }
  
  
  function bindSejour(){
    global $g;
    
    $this->_bind_sejour = false;
    
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
    // Affectation du sejour_id au RPU
    $this->sejour_id = $this->_ref_sejour->_id;
  }
  
  function store() {
    // Bind Sejour
    if($this->_bind_sejour){
      $this->bindSejour(); 
    }
    
    // Standard Store
    if($msg = parent::store()){
      return $msg;
    }
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