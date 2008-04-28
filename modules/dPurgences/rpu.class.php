<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPurgences
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CRPU class
 * R�sum� de Passage aux Urgences
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
  var $pec_transport   = null;
  var $motif           = null;
  var $ccmu            = null;
  var $gemsa           = null;
  var $destination     = null;
  var $orientation     = null;
  var $radio_debut     = null;
  var $radio_fin       = null;
  var $mutation_sejour_id = null;
  var $box_id          = null;
  
  // Legacy Sherpa fields
  var $type_pathologie = null; // Should be $urtype
  var $urprov = null;
  var $urmuta = null;
  var $urtrau = null;
  
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
  var $_ref_sejour_mutation = null;
  
  // Behaviour fields
  var $_bind_sejour = null;
  var $_etablissement_transfert_id = null;
  
  var $_sortie          = null;
  var $_mode_sortie     = null;
  
  
  function CRPU() {
    $this->CMbObject("rpu", "rpu_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "sejour_id"        => "notNull ref class|CSejour",
      "diag_infirmier"   => "text",
      "mode_entree"      => "enum list|6|7|8 notNull",
      "provenance"       => "enum list|1|2|3|4|5|8",
      "transport"        => "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo notNull",
      "pec_transport"    => "enum list|med|paramed|aucun",
      "motif"            => "text",
      "ccmu"             => "enum list|1|P|2|3|4|5|D",
      "gemsa"            => "enum list|1|2|3|4|5|6",
      "type_pathologie"  => "enum list|C|E|M|P|T",
      "destination"      => "enum list|1|2|3|4|6|7",
      "orientation"      => "enum list|HDT|HO|SC|SI|REA|UHCD|MED|CHIR|OBST|FUGUE|SCAM|PSA|REO",
      "radio_debut"      => "dateTime",
      "radio_fin"        => "dateTime",
      "mutation_sejour_id" => "ref class|CSejour",
      "box_id"           => "ref class|CLit",
      
      "_mode_sortie"     => "enum list|6|7|8|9 default|8",
      "_sortie"          => "dateTime",
      "_patient_id"      => "notNull ref class|CPatient",
      "_responsable_id"  => "notNull ref class|CMediusers",
      "_entree"          => "dateTime",
      "_etablissement_transfert_id" => "ref class|CEtabExterne"
     );
     
		$specs["urprov"] = "";
		$specs["urmuta"] = "";
		$specs["urtrau"] = "";    

		// Legacy Sherpa fields
		if (CModule::getActive("sherpa")) {
		    $urgDro = new CSpUrgDro();
		    $specs["urprov"] = $urgDro->_props["urprov"];
		    $specs["urmuta"] = $urgDro->_props["urmuta"];
		    $specs["urtrau"] = $urgDro->_props["urtrau"];    
		}

		return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    $sejour =& $this->_ref_sejour;

    $this->_responsable_id = $sejour->praticien_id;
    $this->_entree         = $sejour->_entree;
    $this->_DP             = $sejour->DP;
    
    $patient =& $sejour->_ref_patient;
    
    $this->_patient_id = $patient->_id;
    $this->_cp         = $patient->cp;
    $this->_ville      = $patient->ville;
    $this->_naissance  = $patient->naissance;
    $this->_view       = $patient->_view;
    
    
    // Calcul des valeurs de _mode_sortie
    if($this->_ref_sejour->mode_sortie == "transtert" && $this->mutation_sejour_id){
    	$this->_mode_sortie = 6;
    }
    if($this->_ref_sejour->mode_sortie == "transfert" && !$this->mutation_sejour_id){
    	$this->_mode_sortie = 7; 
    }
    if($this->_ref_sejour->mode_sortie == "normal"){
    	$this->_mode_sortie = 8;
    }
    if($this->_ref_sejour->mode_sortie == "deces"){
    	$this->_mode_sortie = 9;
    }
    $this->_sortie = $this->_ref_sejour->sortie_reelle;
    $this->_etablissement_transfert_id = $this->_ref_sejour->etablissement_transfert_id;
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
      $this->_ref_consult->loadRefPraticien();      
      $this->_ref_consult->_ref_praticien->loadRefFunction();
      $this->_ref_consult->getNumDocsAndFiles();
    }
  }
  
  function loadRefSejourMutation() {
    $this->_ref_sejour_mutation = new CSejour;
    $this->_ref_sejour_mutation->load($this->mutation_sejour_id);
    $this->_ref_sejour_mutation->loadNumDossier();
  }
  
  function bindSejour() {
    if (!$this->_bind_sejour) {
      return;
    }
    
    global $g;
    
    $this->_bind_sejour = false;
    
    $this->loadRefsFwd();
    $sejour =& $this->_ref_sejour;
    $sejour->patient_id = $this->_patient_id;
    $sejour->group_id = $g;
    $sejour->praticien_id = $this->_responsable_id;
    $sejour->type = "urg";
    $sejour->entree_prevue = $this->_entree;
    $sejour->entree_reelle = $this->_entree;
    $sejour->sortie_prevue = mbDate(null, $this->_entree)." 23:59:59";
    if ($msg = $sejour->store()) {
      return $msg;
    }
    // Affectation du sejour_id au RPU
    $this->sejour_id = $sejour->_id;
  }
  
  function store() {
    // Bind Sejour
    if ($msg = $this->bindSejour()) {
      return $msg;
    }
       	
    // Standard Store
    if ($msg = parent::store()){
      return $msg;
    }
    
    $this->_ref_sejour->onStore();
  }
  
  function delete() {
    if ($msg = parent::delete()) {
      return $msg;
    }
    
    // @TODO: N'est-ce pas un cascaded
    $this->loadRefsFwd();
    return $this->_ref_sejour->delete();
  }
}
?>