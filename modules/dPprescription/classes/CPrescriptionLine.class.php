<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("dPpatients", "IPatientRelated");

class CPrescriptionLine extends CMbObject implements IPatientRelated {
  
  // DB Fields
  var $prescription_id     = null;
  var $ald                 = null;
  var $praticien_id        = null;
  var $signee              = null;
  var $creator_id          = null;
  var $debut               = null;  // Date de debut
  var $time_debut          = null;  // Heure de debut
  var $duree               = null;  // Duree de la ligne
  var $unite_duree         = null;
  var $date_arret          = null;  // Date d'arret
  var $time_arret          = null;  // Heure d'arret
  var $child_id            = null;
  var $decalage_line       = null;  // Permet de definir le decalage de la ligne par rapport au jour de decalage specifié
  var $jour_decalage       = null;  // Jour de decalage: I/E/S/N
  var $valide_infirmiere   = null;
  var $fin                 = null;              
  var $jour_decalage_fin   = null;  // Jour de fin: I/S
  var $decalage_line_fin   = null;  // Decalage de la ligne
  var $time_fin            = null;  // Heure de fin de la ligne de prescription
  var $conditionnel        = null;
  var $condition_active    = null;
  var $unite_decalage      = null;
  var $unite_decalage_fin  = null;
  var $operation_id        = null;
  var $emplacement         = null;
  
  var $perop               = null;
	var $inscription         = null;
	var $protocole_id        = null;
	var $recusee             = null;
	
  // Form Fields
  var $_fin                = null;
  var $_protocole          = null;
  var $_count_parent_line  = null;
  var $_count_prises_line  = null;  
  var $_fin_reelle         = null;
  var $_debut_reel         = null;
  var $_active             = null;
  var $_recent_modification = false;
  
  // Object References
  var $_ref_prescription   = null;
  var $_ref_praticien      = null;
  var $_ref_creator        = null;
  
  var $_ref_parent_line    = null;
  var $_ref_child_line     = null;
  var $_ref_log_signee     = null;
  var $_ref_log_date_arret = null;
  var $_ref_prises         = null;
  var $_ref_administrations = null;
  var $_ref_transmissions   = null;
  var $_ref_alerte = null;
	
  // Dossier/Feuille de soin
  var $_administrations = null;          // Administrations d'une ligne stockées par date, heure, type de prise
  var $_administrations_by_line = null; // Administrations d'une ligne stockées par date
  var $_transmissions   = null;
  var $_quantity_by_date = null;
  var $_prises_for_plan   = null;

  var $_nb_prises_interv = null; // Nombre de prises qui dependent de l'intervention
  var $_update_planif_systeme = null; // Permet de forcer le calcul des planifs systemes pour une ligne
  var $_count_locked_planif = null;
	var $_planifs_systeme = array();
	var $_urgence = null;
  var $_duree   = null;


  // Can fields
  var $_perm_edit = null;
  var $_dates_urgences = null;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["prescription_id"]   = "ref notNull class|CPrescription cascade";
    $specs["ald"]               = "bool";
    $specs["praticien_id"]      = "ref class|CMediusers";
    $specs["creator_id"]        = "ref notNull class|CMediusers";
    $specs["signee"]            = "bool";
    $specs["debut"]             = "date";
    $specs["time_debut"]        = "time";
    $specs["duree"]             = "num min|0";
    $specs["unite_duree"]       = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour";
    $specs["date_arret"]        = "date";
    $specs["time_arret"]        = "time";
    $specs["child_id"]          = "ref class|$this->_class unlink show|0";
    $specs["decalage_line"]     = "num show|0";
    $specs["jour_decalage"]     = "enum list|E|I|S|N default|E show|0";
    $specs["fin"]               = "date";
    $specs["valide_infirmiere"] = "bool";
    $specs["jour_decalage_fin"] = "enum list|I|S show|0";
    $specs["decalage_line_fin"] = "num show|0";
    $specs["time_fin"]          = "time show|0";
    $specs["conditionnel"]      = "bool";
    $specs["condition_active"]  = "bool";
    $specs["unite_decalage"]    = "enum list|jour|heure default|jour show|0";
    $specs["unite_decalage_fin"]= "enum list|jour|heure default|jour show|0";
    $specs["emplacement"]       = "enum notNull list|service|bloc|service_bloc default|service";
    $specs["operation_id"]      = "ref class|COperation";
    $specs["perop"]             = "bool default|0"; 
    $specs["inscription"]       = "bool default|0";
    $specs["protocole_id"]      = "ref class|CPrescription show|0";
    $specs["recusee"]           = "bool default|0";
    $specs["_fin"]              = "date moreEquals|debut";
    $specs["_fin_reelle"]       = "date";
    $specs["_duree"]                 = "num min|0";

