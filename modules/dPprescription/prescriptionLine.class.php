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
  
  // Form Fields
  var $_fin                = null;
  var $_protocole          = null;
  var $_count_parent_line  = null;
  var $_count_prises_line  = null;  
  var $_fin_reelle         = null;
  var $_debut_reel         = null;
  
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
	
  
  function getSpecs() {
    $specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id"   => "notNull ref class|CPrescription cascade",
      "ald"               => "bool",
      "praticien_id"      => "notNull ref class|CMediusers",
      "creator_id"        => "notNull ref class|CMediusers",
      "signee"            => "bool",
      "debut"             => "date",
      "time_debut"        => "time",
      "duree"             => "num",
      "unite_duree"       => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "date_arret"        => "date",
      "time_arret"        => "time",
      "child_id"          => "ref class|$this->_class_name",
      "decalage_line"     => "num",
      "jour_decalage"     => "enum list|E|I|S|N default|E",
      "fin"               => "date",
      "valide_infirmiere" => "bool",
      "jour_decalage_fin" => "enum list|I|S",
      "decalage_line_fin" => "num",
      "time_fin"          => "time",
      "_fin"              => "date moreEquals|debut",
      "_fin_reelle"   => "date"
    );
    return array_merge($specsParent, $specs);
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
	    $this->_ref_prescription->load($this->prescription_id);
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
    $backRefs["administration"] = "CAdministration object_id";
    $backRefs["parent_line"] = "$this->_class_name child_id";  
    $backRefs["transmissions"] = "CTransmissionMedicale object_id";
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
      if($this->unite_duree == "minute" || $this->unite_duree == "heure" || $this->unite_duree == "demi_journee"){
        $this->_fin = $this->debut;
      }
      if($this->unite_duree == "jour"){
        $this->_fin = mbDate("+ $this->duree DAYS", $this->debut);
      }
      if($this->unite_duree == "semaine"){
        $this->_fin = mbDate("+ $this->duree WEEKS", $this->debut);
      }
      if($this->unite_duree == "quinzaine"){
        $duree_temp = 2 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp WEEKS", $this->debut);
      }
      if($this->unite_duree == "mois"){
        $this->_fin = mbDate("+ $this->duree MONTHS", $this->debut);  
      }
      if($this->unite_duree == "trimestre"){
        $duree_temp = 3 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);  
      }
      if($this->unite_duree == "semestre"){
        $duree_temp = 6 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);  
      }
      if($this->unite_duree == "an"){
        $this->_fin = mbDate("+ $this->duree YEARS", $this->debut);  
      }
      // Si la duree est superieure à une journée, on transforme la date de fin
      if($this->debut != $this->_fin){
        $this->_fin = mbDate(" -1 DAYS", $this->_fin);  
      }
    }
    
    
    // Calcul du debut reel de la ligne
    $time_debut = ($this->time_debut) ? $this->time_debut : "06:00:00";
    $this->_debut_reel = $this->debut." $time_debut";
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
    $prescription = new CPrescription();
    $prescription->load($prescription_id);
    
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
				$key_administration = $_administration->prise_id ? $_administration->prise_id : $_administration->unite_prise;

				// Permet de stocker les administrations par unite de prise / type de prise
				@$this->_administrations[$key_administration][$date][$_administration->_heure]["quantite"] += $_administration->quantite;
				@$this->_administrations_by_line[$date] += $_administration->quantite;
				
				
				// Chargement du log de creation de l'administration
			  $log = new CUserLog;
        $log->object_id = $_administration->_id;
				$log->object_class = $_administration->_class_name;
				$log->loadMatchingObject();
				$log->loadRefsFwd();
				
				if($this->_class_name == "CPrescriptionLineMedicament"){
					
				  $this->_ref_produit->loadConditionnement();
				  $log->_ref_object->_ref_object =& $this;
				}
				
				if($_administration->prise_id){
					$_administration->loadRefPrise();
				}
				@$this->_administrations[$key_administration][$date][$_administration->_heure]["administrations"][$_administration->_id] = $log;
				$_administration->loadRefsTransmissions();  
				@$this->_transmissions[$key_administration][$date][$_administration->_heure]["nb"] += count($_administration->_ref_transmissions);
				@$this->_transmissions[$key_administration][$date][$_administration->_heure]["list"][$_administration->_id] = $_administration->_ref_transmissions;
			}		
    }		
    if(!$this->_ref_prises){
      $this->loadRefsPrises();
    }
  }
  
    /*
   * Chargement des prises
   */
  function calculPrises($prescription, $date, $mode_feuille_soin = 0, $name_chap = "", $name_cat = ""){
  	$type = ($this->_class_name == "CPrescriptionLineMedicament") ? "med" : "elt";
  	
  	foreach($this->_ref_prises as &$_prise){
  		$key_tab = $_prise->moment_unitaire_id ? $_prise->unite_prise : $_prise->_id;
			$key_prise = $_prise->moment_unitaire_id ? $_prise->unite_prise : "autre";
			$poids_ok = 1;
			if(!is_numeric($_prise->unite_prise) && $this->_class_name == "CPrescriptionLineMedicament" && !$mode_feuille_soin){
				//$quantite = 0;
				$_unite_prise = str_replace('/kg', '', $_prise->unite_prise);
        // Dans le cas d'un unite_prise/kg
        if($_unite_prise != $_prise->unite_prise){
          // On recupere le poids du patient pour calculer la quantite
          if(!$prescription->_ref_object->_ref_patient){
           $prescription->_ref_object->loadRefPatient();
          }
          $patient =& $prescription->_ref_object->_ref_patient;          
          if(!$patient->_ref_constantes_medicales){
            $patient->loadRefConstantesMedicales();
          }
          $const_med = $patient->_ref_constantes_medicales;
          $poids     = $const_med->poids;
					
          if($poids){
            $_prise->quantite  *= $poids;
          } else {
          	$poids_ok = 0;
          	$_prise->quantite = 0;
          }
        }
    	}
				
			// Stockage du nombre de ligne de medicaments
		  if(!@array_key_exists($key_tab, $prescription->_lines[$type][$this->_id])){
		    if($_prise->nb_tous_les && $_prise->calculDatesPrise($date) || !$_prise->nb_tous_les){
		    	if($name_cat){
		    		@$prescription->_nb_produit_by_cat[$name_cat]++;
          } else {
		    	  @$prescription->_nb_produit_by_cat["med"]++;	
		    	}
		    }
			}
			if($_prise->moment_unitaire_id && !($_prise->nb_tous_les && $_prise->unite_tous_les)){
				$dateTimePrise = mbAddDateTime(mbTime($_prise->_ref_moment->heure), $date);
				if(($this->_fin_reelle > $dateTimePrise || !$this->_fin_reelle) && $poids_ok){
					if($_prise->_ref_moment->heure){
	 	  	    @$prescription->_list_prises[$type][$date][$this->_id][$_prise->unite_prise][substr($_prise->_ref_moment->heure, 0, 2)] += $_prise->quantite;
					}
	 	  	  @$prescription->_list_prises[$type][$date][$this->_id]["total"] += $_prise->quantite;
				}
			}
	 	  if($_prise->moment_unitaire_id && ($_prise->nb_tous_les && $_prise->unite_tous_les) && $_prise->calculDatesPrise($date)){
	 	  	$dateTimePrise = mbAddDateTime(mbTime($_prise->_ref_moment->heure), $date);
	 	  	if($this->_fin_reelle > $dateTimePrise && $poids_ok){
	 	  	  if($_prise->_ref_moment->heure){ 
	 	  		  @$prescription->_list_prises[$type][$date][$this->_id][$_prise->unite_prise][substr($_prise->_ref_moment->heure, 0, 2)] += $_prise->quantite;	
	 	  	  }
	 	  		@$prescription->_list_prises[$type][$date][$this->_id]["total"] += $_prise->quantite;
	 	  	}
	 	  }
	 	  
	 	  if(!$_prise->moment_unitaire_id && ($_prise->nb_tous_les || $_prise->nb_fois)){
	 	  	if($_prise->_unite == "jour"){
	 	  		if($_prise->nb_fois){
	 	  		  @$prescription->_list_prises[$type][$date][$this->_id]["total"] += $_prise->quantite * $_prise->nb_fois;
	 	  		}
	 	  		if($_prise->nb_tous_les && $_prise->calculDatesPrise($date)){
	 	  		  @$prescription->_list_prises[$type][$date][$this->_id]["total"] += $_prise->quantite;
	 	  		}
	 	  	}
	 	  }
	 	  if ($_prise->nb_tous_les && $_prise->unite_tous_les && !$_prise->calculDatesPrise($date)){
	 	  	continue;
	 	  }
	 	  if($name_chap && $name_cat){
	 	    $prescription->_lines[$type][$name_chap][$name_cat][$this->_id][$key_tab] = $this;		
	 	  } else {
	 	    $prescription->_lines[$type][$this->_id][$key_tab] = $this;
	 	  }
		  $prescription->_prises[$type][$this->_id][$key_tab][] = $_prise;
		  $prescription->_intitule_prise[$type][$this->_id][$key_prise][$_prise->_id] = $_prise->_view;
	    
   }
  }
}

?>