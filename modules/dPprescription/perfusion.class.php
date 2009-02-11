<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPerfusion class
 */
class CPerfusion extends CMbObject {
	// DB Table key
  var $perfusion_id = null;
  
  // DB Fields
  var $prescription_id  = null; // Prescription
  var $type             = null; // Type de perfusion: seringue electrique / PCA
  var $libelle          = null; // Libelle de la perfusion
  var $vitesse          = null; // Stockée en ml/h
  var $voie             = null; // Voie d'administration des produits
  var $date_debut       = null; // Date de debut
  var $time_debut       = null; // Heure de debut
  var $duree            = null; // Duree de la perf (en heures)
  var $next_perf_id     = null; // Perfusion suivante (pour garder un historique lors de la modification de la perfusion)
  var $praticien_id     = null; // Praticien responsable de la perfusion
  var $creator_id       = null; // Createur de la perfusion
  var $signature_prat   = null; // Signature par le praticien responsable
  var $signature_pharma = null; // Signature par le pharmacien
  var $validation_infir = null; // Validation par l'infirmiere
  var $date_arret       = null; // Date d'arret de la perf (si arret anticipé) 
  var $time_arret       = null; // Heure d'arret de la perf (si arret anticipe)
  var $accord_praticien = null;
  var $decalage_interv  = null; // Nb heures de decalage par rapport à l'intervention (utilisé pour les protocoles de perfusions)
  var $operation_id     = null;
  
  var $date_debut_adm   = null; // Date de debut de la perf
  var $time_debut_adm   = null; // Heure de debut de la perf
  
  var $date_fin_adm     = null; // Date de fin de la perf
  var $time_fin_adm     = null; // Heure de fin de la perf
  var $emplacement      = null;
  
  // Champs specifiques aux PCA
  var $mode_bolus          = null; // Mode de bolus 
  var $dose_bolus          = null; // Dose du bolus (en mg)
  var $periode_interdite   = null; // Periode interdite en minutes
  //var $dose_limite_horaire = null; // Dose maxi pour une heure
  
  // Fwd Refs
  var $_ref_prescription = null;
  var $_ref_praticien    = null;
  
  // Back Refs
  var $_ref_lines        = null;
  
  // Form fields
  var $_debut             = null; // Debut de la perfusion (dateTime)
  var $_fin               = null; // Fin de la perfusion (dateTime)
  var $_protocole         = null; // Perfusion de protocole ?
  var $_add_perf_contigue = null;
  var $_count_parent_line = null;
  var $_debut_adm = null;
  var $_fin_adm   = null;
  
  // Object references
  var $_ref_log_signature_prat = null;
  
  // Can fields
  var $_perm_edit                        = null;
  var $_can_modify_perfusion             = null;
  var $_can_modify_perfusion_line        = null;
  var $_can_vw_form_signature_praticien  = null;
  var $_can_vw_form_signature_pharmacien = null;
  var $_can_vw_form_signature_infirmiere = null;
  var $_can_vw_signature_praticien       = null;
  var $_can_delete_perfusion             = null;
  var $_can_delete_perfusion_line        = null;
  var $_can_vw_form_add_perf_contigue    = null;
  var $_can_vw_form_stop_perf            = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perfusion';
    $spec->key   = 'perfusion_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
  	$specs["prescription_id"]   = "ref class|CPrescription cascade";
  	$specs["type"]              = "enum notNull list|classique|seringue|PCA";
    $specs["libelle"]           = "str";
    $specs["vitesse"]           = "num pos";
    $specs["voie"]              = "str";
    $specs["date_debut"]        = "date";
    $specs["time_debut"]        = "time";
    $specs["duree"]             = "num pos";
    $specs["next_perf_id"]      = "ref class|CPerfusion"; 
    $specs["praticien_id"]      = "ref class|CMediusers";
    $specs["creator_id"]        = "ref class|CMediusers";
    $specs["signature_prat"]    = "bool default|0";
    $specs["signature_pharma"]  = "bool default|0";
    $specs["validation_infir"]  = "bool default|0";
    $specs["date_arret"]        = "date";
    $specs["time_arret"]        = "time";
    $specs["accord_praticien"]  = "bool";
    $specs["_debut"]            = "dateTime";
    $specs["_fin"]              = "dateTime";
    $specs["decalage_interv"]   = "num";
    $specs["operation_id"]      = "ref class|COperation";
    $specs["mode_bolus"]        = "enum list|sans_bolus|bolus|perfusion_bolus default|sans_bolus";
    $specs["dose_bolus"]        = "float";
    $specs["periode_interdite"] = "num pos";
    $specs["date_debut_adm"]    = "date";
    $specs["time_debut_adm"]    = "time";
    $specs["date_fin_adm"]      = "date";
    $specs["time_fin_adm"]      = "time";
    $specs["emplacement"]       = "enum notNull list|service|bloc|service_bloc default|service";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    
    // Calcul de la view
    $this->_view = ($this->libelle) ? "$this->libelle " : "";
    $this->_view .= ($this->type) ? " $this->type, " : "";
    $this->_view .= $this->voie;
    $this->_view .= ($this->vitesse) ? " à $this->vitesse ml/h" : "";
    
