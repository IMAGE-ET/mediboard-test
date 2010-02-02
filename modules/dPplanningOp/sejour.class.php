<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
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
  
  var $etablissement_transfert_id        = null;
  var $etablissement_entree_transfert_id = null;
  
  // DB Fields
  var $type               = null; // remplace $op->type_adm
  var $modalite           = null;
  var $annule             = null; // complète $op->annule
  var $chambre_seule      = null; // remplace $op->chambre
  var $reanimation        = null; // Entrée en réanimation
  var $zt                 = null; // Entrée en zone de très courte durée
  var $service_id         = null; // Service du séjour

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
  var $adresse_par_prat_id = null;
  var $adresse_par_etab_id = null;
  var $libelle            = null;  
	
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
  var $_adresse_par        = null;
  var $_adresse_par_prat   = null;
  var $_adresse_par_etab   = null;
  var $_etat               = null;
  
  // Behaviour fields
  var $_check_bounds = true;
  var $_en_mutation  = null;
  var $_no_synchro   = null;

  // HPRIM Fields
  var $_hprim_initiateur_group_id  = null; // group initiateur du message HPRIM
    
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
  var $_ref_prescripteurs     = null;
  var $_ref_adresse_par_prat  = null;
  
  // External objects
  var $_ext_diagnostic_principal = null;
  var $_ext_diagnostic_relie     = null;
  var $_ref_hprim_files          = null;
  
  // Distant fields
  var $_dates_operations          = null;
  var $_codes_ccam_operations     = null;
  var $_num_dossier               = null;
  var $_list_constantes_medicales = null;
  var $_cancel_alerts             = null;
  var $_ref_suivi_medical         = null;
  var $_diagnostics_associes      = null;
  
  // Filter Fields
  var $_date_min	 			= null;
  var $_date_max 				= null;
  var $_date_entree     = null;
  var $_date_sortie     = null;
  var $_horodatage      = null;
  var $_admission 			= null;
  var $_service 				= null;
  var $_type_admission  = null;
  var $_specialite 			= null;
  var $_date_min_stat		= null;
  var $_date_max_stat 	= null;
  var $_filter_type 		= null;
  var $_ccam_libelle    = null;
  var $_coordonnees     = null;
  
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
    $spec->measureable = true;
    return $spec;
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["affectations"]         = "CAffectation sejour_id";
	  $backProps["consultations_anesths"] = "CConsultAnesth sejour_id";
	  $backProps["consultations"]        = "CConsultation sejour_id";
	  $backProps["factures"]             = "CFacture sejour_id";
	  $backProps["GHM"]                  = "CGHM sejour_id";
	  $backProps["hprim21_sejours"]      = "CHprim21Sejour sejour_id";
	  $backProps["observations"]         = "CObservationMedicale sejour_id";
	  $backProps["operations"]           = "COperation sejour_id";
	  $backProps["rpu"]                  = "CRPU sejour_id";
	  $backProps["rpu_mute"]             = "CRPU mutation_sejour_id";
	  $backProps["transmissions"]        = "CTransmissionMedicale sejour_id";
    $backProps["dossier_medical"]      = "CDossierMedical object_id";
    $backProps["ghm"]                  = "CGHM sejour_id";
	  return $backProps;
	}

  function getProps() {
   	$specs = parent::getProps();
    $specs["patient_id"]          = "ref notNull class|CPatient seekable";
    $specs["praticien_id"]        = "ref notNull class|CMediusers seekable";
    $specs["group_id"]            = "ref notNull class|CGroups";
    $specs["type"]                = "enum notNull list|comp|ambu|exte|seances|ssr|psy|urg|consult default|ambu";
    $specs["modalite"]            = "enum notNull list|office|libre|tiers default|libre";
    $specs["annule"]              = "bool show|0";
    $specs["chambre_seule"]       = "bool show|0";
    $specs["reanimation"]         = "bool default|0";
    $specs["zt"]                  = "bool default|0";
    $specs["service_id"]          = "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable";
    $specs["entree_prevue"]       = "dateTime notNull show|0";
    $specs["sortie_prevue"]       = "dateTime notNull moreEquals|entree_prevue show|0";
    $specs["entree_reelle"]       = "dateTime show|0";
    $specs["sortie_reelle"]       = "dateTime moreEquals|entree_reelle show|0";
    $specs["saisi_SHS"]           = "bool";
    $specs["modif_SHS"]           = "bool";
    $specs["DP"]                  = "code cim10 show|0";
    $specs["DR"]                  = "code cim10 show|0";
    $specs["pathologie"]          = "str length|3 show|0";
    $specs["septique"]            = "bool show|0";
    $specs["convalescence"]       = "text confidential seekable";
    $specs["rques"]               = "text";
    $specs["ATNC"]                = "bool show|0";
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
    $specs["facturable"]          = "bool notNull default|1 show|0";
    $specs["etablissement_transfert_id"] = "ref class|CEtabExterne";
    $specs["etablissement_entree_transfert_id"] = "ref class|CEtabExterne";
    $specs["adresse_par_prat_id"] = "ref class|CMedecin";
    $specs["adresse_par_etab_id"] = "ref class|CEtabExterne";
    $specs["libelle"]             = "str seekable";
    $specs["facture"]             = "bool default|0";
    
    $specs["_entree"]         = "dateTime show";
    $specs["_sortie"] 		    = "dateTime show";
    $specs["_date_entree"] 		= "date";
    $specs["_date_sortie"] 		= "date";
    $specs["_date_min"] 		  = "dateTime";
    $specs["_date_max"] 		  = "dateTime moreEquals|_date_min";
    $specs["_horodatage"]     = "enum list|entree_prevue|entree_reelle|sortie_prevue|sortie_reelle";
    $specs["_admission"] 		  = "text";
    $specs["_service"] 	      = "text";
    $specs["_type_admission"] = "enum notNull list|comp|ambu|exte|seances|ssr|psy default|ambu";
    $specs["_specialite"]     = "text";
    $specs["_date_min_stat"]  = "date";
    $specs["_date_max_stat"]  = "date moreEquals|_date_min_stat";
    $specs["_filter_type"]    = "enum list|comp|ambu|exte|seances|ssr|psy";
    $specs["_num_dossier"]    = "str";
    $specs["_ccam_libelle"]   = "bool default|0";
    $specs["_coordonnees"]    = "bool default|0";
    $specs["_adresse_par"]    = "bool";
    $specs["_adresse_par_prat"] = "str";
    $specs["_adresse_par_etab"] = "str";
    $specs["_etat"]             = "enum list|preadmission|encours|cloture";
    
    $specs["_duree_prevue"]   = "num";
    $specs["_duree_reelle"]   = "num";
    $specs["_date_entree_prevue"] = "date";
    $specs["_date_sortie_prevue"] = "date moreEquals|_date_entree_prevue";
    $specs["_sortie_autorisee"]   = "bool";
    $specs["_protocole_prescription_anesth_id"] = "ref class|CPrescription";
    $specs["_protocole_prescription_chir_id"]   = "ref class|CPrescription";
        
    return $specs;
  }
  
  function check() {
    $msg    = null;
    $pathos = new CDiscipline();
    
    // Test de la pathologies
    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_specs["categorie"]->_list))) {
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
	           $msg.= "Interventions en dehors des nouvelles dates du séjour";  
	        }	
	    	}
	    }
	    
	    $this->completeField("entree_reelle", "annule");
	    if ((mbAddDateTime(str_pad(CAppUI::conf("dPplanningOp CSejour max_cancel_time"), 2, "0", STR_PAD_LEFT).":00:00", $this->entree_reelle) < mbDateTime()) && $this->fieldModified("annule", "1")) {
	    	$msg .= "Impossible d'annuler un dossier en cours.<br />";
	    }
	
	    foreach ($this->getCollisions() as $collision) {
	      $msg .= "Collision avec le séjour du $collision->entree_prevue au $collision->sortie_prevue.<br />"; 
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
    $this->completeField("annule", "type", "group_id", "patient_id");
    if ($this->annule || $this->type == "urg") {
      return $collisions;
    }
    
    // Test de colision avec un autre sejour
    $patient = new CPatient;
    $patient->load($this->patient_id);
    if (!$patient->_id) {
      return $collisions;
    }
    
    $where["annule"] = " = '0'";
    $where["type"] = " != 'urg'";
    $where["group_id"] = " = '".$this->group_id."'";
    $patient->loadRefsSejours($where);

    // suppression de la liste des sejours le sejour courant
    $sejours = $patient->_ref_sejours;

    foreach ($sejours as $sejour) {
      if ($sejour->_id != $this->_id && $this->collides($sejour)) {
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
    if($this->_id && $sejour->_id && $this->_id == $sejour->_id) {
      return false;
    }
    if($this->annule || $sejour->annule) {
      return false;
    }
    if($this->type == "urg" || $sejour->type == "urg") {
      return false;
    }
    
    if($this->group_id != $sejour->group_id) {
      return false;
    }
		
    $this->updateFormFields();
    return (mbDate($sejour->_entree) <= mbDate($this->_sortie) and mbDate($sejour->_sortie) >= mbDate($this->_sortie))
         or(mbDate($sejour->_entree) <= mbDate($this->_entree) and mbDate($sejour->_sortie) >= mbDate($this->_entree))
         or(mbDate($sejour->_entree) >= mbDate($this->_entree) and mbDate($sejour->_sortie) <= mbDate($this->_sortie));
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
    // Vérification de la validité des codes CIM
    if($this->DP != null) {
      $dp = new CCodeCIM10($this->DP, 1);
      if(!$dp->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DP = "";
      }
    }
    if($this->DR != null) {
      $dr = new CCodeCIM10($this->DR, 1);
      if(!$dr->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        $this->DR = "";
      }
    }
    
    // Annulation de l'établissement de transfert si le mode de sortie n'est pas transfert
    if (null !== $this->mode_sortie) {
      if ("transfert" != $this->mode_sortie) {
        $this->etablissement_transfert_id = "";
      }
    }
    
    // Annulation de la sortie réelle si on annule le mode de sortie
    if($this->mode_sortie === ""){
      $this->sortie_reelle = "";
    }
    // On fait le store du séjour
  	if ($msg = parent::store()) {
      return $msg;
    }

    // Cas d'une annulation de séjour
    if ($this->annule) {
      $this->delAffectations();
      $this->cancelOperations();
    }

    // Synchronisation des affectations
    if(!$this->_no_synchro) {
      $this->loadRefsAffectations();
      $firstAff =& $this->_ref_first_affectation;
      $lastAff =& $this->_ref_last_affectation;
      // Cas où on a une premiere affectation différente de l'heure d'admission
      if($firstAff->_id && ($firstAff->entree != $this->_entree)) {
        $firstAff->entree = $this->_entree;
        $firstAff->_no_synchro = 1;
        $firstAff->store();
      }
      // Cas où on a une dernière affectation différente de l'heure de sortie
      if($lastAff->_id && ($lastAff->sortie != $this->_sortie)) {
        $lastAff->sortie = $this->_sortie;
        $lastAff->_no_synchro = 1;
        $lastAff->store();
      }
      //si le sejour a une sortie ==> compléter le champ effectue de la derniere affectation
      if($lastAff->_id){
        $this->_ref_last_affectation->effectue = $this->sortie_reelle ? 1 : 0;
        $this->_ref_last_affectation->store();
      }
    }
    
  }
  
  function delAffectations() {
    $this->loadRefsAffectations();
		// dPhospi might not be active
		if($this->_ref_affectations) {
	    foreach($this->_ref_affectations as $key => $value) {
	      $this->_ref_affectations[$key]->deleteOne();
	    }
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
    $this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
    
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
      $this->_view      = "Séjour du " . mbTransformTime(null, $this->_entree, CAppUI::conf("date"));
      $this->_shortview = "Du "        . mbTransformTime(null, $this->_entree, CAppUI::conf("date"));
      if(mbTransformTime(null, $this->_entree, CAppUI::conf("date")) != mbTransformTime(null, $this->_sortie, CAppUI::conf("date"))) {
        $this->_view      .= " au " . mbTransformTime(null, $this->_sortie, CAppUI::conf("date"));
        $this->_shortview .= " au " . mbTransformTime(null, $this->_sortie, CAppUI::conf("date"));
      }
    }
    $this->_acte_execution = mbAddDateTime($this->entree_prevue);
    $this->_praticien_id = $this->praticien_id;
        
    $this->_adresse_par = ($this->adresse_par_etab_id || $this->adresse_par_prat_id);
    
    if ($this->_adresse_par) {
    	$medecin_adresse_par = new CMedecin();
	    $medecin_adresse_par->load($this->adresse_par_prat_id);
	    $this->_adresse_par_prat = $medecin_adresse_par->_view;
	    
	    $etab = new CEtabExterne();
	    $etab->load($this->adresse_par_etab_id);
	    $this->_adresse_par_etab = $etab->_view;
    }
    
    // Etat d'un sejour : encours, clôturé ou preadmission
    $this->_etat = "preadmission";
    if ($this->entree_reelle) {
      $this->_etat = "encours";
    }
    if ($this->sortie_reelle) {
      $this->_etat = "cloture";
    }
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
        
		$this->completeField('entree_prevue', 'sortie_prevue', 'entree_reelle', 'sortie_reelle', 'type');
    
    // Signaler l'action de validation de la sortie
    if ($this->_modifier_sortie === '1') {
      $this->sortie_reelle = mbDateTime();
    }
    
    if ($this->_modifier_sortie === '0'){
      $this->sortie_reelle = "";
    }    
    
    // Affectation de la date d'entrée prévue si on a la date d'entrée réelle
    if ($this->entree_reelle && !$this->entree_prevue) {
      $this->entree_prevue = $this->entree_reelle;
    }
    
    // Affectation de la date de sortie prévue si on a la date de sortie réelle
    if ($this->sortie_reelle && !$this->sortie_prevue) {
      $this->sortie_prevue = $this->sortie_reelle;
    }
    
    //@TODO : mieux gérer les current et now dans l'updateDBFields et le store
    $entree_reelle = ($this->entree_reelle === 'current'|| $this->entree_reelle ===  'now') ? mbDateTime() : $this->entree_reelle;
    if($entree_reelle && ($this->sortie_prevue < $entree_reelle)) {
      $this->sortie_prevue = $this->type == "comp" ? mbDateTime("+1 DAY", $entree_reelle) : $entree_reelle;
    }
    
		// Synchro durée d'hospi / type d'hospi
    $this->_at_midnight = (mbDate(null, $this->entree_prevue) != mbDate(null, $this->sortie_prevue));
    if($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    } elseif(!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
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
  
  function loadRefEtabExterne($cache = 0){
    $this->_ref_etabExterne = new CEtabExterne();
    if($cache) {
      $this->_ref_etabExterne = $this->_ref_etabExterne->getCached($this->etablissement_transfert_id);
    } else {
      $this->_ref_etabExterne->load($this->etablissement_transfert_id);
    }
  }
  
  function countNotificationVisite($date = ''){
    $this->completeField("praticien_id");
    if(!$date){
      $date = mbDate();
    }
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->_id'";
    $where["user_id"]  = " = '$this->praticien_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    return $observation->countList($where);
  }
  
  function loadRefPatient($cache = 0) {
    if (!$this->_ref_patient) {
      $this->_ref_patient = new CPatient();
      $this->_ref_patient->load($this->patient_id);
    }
    
    $this->getDroitsCMU();

    // View
    if (substr($this->_view, 0, 9) == "Séjour du") {
      $this->_view = $this->_ref_patient->_view . " - " . $this->_view;
    }
  }
  
  function loadRefPraticien($cache = 0) {
    $this->_ref_praticien = $this->loadFwdRef("praticien_id", $cache);
    $this->_ref_praticien->loadRefFunction();
  }
  
  function loadExtDiagnostics() {
    $this->_ext_diagnostic_principal = $this->DP ? new CCodeCIM10($this->DP, 1) : null;
    $this->_ext_diagnostic_relie     = $this->DR ? new CCodeCIM10($this->DR, 1) : null;
  }
  
  function loadDiagnosticsAssocies($split = true) {
    $this->_diagnostics_associes = array();
    if ($this->_ref_dossier_medical->_id){
      foreach($this->_ref_dossier_medical->_codes_cim as $code) {
        if ($split && strlen($code) >= 4) {
          $this->_diagnostics_associes[] = substr($code, 0, 3).".".substr($code, 3);
        } else {
          $this->_diagnostics_associes[] = $code;
        }
      }
    }
    
    return $this->_diagnostics_associes;
  }
  
  function loadRefPrestation($cache = 0) {
    $this->_ref_prestation = new CPrestation;
    if($cache) {
      $this->_ref_prestation = $this->_ref_prestation->getCached($this->prestation_id);
    } else {
      $this->_ref_prestation->load($this->prestation_id);
    }
  }
  
  function loadRefsTransmissions(){
    $this->_ref_transmissions = $this->loadBackRefs("transmissions");	
  }
  
  function loadSuiviMedical() {
    $this->loadBackRefs("observations");
    $this->loadBackRefs("transmissions");
    
    $this->_ref_suivi_medical = array();

    if(isset($this->_back["observations"])){
	    foreach($this->_back["observations"] as $curr_obs) {
	      $curr_obs->loadRefsFwd();
	      $curr_obs->_ref_user->loadRefFunction();
	      $this->_ref_suivi_medical[$curr_obs->date.$curr_obs->_id."obs"] = $curr_obs;
	    }
    }
    if(isset($this->_back["transmissions"])){
    	foreach($this->_back["transmissions"] as $curr_trans) {
	      $curr_trans->loadRefsFwd();    
	      if($curr_trans->_ref_object instanceof CAdministration){
	        $curr_trans->_ref_object->loadRefsFwd();
	        if($curr_trans->_ref_object->_ref_object instanceof CPrescriptionLineMedicament){
	          $curr_trans->_ref_object->_ref_object->_ref_produit->loadClasseATC();
	        }
	      }
	      if($curr_trans->_ref_object instanceof CPrescriptionLineMedicament){
	        $curr_trans->_ref_object->_ref_produit->loadClasseATC();
	      }
	      $this->_ref_suivi_medical[$curr_trans->date.$curr_trans->_id."trans"] = $curr_trans;
	    }
    }
    krsort($this->_ref_suivi_medical);
  }
  
  function loadRefEtablissement($cache = 0) {
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    if($cache) {
      $this->_ref_group = $this->_ref_group->getCached($this->group_id);
    } else {
      $this->_ref_group->load($this->group_id);
    }
  }
  
  function loadRefRPU() {
    $this->_ref_rpu = $this->loadUniqueBackRef("rpu");
  }
  
  function loadRefAdresseParPraticien() {
    $this->_ref_adresse_par_prat = new CMedecin();
    $this->_ref_adresse_par_prat->load($this->adresse_par_prat_id);
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
    if ($this->type == "urg" && !$this->_ref_consult_atu->_id) {
      foreach ($this->_ref_consultations as $_consult) {
        $_consult->loadRefPraticien();      
        $praticien = $_consult->_ref_praticien;
        $praticien->loadRefFunction();
        if ($praticien->isUrgentiste()) {
          $this->_ref_consult_atu = $_consult;
          $this->_ref_consult_atu->countDocItems();
        }
      }
    }
  }
  
  /*
   * Chargement de toutes les prescriptions liées au sejour (object_class CSejour)
   */
  function loadRefsPrescriptions() {
  	$prescriptions = $this->loadBackRefs("prescriptions");
  	// Si $prescriptions n'est pas un tableau, module non installé
    if(!is_array($prescriptions)){
    	$this->_ref_last_prescription = null;
    	return;
    }
    $this->_count_prescriptions = count($prescriptions);
  	$this->_ref_prescriptions["pre_admission"] = new CPrescription();
  	$this->_ref_prescriptions["sejour"] = new CPrescription();
  	$this->_ref_prescriptions["sortie"] = new CPrescription();
  	
  	// Stockage des prescriptions par type
  	foreach($prescriptions as $_prescription){
	    $this->_ref_prescriptions[$_prescription->type] = $_prescription;
  	}
  }
  
  function loadRefsPrescripteurs(){
    $prescription_sejour = new CPrescription();
    $this->loadRefsPrescriptions();
    foreach($this->_ref_prescriptions as $_prescription){
      $_prescription->getPraticiens();
      if(is_array($_prescription->_praticiens)){
	      foreach($_prescription->_praticiens as $_praticien_id => $_praticien_view){
          if(!is_array($this->_ref_prescripteurs) || !array_key_exists($_praticien_id, $this->_ref_prescripteurs)){
            $praticien = new CMediusers(); 
	          $this->_ref_prescripteurs[$_praticien_id] = $praticien->load($_praticien_id);
	        } 
	      }
      }
    }
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
  
  function loadRefsFwd($cache = 0) {
    $this->loadRefPatient($cache);
    $this->loadRefPraticien($cache);
    $this->loadRefEtablissement($cache);
    $this->loadRefEtabExterne($cache);
    $this->loadExtCodesCCAM();
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
    
    // Chargement de la consultation anesth pour l'affichage de la fiche d'anesthesie
    $this->loadRefsConsultAnesth();
    $this->_ref_consult_anesth->loadRefConsultation();
    
    $this->loadSuiviMedical();
    $this->_ref_patient->loadRefPhotoIdentite();
  }


/**
   * Charge le sejour ayant les traits suivants :
   * - Meme patient
   * - Meme praticien si praticien connu
   * - Date de d'entree et de sortie équivalentes
   * @return Nombre d'occurences trouvées 
   */
  function loadMatchingSejour($strict = null) {
  	if ($strict && $this->_id) {
      $where["sejour_id"] = " != '$this->_id'";
    } 
		$where["patient_id"] = " = '$this->patient_id'";
		
		$this->_entree = CValue::first($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = CValue::first($this->sortie_reelle, $this->sortie_prevue);
		
		if ($this->_entree){
			$date_entree = mbDate($this->_entree); 
		  $where[] = "DATE(entree_prevue) = '$date_entree' OR DATE(entree_reelle) = '$date_entree'";
    }
    if ($this->_sortie){
      $date_sortie = mbDate($this->_sortie); 
      $where[] = "DATE(sortie_prevue) = '$date_sortie' OR DATE(sortie_reelle) = '$date_sortie'";
    }
		
    $this->loadObject($where);
    return $this->countList($where);
  }
	
	
  function loadNumDossier() {
    // Aucune configuration de numéro de dossier
    if (null == $tag_dossier = CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      $this->_num_dossier = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return;
    }  
    
    // Objet inexistant
    if (!$this->_id) {
      return "-";
    }
    
    // sinon, $_num_dossier = valeur id400
    // creation du tag de l'id Externe
    global $g;
    $tag = str_replace('$g',$g, $tag_dossier);

    // Recuperation de la valeur de l'id400
    $id400 = new CIdSante400();
    $id400->loadLatestFor($this, $tag);
    
    // Stockage de la valeur de l'id400
    $this->_ref_numdos  = $id400;
    $this->_num_dossier = $id400->id400;
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
    
    // Agrégats des codes CCAM des opérations
    $this->_codes_ccam_operations = CMbArray::pluck($this->_ref_operations, "codes_ccam");
    CMbArray::removeValue("", $this->_codes_ccam_operations);
    $this->_codes_ccam_operations = implode("|", $this->_codes_ccam_operations);
    
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
    $this->_ref_GHM = $this->loadUniqueBackRef("ghm");
    if (!$this->_ref_GHM->_id) {
      $this->_ref_GHM->sejour_id = $this->sejour_id;
    }
    $this->_ref_GHM->_ref_sejour = $this;
    $this->_ref_GHM->bindInfos();
    $this->_ref_GHM->getGHM();
  }
  
  function loadHprimFiles() {
    $hprimFile = new CHPrimXMLEvenementPmsi();
    $hprimFile->setFinalPrefix($this);
    $hprimFile->getSentFiles();
    $this->_ref_hprim_files = $hprimFile->sentFiles;
  }
  
  function fillLimitedTemplate(&$template) {
    
    $template->addDateProperty("Admission - Date"             , $this->entree_prevue);
    $template->addTimeProperty("Admission - Heure"            , $this->entree_prevue);
    $template->addProperty("Hospitalisation - Durée"          , $this->_duree_prevue);
    $template->addDateProperty("Hospitalisation - Date sortie", $this->sortie_prevue);
    
		$this->loadNumDossier();
		$template->addProperty("Sejour - Numéro de dossier"       , $this->_num_dossier );
		
    $this->loadRefPraticien();
    $template->addProperty("Hospitalisation - Praticien"    , "Dr ".$this->_ref_praticien->_view);
    
    // Diagnostics
    $this->loadExtDiagnostics();
    $diag = $this->DP ? "$this->DP: {$this->_ext_diagnostic_principal->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Principal"    , $diag);
    $diag = $this->DR ? "$this->DR: {$this->_ext_diagnostic_relie->libelle}" : null;
    $template->addProperty("Sejour - Diagnostic Relié"        , $diag);
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
    
    if(CModule::getActive('dPprescription')){
	    // Chargement du fillTemplate de la prescription
	    $this->loadRefsPrescriptions();
	    $prescription = isset($this->_ref_prescriptions["sejour"]) ? $this->_ref_prescriptions["sejour"] : new CPrescription();
	    $prescription->type = "sejour";
	    $prescription->fillLimitedTemplate($template);
    }
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
   * Builds an array containing cancel alerts for the sejour
   * @param ref|COperation excluded_id Exclude given operation
   * @return void Valuate $this->_cancel_alert
   */
  function makeCancelAlerts($excluded_id = null) {
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
		    // Needed for correct view
		    $_operation->loadRefPraticien();
		    $_operation->loadRefPlageOp();
		    
		    // Exclude one
		    if ($_operation->_id == $excluded_id) {
		      continue;
		    }
		    
		    if ($_operation->annulee == 0) {
		      $operation_view = " le " 
						. mbDateToLocale(mbDate($_operation->_datetime)) 
		        . " par le Dr " 
						. $_operation->_ref_chir->_view;
		      $_operation->countActes();
		      if ($_operation->_count_actes) {
		        $this->_cancel_alerts["acted"][$_operation->_id] = $operation_view;
		      }
		      
		      $this->_cancel_alerts["all"][$_operation->_id] = $operation_view;
		    }
		  }
		}
  }
}
?>