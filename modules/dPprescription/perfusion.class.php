<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  var $commentaire      = null;
  
  var $date_pose   = null; // Date de la pose de la perf
  var $time_pose   = null; // Heure de la pose de la perf
  
  var $date_retrait     = null; // Date de retrait de la perf
  var $time_retrait     = null; // Heure de retrait de la perf
  var $emplacement      = null;
  var $nb_tous_les      = null;
  
  // Champs specifiques aux PCA
  var $mode_bolus          = null; // Mode de bolus 
  var $dose_bolus          = null; // Dose du bolus (en mg)
  var $periode_interdite   = null; // Periode interdite en minutes
  //var $dose_limite_horaire = null; // Dose maxi pour une heure
  
  var $substitute_for_id    = null;
  var $substitute_for_class = null;
  var $substitution_active = null;
  var $substitution_plan_soin = null;
  
	var $conditionnel = null;
	var $condition_active = null;
	
	// Fwd Refs
  var $_ref_prescription = null;
  var $_ref_praticien    = null;
  
  // Back Refs
  var $_ref_lines        = null;
  
  // Form fields
  var $_debut             = null; // Debut de la perfusion (dateTime)
  var $_fin               = null; // Fin de la perfusion (dateTime)
  var $_protocole         = null; // Perfusion de protocole
  var $_add_perf_contigue = null;
  var $_count_parent_line = null;
  var $_recent_modification = null;
  var $_count_substitution_lines = null;

  var $_pose = null;
  var $_retrait   = null;
  var $_voies = null;
  var $_active = null;
	  
  // Object references
  var $_ref_log_signature_prat = null;
  var $_ref_substitute_for = null; // ligne (med ou perf) que la ligne peut substituer


  var $_short_view = null;

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
  
  var $_quantite_totale                  = null;
  var $_prises_prevues                   = null;

  var $_frequence = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perfusion';
    $spec->key   = 'perfusion_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
  	$specs["prescription_id"]        = "ref class|CPrescription cascade";
  	$specs["type"]                   = "enum notNull list|classique|seringue|PCA";
    $specs["libelle"]                = "str";
    $specs["vitesse"]                = "num pos";
    $specs["voie"]                   = "str";
    $specs["date_debut"]             = "date";
    $specs["time_debut"]             = "time";
    $specs["duree"]                  = "num pos";
    $specs["next_perf_id"]           = "ref class|CPerfusion"; 
    $specs["praticien_id"]           = "ref class|CMediusers";
    $specs["creator_id"]             = "ref class|CMediusers";
    $specs["signature_prat"]         = "bool default|0";
    $specs["signature_pharma"]       = "bool default|0";
    $specs["validation_infir"]       = "bool default|0";
    $specs["date_arret"]             = "date";
    $specs["time_arret"]             = "time";
    $specs["accord_praticien"]       = "bool";
    $specs["_debut"]                 = "dateTime";
    $specs["_fin"]                   = "dateTime";
    $specs["decalage_interv"]        = "num";
    $specs["operation_id"]           = "ref class|COperation";
    $specs["mode_bolus"]             = "enum list|sans_bolus|bolus|perfusion_bolus default|sans_bolus";
    $specs["dose_bolus"]             = "float";
    $specs["periode_interdite"]      = "num pos";
    $specs["date_pose"]              = "date";
    $specs["time_pose"]              = "time";
    $specs["date_retrait"]           = "date";
    $specs["time_retrait"]           = "time";
    $specs["emplacement"]            = "enum notNull list|service|bloc|service_bloc default|service";
    $specs["substitute_for_id"]      = "ref class|CMbObject meta|substitute_for_class cascade";
    $specs["substitute_for_class"]   = "enum list|CPrescriptionLineMedicament|CPerfusion default|CPerfusion";
    $specs["substitution_active"]    = "bool";
    $specs["substitution_plan_soin"] = "bool";
    $specs["nb_tous_les"]            = "num";
    $specs["commentaire"]            = "str helped";
		$specs["conditionnel"]           = "bool";
    $specs["condition_active"]       = "bool";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    
    // Calcul de la view
    $this->_view = ($this->libelle) ? "$this->libelle " : "";
    $this->_view .= ($this->type) ? " ".CAppUI::tr("CPerfusion.type.$this->type").", " : "";
    $this->_view .= $this->voie;
    $this->_view .= ($this->vitesse) ? " à $this->vitesse ml/h" : "";
    
    if($this->vitesse){
      $this->_frequence = "à $this->vitesse ml/h";
    }
    if($this->nb_tous_les){
      $this->_frequence = "toutes les $this->nb_tous_les h";
    }
    
    if($this->type == "PCA"){
      $this->_view .= ($this->mode_bolus) ? ", mode PCA: ".CAppUI::tr("CPerfusion.mode_bolus.".$this->mode_bolus) : "";
      $this->_view .= ($this->dose_bolus) ? ", bolus de $this->dose_bolus mg" : "";
      $this->_view .= ($this->periode_interdite) ? ", période interdite de $this->periode_interdite min" : "";
    }
        
    if($this->date_pose && $this->time_pose){
      $this->_pose = "$this->date_pose $this->time_pose";
    }
    if($this->date_retrait && $this->time_retrait){
      $this->_retrait = "$this->date_retrait $this->time_retrait";
    }
    
    // Calcul du debut de la perfusion
    $this->_debut = ($this->date_pose) ? "$this->date_pose $this->time_pose" : "$this->date_debut $this->time_debut";

    // Calcul de la fin de la perfusion
    $this->_date_fin = $this->duree ? mbDateTime("+ $this->duree HOURS", "$this->_debut") : $this->_debut;
    $this->_fin = ($this->date_arret && $this->time_arret) ? "$this->date_arret $this->time_arret" 
                                                           : ($this->date_retrait ? "$this->date_retrait $this->time_retrait" : $this->_date_fin); 

    $this->loadRefPrescription();
    $this->_protocole = $this->_ref_prescription->object_id ? '0' : '1';
    $this->getRecentModification();    
    
    if($this->_protocole){
      $this->countSubstitutionsLines();
    }
		$this->_active = (!$this->conditionnel) ? 1 : $this->condition_active;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lines_perfusion"]  = "CPerfusionLine perfusion_id";
    $backProps["prev_line"]     = "CPerfusion next_perf_id";
    $backProps["transmissions"] = "CTransmissionMedicale object_id";
    $backProps["substitutions_medicament"] = "CPrescriptionLineMedicament substitute_for_id";
    $backProps["substitutions_perfusion"]  = "CPerfusion substitute_for_id";
    return $backProps;
  }
  
  function getAdvancedPerms($is_praticien = 0, $mode_protocole = 0, $mode_pharma = 0, $operation_id = 0) {
		global $AppUI, $can;

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
                   ($this->praticien_id == $AppUI->user_id || $is_praticien || $operation_id || $mode_pharma));
    }
    $this->_perm_edit = $perm_edit;
    
    // Modification de la perfusion et des lignes des perfusions
    if($perm_edit){
    	$this->_can_modify_perfusion = 1;
    	$this->_can_modify_perfusion_line = 1;
    }
    if($this->signature_prat){
      $this->_can_vw_form_add_perf_contigue = 1;
  		$this->_can_vw_form_stop_perf = 1;
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
	}
  
	function getRecentModification(){
		// modification recente si moins de 2 heures
    $nb_hours = CAppUI::conf("dPprescription CPrescription time_alerte_modification");
    $min_datetime = mbDateTime("- $nb_hours HOURS");
    $last_modif_date = $this->loadLastLogForField()->date;
    
    if($last_modif_date && $last_modif_date >= $min_datetime){
      $this->_recent_modification = true;
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
   * Chargement des lignes de substitution possibles
   */
  function loadRefsSubstitutionLines(){
    if(!$this->substitute_for_id){
		  $this->_ref_substitution_lines["CPrescriptionLineMedicament"] = $this->loadBackRefs("substitutions_medicament"); 
      $this->_ref_substitution_lines["CPerfusion"] = $this->loadBackRefs("substitutions_perfusion");  
      $this->_ref_substitute_for = $this;
    } else {
	    $_base_line = new $this->substitute_for_class;
		  $_base_line->load($this->substitute_for_id);
		  $_base_line->loadRefsSubstitutionLines();
	    $this->_ref_substitution_lines = $_base_line->_ref_substitution_lines;
	    $this->_ref_substitution_lines[$_base_line->_class_name][$_base_line->_id] = $_base_line;
			unset($this->_ref_substitution_lines[$this->_class_name][$this->_id]);		
		  $this->_ref_substitute_for = $_base_line;			  
	  }
		foreach($this->_ref_substitution_lines["CPerfusion"] as $_substitution_line){
      $_substitution_line->loadRefsLines();
    }
  }
  
  /*
   * Permet de connaitre le nombre de lignes de substitutions possibles
   */
  function countSubstitutionsLines(){
    if(!$this->substitute_for_id){
      $this->_count_substitution_lines = $this->countBackRefs("substitutions_medicament") + $this->countBackRefs("substitutions_perfusion");
    } else {
      $object = new $this->substitute_for_class;
      $object->load($this->substitute_for_id);
      $object->countSubstitutionsLines();    
      $this->_count_substitution_lines = $object->_count_substitution_lines;
    }
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
		if(!$this->_short_view){
		  foreach($this->_ref_lines as $_perf_line){
		  	$this->_short_view .= $_perf_line->_ref_produit->libelle_abrege.", ";
		  }
			$this->_short_view .= "($this->voie - ".CAppUI::tr('CPerfusion.type.'.$this->type).")";
		}
	}
  
  /*
   * Chargement des differentes voies disponibles pour la perfusion
   */
  function loadVoies(){
    foreach($this->_ref_lines as $_perf_line){
    	if(!$_perf_line->_ref_produit->voies){
			  $_perf_line->_ref_produit->loadVoies();
			}
			
			$_perf_line->loadRefProduitPrescription();
			if($_perf_line->_ref_produit_prescription->_id){
			  $this->_voies[$_perf_line->_ref_produit_prescription->voie] = $_perf_line->_ref_produit_prescription->voie;
      }
			if($_perf_line->_ref_produit->voies){
	      foreach($_perf_line->_ref_produit->voies as $_voie){
	        //if(CPrescriptionLineMedicament::$voies[$_voie]["perfusable"]){
	          $this->_voies[$_voie] = $_voie;
	        //}
	      }
		  }
			
    }
  }
  
  
  /*
   *  Calcul de la quantite totale de la perf en ml
   */
  function calculQuantiteTotal(){
    if(!$this->_ref_lines){
      $this->loadRefsLines();
    }
    foreach($this->_ref_lines as $_perf_line){
      if($_perf_line->unite && $_perf_line->quantite){
	      $_unite_prise = str_replace('/kg', '', $_perf_line->unite);
			  // Si l'unite de prise est en fonction du poids du patient
	      if($_unite_prise != $_perf_line->unite){
	        $this->loadRefPrescription();
	        $this->_ref_prescription->loadRefObject();
	        $this->_ref_prescription->_ref_object->loadRefPatient();
			    $patient =& $this->_ref_prescription->_ref_object->_ref_patient;
	        if(!$patient->_ref_constantes_medicales){
	          $patient->loadRefConstantesMedicales();
	        }
	        $poids = $patient->_ref_constantes_medicales->poids;
			  }

			  // Chargement du tableau de correspondance entre les unites de prises
	      $_perf_line->_ref_produit->loadRapportUnitePriseByCIS();
	      $coef = @$_perf_line->_ref_produit->rapport_unite_prise[$_unite_prise]["ml"];
	      if(!$coef){
	        $coef = 1;
	      }
	      $_perf_line->_quantite_administration = $_perf_line->quantite * $coef;
	      if(isset($poids)){
	        $_perf_line->_quantite_administration *= $poids;
	      }
	      if(isset($_perf_line->_ref_produit->rapport_unite_prise[$_unite_prise]["ml"])){
			    $this->_quantite_totale += $_perf_line->_quantite_administration;
	      }
			  $produit =& $_perf_line->_ref_produit;   
				if(!$produit->libelle_unite_presentation){
			    $produit->loadLibellePresentation();
          $produit->loadUnitePresentation();
				}
    	  $_perf_line->_unite_administration = $produit->_unite_administration = $produit->libelle_unite_presentation;
		    $_perf_line->_unite_dispensation = $produit->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;

		    // Calcul du ration entre quantite d'administration et quantite de dispensation
		    if($_perf_line->_unite_dispensation == $produit->libelle_unite_presentation){
		      $_perf_line->_ratio_administration_dispensation = 1;
		    } else {
		      $_perf_line->_ratio_administration_dispensation = 1 / $produit->nb_unite_presentation;
		    }
		    $_perf_line->_quantite_dispensation = $_perf_line->_quantite_administration * $_perf_line->_ratio_administration_dispensation; 
      }
    }
  }
  
  /*
   * Calcul des prises prevues pour la perfusion
   */
  function calculPrisesPrevues(){    
    // Test des infos essentielles au calcul
    if($this->date_debut && $this->time_debut && $this->duree){
      // Premiere prise lors du debut de la ligne
      //$date_time_temp = "$this->date_debut $this->time_debut";
      $date_time_temp = $this->_debut;
      $date = mbDate($date_time_temp);
			$hour = mbTransformTime(null, $date_time_temp, "%H");
			    
      $this->_prises_prevues[$date][$hour]["real_hour"] = mbTime($date_time_temp);
      $this->_prises_prevues[$date][$hour]["plan_hour"] = "$hour:00:00";
    
      
      // Perfusion à la vitesse de x ml/h
      if($this->vitesse && $this->_quantite_totale){
        // calcul du nombre d'heure entre le renouvellement de la perf
        $nb_hours = $this->_quantite_totale / $this->vitesse;
        
        // Calcul de l'incrementation
        $explode_hour = explode(".", $nb_hours);
        $nb_hours = $explode_hour[0]; 
        if(isset($explode_hour[1])){
          $minutes = substr($explode_hour[1],0,1) * 6;
        }
        $increment = "+ $nb_hours hours ";
        if(isset($minutes)){
          $increment .= "$minutes minutes";
        }
        
        // Calcul des prises en fonction de la vitesse
			  while((mbDateTime($increment, $date_time_temp)) < $this->_fin){
	        $date_time_temp = mbDateTime($increment, $date_time_temp);
	        
			    $date = mbDate($date_time_temp);
			    $hour = mbTransformTime(null, $date_time_temp, "%H");

			    $this->_prises_prevues[$date][$hour]["real_hour"] = mbTime($date_time_temp);
	        $this->_prises_prevues[$date][$hour]["plan_hour"] = "$hour:00:00";
	      } 
      }
  
      // Perfusion toutes les x heures
      if($this->nb_tous_les){
        // Calcul des prises en fonction de la vitesse
			  while((mbDateTime("+ $this->nb_tous_les hours", $date_time_temp)) < $this->_fin){
	        $date_time_temp = mbDateTime("+ $this->nb_tous_les hours", $date_time_temp);
	        
			    $date = mbDate($date_time_temp);
			    $hour = mbTransformTime(null, $date_time_temp, "%H");
			    
          $this->_prises_prevues[$date][$hour]["real_hour"] = mbTime($date_time_temp);
	        $this->_prises_prevues[$date][$hour]["plan_hour"] = "$hour:00:00";
	      }
      }
    }
  }
  
  /*
   * Calcul des administrations
   */
  function calculAdministrations(){
    foreach($this->_ref_lines as $_perf_line){
      $_perf_line->loadRefsAdministrations();
      foreach($_perf_line->_ref_administrations as $_administration){
        $date = mbDate($_administration->dateTime);
			  $hour = mbTransformTime(null, $_administration->dateTime, "%H");
			    
        if(!isset($_perf_line->_administrations[$date][$hour])){
          $_perf_line->_administrations[$date][$hour] = 0;
        }
        $_perf_line->_administrations[$date][$hour] += $_administration->quantite;
      }
    }
  }
  
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien = $this->_ref_praticien->getCached($this->praticien_id);
    $this->_ref_praticien->loadRefFunction();
  }
  
  /*
   * Calcul permettant de savoir si la ligne possède un historique
   */
  function countParentLine(){
    $perfusion = new CPerfusion();
    $perfusion->next_perf_id = $this->_id;
    $this->_count_parent_line = $perfusion->countMatchingList(); 
  }
  
  /*
   * Chargement du log de signature du praticien
   */
  function loadRefLogSignaturePrat(){
    $this->_ref_log_signature_prat = $this->loadLastLogForField("signature_prat");
  }
}
  
?>