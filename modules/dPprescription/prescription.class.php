<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescription extends CMbObject {
	
  // DB Table key
  var $prescription_id = null;
  
  // DB Fields
  var $praticien_id    = null;
  var $function_id     = null;
  var $group_id        = null;
  
  var $object_class    = null;
  var $object_id       = null;
  var $libelle         = null;
  var $type            = null;
  
  // Form fields
  var $_owner          = null;
  
  // Object References
  var $_ref_object     = null;
  var $_ref_patient    = null;
  var $_ref_current_praticien = null;
  var $_ref_praticien = null;
  var $_ref_function  = null;
  var $_ref_group     = null;

  
  // BackRefs
  var $_ref_prescription_lines                = null;
  var $_ref_prescription_lines_element        = null;
  var $_ref_prescription_lines_element_by_cat = null;
  var $_ref_prescription_lines_comment        = null;
  var $_ref_perfusions                        = null;
  var $_ref_lines_dmi                         = null;
  
  // Others Fields
  var $_type_sejour = null;
  var $_counts_by_chapitre = null;
  var $_counts_by_chapitre_non_signee = null;
  var $_counts_no_valide = null;
  var $_dates_dispo = null;
  var $_current_praticien_id = null;  // Praticien utilisé pour l'affichage des protocoles / favoris dans la prescription
  var $_praticiens = null;            // Tableau de praticiens prescripteur
  var $_dateTime_min = null;
  var $_dateTime_max = null;
  
  // Dossier/Feuille de soin
  var $_prises = null;
  var $_list_prises = null;
  var $_lines = null;
  var $_administrations = null;
  var $_transmissions = null;
  var $_prises_med = null;
  var $_list_prises_med = null;
  var $_ref_lines_med_for_plan = null;
  var $_ref_lines_elt_for_plan = null;
  var $_ref_perfusions_for_plan = null;
  var $_ref_injections_for_plan = null;
  
  var $_scores = null; // Tableau de stockage des scores de la prescription 
  var $_score_prescription = null; // Score de la prescription, 0:ok, 1:alerte, 2:grave
  var $_alertes = null;
  
  var $_nb_produit_by_cat = null;
  var $_nb_produit_by_chap = null;
 
  var $_date_plan_soin = null;
  var $_type_alerte = null;
  var $_chapitre = null;
  
  static $images = array("med"      => "modules/soins/images/medicaments.png",
											   "inj"      => "images/icons/anesth.png",
											   "perf"     => "modules/soins/images/perfusion.png",
                         "anapath"  => "modules/soins/images/microscope.png",
                         "biologie" => "images/icons/labo.png",
                         "imagerie" => "modules/soins/images/radio.png",
                         "consult"  => "modules/soins/images/stethoscope.png",
                         "kine"     => "modules/soins/images/bequille.png",
                         "soin"     => "modules/soins/images/infirmiere.png",
                         "dm"       => "modules/soins/images/pansement.png",
                         "dmi"      => "modules/soins/images/dmi.png");
                          
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription';
    $spec->key   = 'prescription_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line_medicament"]     = "CPrescriptionLineMedicament prescription_id";
    $backRefs["prescription_line_element"]        = "CPrescriptionLineElement prescription_id";
    $backRefs["prescription_line_comment"]        = "CPrescriptionLineComment prescription_id";
    $backRefs["prescription_protocole_pack_item"] = "CPrescriptionProtocolePackItem prescription_id";
    $backRefs["perfusion"]                        = "CPerfusion prescription_id";
		$backRefs["protocoles_op_chir"]               = "CProtocole protocole_prescription_chir_id";
		$backRefs["protocoles_op_anesth"]             = "CProtocole protocole_prescription_anesth_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["praticien_id"]  = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";  
    $specs["group_id"]      = "ref class|CGroups";
    $specs["object_id"]     = "ref class|CCodable meta|object_class";
    $specs["object_class"]  = "enum notNull list|CSejour|CConsultation";
    $specs["libelle"]       = "str";
    $specs["type"]          = "enum notNull list|traitement|pre_admission|sejour|sortie|externe";
    $specs["_type_sejour"]  = "enum notNull list|pre_admission|sejour|sortie";
    $specs["_dateTime_min"] = "dateTime";
    $specs["_dateTime_max"] = "dateTime";
    $specs["_owner"]        = "enum list|prat|func|group";
    $specs["_score_prescription"] = "enum list|0|1|2";
    $specs["_date_plan_soin"] = "date";
    $specs["_type_alerte"] = "enum list|hors_livret|interaction|allergie|profil|IPC";
    $specs["_chapitres"] = "enum list|med|inj|perf|anapath|biologie|consult|dmi|imagerie|kine|soin|dm";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    if(!$this->object_id){
      $this->_view = "Protocole: ".$this->libelle;
    } else {
	    $this->_view = "Prescription du Dr ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
	    if($this->libelle){
	    	$this->_view .= "($this->libelle)";
	    }
    }
    $this->loadRefCurrentPraticien();
  }
  
  function check() {  	
    if ($msg = parent::check()) {
  	  return $msg;
  	}
 
  	// Test permettant d'eviter que plusieurs prescriptions identiques soient créées 
  	if($this->object_id !== null && $this->object_class !== null && $this->praticien_id !== null && $this->type !== null){
      $prescription = new CPrescription();
      $prescription->object_id = $this->object_id;
      $prescription->object_class = $this->object_class;
      if($prescription->type !== "externe"){
        $prescription->praticien_id = $this->praticien_id;
      }
      $prescription->type = $this->type;
  	  $prescription->loadMatchingObject();
  	  
  	  if($prescription->_id){
  	  	return "Prescription déjà existante";
  	  }	
  	}	
  }
  
  
  function applyDateProtocole(&$_line, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, $date_operation, 
                              $hour_operation, $operation, $sejour, $mode_preview){
    global $AppUI;
		$_line->loadRefsPrises();
		if(!$mode_preview){
		  $_line->_id = "";
		}
    $_line->unite_duree = "jour";
    $_line->debut = "";
		
    // Calcul de la date d'entree
	  switch($_line->jour_decalage){
	  	case 'E': 
	  	  $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->_entree;
	  	  break;
	  	case 'I': 
	  	  $date_debut = "";
		  	$time_debut = "";
	  	  if($date_operation){
	  	    $date_debut = mbDate($date_operation);
	  	    $time_debut = mbTime($date_operation); 
	  	  } else {
		  	  if($operation->_id){
		  	    $date_debut = $operation->_ref_plageop->date; 
	  	      $time_debut = $hour_operation;
		  	  }
	  	  }
	  	  break;
	  	case 'S': $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->_sortie; break;
	  	case 'N': $date_debut = mbDate(); break;
	  }
	  
	  
    // Calcul de la date de sortie
	  switch($_line->jour_decalage_fin){
	  	case 'I': 
	  	  $date_fin = "";
	  	  $time_fin = "";
	  	  if($date_operation){
	  	    $date_fin = mbDate($date_operation);
	  	    $time_fin = mbTime($date_operation); 
	  	  } else {
		  	  if($operation->_id){
		  	    $date_fin = $operation->_ref_plageop->date;
		  	    $time_fin = $hour_operation;
		  	  }
	  	  }
	  	  break;
	  	case 'S': $date_fin = ($fin_sejour) ? $fin_sejour : $sejour->_sortie; break;
	  }
	  
	  $unite_decalage_debut = $_line->unite_decalage === "heure" ? "HOURS" : "DAYS";
	  $unite_decalage_fin   = $_line->unite_decalage_fin === "heure" ? "HOURS" : "DAYS";

	  if(!$_line->jour_decalage){
	    $date_debut = $date_sel;  
	  }
	  
	  // Decalage de la fin
	  if($_line->decalage_line_fin){
	    $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
	  	if($unite_decalage_fin === "DAYS"){
	      $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_fin);	
	  	} else {
	  	  //$_line->time_fin = mbTime("$signe_fin $_line->decalage_line_fin HOURS", $time_fin);   
	  	  $date_time_fin = mbDateTime("$signe_fin $_line->decalage_line_fin HOURS", "$date_fin $time_fin");
	  	  $date_fin = mbDate($date_time_fin);
	  	  $time_fin = mbTime($date_time_fin);
	  	  $_line->time_fin = $time_fin;
	  	}
	  }

	  // Decalage du debut
    if($_line->decalage_line){
      $signe = ($_line->decalage_line >= 0) ? "+" : "";
		  if($unite_decalage_debut === "DAYS"){ 
	      $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut);	
		  } else {
		    //$_line->debut = $date_debut;
		    //$_line->time_debut = mbTime("$signe $_line->decalage_line HOURS", $time_debut);	  
        $date_time_debut = mbDateTime("$signe $_line->decalage_line HOURS", "$date_debut $time_debut");
	  	  $_line->debut = mbDate($date_time_debut);
	  	  $_line->time_debut = mbTime($date_time_debut);
		  }
    } else {
	    $_line->debut = mbDate($date_debut);
    	if($time_debut){
	      $_line->time_debut = $time_debut;
	    }
    }


	  // Calcul de la duree
	  if($_line->jour_decalage_fin){
	  	$_line->duree = mbDaysRelative($_line->debut, $date_fin);
	  	if($_line->jour_decalage_fin !== "S"){
	  	  $_line->duree++;
	  	}
	  }	  
	  
	  // Permet d'eviter les durees negatives lors de l'application d'un protocole
	  if($_line->duree < 0){
	    $_line->duree = 0;
	  }
	  

	  $_line->prescription_id = $this->_id;
	  $_line->praticien_id = $praticien_id;
	  $_line->creator_id = $AppUI->user_id;
	  
	  if(!$mode_preview){
		  if($_line->jour_decalage === "I" || $_line->jour_decalage_fin === "I"){
		    if($operation_id){
		      $_line->operation_id = $operation_id;
		    } else {
		      $_line->debut = "";
		      $_line->duree = "";
		      $_line->time_debut = "";
		      $_line->time_fin = "";
		    }
		  }

	    $msg = $_line->store();
	    $AppUI->displayMsg($msg, "{$_line->_class_name}-msg-create");  
	  }
	 	
		// Parcours des prises
    if(!$mode_preview){
			foreach($_line->_ref_prises as $prise){
			  $prise->_id = "";
				$prise->object_id = $_line->_id;
				$prise->object_class = $_line->_class_name;
				if($prise->decalage_intervention != null){
					if($date_operation){
	  	      $time_operation = mbTime($date_operation); 
	  	    } elseif ($operation->_id) {
		  	    $time_operation = $hour_operation;
		  	  }
	  	    $signe_decalage_intervention = ($prise->decalage_intervention >= 0) ? "+" : "";
				  $prise->heure_prise = mbTime("$signe_decalage_intervention $prise->decalage_intervention HOURS", $time_operation);	  
				}
			  if(!$mode_preview){
				  $msg = $prise->store();
			    $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");  
			  }	
			}	  
    }
  }
  
  // Permet d'appliquer un protocole à une prescription
  function applyProtocole($protocole_id, $praticien_id, $date_sel, $operation_id, $debut_sejour="", $fin_sejour="", $date_operation="") {
    global $AppUI;
    
    // Mode utilisé pour visualiser le protocole au moment de sa création
    $mode_preview = ($debut_sejour && $fin_sejour);
    
    // Chargement du protocole
    $protocole = new CPrescription();
    $protocole->load($protocole_id);
    
    // Creation d'un protocole de pré-admission s'il n'y en a pas
    if (!$mode_preview && $this->object_class === 'CSejour') {
      $this->loadRefObject();
	    $this->_ref_object->loadRefsPrescriptions();
	    if (!$this->_ref_object->_ref_prescriptions["pre_admission"]->_id) {
	      $prescription = new CPrescription;
	      $prescription->object_class = "CSejour";
	      $prescription->object_id = $this->object_id;
	      $prescription->type = "pre_admission";
	      $prescription->store();
	      $this->_ref_object->_ref_prescriptions["pre_admission"] = $prescription;
	    }
    }
	
	  // Chargement des lignes de medicaments, d'elements et de commentaires
	  $protocole->loadRefsLinesMed();
	  $protocole->loadRefsLinesElementByCat();
	  $protocole->loadRefsLinesAllComments();
	  // Chargement des perfusions et des lignes associées
    $protocole->loadRefsPerfusions();
    foreach($protocole->_ref_perfusions as &$_perfusion){
      $_perfusion->loadRefsLines(); 
    }
    
	  $operation = new COperation();
	  $hour_operation = "";
  	$sejour = new CSejour();
	    
	  if($operation_id){
  		// Chargement de l'operation
  		$operation->load($operation_id);
  		$operation->loadRefPlageOp();
			if($operation->_id){
  		  $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
  		}
		  if($this->_ref_object->_class_name === "CSejour"){
		  	$sejour =& $this->_ref_object;
	    }
	  }
	
	  // Parcours des lignes de medicaments
		foreach($protocole->_ref_prescription_lines as &$_line_med){	    
		  if(!$mode_preview){
		    // Chargement des lignes de substitutions de la ligne de protocole
		    $_line_med->loadRefsSubstitutionLines();
			  $_substitutions = $_line_med->_ref_substitution_lines;
		  }
		  
		  // Creation et modification de la ligne en fonction des dates
	    $this->applyDateProtocole($_line_med, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
		                            $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
		                            
		  if(!$mode_preview){
			  // Creation d'une nouvelle ligne de substitution qui pointe vers la ligne qui vient d'etre crée
			  foreach($_substitutions as &$_line_subst){
			    $_line_subst->substitute_for = $_line_med->_id;
			    $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
		                                $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
		    }
		  }  
		}
		
		// Parcours des lignes d'elements
		foreach($protocole->_ref_prescription_lines_element_by_cat as &$elements_by_chap){
		  foreach($elements_by_chap as &$elements_by_cat){
		    foreach($elements_by_cat as &$_lines){
		      foreach($_lines as $_line_elt){
		        $this->applyDateProtocole($_line_elt, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
		                                  $date_operation, $hour_operation, $operation, $sejour, $mode_preview);
		      } 
		    }
		  }
		}
	
		// Parcours des perfusions
	  foreach($protocole->_ref_perfusions as &$_perfusion){
	    $_perfusion->loadRefPraticien();
	    $_perfusion->_id = "";
	    $_perfusion->prescription_id = $this->_id;
	    $_perfusion->praticien_id = $praticien_id;
	    $_perfusion->creator_id = $AppUI->user_id;
	    
	    if($_perfusion->decalage_interv != null){
	      if(!$mode_preview){
				  if($operation_id){
				    $_perfusion->operation_id = $operation_id;
				  } else {
				    $_perfusion->date_debut = "";
				    $_perfusion->time_debut = "";
				  }
				}
			 	
	      if($date_operation){
	  	    $date_debut = mbDate($date_operation);
	  	    $time_debut = mbTime($date_operation); 
	  	  } else {
		  	  if($operation->_id){
		  	    $date_debut = $operation->_ref_plageop->date; 
	  	      $time_debut = $hour_operation;
		  	  }
	  	  }
	  	  if($_perfusion->decalage_interv == ""){
	  	    $_perfusion->decalage_interv = 0;
	  	  }
	  	  $signe = ($_perfusion->decalage_interv >= 0) ? "+" : "";
	      $date_time_debut = mbDateTime("$signe $_perfusion->decalage_interv HOURS", "$date_debut $time_debut");
	  	  $_perfusion->date_debut = mbDate($date_time_debut);
	  	  $_perfusion->time_debut = mbTime($date_time_debut);
	    }
	    
	    if(!$mode_preview){
	      $msg = $_perfusion->store();
	      $AppUI->displayMsg($msg, "CPerfusion-msg-create");
	    }
	    foreach($_perfusion->_ref_lines as $_line){
	      $_line->_id = "";
	      $_line->perfusion_id = $_perfusion->_id;
	      if(!$mode_preview){
	        $msg = $_line->store();
			    $AppUI->displayMsg($msg, "CPerfusionLine-msg-create");
	      }
	    }
	  }
  
  
		// Parcours des lignes de commentaires
		foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
			$line_comment->_id = "";
			$line_comment->prescription_id = $this->_id;
			$line_comment->praticien_id = $praticien_id;
			$line_comment->creator_id = $AppUI->user_id;
			if(!$mode_preview){
			  $msg = $line_comment->store();
			  $AppUI->displayMsg($msg, "CPrescriptionLineComment-msg-create");
			}
		}
	
	  if($mode_preview){
	    return $protocole;
	  }
  }
  
  /*
   * Permet d'applique un protocole ou un pack à partir d'un identifiant (pack-$id ou prot-$id)
   */
  function applyPackOrProtocole($pack_protocole_id, $praticien_id, $date_sel, $operation_id){
    // Aplication du protocole/pack chir
    if($pack_protocole_id){
      $pack_protocole = explode("-", $pack_protocole_id);
      $pack_id = ($pack_protocole[0] === "pack") ? $pack_protocole[1] : "";
      $protocole_id = ($pack_protocole[0] === "prot") ? $pack_protocole[1] : "";
      if($pack_id){
        $pack = new CPrescriptionProtocolePack();
			  $pack->load($pack_id);
			  $pack->loadRefsPackItems();
			  foreach($pack->_ref_protocole_pack_items as $_pack_item){
			    $_pack_item->loadRefPrescription();
			    $_protocole =& $_pack_item->_ref_prescription;
			    $this->applyProtocole($_protocole->_id, $praticien_id, $date_sel, $operation_id);
			  }
      }
      if($protocole_id){
        $this->applyProtocole($protocole_id, $praticien_id, $date_sel, $operation_id);
      }
    }
  }
  
  
  /*
   * Calcul du praticien_id responsable de la prescription
   */
  function calculPraticienId(){
  	global $AppUI;
  	
  	if ($this->object_id !== null && $this->object_class !== null && $this->type !== null && $this->object_id){
  		// Chargement de l'object
  		$object = new $this->object_class;
  	  $object->load($this->object_id);
  	  $object->loadRefsFwd();
  		
  		if($this->type !== "sejour"){
  		  $this->praticien_id = $AppUI->_ref_user->isPraticien() ? $AppUI->_ref_user->_id : $object->_praticien_id;
  		}
  		else {
  			$this->praticien_id = $object->_praticien_id;
  		}
  	}
  }
  
  
  function store(){  	
  	if(!$this->_id){
  		$this->calculPraticienId(); 
  	}
    return parent::store();
  }
  
  static function loadAllProtocolesFor($praticien_id = null, $function_id = null, $group_id = null, $object_class = null, $type = null) {
    $_protocoles = array();
    $protocoles = array(
      "prat"  => array(), 
      "func"  => array(),
      "group" => array()
    );
    
    if($praticien_id){
      $praticien = new CMediusers;
      $praticien->load($praticien_id);
      $function_id = $praticien->function_id;
    }
    if($function_id){
      $function = new CFunctions();
      $function->load($function_id);
      $group_id = $function->group_id;  
    }
    
    // Clauses de recherche
    $protocole = new CPrescription();
    $where = array();
    $where["object_id"] = "IS NULL";
    
    if ($object_class) {  
  		$where["object_class"] = "= '$object_class'";
    }
    if ($type) {
  		$where["type"] = "= '$type'";
    }
    
    $order = "object_class, type, libelle";

		// Protocoles du praticien
    if($praticien_id){
      $where["function_id"]  = "IS NULL";
      $where["group_id"]     = "IS NULL";
      $where["praticien_id"] = "= '$praticien_id'";
      $_protocoles["prat"]    = $protocole->loadlist($where, $order);
    }
    
		// Protocoles du cabinet
    if($function_id){
	 	  $where["praticien_id"] = "IS NULL";
	 	  $where["group_id"]     = "IS NULL";
      $where["function_id"]  = "= '$function_id'";
      $_protocoles["func"]    = $protocole->loadlist($where, $order);
    }
    
    // Protocoles de l'etablissement
    if($group_id){
      $where["function_id"]  = "IS NULL";
      $where["praticien_id"] = "IS NULL";
      $where["group_id"]     = "= '$group_id'";
      $_protocoles["group"]   = $protocole->loadlist($where, $order);
    }
    
    if ($object_class) {
      // Classement de tous les protocoles de classe object_class
      foreach($_protocoles as $type => $protocoles_by_type){
        foreach($protocoles_by_type as $protocole_id => $_protocole){
          $protocoles[$type][$_protocole->_id] = $_protocole;
        }
      }
    }
    else {
      // Classement de tous les protocoles par object_class
      foreach($_protocoles as $type => $protocoles_by_type){
        foreach($protocoles_by_type as $protocole_id => $_protocole){
          $protocoles[$type][$_protocole->object_class][$_protocole->_id] = $_protocole;
        }
      }
    }
		return $protocoles;
  }
  /*
   * Chargement du praticien
   */
  function loadRefPraticien() {
  	$this->_ref_praticien = new CMediusers();
  	$this->_ref_praticien = $this->_ref_praticien->getCached($this->praticien_id);
  }

  function loadRefFunction() {
  	$this->_ref_function = new CFunctions();
  	$this->_ref_function = $this->_ref_function->getCached($this->function_id);
  }
  
  function loadRefGroup() {
  	$this->_ref_group = new CGroups();
  	$this->_ref_group = $this->_ref_group->getCached($this->group_id);
  }
  
  /*
   * Chargement des perfusions
   */
  function loadRefsPerfusions($with_child = 0, $emplacement = ""){
    //$this->_ref_perfusions = $this->loadBackRefs("perfusion");
    $perfusion = new CPerfusion();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    if($with_child != 1){
      $where["next_perf_id"] = "IS NULL";
    }
    if($emplacement){
      $where[] = "emplacement = '$emplacement' OR emplacement = 'service_bloc'";
    }
    $this->_ref_perfusions = $perfusion->loadList($where);
  }
  
  /*
   * Chargement du praticien utilisé pour l'affichage des protocoles/favoris
   */
  function loadRefCurrentPraticien() {
    if ($this->_ref_current_praticien) {
      return;
    }

    global $AppUI;
  	if ($AppUI->_ref_user->isPraticien()) {
  	  $this->_ref_current_praticien = $AppUI->_ref_user;
  	}
    else {
    	$this->_ref_object->loadRefPraticien();
    	$this->_ref_current_praticien = $this->_ref_object->_ref_praticien;
    }

    $this->_current_praticien_id = $this->_ref_current_praticien->_id;
  }
  
  /*
   * Chargement de l'objet de la prescription
   */ 
  function loadRefObject(){
  	$this->_ref_object = new $this->object_class;
    $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
  
  /*
   * Chargement du patient
   */
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->patient_id);	
  }
  
  /*
   * Forward Refs
   */
  function loadRefsFwd() {
    $this->loadRefPraticien();
    $this->loadRefObject();
    $this->loadRefPatient();
  }
  
  /*
   * Compte le nombre de lignes non validées dans la prescription
   */ 
  function countNoValideLines(){
    $this->_counts_no_valide = 0;
    if($this->_id){
      $line = new CPrescriptionLineMedicament();
      $where = array();
      $where["signee"] = " = '0'";
      $where["prescription_id"] = " = '$this->_id'";
      $where["child_id"] = "IS NULL";
      $where["substitution_line_id"] = "IS NULL";
      $this->_counts_no_valide = $line->countList($where);
    }
  }
  
  
	/*
	 * Chargement du nombre des medicaments et d'elements
	 */
  function countLinesMedsElements($praticien_sortie_id = null){
    $this->_counts_by_chapitre_non_signee = array();
  	$this->_counts_by_chapitre = array();
  	
  	$line_comment_med = new CPrescriptionLineComment();
  	$ljoin_comment["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	
  	// Count sur les medicaments
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    $where["category_prescription.chapitre"] = "IS NULL";
    
  	$line_med = new CPrescriptionLineMedicament();
  	$whereMed["prescription_id"] = " = '$this->_id'";
  	$whereMed["child_id"] = "IS NULL";
  	$whereMed["substitution_line_id"] = "IS NULL";
  	$whereMed["substitution_active"] = " = '1'";
  	if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  		$whereMed["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	$this->_counts_by_chapitre["med"] = $line_med->countList($whereMed);
  	$this->_counts_by_chapitre["med"] += $line_comment_med->countList($where, null, null, null, $ljoin_comment);
  	
  	$whereMed["signee"] = " = '0'";
  	$where["signee"]  =" = '0'";
  	$this->_counts_by_chapitre_non_signee["med"] = $line_med->countList($whereMed);
  	$this->_counts_by_chapitre_non_signee["med"] += $line_comment_med->countList($where, null, null, null, $ljoin_comment);
  	
  	
  	$perfusion_line  = new CPerfusionLine();
  	$ljoinPerf["perfusion"] = "perfusion_line.perfusion_id = perfusion.perfusion_id";
  	$wherePerf["perfusion.prescription_id"] = " = '$this->_id'";
  	$wherePerf["perfusion.next_perf_id"] = " IS NULL";
  	$this->_counts_by_chapitre["med"] += $perfusion_line->countList($wherePerf, null, null, null, $ljoinPerf);
  	$wherePerf["signature_prat"] = " = '0'";
  	$this->_counts_by_chapitre_non_signee["med"] += $perfusion_line->countList($wherePerf, null, null, null, $ljoinPerf);
  	
  	
  	// Count sur les elements
  	$ljoin_element["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	  $ljoin_element["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
	  
  	$line_element = new CPrescriptionLineElement();
  	$line_comment = new CPrescriptionLineComment();
		
  	$category = new CCategoryPrescription;
    $chapitres = explode("|", $category->_specs["chapitre"]->list);
      	
  	// Initialisation du tableau
    foreach ($chapitres as $chapitre){
    	$this->_counts_by_chapitre[$chapitre] = 0;
    }
  	
    // Parcours des elements
 	  $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    if($praticien_sortie_id){
  		$where["praticien_id"] = " = '$praticien_sortie_id'";
  	}
  	
    foreach ($chapitres as $chapitre) {
  	  $where["category_prescription.chapitre"] = " = '$chapitre'";
   	  $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
			$nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
			$this->_counts_by_chapitre[$chapitre] = $nb_element + $nb_comment;
  	}
  	
  	$where["signee"] = " = '0'";
  	foreach ($chapitres as $chapitre) {
  	  $where["category_prescription.chapitre"] = " = '$chapitre'";
   	  $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
			$nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
			$this->_counts_by_chapitre_non_signee[$chapitre] = $nb_element + $nb_comment;
  	}
  }
  
  
  function getPraticiens(){
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT DISTINCT prescription_line_medicament.praticien_id
						FROM prescription_line_medicament
						WHERE prescription_line_medicament.prescription_id = '$this->_id'";
    $praticiens_med = $ds->loadList($sql);
    
    $sql = "SELECT DISTINCT prescription_line_element.praticien_id
						FROM prescription_line_element
						WHERE prescription_line_element.prescription_id = '$this->_id'";
    $praticiens_elt = $ds->loadList($sql);
    
    $sql = "SELECT DISTINCT prescription_line_comment.praticien_id
						FROM prescription_line_comment
						WHERE prescription_line_comment.prescription_id = '$this->_id'";
    $praticiens_comment = $ds->loadList($sql);

    $sql = "SELECT DISTINCT perfusion.praticien_id
						FROM perfusion
						WHERE perfusion.prescription_id = '$this->_id'";
    $praticiens_perf = $ds->loadList($sql);
    
    foreach($praticiens_med as $_prats_med){
      foreach($_prats_med as $_prat_med_id){
        if(!isset($this->_praticiens[$_prat_med_id])){
          $praticien = new CMediusers();
          $praticien->load($_prat_med_id);
          $this->_praticiens[$praticien->_id] = $praticien->_view;
        }
      }
    }
    foreach($praticiens_elt as $_prats_elt){
      foreach($_prats_elt as $_prat_elt_id){
        if(!isset($this->_praticiens[$_prat_elt_id])){
          $praticien = new CMediusers();
          $praticien->load($_prat_elt_id);
          $this->_praticiens[$praticien->_id] = $praticien->_view;
        }
      }
    }
    foreach($praticiens_comment as $_prats_comment){
      foreach($_prats_comment as $_prat_comment_id){
        if(!isset($this->_praticiens[$_prat_comment_id])){
          $praticien = new CMediusers();
          $praticien->load($_prat_comment_id);
          $this->_praticiens[$praticien->_id] = $praticien->_view;
        }
      }
    }
    foreach($praticiens_perf as $_prats_perf){
      foreach($_prats_perf as $_prat_perf_id){
        if(!isset($this->_praticiens[$_prat_perf_id])){
          $praticien = new CMediusers();
          $praticien->load($_prat_perf_id);
          $this->_praticiens[$praticien->_id] = $praticien->_view;
        }
      }
    }
  }
  
  /*
   * Chargement de l'historique
   */
  function loadRefsLinesHistorique(){
  	$this->loadRefObject();
  	$historique = array();
  	$this->_ref_object->loadRefsPrescriptions();
  	if($this->type === "sejour" || $this->type === "sortie"){
  		$prescription_pre_adm =& $this->_ref_object->_ref_prescriptions["pre_admission"];
  		$prescription_pre_adm->loadRefsLinesMedComments("0");
  		$prescription_pre_adm->loadRefsLinesElementsComments("0");
  		$historique["pre_admission"] = $prescription_pre_adm;
  		
  	  if($this->type === "sortie"){
        $prescription_sejour =& $this->_ref_object->_ref_prescriptions["sejour"];
        $prescription_sejour->loadRefsLinesMedComments("0");
        $prescription_sejour->loadRefsLinesElementsComments("0");
        $historique["sejour"] = $prescription_sejour;
      }
  	}

  	return $historique;
  }
  
  /*
   * Chargement des lignes de prescription de médicament
   */
  function loadRefsLinesMed($with_child = 0, $with_subst = 0, $emplacement="") {
    $line = new CPrescriptionLineMedicament();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    
    if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
    if($with_subst != "1"){
      $where["substitution_line_id"] = "IS NULL";
    }
    if($emplacement){
      $where[] = "emplacement = '$emplacement' OR emplacement = 'service_bloc'";
    }
    // Permet de ne pas afficher les lignes de substitutions
    $where["substitution_active"] = " = '1'";
    
    $order = "prescription_line_medicament_id DESC";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
  }
  
  
  /*
   * Chargement des lignes de prescription de médicament par catégorie ATC
   */
  
  function loadRefsLinesMedByCat($with_child = 0, $with_subst = 0, $emplacement = "") {
    $this->loadRefsLinesMed($with_child, $with_subst, $emplacement);
  	$this->_ref_prescription_lines_by_cat = array();
    foreach($this->_ref_prescription_lines as &$_line){
    	$_line->_ref_produit->loadClasseATC();
    	$this->_ref_prescription_lines_by_cat[$_line->_ref_produit->_ref_ATC_2_code][$_line->_id] = $_line;
    }
    foreach($this->_ref_prescription_lines as &$_line){
    	$_line->_ref_produit->loadClasseATC();
    	$this->_ref_prescription_lines_by_cat[$_line->_ref_produit->_ref_ATC_2_code][$_line->_id] = $_line;
    }
  }
  
  /*
   * Chargement des lignes de medicaments (medicaments + commentaires)
   */
  function loadRefsLinesMedComments($withRefs = "1"){
    // Chargement des lignes de medicaments
  	$this->loadRefsLinesMed();
  	// Chargement des lignes de commentaire du medicament
  	$this->loadRefsLinesComment("medicament");
  	
  	// Initialisation du tableau de fusion
  	$this->_ref_lines_med_comments["med"] = array();
  	$this->_ref_lines_med_comments["comment"] = array();
  	
  	foreach($this->_ref_prescription_lines as &$line_med){
  		if($withRefs){
  			$line_med->loadRefsPrises();
  		  //$this->_praticiens[$line_med->praticien_id] = $line_med->_ref_praticien->_view;
  		}
  		$this->_ref_lines_med_comments["med"][] = $line_med;
  	}
  	
  	if(isset($this->_ref_prescription_lines_comment["medicament"][""]["comment"])){
      foreach($this->_ref_prescription_lines_comment["medicament"][""]["comment"] as &$comment_med){
  	  	if($withRefs){
  	  	  //$this->_praticiens[$comment_med->praticien_id] = $comment_med->_ref_praticien->_view;
  	  	}
  	  	$this->_ref_lines_med_comments["comment"][] = $comment_med;
      }
  	}
  }
  
  /*
   * Chargement des lignes d'element
   */
  function loadRefsLinesElement($chapitre = "", $withRefs = "1", $emplacement=""){
  	$line = new CPrescriptionLineElement();
  	$where = array();
  	$ljoin = array();
  	
  	if($chapitre){
  	  $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
	    $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = '$chapitre'";
  	}
  	
    $where["prescription_id"] = " = '$this->_id'";
    
    if($emplacement){
      $where[] = "emplacement = '$emplacement' OR emplacement = 'service_bloc'";
    }
    
    $order = "prescription_line_element_id DESC";
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    
    foreach ($this->_ref_prescription_lines_element as &$line_element){	
    	$line_element->loadRefElement();
    	if($withRefs){
	    	$line_element->loadRefsPrises();
	    	$line_element->loadRefExecutant();
	    }
	    //$this->_praticiens[$line_element->praticien_id] = $line_element->_ref_praticien->_view;
    	$line_element->_ref_element_prescription->loadRefCategory();
    	
    }
  }
  
  
  /*
   * Chargement des lignes d'elements par catégorie
   */
  function loadRefsLinesElementByCat($withRefs = "1", $chapitre = "", $emplacement=""){
  	$this->loadRefsLinesElement($chapitre, $withRefs, $emplacement);
  	$this->_ref_prescription_lines_element_by_cat = array();
  	
  	foreach($this->_ref_prescription_lines_element as $line){
  	  $line->_ref_element_prescription->loadRefCategory();
  	  $category =& $line->_ref_element_prescription->_ref_category_prescription;
  		$this->_ref_prescription_lines_element_by_cat[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
  		$this->_ref_lines_elements_comments[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
  	}

  	ksort($this->_ref_prescription_lines_element_by_cat);
  }
  
  
  /*
   * Chargement des lignes de commentaires
   */
  function loadRefsLinesComment($chapitre = null, $withRefs = "1"){
  	$this->_ref_prescription_lines_comment = array();
    
  	// Initialisation des tableaux
  	$category = new CCategoryPrescription();
  	
  	foreach($category->_specs["chapitre"]->_list as $_chapitre){
  	  $this->_ref_prescription_lines_comment[$_chapitre] = array();	
  	}

  	$commentaires = array();
  	$line_comment = new CPrescriptionLineComment();
  	
  	$where["prescription_id"] = " = '$this->_id'";
  	$order = "prescription_line_comment_id DESC";
  	$ljoin = array();
  	
  	if ($chapitre && $chapitre !== "medicament"){
  		$ljoin["category_prescription"] = "prescription_line_comment.category_prescription_id = category_prescription.category_prescription_id";
  	  $where["category_prescription.chapitre"] = " = '$chapitre'"; 	
  	}
  	if ($chapitre === "medicament"){
  	  $where["category_prescription_id"] = " IS NULL"; 		
  	}
  	
  	$commentaires = $line_comment->loadList($where, $order, null, null, $ljoin);
  	
  	foreach($commentaires as $_line_comment){
  	  if ($withRefs){
          $_line_comment->loadRefExecutant();
  	  }
      if($_line_comment->category_prescription_id){
  	  	// Chargement de la categorie
          $_line_comment->loadRefCategory();
  	  	$cat = new CCategoryPrescription();
  	  	$cat->load($_line_comment->category_prescription_id);
  	  	$chapitre = $cat->chapitre;
  	  } else {
  	  	$chapitre = "medicament";
  	  }
      $this->_ref_prescription_lines_comment[$chapitre]["$_line_comment->category_prescription_id"]["comment"][$_line_comment->_id] = $_line_comment;
      $this->_ref_lines_elements_comments[$chapitre]["$_line_comment->category_prescription_id"]["comment"][$_line_comment->_id] = $_line_comment;
      //$this->_praticiens[$_line_comment->praticien_id] = $_line_comment->_ref_praticien->_view;
    }
  }
  
  /*
   * Chargement de toutes les lignes (y compris medicaments)
   */
  function loadRefsLinesAllComments(){
  	$this->_ref_prescription_lines_all_comments = $this->loadBackRefs("prescription_line_comment");
  }
  
  /*
   * Chargement des lignes d'elements (Elements + commentaires)
   */
  function loadRefsLinesElementsComments($withRefs = "1", $chapitre=""){
  	$this->loadRefsLinesElementByCat($withRefs, $chapitre);
  	$this->loadRefsLinesComment("",$withRefs);
  	
  	// Suppression des ligne de medicaments
  	unset($this->_ref_lines_elements_comments["medicament"]);  	
  	ksort($this->_ref_prescription_lines_element_by_cat);

  	// Initialisation des tableaux
		if(count($this->_ref_lines_elements_comments)){
	  	foreach($this->_ref_lines_elements_comments as &$chapitre){
	  		foreach($chapitre as &$cat){
		    	if(!isset($cat["comment"])){
		    		$cat["comment"] = array();
		    	}
		      if(!isset($cat["element"])){
		    		$cat["element"] = array();
		    	}
	  		}
	    }
		}
  }
  
  /*
   * Chargement des lignes de DMI
   */
  function loadRefsLinesDMI(){
    $line_dmi = new CPrescriptionLineDMI();
    $line_dmi->prescription_id = $this->_id;
    $this->_ref_lines_dmi = $line_dmi->loadMatchingList();
  }
 
  /*
   * Chargement des medicaments favoris d'un praticien
   */
  static function getFavorisMedPraticien($praticien_id){
  	$favoris = array();
  	$listFavoris = array();
  	$favoris = CBcbProduit::getFavoris($praticien_id);
  	foreach($favoris as $_fav){
  		$produit = new CBcbProduit();
  		$produit->load($_fav["code_cip"],"0");
  		$listFavoris[$produit->libelle] = $produit;
    }
    ksort($listFavoris);
  	return $listFavoris;
  }
  
  /*
   * Chargement des injectables favoris du praticien
   */
  static function getFavorisInjectablePraticien($praticien_id){
  	$favoris_inj = array();
  	$listFavoris = array();
  	$favoris_inj = CBcbProduit::getFavorisInjectable($praticien_id);
  	foreach($favoris_inj as $_fav_inj){
  		$produit = new CBcbProduit();
  		$produit->load($_fav_inj["code_cip"],"0");
  		$listFavoris[$produit->libelle] = $produit;
    }
    ksort($listFavoris);
  	return $listFavoris;
  }
  
  /*
   * Chargement des favoris de prescription pour un praticien donné
   */
  static function getFavorisPraticien($praticien_id){
  	$listFavoris["medicament"] = CPrescription::getFavorisMedPraticien($praticien_id);
  	$listFavoris["injectable"] = CPrescription::getFavorisInjectablePraticien($praticien_id);
  	
  	$category = new CCategoryPrescription();
  	
    foreach($category->_specs["chapitre"]->_list as $chapitre){
  	  $listFavoris[$chapitre] = array();
  	  $favoris[$chapitre] = CElementPrescription::getFavoris($praticien_id, $chapitre);	  
    }
    
	  foreach($favoris as $key => $typeFavoris) {
	  	foreach($typeFavoris as $curr_fav){
	  		$element = new CElementPrescription();
	  	  $element->load($curr_fav["element_prescription_id"]);
	  		$listFavoris[$key][] = $element;
      }
	  }
	  return $listFavoris;  	
  }
  

  /*
   * Controle des allergies
   */
  function checkAllergies($listAllergies, $code_cip) {
    if(!isset($this->_scores["allergie"])){
      $this->_scores["allergie"] = array();
    }
    if(!isset($this->_alertes["allergie"])){
      $this->_alertes["allergie"] = array();
    }
    $niveau_max = 0;
    foreach($listAllergies as $key => $all) {
      if($all->CIP == $code_cip) {
        $this->_alertes["allergie"][$code_cip][$key]["libelle"] = $all->LibelleAllergie;
      }
      $this->_scores["allergie"][$all->CIP] = $all;
    }
  }
  
  /*
   * Controle des interactions
   */
  function checkInteractions($listInteractions, $code_cip) {
    if(!isset($this->_scores["interaction"])){
      $this->_scores["interaction"] = array();
    }
    if(!isset($this->_alertes["interaction"])){
      $this->_alertes["interaction"] = array();
    }
    
    $niveau_max = 0;
    foreach($listInteractions as $key => $int) {
      if($int->CIP1 == $code_cip || $int->CIP2 == $code_cip) {
        @$this->_alertes["interaction"][$int->CIP1][$key]["libelle"] = $int->Type;
        @$this->_alertes["interaction"][$int->CIP1][$key]["niveau"] = $int->Niveau;
        @$this->_alertes["interaction"][$int->CIP2][$key]["libelle"] = $int->Type;
        @$this->_alertes["interaction"][$int->CIP2][$key]["niveau"] = $int->Niveau;
        @$this->_scores["interaction"]["niv$int->Niveau"]++;
      }
      $niveau_max = max($int->Niveau, $niveau_max);
    }
    if(count($this->_scores["interaction"])){
      $this->_scores["interaction"]["niveau_max"] = $niveau_max;
    }
  }
  
  /*
   * Controle des IPC
   */
  function checkIPC($listIPC, $code_cip) {
    if(!isset($this->_scores["IPC"])){
      $this->_scores["IPC"] = 0;
    }
    @$this->_alertes["IPC"] = array();
  }
  
  /*
   * Controle du profil du patient
   */
  function checkProfil($listProfil, $code_cip) {
    if(!isset($this->_scores["profil"])){
      $this->_scores["profil"] = array();
    }
    if(!isset($this->_alertes["profil"])){
      $this->_alertes["profil"] = array();
    }

    $niveau_max = 0;
    foreach($listProfil as $key => $profil) {
      if($profil->CIP == $code_cip) {
        @$this->_alertes["profil"][$code_cip][$key]["libelle"] = $profil->LibelleMot;   
        @$this->_alertes["profil"][$code_cip][$key]["niveau"] = $profil->Niveau;   
        @$this->_scores["profil"]["niv$profil->Niveau"]++;
      }
      $niveau_max = max($profil->Niveau, $niveau_max);
    }
    if(count($this->_scores["profil"])){
      $this->_scores["profil"]["niveau_max"] = $niveau_max;
    }
  }
  
  /*
   * Génération du Dossier/Feuille de soin
   */
  function calculPlanSoin($date, $mode_feuille_soin = 0, $mode_semainier = 0, $mode_dispensation = 0, $code_cip = "", $with_calcul = true){  
    // Stockage du tableau de ligne de medicaments
  	$lines["medicament"] = $this->_ref_prescription_lines;
    if($this->object_id && !$mode_dispensation){
      if(isset($this->_ref_object->_ref_prescription_traitement)){
  	    $lines["traitement"] = $this->_ref_object->_ref_prescription_traitement->_ref_prescription_lines;
      }
    }
    
  	// Parcours des lignes
    foreach($lines as $cat_name => $lines_cat){
		  if(count($lines_cat)){
		    foreach($lines_cat as &$_line_med){
		      
		      // Code Cip
		      if($code_cip && ($code_cip != $_line_med->code_cip)){
		        continue;
		      }
		      
		    	// On met a jour la date de fin de la ligne de medicament si celle ci n'est pas indiquée
				  if(!$_line_med->_fin_reelle){
				    $_line_med->_fin_reelle = $_line_med->_ref_prescription->_ref_object->_sortie;
				  }
		  	    	
			    if($with_calcul){
			      // Chargement des administrations
			      $_line_med->calculAdministrations($date, $mode_feuille_soin, $mode_dispensation);
			    }
					// Si aucune prise
           $_line_med->_ref_produit->loadClasseATC();
           $_line_med->_ref_produit->loadRefsFichesATC();
           $code_ATC = $_line_med->_ref_produit->_ref_ATC_2_code;
					
          if(($date >= $_line_med->debut && $date <= mbDate($_line_med->_fin_reelle))){     
            if ((count($_line_med->_ref_prises) < 1) && (!isset($this->_lines["med"][$code_ATC][$_line_med->_id]["aucune_prise"]))){
						  if($_line_med->_is_injectable){
						    $this->_ref_injections_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;  
						  } else { 
 	              $this->_ref_lines_med_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;
						  }
 	            continue;
						}
					  // Chargement des prises
					  $_line_med->calculPrises($this, $date, $mode_feuille_soin, null, null, $with_calcul);
           }
					// Stockage d'une ligne possedant des administrations ne faisant pas reference à une prise ou unite de prise
					if(!$mode_feuille_soin){
					  if(isset($_line_med->_administrations['aucune_prise']) && count($_line_med->_ref_prises) >= 1){
					    if($_line_med->_is_injectable){
					      $this->_ref_injections_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;
					    } else {
					      $this->_ref_lines_med_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;   
					    }
					  }
					}

				  // Suppression des prises prevues replanifiées
				  if($_line_med->_quantity_by_date && $with_calcul){
				    foreach($_line_med->_quantity_by_date as $_type => $quantity_by_date){
				      foreach($quantity_by_date as $_date => $quantity_by_hour){
				        if(!isset($_line_med->_quantity_by_date[$_type][$_date]['total'])){
				          $_line_med->_quantity_by_date[$_type][$_date]['total'] = 0;
						    }
						    if(isset($quantity_by_hour['quantites'])){
					        foreach($quantity_by_hour['quantites'] as $_hour => $quantity){
					          $heure_reelle = @$quantity[0]["heure_reelle"];
	
					          // Recherche d'une planification correspondant à cette prise prevue
	                  $planification = new CAdministration();
	                  if(is_numeric($_type)){
		                  $planification->prise_id = $_type;
		                } else {
		                  $planification->unite_prise = $_type;
		                }
		                $planification->original_dateTime = "$_date $heure_reelle:00:00";
		                $planification->object_id = $_line_med->_id;
		                $planification->object_class = "CPrescriptionLineMedicament";
		                $count_planifications = $planification->countMatchingList();
		                if($count_planifications){
		                  $_line_med->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
		                  $_line_med->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total_disp'] = 0;
		                }
		                if($mode_semainier){
							        $_line_med->_quantity_by_date[$_type][$_date]['total'] += $_line_med->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'];
							        $_line_med->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
		                }
					        }
						    }
				      }
				    }
				  }
		    }
		  }
    }
     
    if(!$mode_dispensation){
			// Parcours des elements
	    if($this->_ref_prescription_lines_element_by_cat){
				foreach($this->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
				  foreach($elements_chap as $name_cat => $elements_cat){
				    // Initialisation du compteur de lignes
				    foreach($elements_cat as &$_elements){
				 	    foreach($_elements as &$_line_element){
				 	      
				      	  // Chargement des administrations et des transmissions
				      	  if($with_calcul){
				      	    $_line_element->calculAdministrations($date, $mode_feuille_soin);
				      	  }
				      	  
		      	    	if($name_chap == "imagerie" || $name_chap == "consult"){
		                if(($_line_element->debut == $date) && $_line_element->time_debut){
								  	  $time_debut = substr($_line_element->time_debut, 0, 2);
								  	  @$_line_element->_quantity_by_date["aucune_prise"][$_line_element->debut]['quantites'][$time_debut]['total'] = 1;
								  	  @$_line_element->_quantity_by_date["aucune_prise"][$_line_element->debut]['quantites'][$time_debut]['total_disp'] = 1;
								  	  
								  	  @$_line_element->_quantity_by_date["aucune_prise"][$_line_element->debut]['quantites'][$time_debut][] = array("quantite" => 1, "heure_reelle" => $time_debut);
		      	    	  }
		      	    	}
				 	    	  
				      	  // Si aucune prise  
				      	  if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_fin_reelle))){
						        if ((count($_line_element->_ref_prises) < 1) && (!isset($this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))){
						          $this->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
						          if($name_chap != "imagerie" && $name_chap != "consult"){
						            continue;
						          }
								    }
							        // Chargement des prises
							        $_line_element->calculPrises($this, $date, $mode_feuille_soin, $name_chap, $name_cat, $with_calcul);
							      }
							    // Stockage d'une ligne possedant des administrations ne faisant pas reference à une prise ou unite de prise
							    if(!$mode_feuille_soin){
							      if(isset($_line_element->_administrations['aucune_prise']) && count($_line_element->_ref_prises) >= 1){
								      $this->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
							      }
							    }
				 	    
	 			 	      // Suppression des prises prevues replanifiées
				 	    	if($_line_element->_quantity_by_date && $with_calcul){
							    foreach($_line_element->_quantity_by_date as $_type => $_quantity_by_date){
							      foreach($_quantity_by_date as $_date => $_quantity_by_hour){
							        if(!isset($_line_element->_quantity_by_date[$_type][$_date]['total'])){
							          $_line_element->_quantity_by_date[$_type][$_date]['total'] = 0;
									    }
									    if(isset($_quantity_by_hour['quantites'])){
								        foreach($_quantity_by_hour['quantites'] as $_hour => $_quantity){
													$heure_reelle = @$_quantity[0]["heure_reelle"];
		
				                  // Recherche d'une planification correspondant à cette prise prevue
				                  $planification = new CAdministration();
				                  if(is_numeric($_type)){
					                  $planification->prise_id = $_type;
					                } else {
					                  $planification->unite_prise = $_type;
					                }
					                $planification->original_dateTime = "$_date $heure_reelle:00:00";
					                $planification->object_id = $_line_element->_id;
					                $planification->object_class = "CPrescriptionLineElement";
					                $count_planifications = $planification->countMatchingList();

					                if($count_planifications){
					                  $_line_element->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
					                }
								        	if($mode_semainier){
										        $_line_element->_quantity_by_date[$_type][$_date]['total'] += $_line_element->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'];
										        $_line_element->_quantity_by_date[$_type][$_date]['quantites'][$_hour]['total'] = 0;
					                }
								        }
									    }
							      }
							    }
							  }
					    }
					  }
				  }
		    }
	    }
    }
    if(!$mode_dispensation){
	    // Parcours des perfusions
	    if($this->_ref_perfusions){
	      foreach($this->_ref_perfusions as &$_perfusion){
	        if(($date >= mbDate($_perfusion->_debut)) && ($date <= mbDate($_perfusion->_fin))){
	          $this->_ref_perfusions_for_plan[$_perfusion->_id] = $_perfusion;
	        }
	      }
	    }
    }
  }
}

?>