    return $specs;
  }
  
  function loadRelPatient(){
    return $this->loadRefPrescription()->loadRefPatient();
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->countParentLine();
    $this->countPrisesLine();
    $this->loadRefPrescription();
    $this->loadRefPraticien();
    $this->_protocole = ($this->_ref_prescription->object_id) ? "0" : "1";
    
		
		$this->_active = (!$this->conditionnel) ? 1 : $this->condition_active;
    $this->getRecentModification();
    $this->calculDatesUrgences();
		
		$this->_fin = "";
		
    if($this->duree && $this->debut){
      switch ($this->unite_duree) {
        case "minute":
        case "heure":
        case "demi_journee":
          $this->_fin = $this->debut; break;
        case "jour": 
          $this->_fin = mbDate("+ $this->duree DAYS", $this->debut); break;
        case "semaine":
          $this->_fin = mbDate("+ $this->duree WEEKS", $this->debut); break;
        case "quinzaine":
          $duree_temp = 2 * $this->duree;
          $this->_fin = mbDate("+ $duree_temp WEEKS", $this->debut); break;
        case "mois":
          $this->_fin = mbDate("+ $this->duree MONTHS", $this->debut); break;
        case "trimestre":
          $duree_temp = 3 * $this->duree;
          $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut); break;
        case "semestre":
          $duree_temp = 6 * $this->duree;
          $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut); break;
        case "an":
          $this->_fin = mbDate("+ $this->duree YEARS", $this->debut); break;
      }
      
      // Si la duree est superieure à une journée, on transforme la date de fin
      if($this->debut != $this->_fin){
        $this->_fin = mbDate(" -1 DAYS", $this->_fin);  
      }
    }
    
    // Calcul du debut reel de la ligne
    $time_debut = ($this->time_debut) ? $this->time_debut : "00:00:00";
    $this->_debut_reel = "$this->debut $time_debut";
		
		// Calcul de la fin reelle de la ligne
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
  
    // Si l'unite de duree est l'heure
    if($this->unite_duree == "heure" || $this->unite_duree == "minute"){
      $_unite = ($this->unite_duree == "heure") ? "HOURS" : "MINUTES";
      $this->_fin_reelle = mbDateTime("+ $this->duree $_unite", $this->_debut_reel);
    } else {
      $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "";      
		}
  }
  
  /*
   * Forward Refs
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefPrescription();
    $this->loadRefPraticien();
    $this->loadRefCreator();
    $this->loadRefChildLine();
  }
  
  /*
   * BackRefs
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsPrises();
    $this->loadRefParentLine();
  }
  
  /**
   * Chargement de la prescription
   * 
   * @return CPrescription
   */
  function loadRefPrescription() {
    if (!$this->_ref_prescription) {
      $this->_ref_prescription = new CPrescription();
      $this->_ref_prescription = $this->_ref_prescription->getCached($this->prescription_id);
    }
		
		return $this->_ref_prescription;
  }
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien() {
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien = $this->_ref_praticien->getCached($this->praticien_id);
    $this->_ref_praticien->loadRefFunction();
  }
  
  /*
   * Chargement du createur de la ligne
   */
  function loadRefCreator() {
    $user = new CMediusers();
    $this->_ref_creator = $user->getCached($this->creator_id);
  }
  
  /*
   * Chargement du log de signature de la ligne
   */
  function loadRefLogSignee(){
    $this->_ref_log_signee = $this->loadLastLogForField("signee");
  }
  
  /*
   * Chargement de la ligne suivante
   */
  function loadRefChildLine(){
    $this->_ref_child_line = new $this->_class;
    if($this->child_id){
      $this->_ref_child_line->_id = $this->child_id;
      $this->_ref_child_line->loadMatchingObject();
    }  
  }
  
  function loadRefsTransmissions(){
    $this->_ref_transmissions = $this->loadBackRefs("transmissions");
    foreach($this->_ref_transmissions as &$_trans){
      $_trans->loadRefsFwd();
    }           
  }
  
  function loadRefsAdministrations($date=""){
    $administration = new CAdministration();
    $where = array();
    $where["object_id"] = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class'";
    if($date){
      $where["dateTime"] = "LIKE '$date%'";
    }
    $this->_ref_administrations = $administration->loadList($where);
  }
  
  /*
   * Chargement des prises de la ligne
   */
  function loadRefsPrises(){
    $this->_ref_prises = $this->loadBackRefs("prise_posologie","moment_unitaire_id, prise_posologie_id");
    foreach ($this->_ref_prises as &$prise) {
      if($prise->decalage_intervention != NULL){
        $this->_nb_prises_interv++;
      }
      $prise->_ref_object =& $this;
      $prise->loadRefMoment();
      $prise->loadRefsFwd();
    }
		
		return $this->_ref_prises;
  }
 
  /*
   * Calcul permettant de savoir si la ligne possède un historique
   */
  function countParentLine(){
    $line = new $this->_class;
    $line->child_id = $this->_id;
    $this->_count_parent_line = $line->countMatchingList(); 
  }

  /*
   * Calcul du nombre de prises que possède la ligne
   */
  function countPrisesLine(){
    $prise = new CPrisePosologie();
    $prise->object_id = $this->_id;
    $prise->object_class = $this->_class;
    $this->_count_prises_line = $prise->countMatchinglist();  
  }
	
  /*
   * Chargement de la ligne precedent la ligne courante
   */
  function loadRefParentLine(){
    $this->_ref_parent_line = $this->loadUniqueBackRef("parent_line");
  }

  /*
   * Chargement récursif des parents d'une ligne, permet d'afficher l'historique d'une ligne
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
	  
  function delete(){
    // Chargement de la child_line de l'objet à supprimer
    $line = new $this->_class;
    $line->child_id = $this->_id;
    $line->loadMatchingObject();

    if($line->_id){
      if (!$this->canDeleteEx()) {
        // On vide le child_id
        $line->child_id = "";
        if($msg = $line->store()){
          return $msg;
        }
      }
    }
    // Suppression de la ligne
    return parent::delete();
  }
    
  /*
   * Chargement du log d'arret de la ligne
   */
  function loadRefLogDateArret(){   
    $this->_ref_log_date_arret = $this->loadLastLogForField("date_arret");
  }
  
  /*
   * Duplication d'une ligne
   */
  function duplicateLine($praticien_id, $prescription_id, $debut="", $time_debut="") {
    if(!$debut){
      $debut = mbDate();
    }
    if(!$time_debut){
      $time_debut = mbTime();
    }
    
    // Chargement de la prescription
    $prescription = new CPrescription();
    $prescription->load($prescription_id);
    
    // Chargement de la ligne de prescription
    $new_line = new CPrescriptionLineMedicament();
    $new_line->load($this->_id);
		
		if($new_line->date_arret){
			$debut = $new_line->date_arret;
		}
		if($new_line->time_arret){
      $time_debut = $new_line->time_arret;
    }

    $new_line->loadRefsPrises();
    $new_line->loadRefPrescription(); 
    $new_line->_id = "";
    
    // Si date_arret (cas du sejour)
    $new_line->debut = $debut;
    $new_line->time_debut = $time_debut;
    $new_line->date_arret = "";
    $new_line->time_arret = "";
    $new_line->unite_duree = "jour";
    if($new_line->duree < 0){
      $new_line->duree = "";
    }
    $new_line->praticien_id = $praticien_id;
    $new_line->signee = 0;
    $new_line->valide_pharma = 0;
    $new_line->valide_infirmiere = 0;
    $new_line->creator_id = CAppUI::$user->_id;
    $msg = $new_line->store();
    CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
    
    foreach($new_line->_ref_prises as &$prise){
      $prise->_id = "";
      $prise->object_id = $new_line->_id;
      $prise->object_class = "CPrescriptionLineMedicament";
      $msg = $prise->store();
      CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
    }
    
    $old_line = new CPrescriptionLineMedicament();
    $old_line->load($this->_id);
    
    $old_line->child_id = $new_line->_id;
    if($prescription->type !== "sortie" && !$old_line->date_arret){
      $old_line->date_arret = mbDate();
      $old_line->time_arret = mbTime();
    }
    $old_line->store();
		return $new_line->_guid;
  }
  
  /*
   * Suppression à l'affichage des prises qui ont été déplacées (planification)
   */
  function removePrisesPlanif($mode_semainier = 0){
    // Suppression des prises prevues replanifiées
    if($this->_quantity_by_date){
      foreach($this->_quantity_by_date as $_type => $quantity_by_date){
        foreach($quantity_by_date as $_date => $quantity_by_hour){
          if(!isset($this->_quantity_by_date[$_type][$_date]['total'])){
            $this->_quantity_by_date[$_type][$_date]['total'] = 0;
          }
          if(isset($quantity_by_hour['quantites'])){
            foreach($quantity_by_hour['quantites'] as $_hour => $quantity){
              $heure_reelle = @$quantity[0]["heure_reelle"];
              // Recherche d'une planification correspondant à cette prise prevue
              $planification = new CAdministration();
              if(is_numeric($_type)){
                $planification->prise_id = $_type;
              } else {
                $planification->unite_prise = addslashes($_type);
              }
              $planification->original_dateTime = "$_date $heure_reelle:00:00";
              $planification->object_id = $this->_id;
              $planification->object_class = $this->_class;
              $count_planifications = $planification->countMatchingList();
              if($count_planifications){
                $this->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
                $this->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total_disp'] = 0;
              }
              if($mode_semainier){
                $this->_quantity_by_date[$_type][$_date]['total'] += $this->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'];
                $this->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
              }
            }
          } 
        }
      }
    }
  }
  
	/*
   * Suppression des planifications systemes
   */
  function removePlanifSysteme(){
		$ds = CSQLDataSource::get("std");
    $query = "DELETE planification_systeme.* FROM planification_systeme 
              LEFT JOIN administration ON administration.planification_systeme_id = planification_systeme.planification_systeme_id
              WHERE planification_systeme.object_id = '$this->_id'
							AND planification_systeme.object_class = '$this->_class'
              AND administration.administration_id IS NULL;";
    $ds->exec($query);
  }
	
  function loadRefAlerte(){
    $this->_ref_alerte = new CAlert();
    $this->_ref_alerte->setObject($this);
    $this->_ref_alerte->handled = "0";
    $this->_ref_alerte->loadMatchingObject();
  }
	
  /*
   * Permet de savoir si la ligne a ete recemment modifiée
   */
  function getRecentModification(){
		if(@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")){
			$this->loadRefAlerte();
			if($this->_ref_alerte->_id){
				$this->_recent_modification = true;
        if($this->_ref_alerte->level == "high"){
					 $this->_urgence = true;
        }
			}
		
		} else {
			$service_id = isset($_SESSION["soins"]["service_id"]) && $_SESSION["soins"]["service_id"] ?
      $_SESSION["soins"]["service_id"] : "none";
	      
	    if ($service_id == "NP") {
	      $service_id = "none";
	    }
	    
	    $configs = CConfigService::getAllFor($service_id);
	    
	    // modification recente si moins de $nb_hours heures
	    $nb_hours = $configs["Affichage alertes de modifications"];
	    $this->_recent_modification = $this->hasRecentLog($nb_hours);
		}
  }
  
  /*
   * Permet de savoir si la ligne contient des prises urgentes
   */
  function calculDatesUrgences(){
    $prise = new CPrisePosologie();
    $where = array();
    $where["object_id"] = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class'";
    $where["urgence_datetime"] = "IS NOT NULL";
    $prises_urgentes = $prise->loadList($where);
    foreach($prises_urgentes as $_prise_urg){
      $this->_dates_urgences[mbDate($_prise_urg->urgence_datetime)][$_prise_urg->_id] = mbTransformTime(null, $_prise_urg->urgence_datetime, "%Y-%m-%d %H:00:00");  
    }
  }
  
  /*
   * Chargement des administrations et transmissions
   */
  function calculAdministrations($date, $mode_dispensation = 0){
    $this->loadRefsAdministrations($date);
    
    foreach($this->_ref_administrations as $_administration){
      $heure_adm = substr($_administration->_heure, 0, 2);
      $key_administration = $_administration->prise_id ? $_administration->prise_id : $_administration->unite_prise;
			
      $administrations =& $this->_administrations[$key_administration][$date][$heure_adm];
    
      // Planifications
      if($_administration->planification){
        @$this->_quantity_by_date[$key_administration][$date]['total'] += $_administration->quantite;
        if(!isset($administrations['quantite_planifiee'])){
          $administrations['quantite_planifiee'] = 0;
        }
        $administrations['quantite_planifiee'] += $_administration->quantite; 
        if(!isset($administrations['planification_id'])){
          $administrations['planification_id'] = "";
        }
        $administrations['planification_id'] = $_administration->_id;
        $administrations['original_date_planif'] = $_administration->original_dateTime;
      }
      // Administrations
      else {
        $_administration->loadRefConstantesMedicales();
        // Stockage des administrations en fonction de l'heure
				$minute = mbTransformTime(null, $_administration->dateTime, "%M");
				if(!isset($administrations["adm_in_hour"][$minute])){
					$administrations["adm_in_hour"][$minute] = 0;
				}
				$administrations["adm_in_hour"][$minute] += $_administration->quantite;

			  if(!isset($administrations['quantite'])){
          $administrations['quantite'] = '';
          $administrations['administrations'][$_administration->_id] = '';
        }
        if(!isset($this->_administrations_by_line[$key_administration][$date])){
          $this->_administrations_by_line[$key_administration][$date] = 0;
        }
        
        // Permet de stocker les administrations par unite de prise / type de prise
        $administrations['quantite'] += $_administration->quantite;

        // Stockage de la liste des administrations en fonction de la date et de l'heure sous la forme d'un token field
        if(!isset($administrations['list'])){
          $administrations['list'] = $_administration->_id;
        } else {
          $administrations['list'] .= "|".$_administration->_id;
        }
        
        // Sockage de la liste des administrations en fonction de la date sous forme de token field
        if(!isset($this->_administrations[$key_administration][$date]['list'])){
          $this->_administrations[$key_administration][$date]['list'] = $_administration->_id;
        } else {
          $this->_administrations[$key_administration][$date]['list'] .= "|".$_administration->_id;
        }
        $this->_administrations_by_line[$key_administration][$date] += $_administration->quantite; 
        
        if(!$mode_dispensation){
          // Chargement du log de creation de l'administration
          $log = new CUserLog;
          $log->object_id = $_administration->_id;
          $log->object_class = $_administration->_class;
          $log->loadMatchingObject();
          $log->loadRefsFwd();
          $log->_ref_object->_ref_object =& $this;
          
          if($_administration->prise_id){
            $_administration->loadRefPrise();
          }
          $administrations["administrations"][$_administration->_id] = $log;
          $_administration->loadRefsTransmissions();  
          
          if(!isset($this->_transmissions[$key_administration][$date][$heure_adm])){
            $this->_transmissions[$key_administration][$date][$heure_adm]['nb'] = '';
            $this->_transmissions[$key_administration][$date][$heure_adm]['list'][$_administration->_id] = '';
          }
          $_transmissions =& $this->_transmissions[$key_administration][$date][$heure_adm];
          $_transmissions["nb"] += count($_administration->_ref_transmissions);
          $_transmissions["list"][$_administration->_id] = $_administration->_ref_transmissions;
        }
      } 
    }
		
    if(!$this->_ref_prises){
      $this->loadRefsPrises();
    }
  }
  
  /*
   * Chargement ou recuperation de toutes les prises prevues pour une ligne
   * return planifs
   */
  function calculPlanifSysteme(){
  	if(!CPlanificationSysteme::$_calcul_planif){
  		return;
  	}
		
		  
    if(!$this->_ref_prises){
      $this->loadRefsPrises();
    }
    if($this instanceof CPrescriptionLineElement && $this->debut && $this->time_debut){
    	$chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
      
			// On genere une planif a la date et heure de debut si aucune poso n'est presente
			if($this->debut && $this->time_debut && count($this->_ref_prises) == 0){
				$new_planif = new CPlanificationSysteme();
        $new_planif->dateTime = "$this->debut $this->time_debut";
	      $new_planif->object_id = $this->_id;
	      $new_planif->object_class = $this->_class;
        $new_planif->sejour_id = $this->_ref_prescription->object_id;    
        $new_planif->store();
			}
    }
		
    foreach($this->_ref_prises as &$_prise) {
    	$_prise->calculPlanifs();
    }
  }
  
  /*
   * Chargement des prises
   */
  function calculPrises($prescription, $date, $name_chap = "", $name_cat = "", $with_calcul = true, $manual_planif = false) {
    $total_day = 0;

		// Chargement des planifications pour la date courante
		$planif = new CPlanificationSysteme();
    $where["object_id"] = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class'";
    $where["dateTime"] = " LIKE '$date%'";
		
    $planifs = $planif->loadList($where, "dateTime");
			
    foreach($this->_ref_prises as &$_prise) {
    	// Mise a jour de la quantite (en fonction du poids et de l'unite d'administration)
      $_prise->updateQuantite();
      
      // Cle permettant de ranger les prises prevues, unite_prise si la prise est de type moment sinon prise->_id
			if($this instanceof CPrescriptionLineElement){
        $key_tab = ($_prise->moment_unitaire_id || $_prise->heure_prise || $_prise->condition || $_prise->datetime) ? $this->_chapitre : $_prise->_id;
			} else {
			  $key_tab = ($_prise->moment_unitaire_id || $_prise->heure_prise || $_prise->condition || $_prise->datetime) ? $_prise->unite_prise : $_prise->_id;
      }
			// Stockage des lignes qui composent le plan de soin
      if($name_chap && $name_cat){
        $prescription->_ref_lines_elt_for_plan[$name_chap][$name_cat][$this->_id][$key_tab] = $this;
      } else {
        $this->_ref_produit->loadClasseATC();
        $code_ATC = $this->_ref_produit->_ref_ATC_2_code;
        if($this->_is_injectable){
          $prescription->_ref_injections_for_plan[$code_ATC][$this->_id][$key_tab] = $this;  
        } else {
          $prescription->_ref_lines_med_for_plan[$code_ATC][$this->_id][$key_tab] = $this;
        }
      }
      // Mode permettant de calculer seulement les onglets visibles
      if(!$with_calcul){
        continue;
      }
			
      $line_plan_soin =& $this->_quantity_by_date[$key_tab][$date]['quantites'];
      
      // Parcours des planifs et ajout dans le plan de soin
			foreach($planifs as $_planif){
        if($_planif->prise_id != $_prise->_id){
          continue;
        }
				
				$_heure = substr(mbTime($_planif->dateTime), 0, 2);
        $_planif->_quantite = $_prise->_quantite_administrable;
						
				if($manual_planif && !preg_match("/[0-9]{1,2}h/i", $_prise->_ref_moment->libelle) && !$_prise->urgence_datetime){
					// On n'ajoute a la liste que les planifs qui ne sont pas deja planifiées
	        $_manual_planification = new CAdministration();    
	        if(is_numeric($key_tab)){
	          $_manual_planification->prise_id = $key_tab;
	        } else {
	          $_manual_planification->unite_prise = addslashes($key_tab);
	        }
	        $_manual_planification->original_dateTime = "$date $_heure:00:00";
	        $_manual_planification->object_id = $this->_id;
	        $_manual_planification->object_class = $this->_class;
	        $count_planifications = $_manual_planification->countMatchingList();
	        if($count_planifications == 0){
	          $this->_planifs_systeme[$key_tab][$_planif->_id] = $_planif;
	        }
				} else {
					// Ajout dans le plan de soins des planifs systemes
					if(!isset($line_plan_soin[$_heure])){
	          $line_plan_soin[$_heure] = array("total" => 0, "total_disp" => 0);
	        }
	        $line_plan_soin[$_heure]["total"] += $_prise->_quantite_administrable;
	        $line_plan_soin[$_heure]["total_disp"] += $_prise->_quantite_dispensation;
	        $line_plan_soin[$_heure][] = array("quantite" => $_prise->_quantite_administrable, "heure_reelle" => $_heure, "full_hour" => substr(mbTime($_planif->dateTime), 0, 5));
	       
				  $line_plan_soin[$_heure]["urgence"] = $_prise->urgence_datetime ? true : false;
				  
	        if(!isset($line_plan_soin[$_heure]["nb_adm"])){
	          $line_plan_soin[$_heure]["nb_adm"] = 0;
	        }
	        $line_plan_soin[$_heure]["nb_adm"]++;
	        $total_day += $_prise->_quantite_administrable;
				}
      }
			if($manual_planif && $this->_planifs_systeme){
				foreach($this->_planifs_systeme as &$_planifs_by_key){
					krsort($_planifs_by_key);
				}
			}
			      
      // Stockage du libelle de l'unite de prise
      if($_prise->moment_unitaire_id || $_prise->heure_prise || $_prise->condition || $_prise->datetime){
        if($this instanceof CPrescriptionLineElement){
          $this->_prises_for_plan[$this->_chapitre][$_prise->_id] = $_prise;
        } else {
          $this->_prises_for_plan[$_prise->unite_prise][$_prise->_id] = $_prise;
        }
			} else {
        $this->_prises_for_plan[$_prise->_id] = $_prise;
      }
    }
		
		// Pre-remplissage du plan de soins avec les planifs systemes pour les lignes ne possedant pas de posologie
		if($with_calcul && $this instanceof CPrescriptionLineElement){
			$chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
      $line_plan_soin =& $this->_quantity_by_date["aucune_prise"][$date]['quantites'];
      
			foreach($planifs as $_planif){
	      $_heure = substr(mbTime($_planif->dateTime), 0, 2);
	      if(!isset($line_plan_soin[$_heure])){
	        $line_plan_soin[$_heure] = array("total" => 0, "total_disp" => 0);
	      }
	      $line_plan_soin[$_heure]["total"] += 1;
	      $line_plan_soin[$_heure]["total_disp"] += 1;
	      $line_plan_soin[$_heure][] = array("quantite" => 1, "heure_reelle" => $_heure);
	      $total_day += 1;
	    }
		}
    return $total_day;
  }
	
	/*
   * Store-like function, suppression des prises de la ligne
   */
  function deletePrises(){
    $this->_delete_prises = 0;
    // Chargement des prises 
    $this->loadRefsPrises();
    // Parcours des suppression des prises
    foreach($this->_ref_prises as &$_prise){
      if($msg = $_prise->delete()){
        return $msg;
      }
    }
  }
}

?>