<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Thomas Despoix
 */

CAppUI::requireModuleClass("dPccam", "codable");

/**
 * Classe CSejour. 
 * @abstract Gère les séjours en établissement
 */
class CSejour extends CCodable {
  // DB Table key
  var $sejour_id = null;
  
  // DB Réference
  var $patient_id         = null; // remplace $op->pat_id
  var $praticien_id       = null; // clone $op->chir_id
  var $group_id           = null;
  
  var $etablissement_transfert_id = null;
  
  // DB Fields
  var $type               = null; // remplace $op->type_adm
  var $modalite           = null;
  var $annule             = null; // complète $op->annule
  var $chambre_seule      = null; // remplace $op->chambre
  var $reanimation        = null; // Entrée en réanimation
  var $zt                 = null; // Entrée en zone de très courte durée

  var $entree_prevue      = null;
  var $sortie_prevue      = null;
  var $entree_reelle      = null;
  var $sortie_reelle      = null;
  
  var $saisi_SHS          = null; // remplace $op->saisie
  var $modif_SHS          = null; // remplace $op->modifiee

  var $DP                 = null; 
  var $DR                 = null;
  var $pathologie         = null; // remplace $operation->pathologie
  var $septique           = null; // remplace $operation->septique
  var $convalescence      = null; // remplace $operation->convalescence

  var $rques              = null;
  var $ATNC               = null;
  var $hormone_croissance = null;
  var $lit_accompagnant   = null;
  var $isolement          = null;
  var $television         = null;
  var $repas_diabete      = null;
  var $repas_sans_sel     = null;
  var $repas_sans_residu  = null;
  var $repas_sans_porc    = null;
  var $mode_sortie        = null;
  var $prestation_id      = null;
  var $facturable         = null; 
  
  // Form Fields
  var $_entree             = null;
  var $_sortie             = null;
  var $_duree_prevue       = null;
  var $_duree_reelle       = null;
  var $_duree              = null;
  var $_date_entree_prevue = null;
  var $_date_sortie_prevue = null;
  var $_hour_entree_prevue = null;
  var $_hour_sortie_prevue = null;
  var $_min_entree_prevue  = null;
  var $_min_sortie_prevue  = null;
  var $_sortie_autorisee   = null;
  var $_guess_num_dossier  = null;
  var $_at_midnight        = null;
  var $_couvert_cmu        = null;
  var $_curr_op_id         = null;
  var $_curr_op_date       = null;
  var $_protocole_prescription_anesth_id = null;
  var $_protocole_prescription_chir_id   = null;
  
  // Behaviour fields
  var $_check_bounds = true;
  
  // Object References
  var $_ref_patient           = null; // Declared in CCodable
  var $_ref_praticien         = null;
  var $_ref_operations        = null;
  var $_ref_last_operation    = null;
  var $_ref_affectations      = null;
  var $_ref_first_affectation = null;
  var $_ref_last_affectation  = null;
  var $_ref_GHM               = array();
  var $_ref_group             = null;
  var $_ref_etabExterne       = null;
  var $_ref_dossier_medical   = null;
  var $_ref_rpu               = null;
  var $_ref_consult_anesth    = null;
  var $_ref_consultations     = null;
  var $_ref_consult_atu       = null;
  var $_ref_prescriptions     = null;
  var $_ref_last_prescription = null;
  var $_ref_numdos            = null;
  
  // External objects
  var $_ext_diagnostic_principal = null;
  var $_ext_diagnostic_relie     = null;
  var $_ref_hprim_files          = null;
  
  // Distant fields
  var $_dates_operations = null;
  var $_num_dossier      = null;
  var $_list_constantes_medicales = null;
  var $_cancel_alerts    = null;
  
  // Filter Fields
  var $_date_min	 			= null;
  var $_date_max 				= null;
  var $_date_entree     = null;
  var $_date_sortie     = null;
  var $_admission 			= null;
  var $_service 				= null;
  var $_type_admission  = null;
  var $_specialite 			= null;
  var $_date_min_stat		= null;
  var $_date_max_stat 	= null;
  var $_filter_type 		= null;
  var $_ccam_libelle    = null;
  
  // Object tool field
  var $_modifier_sortie = null;
  
