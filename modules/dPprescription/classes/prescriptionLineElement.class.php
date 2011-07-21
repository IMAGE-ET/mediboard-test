<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineElement extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_element_id = null;
  
  // DB Fields
  var $element_prescription_id        = null;
  var $executant_prescription_line_id = null; 
  var $user_executant_id              = null;
  
	var $ide_domicile = null;
	var $cip_dm = null;
	var $quantite_dm = null;
	var $commentaire = null;
	
  // Object references
  var $_ref_element_prescription      = null;
  var $_ref_executant                 = null;
  var $_executant                     = null;
  
	var $_ref_dm = null;
	var $_ref_task = null;
	
	var $_chapitre = null;
	var $_unite_prise = null;
	var $_most_used_poso = null;
  var $_delete_prises = null;
	
  // Can fields
  var $_can_select_executant               = null;
  var $_can_delete_line                    = null;
  var $_can_view_signature_praticien       = null;
  var $_can_view_form_signature_praticien  = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_view_form_ald                  = null;
	var $_can_view_form_ide                  = null;
  var $_can_view_form_conditionnel         = null;
  var $_can_modify_poso                    = null;
  var $_can_modify_comment                 = null;
  var $_can_modify_dates                   = null; 
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_element';
    $spec->key   = 'prescription_line_element_id';
    $spec->events = array(
      "prescription"   => array(
        "multiple"   => false, 
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
      "signature" => array(
        "multiple"   => false,
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
      "administration" => array(
        "multiple"   => true,
        "reference1" => array("CSejour",  "prescription_id.object_id"),
        "reference2" => array("CPatient", "prescription_id.object_id.patient_id"),
      ),
    );
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["element_prescription_id"]        = "ref notNull class|CElementPrescription";
    $specs["executant_prescription_line_id"] = "ref class|CExecutantPrescriptionLine";
    $specs["user_executant_id"]              = "ref class|CMediusers";
		$specs["ide_domicile"]                   = "bool default|0";
		$specs["cip_dm"]                         = "numchar length|7";
    $specs["quantite_dm"]                    = "float";
    $specs["commentaire"]                    = "text helped|element_prescription_id";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["parent_line"]     = "CPrescriptionLineElement child_id";  
    $backProps["transmissions"]   = "CTransmissionMedicale object_id";
    $backProps["administration"]  = "CAdministration object_id";
    $backProps["prise_posologie"] = "CPrisePosologie object_id";
    $backProps["planifications"]  = "CPlanificationSysteme object_id";
		$backProps["evenements_ssr"]  = "CEvenementSSR prescription_line_element_id";
		$backProps["task"]            = "CSejourTask prescription_line_element_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefElement();
    $this->_ref_element_prescription->loadRefCategory();
    $this->_view = $this->_ref_element_prescription->_view;
    
    $chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
    $this->_chapitre = $chapitre;
		
    // Un element ne peut jamais être un traitement
		if($chapitre){
		  $this->_unite_prise = CAppUI::conf("dPprescription CCategoryPrescription $chapitre unite_prise");
    }
    $this->_duree_prise = "";
    
    if($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    } else {
	    if($this->debut && (!$this->_fin || (mbDate($this->debut) == mbDate($this->_fin)))){
	      $this->_duree_prise .= "le ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    } else {
		    if($this->duree && $this->_fin){
		    	$this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y")." pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
		    }
	    }
    }
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
    // Calcul de la date de fin de la ligne

		// Si l'unite de duree est l'heure
    if($this->unite_duree == "heure" || $this->unite_duree == "minute"){
      $_unite = ($this->unite_duree == "heure") ? "HOURS" : "MINUTES";
      $this->_fin_reelle = mbDateTime("+ $this->duree $_unite", $this->_debut_reel);
    } else {
      $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "$this->debut 23:59:00";    
    }
		
		if($this->date_arret){
    	$this->_fin_reelle = $this->date_arret;
      $this->_fin_reelle .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
    if($chapitre == "imagerie" || $chapitre == "consult"){
      $this->_debut_reel = "$this->debut 00:00:00";
      $this->_fin_reelle = "$this->debut 23:59:59";
    }
  }
  
  function updateLongView(){
    $this->loadRefsPrises();
    $this->_long_view = "$this->_view, "; 
    foreach($this->_ref_prises as $_poso){
      $this->_long_view .= "$_poso->_view, ";
    }
    $this->_long_view .= $this->_duree_prise;
  }
  
  function updateDBFields(){
    parent::updateDBFields();
    if($this->_executant !== null){
      if($this->_executant == ""){
        $this->executant_prescription_line_id = "";
        $this->user_executant_id = "";
      } else {
	      $explode_executant = explode("-", $this->_executant);
	      if($explode_executant[0] === "CExecutantPrescriptionLine"){
	        $this->executant_prescription_line_id = $explode_executant[1];
	      } else {
	        $this->user_executant_id = $explode_executant[1];
	      }
      }
    }
  }
  
  function countLockedPlanif(){
    $administration = new CAdministration();
    $ljoin["planification_systeme"] = "planification_systeme.planification_systeme_id = administration.planification_systeme_id";
    $ljoin["prescription_line_element"] = "prescription_line_element.prescription_line_element_id = planification_systeme.object_id AND planification_systeme.object_class = 'CPrescriptionLineElement'";
    
    // Chargement des planifications sur la ligne
    $planification = new CPlanificationSysteme();
    $where = array();
    $where["prescription_line_element.prescription_line_element_id"] = " = '$this->_id'";
    $this->_count_locked_planif = $count_planifications = $administration->countList($where, null, null, null, $ljoin);
  }
	
  function store(){
    $mode_creation = !$this->_id;
		
    $calcul_planif = ($this->fieldModified("debut") || 
                      $this->fieldModified("time_debut") || 
                      $this->fieldModified("duree") || 
                      $this->fieldModified("unite_duree")|| 
                      $this->fieldModified("time_fin") ||
											$this->fieldModified("inscription") ||
											$this->fieldModified("date_arret") ||
											$this->_update_planif_systeme) ? true : false;
    
		// Lors du passage d'une inscription à une prescription, on modifie l'unité de prises des adm deja créées
    if($this->fieldModified("inscription", "0")){
			// Chargment du chapitre de l'element
			$this->completeField("element_prescription_id");
      $this->loadRefElement();
      $chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;

			// Chargement de la 1ere prise
      $this->loadRefsPrises();
			
			$unite_prise = "";
			$prise_id = "";
			
			// S'il une prise a été créé, on recupere la premiere
			if(count($this->_ref_prises)){
				$first_prise = reset($this->_ref_prises);

        if($first_prise->moment_unitaire_id){
        	$unite_prise = $chapitre;
        } else {
        	$prise_id = $first_prise->prise_posologie_id;
        }
      } 
			// Sinon, stockage du chapitre comme unite de prise
			else {
      	//$unite_prise = $chapitre;
      }
			
      $this->loadRefsAdministrations();
      foreach($this->_ref_administrations as $_administration){
      	if($unite_prise){
      	  $_administration->unite_prise = $unite_prise;       
        }
				if($prise_id){
				  $_administration->prise_id = $prise_id;
        }
      	if($msg = $_administration->store()){
          return $msg;
        }
      }
    }
		
    if($msg = parent::store()){
  		return $msg;
  	}
    
		if($calcul_planif && $this->_ref_prescription->type == "sejour"){
		  $this->countLockedPlanif();
			if($this->_count_locked_planif == 0 && !$this->inscription){
		    $this->removePlanifSysteme();
		    $this->calculPlanifSysteme();	
			}
	  }
		
  	// On met en session le dernier guid créé
    if($mode_creation){
      $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
			
			// Pre-remplissage de la posologie la plus utilisée
	    if($this->_most_used_poso){
	      $posos = $this->getMostUsedPoso();
	      if(count($posos)){
	        $this->applyPoso(reset($posos));
	      }
	    }
    }
		
		if($this->_delete_prises){
      if($msg = $this->deletePrises()){
        return $msg;
      }
    }
		
	}
		
  function loadView() {
    $this->loadRefsPrises();
    $this->loadRefsTransmissions();
    $this->loadRefPraticien();
    $this->loadRefPrescription();
		$prescription =& $this->_ref_prescription;
		$prescription->loadRefPatient();
    $prescription->loadRefObject();
  }
  
  /*
   * Vue modifiée en fonction de la présence de prises
   */
  function loadCompleteView(){
  	$this->loadRefsPrises();
  	// Si la ligne comportent des prises ==> "à partir du"
  	if(count($this->_ref_prises)){
  		$this->_duree_prise = "";
	    if($this->fin){
	    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
	    } else {
        if($this->debut){
          $this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
        }
	    	if($this->duree){
	    		$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
	    	} 	
	    } 
  	}
  }
  
  function loadRefDM(){
    $this->_ref_dm = CBcbProduit::get($this->cip_dm);
  }
	
  function canEdit(){
    $nb_hours = CAppUI::conf("dPprescription CPrescription max_time_modif_suivi_soins");
    $datetime_max = mbDateTime("+ $nb_hours HOURS", "$this->debut $this->time_debut");
    return $this->_canEdit = (mbDateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->praticien_id);
  }
	
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $mode_protocole = 0, $mode_pharma = 0, $operation_id = 0) {	
		global $can;
    
    $current_user = CMediusers::get();

    // Cas d'une ligne de protocole  
    if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
        $is_praticien = CAppUI::$user->isPraticien();
        $perm_edit = (!$is_praticien || ($is_praticien && CAppUI::$user->_id == $protocole->praticien_id)) ? 1 : 0;
        
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {
			
			$chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
			
      $perm_edit = $can->admin || ((!$this->signee) &&
                 ($this->praticien_id == $current_user->user_id || $operation_id || $is_praticien || 
								 ($current_user->isExecutantPrescription() && CAppUI::conf("dPprescription CPrescription droits_infirmiers_$chapitre") && !CAppUI::conf("dPprescription CPrescription role_propre"))));
    }
    
    $this->_perm_edit = $perm_edit;
    
    // Modification des dates
    if($perm_edit){
    	$this->_can_modify_dates = 1;
    }
    // View ALD
    if($perm_edit){
    	$this->_can_view_form_ald = 1;
			$this->_can_view_form_ide = 1;
    }
    // View Conditionnel
    if($perm_edit){
    	$this->_can_view_form_conditionnel = 1;
    }
    // View signature praticien
    if(!$this->_protocole){
    	$this->_can_view_signature_praticien = 1;
    }
		
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && ($is_praticien || $this->praticien_id == $current_user->_id)){
    	$this->_can_view_form_signature_praticien = 1;
    }

    // Suppression de la ligne
    if ($perm_edit) {
  	  $this->_can_delete_line = 1;
  	}
  	// Modification de la posologie
  	if ($perm_edit){
    	$this->_can_modify_poso = 1;
    }
    // Modification de l'executant et du commentaire
    if ($perm_edit){
    	$this->_can_select_executant = 1;
    	$this->_can_modify_comment = 1;
    } 
	}
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefElement();
    $this->loadRefExecutant();
  }
  
  /*
   * Chargement de l'element
   */
  function loadRefElement() {
    if (!$this->_ref_element_prescription) {
	  	$element = new CElementPrescription();
	  	$this->_ref_element_prescription = $element->getCached($this->element_prescription_id);	
    }
  }
  
  /*
   * Chargement de l'executant
   */
  function loadRefExecutant(){
    if (!$this->_ref_executant) {
      if($this->executant_prescription_line_id){
		    $executant = new CExecutantPrescriptionLine();
		    $this->_ref_executant = $executant->getCached($this->executant_prescription_line_id);
      }
      if($this->user_executant_id){
        $user = new CMediusers();
        $this->_ref_executant = $user->getCached($this->user_executant_id);
      }
    }
  }
	
	function loadRefTask() {
		$this->_ref_task = $this->loadUniqueBackRef("task");
	}
	
	function applyPoso($poso){ 
    // Chargement d'une ligne possedant la poso la plus utilisée
    $line_element = new CPrescriptionLineElement();
    $line_element->load($poso['prescription_line_element_id']);
    $line_element->loadRefsPrises();
    
    // Pre-remplissage des prises les plus utilisées
    foreach($line_element->_ref_prises as $_prise){
      if(!$_prise->urgence_datetime && !$_prise->decalage_intervention){
        $_prise->_id = '';
        $_prise->object_id = $this->_id;
        $_prise->object_class = $this->_class_name;
        $_prise->_ref_object = null;
        $_prise->store();
      }
    }
  }
	
	  /*
   * Chargement des 5 posos les plus utilisées
   */
  function loadMostUsedPoso($element_prescription_id = "", $praticien_id = "", $type = ""){
    $temp_view = array();
    $this->_most_used_poso = array();
    $most_used_lines = $this->getMostUsedPoso($element_prescription_id, $praticien_id, $type);
		
    foreach($most_used_lines as $_key => $_line){
      if(is_array($_line)){
        $view = "";
        $line = new CPrescriptionLineElement();
        $line->load($_line['prescription_line_element_id']);
        $line->loadRefsPrises();
        $last_prise = end($line->_ref_prises);
        foreach($line->_ref_prises as $_prise){
          $view .= $_prise->_view;
          if($_prise->_id != $last_prise->_id){
            $view .= ", ";
          }
        }
        
        if(!isset($temp_view[$view])){
          $temp_view[$view] = array("occ" => "", "line_id" => "");
        }
        $temp_view[$view]["occ"] += $_line["count_signature"];
        $temp_view[$view]["line_id"] = $_line['prescription_line_element_id'];
      }
    }
		
    foreach($temp_view as $curr_view => $_tab){
      $this->_most_used_poso[$_tab["line_id"]]["view"] = $curr_view;
      $this->_most_used_poso[$_tab["line_id"]]["occ"] = $_tab["occ"];
      $pourcentage = $_tab["occ"] ? (100 * $_tab["occ"] / $most_used_lines['total']) : "0"; 
      $this->_most_used_poso[$_tab["line_id"]]["pourcentage"] = round($pourcentage, 2);
    }
  }
  

  /*
   * Recuperation des posologies les plus utilisées
   */
  function getMostUsedPoso($element_prescription_id = "", $praticien_id = "", $type = ""){
    $ds = CSQLDataSource::get("std");
   
	  $_element_prescription_id = $element_prescription_id ? $element_prescription_id : $this->element_prescription_id;
    $_praticien_id = $praticien_id ? $praticien_id : $this->praticien_id;
    $_type = $type ? $type : $this->_ref_prescription->type;
    
    $sql = "CREATE TEMPORARY TABLE posos AS
              SELECT prescription_line_element.prescription_line_element_id, prise_posologie.*
              FROM prise_posologie
              LEFT JOIN prescription_line_element ON prescription_line_element.prescription_line_element_id = prise_posologie.object_id AND prise_posologie.object_class = 'CPrescriptionLineElement'
              LEFT JOIN prescription ON prescription.prescription_id = prescription_line_element.prescription_id
              WHERE prescription_line_element.element_prescription_id = '$_element_prescription_id'";
							
              if($_praticien_id != 'global'){
               $sql .= "AND prescription_line_element.praticien_id = '$_praticien_id'";
              }
              
							$sql .= "AND prescription.type = '$_type'
              AND prise_posologie.decalage_intervention IS NULL
              AND prise_posologie.urgence_datetime IS NULL
              ORDER BY moment_unitaire_id;";
    $ds->exec($sql);

    $sql = "CREATE TEMPORARY TABLE signatures AS
              SELECT posos.prescription_line_element_id, CONVERT(GROUP_CONCAT(CONCAT_WS('-',quantite, nb_fois, unite_fois, nb_tous_les, unite_tous_les, decalage_prise, unite_prise, decalage_intervention, heure_prise, moment_unitaire_id ) SEPARATOR '|') USING latin1) as signature
              FROM posos
              LEFT JOIN prescription_line_element ON prescription_line_element.prescription_line_element_id = posos.prescription_line_element_id
              GROUP BY prescription_line_element_id";
    $ds->exec($sql);
				
    $sql = "SELECT signature, prescription_line_element_id, count(*) as count_signature 
            FROM signatures
            GROUP BY signature
            ORDER BY count_signature DESC
            LIMIT 5;";
    $signatures = $ds->loadList($sql);
    	
    $sql = "SELECT count(*) FROM signatures";
    $signatures["total"] = $ds->loadResult($sql);
  
    $sql = "DROP TABLE posos";
    $ds->exec($sql);
    
    $sql = "DROP TABLE signatures";
    $ds->exec($sql);
    return $signatures;
  }
}

?>