    if($this->type == "PCA"){
      $this->_view .= ($this->mode_bolus) ? ", mode PCA: ".CAppUI::tr("CPerfusion.mode_bolus.".$this->mode_bolus) : "";
      $this->_view .= ($this->dose_bolus) ? ", bolus de $this->dose_bolus mg" : "";
      $this->_view .= ($this->periode_interdite) ? ", période interdite de $this->periode_interdite min" : "";
    }
    
    if($this->date_debut_adm){
      $this->_debut_adm = "$this->date_debut_adm $this->time_debut_adm";
    }
    if($this->date_fin_adm){
      $this->_fin_adm = "$this->date_fin_adm $this->time_fin_adm";
    }
    
    // Calcul du debut et de la fin de la ligne
    $this->_debut = "$this->date_debut $this->time_debut";
    
    // DateTime de fin initial de la ligne
    $this->_date_fin = $this->duree ? mbDateTime("+ $this->duree HOURS", "$this->_debut") : $this->_debut;
    $this->_fin = ($this->date_arret && $this->time_arret) ? "$this->date_arret $this->time_arret" : $this->_date_fin; 

    $this->loadRefPrescription();
    $this->_protocole = $this->_ref_prescription->object_id ? '0' : '1';
  }
  
  function updateDBFielfs(){
    parent::updateDBFields();
    if($this->date_arret == "current") {
    	$this->date_arret = mbDate();
    }
    if($this->time_arret == "current") {
    	$this->time_arret = mbTime();
    }
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["lines_perfusion"]  = "CPerfusionLine perfusion_id";
    $backRefs["prev_line"]     = "CPerfusion next_perf_id";  
    $backRefs["transmissions"] = "CTransmissionMedicale object_id";
    return $backRefs;
  }
  
  function getAdvancedPerms($is_praticien = 0, $mode_protocole = 0, $mode_pharma = 0) {
		global $AppUI, $can;
		
		$perm_infirmiere = $this->creator_id == $AppUI->user_id && 
                       !$this->signature_prat && 
                       !$this->validation_infir && 
                       !$this->signature_pharma;
    
    // Cas d'une ligne de protocole  
   
    if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
        $perm_edit = $protocole->_ref_praticien->canEdit();    
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {
      $perm_edit = ($can->admin && !$mode_pharma) || ((!$this->signature_prat || $mode_pharma) && 
                   !$this->signature_pharma && 
                   ($this->praticien_id == $AppUI->user_id  || $perm_infirmiere || $is_praticien || $mode_pharma));
    }
    $this->_perm_edit = $perm_edit;
    
    // Modification de la perfusion et des lignes des perfusions
    if($perm_edit){
    	$this->_can_modify_perfusion = 1;
    	$this->_can_modify_perfusion_line = 1;
    }
    // Affichage du formulaire de signature pharmacien
    if($mode_pharma){
    	$this->_can_vw_form_signature_pharmacien = 1;
    }
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id) && !$mode_pharma){
    	$this->_can_vw_form_signature_praticien = 1;
    }
    // View signature praticien
    if(!$this->_protocole){
    	$this->_can_vw_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
    if(!$mode_pharma && !$this->_protocole && !$is_praticien && !$this->signature_prat && $this->creator_id == $AppUI->user_id && !$this->signature_pharma){
    	$this->_can_vw_form_signature_infirmiere = 1;
    }
    // Suppression de la ligne
    if ($perm_edit || $this->_protocole){
      $this->_can_delete_perfusion = 1;
      $this->_can_delete_perfusion_line = 1;
  	}
  	// Affichage du bouton "Modifier une ligne"
  	if(!$this->_protocole){
  		$this->_can_vw_form_add_perf_contigue = 1;
  		$this->_can_vw_form_stop_perf = 1;
  	}
	}
  
	/*
	 * Duplication d'une perfusion
	 */
	function duplicatePerf(){
    $this->_add_perf_contigue = false;
    
	  // Creation de la nouvelle perfusion
	  $new_perf = new CPerfusion();
    $new_perf->load($this->_id);
    $new_perf->loadRefsLines();
    $new_perf->_id = "";
    $new_perf->signature_pharma = 0;
    $new_perf->signature_prat = 0;
    if($msg = $new_perf->store()){
      return $msg;
    }
    
    // Copie des lignes dans la perfusion
    foreach($new_perf->_ref_lines as $_line){
      $_line->_id = "";
      $_line->perfusion_id = $new_perf->_id;
      if($msg = $_line->store()){
        return $msg;
      }
    }

    // Arret de la ligne et creation de l'historique
    $this->date_arret = mbDate();
    $this->time_arret = mbTime();
    $this->next_perf_id = $new_perf->_id;
	}

	
	function loadRefsTransmissions(){
	  $this->_ref_transmissions = $this->loadBackRefs("transmissions");
	  foreach($this->_ref_transmissions as &$_trans){
	    $_trans->loadRefsFwd();
	  }
	}
	
  function loadView() {
    $this->loadRefsLines();
    $this->loadRefsTransmissions();
  }
  
  function store(){
    if($this->_add_perf_contigue){
      if($msg = $this->duplicatePerf()){
        return $msg;
      }
    }  
    
    $get_guid = $this->_id ? false : true;
    
    if($msg = parent::store()){
  		return $msg;
    }
  	// On met en session le dernier guid créé
    if($get_guid){
  	  $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
    }
  }
  
  
  function delete(){
    // On supprime la ref vers la perf courante
    $perfusion = new CPerfusion();
    $perfusion->next_perf_id = $this->_id;
    $perfusion->loadMatchingObject();
    if($perfusion->_id){
      // On vide le child_id
      $perfusion->next_perf_id = "";
      if($msg = $perfusion->store()){
        return $msg;
      }
    }
    // Suppression de la ligne
    return parent::delete();
  }
  
  /*
   * Chargement de la ligne precedent la ligne courante
   */
  function loadRefParentLine(){
  	$this->_ref_parent_line = $this->loadUniqueBackRef("prev_line");
  }
  
  /*
   * Chargement récursif des parents d'une perfusion
   */
  function loadRefsParents($lines = array()) {
    if(!array_key_exists($this->_id, $lines)){
      $lines[$this->_id] = $this;
    }
    // Chargement de la parent_line
    $this->loadRefParentLine();
    if($this->_ref_parent_line->_id){
      $lines[$this->_ref_parent_line->_id] = $this->_ref_parent_line;
      return $this->_ref_parent_line->loadRefsParents($lines);
    } else {
      return $lines;
    }
  }
  
  /*
   * Chargement de la prescription
   */
  function loadRefPrescription(){
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription = $this->_ref_prescription->getCached($this->prescription_id);
  }
  
  /*
   * Chargement des lignes de la perfusion
   */
  function loadRefsLines(){
    $this->_ref_lines = $this->loadBackRefs("lines_perfusion");
  }
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);
  }
  
  /*
   * Calcul permettant de savoir si la ligne possède un historique
   */
  function countParentLine(){
    $perfusion = new CPerfusion();
    $perfusion->next_perf_id = $this->_id;
    $this->_count_parent_line = $perfusion->countMatchingList(); 
  }
  
  function loadRefLogSignaturePrat(){
    $this->_ref_log_signature_prat = $this->loadLastLogForField("signature_prat");
  }
  
}
  
?>