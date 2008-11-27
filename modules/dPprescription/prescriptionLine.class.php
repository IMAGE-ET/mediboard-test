<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPprescription
 *  @version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescriptionLine class
 */
class CPrescriptionLine extends CMbObject {
  
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
  
  // Form Fields
  var $_fin                = null;
  var $_protocole          = null;
  var $_count_parent_line  = null;
  var $_count_prises_line  = null;  
  var $_fin_reelle         = null;
  var $_debut_reel         = null;
  var $_active             = null;
  
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
  
  // Dossier/Feuille de soin
  var $_administrations = null;          // Administrations d'une ligne stockées par date, heure, type de prise
  var $_administrations_by_line = null; // Administrations d'une ligne stockées par date
  var $_transmissions   = null;
  var $_quantity_by_date = null;
  var $_prises_for_plan   = null;

  var $_nb_prises_interv = null; // Nombre de prises qui dependent de l'intervention

  // Can fields
  var $_perm_edit = null;
  

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["prescription_id"]   = "notNull ref class|CPrescription cascade";
    $specs["ald"]               = "bool";
    $specs["praticien_id"]      = "notNull ref class|CMediusers";
    $specs["creator_id"]        = "notNull ref class|CMediusers";
    $specs["signee"]            = "bool";
    $specs["debut"]             = "date";
    $specs["time_debut"]        = "time";
    $specs["duree"]             = "num min|0";
    $specs["unite_duree"]       = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour";
    $specs["date_arret"]        = "date";
    $specs["time_arret"]        = "time";
    $specs["child_id"]          = "ref class|$this->_class_name";
    $specs["decalage_line"]     = "num";
    $specs["jour_decalage"]     = "enum list|E|I|S|N default|E";
    $specs["fin"]               = "date";
    $specs["valide_infirmiere"] = "bool";
    $specs["jour_decalage_fin"] = "enum list|I|S";
    $specs["decalage_line_fin"] = "num";
    $specs["time_fin"]          = "time";
    $specs["conditionnel"]      = "bool";
    $specs["condition_active"]  = "bool";
    $specs["unite_decalage"]    = "enum list|jour|heure default|jour";
    $specs["unite_decalage_fin"]= "enum list|jour|heure default|jour";
    $specs["emplacement"]       = "notNull enum list|service|bloc default|service";
    $specs["operation_id"]      = "ref class|COperation";
    $specs["_fin"]              = "date moreEquals|debut";
    $specs["_fin_reelle"]       = "date";
    return $specs;
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
   * Back Refs
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    
    $this->loadRefsPrises();
    $this->loadRefParentLine();
  }
  
  /*
   * Chargement de la prescription
   */
  function loadRefPrescription() {
    if (!$this->_ref_prescription) {
	    $this->_ref_prescription = new CPrescription();
	    $this->_ref_prescription = $this->_ref_prescription->getCached($this->prescription_id);
    }
  }
  
  /*
   * Chargement du praticien
   */
  function loadRefPraticien() {
    if (!$this->_ref_praticien) {
	    $user = new CMediusers();
	    $this->_ref_praticien = $user->getCached($this->praticien_id);
    }
  }
  
  /*
   * Chargement du createur de la ligne
   */
  function loadRefCreator() {
    if (!$this->_ref_creator) {
	    $user = new CMediusers();
	    $this->_ref_creator = $user->getCached($this->creator_id);
    }
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
    $this->_ref_child_line = new $this->_class_name;
    if($this->child_id){
      $this->_ref_child_line->_id = $this->child_id;
      $this->_ref_child_line->loadMatchingObject();
    }  
  }
  
  /*
   * Déclaration des backRefs
   */
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prise_posologie"] = "CPrisePosologie object_id";
    $backRefs["administration"]  = "CAdministration object_id";
    $backRefs["parent_line"]     = "$this->_class_name child_id";  
    $backRefs["transmissions"]   = "CTransmissionMedicale object_id";
    return $backRefs;
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
  	$where["object_class"] = " = '$this->_class_name'";
  	if($date){
  	  $where["dateTime"] = "LIKE '$date%'";
  	}
  	$this->_ref_administrations = $administration->loadList($where);
  }
  
  /*
   * Chargement des prises de la ligne
   */
  function loadRefsPrises(){
    $this->_ref_prises = $this->loadBackRefs("prise_posologie");
    foreach ($this->_ref_prises as &$prise) {
      if($prise->decalage_intervention != NULL){
        $this->_nb_prises_interv++;
      }
      $prise->_ref_object =& $this;
      $prise->loadRefsFwd();
    }
  }
 
  /*
   * Calcul permettant de savoir si la ligne possède un historique
   */
  function countParentLine(){
    $line = new $this->_class_name;
    $line->child_id = $this->_id;
    $this->_count_parent_line = $line->countMatchingList(); 
  }

  /*
   * Calcul du nombre de prises que possède la ligne
   */
  function countPrisesLine(){
    $prise = new CPrisePosologie();
    $prise->object_id = $this->_id;
    $prise->object_class = $this->_class_name;
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
    $line = new $this->_class_name;
    $line->child_id = $this->_id;
    $line->loadMatchingObject();
    if($line->_id){
      // On vide le child_id
      $line->child_id = "";
      if($msg = $line->store()){
        return $msg;
      }
    }
    
    // Suppression de la ligne
    if($msg = parent::delete()){
      return $msg;
    }
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->countParentLine();
    $this->countPrisesLine();
    
    $this->_protocole = ($this->_ref_prescription->object_id) ? "0" : "1";
    
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
    $this->_debut_reel = $this->debut." $time_debut";
    
    $this->_active = (!$this->conditionnel) ? 1 : $this->condition_active;
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
    global $AppUI;
    
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
    $date_arret_tp = $new_line->date_arret;
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
    
    
    // Si prescription de sortie, on duplique la ligne en ligne de prescription
    if($prescription->type == "sortie" && $new_line->_traitement && !$date_arret_tp){
      $new_line->prescription_id = $prescription_id;
    }
    $new_line->creator_id = $AppUI->user_id;
    $msg = $new_line->store();
    
    $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
    
    foreach($new_line->_ref_prises as &$prise){
      // On copie les prises
      $prise->_id = "";
      $prise->object_id = $new_line->_id;
      $prise->object_class = "CPrescriptionLineMedicament";
      $msg = $prise->store();
      $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
    }
    
    $old_line = new CPrescriptionLineMedicament();
    $old_line->load($this->_id);
    
    if(!($prescription->type == "sortie" && $old_line->_traitement && !$date_arret_tp)){
      $old_line->child_id = $new_line->_id;
      if($prescription->type != "sortie" && !$old_line->date_arret){
        $old_line->date_arret = mbDate();
        $old_line->time_arret = mbTime();
      }
      $old_line->store();
    }
  }
  
  /*
   * Chargement des administrations et transmissions
   */
  function calculAdministrations($date, $mode_feuille_soin = 0){
  	$type = ($this->_class_name == "CPrescriptionLineMedicament") ? "med" : "elt";
  	
    if(!$mode_feuille_soin){
			$this->loadRefsAdministrations($date);
			foreach($this->_ref_administrations as $_administration){
			  $key_administration = $_administration->prise_id ? $_administration->prise_id : stripslashes($_administration->unite_prise);
			  
			  // Initialisation des tableaux
			  if(!isset($this->_administrations[$key_administration][$date][$_administration->_heure])){
			    $this->_administrations[$key_administration][$date][$_administration->_heure]['quantite'] = '';
			    $this->_administrations[$key_administration][$date][$_administration->_heure]['administrations'][$_administration->_id] = '';
			  }
			  if(!isset($this->_transmissions[$key_administration][$date][$_administration->_heure])){
			    $this->_transmissions[$key_administration][$date][$_administration->_heure]['nb'] = '';
			    $this->_transmissions[$key_administration][$date][$_administration->_heure]['list'][$_administration->_id] = '';
			  }
			  if(!isset($this->_administrations_by_line[$key_administration][$date])){
			    $this->_administrations_by_line[$key_administration][$date] = 0;
			  }
			  
			  // Permet de stocker les administrations par unite de prise / type de prise
			  $this->_administrations[$key_administration][$date][$_administration->_heure]['quantite'] += $_administration->quantite;
				
			  // Stockage de la liste des administrations en fonction de la date et de l'heure sous la forme d'un token field
			  if(!isset($this->_administrations[$key_administration][$date][$_administration->_heure]['list'])){
			    $this->_administrations[$key_administration][$date][$_administration->_heure]['list'] = $_administration->_id;
			  } else {
			    $this->_administrations[$key_administration][$date][$_administration->_heure]['list'] .= "|".$_administration->_id;
			  }
			  
			  // Sockage de la liste des administrations en fonction de la date sous forme de token field
			  if(!isset($this->_administrations[$key_administration][$date]['list'])){
			    $this->_administrations[$key_administration][$date]['list'] = $_administration->_id;
			  } else {
			    $this->_administrations[$key_administration][$date]['list'] .= "|".$_administration->_id;
			  }
			  
			  $this->_administrations_by_line[$key_administration][$date] += $_administration->quantite;
			  	
			  // Chargement du log de creation de l'administration
			  $log = new CUserLog;
		    $log->object_id = $_administration->_id;
			  $log->object_class = $_administration->_class_name;
			  $log->loadMatchingObject();
			  $log->loadRefsFwd();
			  
			  if ($this->_class_name == "CPrescriptionLineMedicament"){
			    $this->_ref_produit->loadConditionnement();
			  }
			  
			  $log->_ref_object->_ref_object =& $this;
			  
			  if($_administration->prise_id){
				  $_administration->loadRefPrise();
			  }
		  	$this->_administrations[$key_administration][$date][$_administration->_heure]["administrations"][$_administration->_id] = $log;
			  $_administration->loadRefsTransmissions();  
			  $this->_transmissions[$key_administration][$date][$_administration->_heure]["nb"] += count($_administration->_ref_transmissions);
			  $this->_transmissions[$key_administration][$date][$_administration->_heure]["list"][$_administration->_id] = $_administration->_ref_transmissions;
		  }		
    }		
    if(!$this->_ref_prises){
      $this->loadRefsPrises();
    }
  }
  
    /*
   * Chargement des prises
   */
  function calculPrises($prescription, $date, $heures, $mode_feuille_soin = 0, $name_chap = "", $name_cat = ""){
  	$type = ($this->_class_name == "CPrescriptionLineMedicament") ? "med" : "elt";
  	
  	foreach($this->_ref_prises as &$_prise) {
  	  // Dans le cas d'un element, on affecte l'unite de prise prevu pour cet element
  	  if($_prise->_ref_object->_class_name == "CPrescriptionLineElement"){
  	    $_prise->unite_prise =  $_prise->_ref_object->_unite_prise;
  	  }
  	  
  	  // Si la prise est de type tous_les et que 
  	  if (($_prise->nb_tous_les && $_prise->unite_tous_les && !$_prise->calculDatesPrise($date))){
		 	  continue;
		 	}
		 	
		 	// Cle permettant de ranger les prises prevues, unite_prise si la prise est de type moment sinon prise->_id
		 	$key_tab = ($_prise->moment_unitaire_id || $_prise->heure_prise) ? $_prise->unite_prise : $_prise->_id;
      
		 	// Stockage des lignes qui composent le plan de soin
  	 	if($name_chap && $name_cat){
		 	  $prescription->_ref_lines_elt_for_plan[$name_chap][$name_cat][$this->_id][$key_tab] = $this;
  	 	} else {
		 	  $this->_ref_produit->loadClasseATC();
		 	  $code_ATC = $this->_ref_produit->_ref_ATC_2_code;
		 	  $prescription->_ref_lines_med_for_plan[$code_ATC][$this->_id][$key_tab] = $this;
  	 	}
			
		 	// Stockage du libelle de l'unite de prise
		 	if($_prise->moment_unitaire_id || $_prise->heure_prise){
		 	  $this->_prises_for_plan[$_prise->unite_prise][$_prise->_id] = $_prise;
		 	} else {
		 	  $this->_prises_for_plan[$_prise->_id] = $_prise;
		 	}

  	  $prise_comptee = 0;
		  $poids_ok = 1;

  	  if($this->_class_name == "CPrescriptionLineMedicament" && !$_prise->_quantite_with_kg){
			  $_unite_prise = str_replace('/kg', '', $_prise->unite_prise);
			  if($_unite_prise != $_prise->unite_prise){
			    // On recupere le poids du patient pour calculer la quantite
	        if(!$prescription->_ref_object->_ref_patient){
	         $prescription->_ref_object->loadRefPatient();
	        }
	        $patient =& $prescription->_ref_object->_ref_patient;
	        if(!$patient->_ref_constantes_medicales){
	          $patient->loadRefConstantesMedicales();
	        }
	        $poids = $patient->_ref_constantes_medicales->poids;
	         
	        if(!$poids){
	          $poids_ok = 0;
	          $_prise->quantite = 0;
	          continue;
	        }
			    $_prise->quantite *= $poids;
			    $_prise->_quantite_with_kg = 1;  
			    $_prise->_unite_sans_kg = $_unite_prise;
        }
      }
      
		  if($_prise->_ref_object->_class_name == "CPrescriptionLineMedicament" && !$_prise->_quantite_with_coef){
		    $unite_prise = ($_prise->_unite_sans_kg) ? $_prise->_unite_sans_kg : $_prise->unite_prise;

		    $line    =& $_prise->_ref_object;
		    $produit =& $line->_ref_produit; 
		    $produit->loadConditionnement();
		    // Gestion des unites de prises exprimées en libelle de presentation (ex: poche ...)
		    if($_prise->unite_prise == $produit->libelle_presentation){		        
		      $_prise->quantite *= $produit->nb_unite_presentation;
		    }
		    
		    // Gestion des unite autres unite de prescription
		    if(!isset($produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation])) {
          $coef = 1;
        } else {
          $coef = $produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation];
        }
        
        $_prise->_quantite_with_coef = 1;
		    $_prise->quantite *= $coef;
		    $_prise->quantite = round($_prise->quantite, 2);
		    
		    $_prise->_ref_object->_unite_administration = $produit->libelle_unite_presentation;
		    $_prise->_ref_object->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;
		    if($_prise->_ref_object->_unite_dispensation == $produit->libelle_unite_presentation){
		      $_prise->_ref_object->_ratio_administration_dispensation = 1;
		    } else {
		      $_prise->_ref_object->_ratio_administration_dispensation = 1 / $produit->nb_unite_presentation;
		    }
		  }
		  
      // Intialisation des tableaux
      if(!isset($this->_quantity_by_date[$key_tab][$date])){
        $list_hours = range(0,24);
        foreach($list_hours as &$hour){
          $this->_quantity_by_date[$key_tab][$date]['quantites'][str_pad($hour, 2, "0", STR_PAD_LEFT)] = '';   
        }
      }
      
      
      if(!isset($this->_quantity_by_date[$key_tab][$date]['total'])){
        $this->_quantity_by_date[$key_tab][$date]['total'] = 0;
      }
     		      
			// Moment unitaire
			if($_prise->moment_unitaire_id){
		    $dateTimePrise = mbAddDateTime(mbTime($_prise->_ref_moment->heure), $date);
		    if(($this->_fin_reelle > $dateTimePrise) && $poids_ok){
				  if($_prise->_ref_moment->heure && count($heures)){
				   $this->_quantity_by_date[$key_tab][$date]['quantites'][$heures[substr($_prise->_ref_moment->heure, 0, 2)]] += $_prise->quantite;
				  }
		  	  $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
		  	  $prise_comptee = 1;
		    }
			}
			
	  
		 	// Tous les sans moment unitaire
		 	if(!$_prise->moment_unitaire_id && $_prise->nb_tous_les && $_prise->unite_tous_les == "jour"){
		 	  $heure = reset($_prise->_heures);
		 	  $dateTimePrise = mbAddDateTime(mbTime($heure), $date);
		 	  if($this->_fin_reelle > $dateTimePrise && $poids_ok){
		 	    if(count($heures)){
		        $this->_quantity_by_date[$key_tab][$date]['quantites'][$heures[substr($heure, 0, 2)]] += $_prise->quantite;
		 	    }
          $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
		      $prise_comptee = 1;
			  }
		 	}
		 	 	
		 	// Seulement Tous les avec comme unite les heures
		  if(!$_prise->moment_unitaire_id && $_prise->nb_tous_les && $_prise->unite_tous_les == "heure"){
        $time_debut = ($this->time_debut) ? $this->time_debut : "00:00:00";
        $date_time_temp = "$this->debut $time_debut";
        while($date_time_temp < "$date 23:59:59"){
          $_hour_tab = substr(mbTime($date_time_temp), 0, 2);
          if($date == mbDate($date_time_temp) && $this->_fin_reelle > $date_time_temp && $poids_ok){
            if(count($heures)){
		          $this->_quantity_by_date[$key_tab][$date]['quantites'][$heures[$_hour_tab]] += $_prise->quantite;
		        }
            $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
		        $prise_comptee = 1;
		      }
          $date_time_temp = mbDateTime("+ $_prise->nb_tous_les HOURS", $date_time_temp);
        }
		  }
		  
		  // Fois par avec comme unite jour
		  if(($_prise->nb_fois && $_prise->unite_fois == 'jour') || ($_prise->quantite && !$_prise->moment_unitaire_id && 
		      !$_prise->nb_fois && !$_prise->unite_fois && !$_prise->unite_tous_les && !$_prise->nb_tous_les && !$_prise->heure_prise)){
		    if($_prise->_heures){
		     foreach($_prise->_heures as $_heure){
		       $dateTimePrise = mbAddDateTime($_heure, $date);
		       if($this->_fin_reelle > $dateTimePrise && $poids_ok){
		         if(count($heures)){
		           $this->_quantity_by_date[$key_tab][$date]['quantites'][$heures[substr($_heure,0,2)]] += $_prise->quantite;
		         }
		         $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
		         $prise_comptee = 1;
		      }
		     }
		    }
		  }
		    
		  // Heure de prises specifié (I+x heures)
      if($_prise->heure_prise){
        if(count($heures)){
          $this->_quantity_by_date[$key_tab][$date]['quantites'][$heures[substr($_prise->heure_prise,0,2)]] += $_prise->quantite;
        }
        $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
		    $prise_comptee = 1;
      }
		  
		 	// Aucun moment unitaire, seulement Tous les ou Fois par, permet la generation du plan sur 5 jours
		 	if(!$_prise->moment_unitaire_id && ($_prise->nb_tous_les || $_prise->nb_fois) && !$prise_comptee){
		 	  if($_prise->_unite_temps == 'jour'){
		 	    if($_prise->nb_fois){
		 	  	  $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite * $_prise->nb_fois;
			  	  if($_prise->nb_tous_les && $_prise->calculDatesPrise($date)){
		 	  	    $this->_quantity_by_date[$key_tab][$date]['total'] += $_prise->quantite;
			  		}
		 	  	}
		 	  }
		 	}
    }
  }
}

?>