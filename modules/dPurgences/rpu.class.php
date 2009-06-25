<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
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
  var $_attente  = null;
  var $_presence = null;
  var $_can_leave_since = null;
  var $_can_leave_since_warning = null;
  var $_can_leave_since_error = null;

  // Patient
  var $_patient_id = null;
  var $_cp         = null;
  var $_ville      = null;
  var $_naissance  = null;
  
  // Sejour
  var $_responsable_id = null;
  var $_annule         = null;
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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rpu';
    $spec->key   = 'rpu_id';
    $spec->measureable = true;
    return $spec;
  }

  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "sejour_id"        => "ref notNull class|CSejour cascade",
      "diag_infirmier"   => "text helped",
      "mode_entree"      => "enum list|6|7|8 notNull",
      "provenance"       => "enum list|1|2|3|4|5|8",
      "transport"        => "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo notNull",
      "pec_transport"    => "enum list|med|paramed|aucun",
      "motif"            => "text helped",
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
      "_patient_id"      => "ref notNull class|CPatient",
      "_responsable_id"  => "ref notNull class|CMediusers",
      "_entree"          => "dateTime",
      "_etablissement_transfert_id" => "ref class|CEtabExterne",
      "_attente"         => "time",
      "_presence"        => "time",
      "_can_leave_since" => "time",
      "_can_leave_since_warning" => "bool",
      "_can_leave_since_error" => "bool",
     );
     
		$specs["urprov"] = "";
		$specs["urmuta"] = "";
		$specs["urtrau"] = "";    

		// Legacy Sherpa fields
		if (CModule::getActive("sherpa")) {
		    $urgDro = new CSpUrgDro();
		    $specs["urprov"] = $urgDro->_props["urprov"] . " notNull";
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
    $this->_annule         = $sejour->annule;
    
    $patient =& $sejour->_ref_patient;
    
    $this->_patient_id = $patient->_id;
    $this->_cp         = $patient->cp;
    $this->_ville      = $patient->ville;
    $this->_naissance  = $patient->naissance;
    $this->_view       = $patient->_view;
    
    
    // Calcul des valeurs de _mode_sortie
    if ($this->_ref_sejour->mode_sortie == "transtert" && $this->mutation_sejour_id) {
    	$this->_mode_sortie = 6;
    }
    
    if ($this->_ref_sejour->mode_sortie == "transfert" && !$this->mutation_sejour_id) {
    	$this->_mode_sortie = 7; 
    }
    
    if ($this->_ref_sejour->mode_sortie == "normal") {
    	$this->_mode_sortie = 8;
    }
    
    if ($this->_ref_sejour->mode_sortie == "deces") {
    	$this->_mode_sortie = 9;
    }
    
    $this->_sortie = $this->_ref_sejour->sortie_reelle;
    $this->_etablissement_transfert_id = $this->_ref_sejour->etablissement_transfert_id;

    $this->_can_leave_since = (mbTime($this->_ref_sejour->sortie_prevue) > mbTime()) ? -1 : mbTimeRelative(mbTime($this->_ref_sejour->sortie_prevue), mbTime());
    if ($this->_can_leave_since != -1) {
    	$this->_can_leave_since_warning = (CAppUI::conf("dPurgences rpu_warning_time") < $this->_can_leave_since) && 
                                      ($this->_can_leave_since < CAppUI::conf("dPurgences rpu_alert_time"));
      $this->_can_leave_since_error   = ($this->_can_leave_since > CAppUI::conf("dPurgences rpu_alert_time"));
    }
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
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
      $this->_ref_consult->countDocItems();
    }
    
	  // Calcul des temps d'attente et présence
		$entree = mbTime($this->_ref_sejour->_entree);
		$this->_presence =  mbSubTime($entree, mbTime());

		if ($this->_ref_sejour->sortie_reelle) {
	    $this->_presence  = mbSubTime($entree, mbTime($this->_ref_sejour->sortie_reelle));
	  }
	  
		$this->_attente  = $this->_presence;
		
	  if ($this->_ref_consult->_id) {
	    $this->_attente  = mbSubTime($entree, mbTime($this->_ref_consult->heure));
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
    $sejour->annule        = $this->_annule;

    // Le patient est souvent chargé à vide ce qui pose problème
    // dans le onStore(). Ne pas supprimer.
    $sejour->_ref_patient = null;

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
}
?>