  function CSejour() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp CSejour locked");
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sejour';
    $spec->key   = 'sejour_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["affectations"]  = "CAffectation sejour_id";
    $backRefs["factures"]      = "CFacture sejour_id";
    $backRefs["GHM"]           = "CGHM sejour_id";
    $backRefs["operations"]    = "COperation sejour_id";
    $backRefs["rpu"]           = "CRPU sejour_id";
    $backRefs["consultations"] = "CConsultation sejour_id";
    $backRefs["prescriptions"] = "CPrescription object_id";
    $backRefs["observations"]  = "CObservationMedicale sejour_id";
    $backRefs["transmissions"] = "CTransmissionMedicale sejour_id";
    return $backRefs;
  }

  function getSpecs() {
   	$specs = parent::getSpecs();
    $specs["patient_id"]          = "notNull ref class|CPatient";
    $specs["praticien_id"]        = "notNull ref class|CMediusers";
    $specs["group_id"]            = "notNull ref class|CGroups";
    $specs["type"]                = "notNull enum list|comp|ambu|exte|seances|ssr|psy|urg default|ambu";
    $specs["modalite"]            = "notNull enum list|office|libre|tiers default|libre";
    $specs["annule"]              = "bool";
    $specs["chambre_seule"]       = "bool";
    $specs["reanimation"]         = "bool default|0";
    $specs["zt"]                  = "bool default|0";
    $specs["entree_prevue"]       = "notNull dateTime";
    $specs["sortie_prevue"]       = "notNull dateTime moreEquals|entree_prevue";
    $specs["entree_reelle"]       = "dateTime";
    $specs["sortie_reelle"]       = "dateTime moreEquals|entree_reelle";
    $specs["saisi_SHS"]           = "bool";
    $specs["modif_SHS"]           = "bool";
    $specs["DP"]                  = "code cim10";
    $specs["DR"]                  = "code cim10";
    $specs["pathologie"]          = "str length|3";
    $specs["septique"]            = "bool";
    $specs["convalescence"]       = "text confidential";
    $specs["rques"]               = "text";
    $specs["ATNC"]                = "bool";
    $specs["hormone_croissance"]  = "bool";
    $specs["lit_accompagnant"]    = "bool";
    $specs["isolement"]           = "bool";
    $specs["television"]          = "bool";
    $specs["repas_diabete"]       = "bool";
    $specs["repas_sans_sel"]      = "bool";
    $specs["repas_sans_residu"]   = "bool";
    $specs["repas_sans_porc"]     = "bool";
    $specs["mode_sortie"]         = "enum list|normal|transfert|deces default|normal";
    $specs["prestation_id"]       = "ref class|CPrestation";
    $specs["facturable"]          = "bool notNull default|1";
    $specs["etablissement_transfert_id"] = "ref class|CEtabExterne";
    
    $specs["_entree"]         = "dateTime";
    $specs["_sortie"] 		    = "dateTime";
    $specs["_date_entree"] 		= "date";
    $specs["_date_sortie"] 		= "date";
    $specs["_date_min"] 		  = "dateTime";
    $specs["_date_max"] 		  = "dateTime moreEquals|_date_min";
    $specs["_admission"] 		  = "text";
    $specs["_service"] 	      = "text";
    $specs["_type_admission"] = "notNull enum list|comp|ambu|exte|seances|ssr|psy default|ambu";
    $specs["_specialite"]     = "text";
    $specs["_date_min_stat"]  = "date";
    $specs["_date_max_stat"]  = "date moreEquals|_date_min_stat";
    $specs["_filter_type"]    = "enum list|comp|ambu|exte|seances|ssr|psy";
    $specs["_num_dossier"]    = "str";
    $specs["_ccam_libelle"]   = "bool default|1";
    $specs["_duree_prevue"]   = "num";
    $specs["_duree_reelle"]   = "num";
    $specs["_date_entree_prevue"] = "date";
    $specs["_date_sortie_prevue"] = "date";
    $specs["_sortie_autorisee"]   = "bool";
    $specs["_protocole_prescription_anesth_id"] = "ref class|CPrescription";
    $specs["_protocole_prescription_chir_id"]   = "ref class|CPrescription";
        
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "patient_id"    => "ref|CPatient",
      "praticien_id"  => "ref|CMediusers",
      "convalescence" => "like"
    );
  }

  function check() {
    $msg    = null;
    $pathos = new CDiscipline();
    
    // Test de la pathologies
    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_enums["categorie"]))) {
      $msg.= "Pathologie non disponible<br />";
    }
    
    // Test de coherence de date avec les interventions
    if ($this->_check_bounds) {
	    $this->completeField("entree_prevue");
	    $this->completeField("sortie_prevue");
	    $entree = $this->entree_prevue;
	    $sortie = $this->sortie_prevue;
	
	    if ($entree !== null && $sortie !== null) {
	    	$this->makeDatesOperations();
	    	foreach($this->_dates_operations as $operation_id => $date_operation){
	    	  $isCurrOp = $this->_curr_op_id == $operation_id;
	        if ($isCurrOp) {
	          $opInBounds = $this->_curr_op_date >= mbDate($entree) && $this->_curr_op_date <= mbDate($sortie);
	        } 
	        else {
	          $opInBounds = $date_operation >= mbDate($entree) && $date_operation <= mbDate($sortie);
	        }
	        if (!$opInBounds) {
	           $msg.= "Interventions en dehors des nouvelles dates du sejour";  
	        }	
	    	}
	    }
	
	    foreach ($this->getCollisions() as $collision) {
	      $msg .= "Collision avec le sejour du $collision->entree_prevue au $collision->sortie_prevue<br />"; 
	    }
    }
    
    return $msg . parent::check();
  }

  /**
   * Cherche les différentes collisions au séjour courant
   * @return array|CSejour
   */
  function getCollisions() {
    $collisions = array();
    
    // Ne concerne pas les annulés
    $this->completeField("annule");
    $this->completeField("type");
    if ($this->annule || $this->type == "urg") {
      return $collisions;
    }
    
    // Test de colision avec un autre sejour
    $this->completeField("patient_id");
    $patient = new CPatient;
    $patient->load($this->patient_id);
    if (!$patient->_id) {
      return $collisions;
    }
    
    $where["annule"] = " = '0'";
    $where["type"] = " != 'urg'";
    $patient->loadRefsSejours($where);
    
    // suppression de la liste des sejours le sejour courant
    $sejours = $patient->_ref_sejours;
    unset($sejours[$this->_id]);
    
    foreach ($sejours as $sejour) {
      if ($this->collides($sejour)) {
        $collisions[$sejour->_id] = $sejour;
      }
    }
    
    return $collisions;
  }
  
  /**
   * Check is the object collide another
   * @param $sejour CSejour
   * @return boolean
   */
  function collides(CSejour $sejour) {
    return (mbDate($sejour->entree_prevue) <= mbDate($this->sortie_prevue) and mbDate($sejour->sortie_prevue) >= mbDate($this->sortie_prevue))
         or(mbDate($sejour->entree_prevue) <= mbDate($this->entree_prevue) and mbDate($sejour->sortie_prevue) >= mbDate($this->entree_prevue))
         or(mbDate($sejour->entree_prevue) >= mbDate($this->entree_prevue) and mbDate($sejour->sortie_prevue) <= mbDate($this->sortie_prevue));
  }
  
  function applyProtocolesPrescription($operation_id = null) {
    // Application du protocole de prescription
    $prescription = new CPrescription;
    $prescription->object_class = $this->_class_name;
    $prescription->object_id = $this->_id;
    $prescription->type = "sejour";
    if ($msg = $prescription->store()) {
      return $msg;
    }
    
    if($this->_protocole_prescription_anesth_id){
      $prescription->applyPackOrProtocole($this->_protocole_prescription_anesth_id, $this->praticien_id, mbDate(), $operation_id);
    }
    if($this->_protocole_prescription_chir_id){
      $prescription->applyPackOrProtocole($this->_protocole_prescription_chir_id, $this->praticien_id, mbDate(), $operation_id);
    }
  }
    
  function store() {
    if (null !== $this->mode_sortie) {
      if ("transfert" != $this->mode_sortie) {
        $this->etablissement_transfert_id = "";
      }
    }
    
    if($this->mode_sortie === ""){
      $this->sortie_reelle = "";
    }
  	
  	if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->annule) {
      $this->delAffectations();
      $this->cancelOperations();
    }

    // Cas où on a une premiere affectation différente de l'heure d'admission
    $this->loadRefsAffectations();
    $firstAff =& $this->_ref_first_affectation;
    $lastAff =& $this->_ref_last_affectation;
    if ($this->entree_prevue) {
      if ($firstAff->affectation_id && ($firstAff->entree != $this->entree_prevue)) {
        $firstAff->entree = $this->entree_prevue;
        $firstAff->_no_synchro = 1;
        $firstAff->store();
      }
      if ($lastAff->affectation_id && ($lastAff->sortie != $this->sortie_prevue)) {
        $lastAff->sortie = $this->sortie_prevue;
        $lastAff->_no_synchro = 1;
        $lastAff->store();
      }
    }
    
    //si le sejour a une sortie ==> compléter le champ effectue de la derniere affectation
    if($lastAff->_id){
      $this->_ref_last_affectation->effectue = $this->sortie_reelle ? 1 : 0;
      $this->_ref_last_affectation->store();
    }
    
  }
  
  function delAffectations() {
    $this->loadRefsAffectations();
    foreach($this->_ref_affectations as $key => $value) {
      $this->_ref_affectations[$key]->deleteOne();
    }
    return null;
  }
  
  function cancelOperations(){
    $this->loadRefsOperations();
    foreach($this->_ref_operations as $key => $value) {
      $value->annulee = 1;
      $this->_ref_operations[$key]->store();
    }
  }
  
  function getActeExecution() {
    $this->updateFormFields();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_entree = mbGetValue($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = mbGetValue($this->sortie_reelle, $this->sortie_prevue);
    
    $this->_duree_prevue       = mbDaysRelative($this->entree_prevue, $this->sortie_prevue);
    $this->_duree_reelle       = mbDaysRelative($this->entree_reelle, $this->sortie_reelle);
    $this->_duree              = mbDaysRelative($this->_entree, $this->_sortie);

    $this->_date_entree_prevue = mbDate(null, $this->entree_prevue);
    $this->_date_sortie_prevue = mbDate(null, $this->sortie_prevue);
    $this->_hour_entree_prevue = mbTransformTime(null, $this->entree_prevue, "%H");
    $this->_hour_sortie_prevue = mbTransformTime(null, $this->sortie_prevue, "%H");
    $this->_min_entree_prevue  = mbTransformTime(null, $this->entree_prevue, "%M");
    $this->_min_sortie_prevue  = mbTransformTime(null, $this->sortie_prevue, "%M");

    switch(CAppUI::conf("dPpmsi systeme_facturation")) {
      case "siemens" :
        $this->_guess_num_dossier = mbTransformTime(null, $this->entree_prevue, "%y");
        $this->_guess_num_dossier .= 
          $this->type == "exte" ? "5" :
          $this->type == "ambu" ? "4" : "0";
        $this->_guess_num_dossier .="xxxxx";
        break;
      default: 
        $this->_guess_num_dossier = "-";
    }
    $this->_at_midnight = ($this->_date_entree_prevue != $this->_date_sortie_prevue);

    if($this->entree_prevue && $this->sortie_prevue) {
      $this->_view = "Séjour du ";
      $this->_view .= mbTransformTime(null, $this->entree_prevue, "%d/%m/%Y");
      $this->_view .= " au ";
      $this->_view .= mbTransformTime(null, $this->sortie_prevue, "%d/%m/%Y");
    }
    $this->_acte_execution = mbAddDateTime($this->entree_prevue);
    $this->_praticien_id = $this->praticien_id;
  }
  
  function updateDBFields() {
    if ($this->_hour_entree_prevue !== null and $this->_min_entree_prevue !== null) {
      $this->entree_prevue = "$this->_date_entree_prevue";
      $this->entree_prevue.= " ".str_pad($this->_hour_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":".str_pad($this->_min_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":00";
    }
    
    if ($this->_hour_sortie_prevue !== null and $this->_min_sortie_prevue !== null) {
      $this->sortie_prevue = "$this->_date_sortie_prevue";
      $this->sortie_prevue.= " ".str_pad($this->_hour_sortie_prevue, 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":".str_pad($this->_min_sortie_prevue, 2, "0", STR_PAD_LEFT); 
      $this->sortie_prevue.= ":00";
    }
        
    // Synchro durée d'hospi / type d'hospi
    $this->_at_midnight = (mbDate(null, $this->entree_prevue) != mbDate(null, $this->sortie_prevue));
    if($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    } elseif(!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
    }
    
    // Signaler l'action de validation de la sortie
    if ($this->_modifier_sortie === '1') {
      $this->sortie_reelle = mbDateTime();
    }
    
    if ($this->_modifier_sortie === '0'){
      $this->sortie_reelle = "";
    }    
  }

  function getTemplateClasses(){
    $this->loadRefsFwd();
    
    $tab = array();
    
    // Stockage des objects liés au séjour
    $tab['CSejour'] = $this->_id;
    $tab['CPatient'] = $this->_ref_patient->_id;
    
    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;
    $tab['COperation'] = 0;
    
    return $tab;
  }
  
  // Calcul des droits CMU pour la duree totale du sejour
  function getDroitsCMU () {
  	if($this->_date_sortie_prevue <= $this->_ref_patient->fin_amo && $this->_ref_patient->cmu){
  		$this->_couvert_cmu = 1;
  	} else {
  		$this->_couvert_cmu = 0;
  	}
  }
  
  function loadRefSejour() {
    $this->_ref_sejour =& $this;
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["group_id"] = "= '$g->_id'";
    
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  
  function loadRefCurrAffectation($date = ""){
    if(!$date){
  		$date = mbDateTime();
  	}
  	$this->_ref_curr_affectation = new CAffectation();
  	$where = array();
  	$where["sejour_id"] = " = '$this->_id'";
	  $where["entree"] = "<= '$date'";
    $where["sortie"] = ">= '$date'";
    $this->_ref_curr_affectation->loadObject($where);
    if($this->_ref_curr_affectation->_id){
      $this->_ref_curr_affectation->loadRefLit();
      $this->_ref_curr_affectation->_ref_lit->loadCompleteView();
    }
  }
  
  
  // Chargement de l'affectation courante (en fct de $date)
  function loadCurrentAffectation($date = "") {
  	if(!$date){
  		$date = mbDateTime();
  	}
  
    $this->loadRefCurrAffectation($date);
    
    $this->_ref_before_affectation = new CAffectation();
    $where = array();
    $where["sortie"] = " < '$date'";
    $where["sejour_id"] = " = '$this->_id'";
    $this->_ref_before_affectation->loadObject($where);
    if($this->_ref_before_affectation->_id){
      $this->_ref_before_affectation->loadRefLit();
      $this->_ref_before_affectation->_ref_lit->loadCompleteView();    
    }
    
    $this->_ref_next_affectation = new CAffectation();
    $where = array();
    $where["entree"] = "> '$date'"; 
    $where["sejour_id"] = " = '$this->_id'";
    $this->_ref_next_affectation->loadObject($where);
    if($this->_ref_next_affectation->_id){
      $this->_ref_next_affectation->loadRefLit();
      $this->_ref_next_affectation->_ref_lit->loadCompleteView();	
    }
  }
    
  // Chargement du dossier medical du sejour
  function loadRefDossierMedical(){
    $this->_ref_dossier_medical = new CDossierMedical();
    $where["object_id"] = "= '$this->_id'";
    $where["object_class"] = "= 'CSejour'";
    $this->_ref_dossier_medical->loadObject($where);
  }
  
  function loadRefEtabExterne(){
    $this->_ref_etabExterne = new CEtabExterne();
    $this->_ref_etabExterne->load($this->etablissement_transfert_id);
  }
  
  function loadRefPatient() {
    if ($this->_ref_patient) {
      return;
    }
    
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
    $this->getDroitsCMU();

    // View
    $this->_view = "Séjour de ";
    $this->_view .= $this->_ref_patient->_view;
    $this->_view .= " du ";
    $this->_view .= mbTransformTime(null, $this->entree_prevue, "%d/%m/%Y");
    $this->_view .= " au ";
    $this->_view .= mbTransformTime(null, $this->sortie_prevue, "%d/%m/%Y");
  }
  
  function loadRefPraticien() {
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadExtDiagnostics() {
    $this->_ext_diagnostic_principal = $this->DP ? new CCodeCIM10($this->DP, 1) : null;
    $this->_ext_diagnostic_relie     = $this->DR ? new CCodeCIM10($this->DR, 1) : null;
  }
  
  function loadRefPrestation() {
    $this->_ref_prestation = new CPrestation;
    $this->_ref_prestation->load($this->prestation_id);
  }
  
  function loadRefsTransmissions(){
    $this->_ref_transmissions = $this->loadBackRefs("transmissions");	
  }
  
  
  function loadRefEtablissement() {
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefRPU() {
    $this->_ref_rpu = $this->loadUniqueBackRef("rpu");
  }
  
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return;
    }
    
    $order = "consultation_anesth_id ASC";
    $this->_ref_consult_anesth = new CConsultAnesth();
    $this->_ref_consult_anesth->sejour_id = $this->_id;
    $this->_ref_consult_anesth->loadMatchingObject($order);
  }
  
  /**
   * Charge les consultations, en particulier l'ATU dans le cas UPATOU
   */
  function loadRefsConsultations() {
    $this->_ref_consultations = $this->loadBackRefs("consultations");
    
    $this->_ref_consult_atu = new CConsultation;
    if ($this->type == "urg" && count($this->_ref_consultations)) {
    	$this->_ref_consult_atu = reset($this->_ref_consultations);
    }
  }
  
  function loadRefsPrescriptions() {
  	$prescriptions = $this->loadBackRefs("prescriptions");
  	// Si $prescriptions n'est pas un tableau, module non installé
    if(!is_array($prescriptions)){
    	$this->_ref_last_prescription = null;
    	return;
    }
    $this->_count_prescriptions = count($prescriptions);
  	$this->_ref_prescriptions["pre_admission"] = new CPrescription();
  	$this->_ref_prescriptions["traitement"] = new CPrescription();
  	$this->_ref_prescriptions["sejour"] = new CPrescription();
  	$this->_ref_prescriptions["sortie"] = new CPrescription();
  	
  	// Stockage des prescriptions par type
  	foreach($prescriptions as $_prescription){
	    $this->_ref_prescriptions[$_prescription->type] = $_prescription;
  	}
  }
  
  
  function loadRefPrescriptionTraitement(){
    $prescription = new CPrescription();
    $prescription->type = "traitement";
    $prescription->object_id = $this->_id;
    $prescription->object_class = $this->_class_name;
    $prescription->loadMatchingObject();
  	$this->_ref_prescription_traitement = $prescription;
  }
  
  function loadListConstantesMedicales() {
    if ($this->_list_constantes_medicales) return;
    
    $this->_list_constantes_medicales = new CConstantesMedicales();
    $where = array();
    $where['context_class'] = " = '$this->_class_name'";
    $where['context_id']    = " = $this->_id";
    $where['patient_id']    = " = $this->patient_id";
    $this->_list_constantes_medicales = $this->_list_constantes_medicales->loadList($where, 'datetime ASC');
  }
  
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefPraticien();
    $this->loadRefEtablissement();
    $this->loadRefEtabExterne();
    $this->loadExtCodesCCAM();
  }
  
  function loadView() {
    $this->loadRefsFwd();
    $this->loadRefsActesCCAM();
  }
  
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_operations as &$operation) {
      $operation->loadRefsFwd();
    }
    foreach ($this->_ref_affectations as &$affectation) {
      $affectation->loadRefLit();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    } 
    
    $this->loadExtDiagnostics();
    
    // Chargement du RPU dans le cas des urgences
    $this->loadRefRPU();
    if ($this->_ref_rpu) {
      $this->_ref_rpu->loadRefSejour();
    }
    
    $this->loadNumDossier();
    
  }

  function loadNumDossier() {
    // Aucune configuration de numéro de dossier
    if (!CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      $this->_num_dossier = "";
      return;
    }
    
    // Objet inexistatn
    if (!$this->_id) {
      return "-";
    }
    
    // sinon, $_num_dossier = valeur id400
    // creation du tag de l'id Externe
    global $g;
    $tag = str_replace('$g',$g, CAppUI::conf("dPplanningOp CSejour tag_dossier"));

    // Recuperation de la valeur de l'id400
    $id400 = new CIdSante400();
    $id400->loadLatestFor($this, $tag);
    
    // Stockage de la valeur de l'id400
    $this->_ref_numdos  = $id400;
    $this->_num_dossier = $id400->id400;
    
    // Si pas d'id400 correspondant, on stocke "_"
    if(!$this->_num_dossier){
      $this->_num_dossier = "-";
    }
  }
  
  function loadFromNumDossier($num_dossier) {
    global $g;
    $tag_dossier = CAppUI::conf("dPplanningOp CSejour tag_dossier");
    if (null == $tag_dossier = str_replace('$g',$g, $tag_dossier)) {
      return;
    }
    
    $idDossier = new CIdSante400();
	  $idDossier->id400 = $num_dossier;
    $idDossier->tag = $tag_dossier;
	  $idDossier->object_class = $this->_class_name;
	  $idDossier->loadMatchingObject();
	  
	  if ($idDossier->_id) {
	    $this->load($idDossier->object_id);
	    $this->_num_dossier = $idDossier->id400;
	  }
  }
    
  function getExecutantId($code_activite) {
      return $this->praticien_id;
  }
  
  function getPerm($permType) {
    if(!$this->_ref_praticien) {
      $this->loadRefPraticien();
    }
    if(!$this->_ref_group) {
      $this->loadRefEtablissement();
    }
    switch($permType) {
      case PERM_EDIT :
        return ($this->_ref_group->getPerm($permType) && $this->_ref_praticien->getPerm($permType));
        break;
      default :
        return parent::getPerm($permType);
    }
  }
  
  function getCurrAffectation($date = null) {
    if(!$date) {
      $date = mbDateTime();
    }
    $curr_affectation = new CAffectation();
    $order = "entree";
    $where = array();
    $where["sejour_id"] = $this->_spec->ds->prepare("= %", $this->sejour_id);
    if(mbTime(null, $date) == "00:00:00") {
      $where["entree"] = $this->_spec->ds->prepare("< %", mbDate(null, $date)." 23:59:59");
      $where["sortie"] = $this->_spec->ds->prepare(">= %", mbDate(null, $date)." 00:00:01");
    } else {
      $where["entree"] = $this->_spec->ds->prepare("< %", $date);
      $where["sortie"] = $this->_spec->ds->prepare(">= %", $date);
    }
    $curr_affectation->loadObject($where, $order);
    return $curr_affectation;
  }
  
  function loadRefsAffectations($order = "sortie DESC") {
    $where = array("sejour_id" => "= '$this->sejour_id'");
    $this->_ref_affectations = new CAffectation();
    $this->_ref_affectations = $this->_ref_affectations->loadList($where, $order);
    if (count($this->_ref_affectations) > 0) {
      $this->_ref_first_affectation = end($this->_ref_affectations);
      $this->_ref_last_affectation = reset($this->_ref_affectations);
    } 
    else {
      $this->_ref_first_affectation = new CAffectation;
      $this->_ref_last_affectation = new CAffectation;
    }
    
    $this->_sortie_autorisee = $this->_ref_last_affectation->confirme;
  }
  
  function loadRefsOperations($where = array()) {
    $where["sejour_id"] = "= '$this->_id'";
    $order = "date ASC";

    $operations = new COperation;
    $this->_ref_operations = $operations->loadList($where, $order);
        
    if (count($this->_ref_operations) > 0) {
      $this->_ref_last_operation = reset($this->_ref_operations);
    } else {
      $this->_ref_last_operation = new COperation;
    }
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
    $this->loadRefsActesCCAM();
  }
  
  function loadRefGHM() {
    $this->_ref_GHM = new CGHM;
    $where["sejour_id"] = "= '$this->sejour_id'";
    $this->_ref_GHM->loadObject($where);
    if(!$this->_ref_GHM->ghm_id) {
      $this->_ref_GHM->sejour_id = $this->sejour_id;
      $this->_ref_GHM->loadRefsFwd();
      $this->_ref_GHM->bindInfos();
      $this->_ref_GHM->getGHM();
    }
  }
  
  function loadHprimFiles() {
    $hprimFile = new CHPrimXMLEvenementPmsi();
    $hprimFile->setFinalPrefix($this);
    $hprimFile->getSentFiles();
    $this->_ref_hprim_files = $hprimFile->sentFiles;
  }
  
  function fillLimitedTemplate(&$template) {
    $dateFormat = "%d / %m / %Y";
    $timeFormat = "%Hh%M";
    
    $template->addProperty("Admission - Date"                 , mbTransformTime(null, $this->entree_prevue, $dateFormat));
    $template->addProperty("Admission - Heure"                , mbTransformTime(null, $this->entree_prevue, $timeFormat));
    $template->addProperty("Hospitalisation - Durée"          , $this->_duree_prevue);
    $template->addProperty("Hospitalisation - Date sortie"    , mbTransformTime(null, $this->sortie_prevue, $dateFormat));
    
    $this->loadRefPraticien();
    $template->addProperty("Hospitalisation - Praticien"    , "Dr ".$this->_ref_praticien->_view);
    
    // Diagnostics
    $this->loadExtDiagnostics();
    $diag = $this->DP ? "$this->DP: {$this->_ext_diagnostic_principal->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Principal"    , $diag);
    $diag = $this->DR ? "$this->DR: {$this->_ext_diagnostic_relie->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Relié"        , $diag);
    
    $str = '';
    $this->loadRefPrescriptionTraitement();
    if ($this->_ref_prescription_traitement->_id) {
      $this->_ref_prescription_traitement->loadRefsLinesMed();
      if ($this->_ref_prescription_traitement->_ref_prescription_lines) {
        foreach ($this->_ref_prescription_traitement->_ref_prescription_lines as $line) {
          $str .= "&bull; {$line->_ref_produit->libelle}<br />";
        }
      }
    }
    
    $str = '';
    $this->loadRefPrescriptionTraitement();
    if ($this->_ref_prescription_traitement->_id) {
      $this->_ref_prescription_traitement->loadRefsLinesMed();
      if ($this->_ref_prescription_traitement->_ref_prescription_lines) {
        foreach ($this->_ref_prescription_traitement->_ref_prescription_lines as $line) {
          $str .= "&bull; {$line->_ref_produit->libelle}<br />";
        }
      }
    }
    $template->addProperty("Sejour - Prescriptions", $str);
    $template->addProperty("Sejour - Remarques", $this->rques);
  }
  
  function fillTemplate(&$template) {
    
    $this->loadRefsFwd();
    
    // Chargement du fillTemplate du praticien
    $this->_ref_praticien->fillTemplate($template);
    
    // Ajout d'un fillTemplate du patient
    $this->_ref_patient->fillTemplate($template);
    
    $this->fillLimitedTemplate($template);
    
    $this->loadRefDossierMedical();
    // Dossier médical
    $this->_ref_dossier_medical->fillTemplate($template, "Sejour");
  }
  
  /**
   * Builds an array containing surgery dates
   */
  function makeDatesOperations() {
    $this->_dates_operations = array();
    
    // On s'assure d'avoir les opérations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }
    
    foreach ($this->_ref_operations as &$operation) {
    	if ($operation->annulee){
    		continue;
    	}
    	
      // On s'assure d'avoir les plages op
      if (!$operation->_ref_plageop) {
        $operation->loadRefPlageOp();
      }

      $this->_dates_operations[$operation->_id] = mbDate($operation->_datetime);
    }
  }
  
  /**
   * Builds san array containing cancel alerts for the sejour
   */
  function makeCancelAlerts() {
	  $this->_cancel_alerts = array(
		  "all" => array(),
		  "acted" => array(),
		);
    
    // On s'assure d'avoir les opérations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }
    
    if ($this->_ref_operations) {
		  foreach ($this->_ref_operations as $_operation ) {
		    $_operation->loadRefPraticien();
		    if ($_operation->annulee == 0) {
		      $operation_view = " le " 
						. mbDateToLocale(mbDate($_operation->_datetime)) 
		        . " par le Dr. " 
						. $_operation->_ref_chir->_view;
		      $_operation->countActes();
		      if ($_operation->_count_actes) {
		        $this->_cancel_alerts["acted"][] = $operation_view;
		      }
		      
		      $this->_cancel_alerts["all"][] = $operation_view;
		    }
		  }
		}
  }
  
}
?>