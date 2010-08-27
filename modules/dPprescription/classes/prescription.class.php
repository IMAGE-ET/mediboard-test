<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

	var $fast_access    = null;
	
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
  var $_ref_prescription_line_mixes                        = null;
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
  var $_ref_prescription_line_mixes_for_plan = null;
	var $_ref_prescription_line_mixes_for_plan_by_type = null;
  
  var $_ref_injections_for_plan = null;
  var $_ref_prescription_line_mixes_by_type = null;
	
  var $_scores = null; // Tableau de stockage des scores de la prescription 
  var $_score_prescription = null; // Score de la prescription, 0:ok, 1:alerte, 2:grave
  var $_alertes = null;
  
  var $_nb_produit_by_cat = null;
  var $_nb_produit_by_chap = null;
 
  var $_date_plan_soin = null;
  var $_type_alerte = null;
  var $_chapitre = null;
  var $_ref_selected_prat = null;
  
  var $_chapter_view = null;
  var $_purge_planifs_systemes = null;
	var $_chapitres = null;
	var $_count_recent_modif_presc = null;
	
	var $_ref_prescription_lines_by_cat = null;
	
	
	static $cache_service = null;
  static $images = array(
		"med"      => "modules/soins/images/medicaments.png",
		"inj"      => "images/icons/anesth.png",
		"perfusion"=> "modules/soins/images/perfusion.png",
		"aerosol"  => "modules/soins/images/aerosol.png",
		"anapath"  => "modules/soins/images/microscope.png",
		"biologie" => "images/icons/labo.png",
		"imagerie" => "modules/soins/images/radio.png",
		"consult"  => "modules/soins/images/stethoscope.png",
		"kine"     => "modules/soins/images/bequille.png",
		"soin"     => "modules/soins/images/infirmiere.png",
		"dm"       => "modules/soins/images/pansement.png",
		"dmi"      => "modules/soins/images/dmi.png",
	);
                          
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription';
    $spec->key   = 'prescription_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["prescription_line_medicament"]     = "CPrescriptionLineMedicament prescription_id";
    $backProps["prescription_line_element"]        = "CPrescriptionLineElement prescription_id";
    $backProps["prescription_line_comment"]        = "CPrescriptionLineComment prescription_id";
    $backProps["prescription_protocole_pack_item"] = "CPrescriptionProtocolePackItem prescription_id";
    $backProps["prescription_line_mix"]            = "CPrescriptionLineMix prescription_id";
    $backProps["protocoles_op_chir"]               = "CProtocole protocole_prescription_chir_id";
    $backProps["protocoles_op_anesth"]             = "CProtocole protocole_prescription_anesth_id";
    $backProps["prescription_line_dmi"]            = "CPrescriptionLineDMI prescription_id";
    return $backProps;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["praticien_id"]  = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";  
    $specs["group_id"]      = "ref class|CGroups";
    $specs["object_id"]     = "ref class|CMbObject meta|object_class purgeable";
    $specs["object_class"]  = "enum notNull list|CSejour|CConsultation|CDossierMedical";
    $specs["libelle"]       = "str";
    $specs["type"]          = "enum notNull list|traitement|pre_admission|sejour|sortie|externe";
    $specs["fast_access"]   = "bool default|0";
		$specs["_type_sejour"]  = "enum notNull list|pre_admission|sejour|sortie";
    $specs["_dateTime_min"] = "dateTime";
    $specs["_dateTime_max"] = "dateTime";
    $specs["_owner"]        = "enum list|prat|func|group";
    $specs["_score_prescription"] = "enum list|0|1|2";
    $specs["_date_plan_soin"] = "date";
    $specs["_type_alerte"] = "enum list|hors_livret|interaction|allergie|profil|IPC";
    $specs["_chapitres"] = "enum list|med|inj|perfusion|oxygene|alimentation|aerosol|anapath|biologie|consult|dmi|imagerie|kine|soin|dm";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    
    if($this->object_class != "CDossierMedical"){
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
  }
  
  function updateChapterView(){
    // Initialisation du tableau par chapitre
    foreach($this->_specs["_chapitres"]->_list as $_chapitre){
      $this->_chapter_view[$_chapitre] = "";
    }
    unset($this->_chapter_view["inj"]);
    
    // Chargement des medicaments et des commentaires
    $this->loadRefsLinesMedComments();
    if($this->_ref_lines_med_comments){
      foreach($this->_ref_lines_med_comments as $lines){
        foreach($lines as $_line){
          if($_line instanceOf CPrescriptionLineMedicament){
            $_line->updateLongView();
            $this->_chapter_view["med"] .= "$_line->_long_view<br />";
          } else {
            $this->_chapter_view["med"] .= "$_line->_view<br />";
          }
        }
      }
    }
    
    // Chargement des elements
    $this->loadRefsLinesElementsComments();
    if($this->_ref_lines_elements_comments){
      foreach($this->_ref_lines_elements_comments as $chapitre => $_lines_by_cat){
        foreach($_lines_by_cat as $_lines){
          foreach($_lines["element"] as $_line_element){
            $_line_element->updateLongView();
            $this->_chapter_view[$chapitre] .= "$_line_element->_long_view<br />";
          }
          foreach($_lines["comment"] as $_line_comment){
            $this->_chapter_view[$chapitre] .= "$_line_comment->_view<br />";
          }
        }
      }
    }
    
    // Chargement des prescription_line_mixes
    $this->loadRefsPrescriptionLineMixes();
    if(count($this->_ref_prescription_line_mixes)){
      foreach($this->_ref_prescription_line_mixes as $_perf){
        $this->_chapter_view["perf"] = "$_perf->_view: ";
        $_perf->loadRefsLines();
        foreach($_perf->_ref_lines as $_perf_line){
          $this->_chapter_view["perf"] .= "$_perf_line->_view, ";
        }
        $this->_chapter_view["perf"] .= "<br />";
      }
    }
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
                              $hour_operation, $operation, $sejour){
    global $AppUI;
        
		if($_line->_class_name == "CPrescriptionLineMix"){
      $_line->loadRefsLines();
    // Gestion des prescription_line_mixes
      $_line->loadRefPraticien();
      $_line->_id = "";
      $_line->prescription_id = $this->_id;
      $_line->praticien_id = $praticien_id;
      $_line->creator_id = $AppUI->user_id;
      
      if(!$_line->decalage_interv){
        $_line->decalage_interv = 0;
      }
      
      if($operation_id){
        $_line->operation_id = $operation_id;
      } else {
        $_line->date_debut = "";
        $_line->time_debut = "";
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
      
      if($_line->jour_decalage == "I"){
        $signe = ($_line->decalage_interv >= 0) ? "+" : "";
        $date_time_debut = mbDateTime("$signe $_line->decalage_interv HOURS", "$date_debut $time_debut");
        $_line->date_debut = mbDate($date_time_debut);
        $_line->time_debut = mbTime($date_time_debut);
      } else {
        $_line->date_debut = mbDate();
        $_line->time_debut = mbTime();
      }
      
      
      $msg = $_line->store();
      CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-create");
      
      foreach($_line->_ref_lines as $_line_perf){
        $_line_perf->_id = "";
        $_line_perf->prescription_line_mix_id = $_line->_id;
        $msg = $_line_perf->store();
        CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");
      } 
    } else {
      $_line->loadRefsPrises();
      $_line->_id = "";
      $_line->unite_duree = "jour";
      $_line->debut = "";
      $time_debut = "";
              
      // Calcul de la date d'entree
      switch($_line->jour_decalage){
        case 'E': 
          $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->_entree;
          break;
        case 'I': 
          $date_debut = "";
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
      
      $date_fin = "";
      $time_fin = "";
              
      // Calcul de la date de sortie
      switch($_line->jour_decalage_fin){
        case 'I': 
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
      	if($date_debut){
          $_line->debut = mbDate($date_debut);
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
			
			// Cas specifique des prescriptions aux urgences dans le suivi de soins      
			if($this->_ref_object instanceof CSejour && $this->_ref_object->type == "urg" && CAppUI::conf("dPprescription CPrescription prescription_suivi_soins")){
	      if($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix){
	      	return;
	      }
				$_line->debut = mbDate();
	      $_line->time_debut = mbTime();  
				$_line->duree = "";
      }
    
      $msg = $_line->store();
      CAppUI::displayMsg($msg, "{$_line->_class_name}-msg-create");  

      // Parcours des prises
      foreach($_line->_ref_prises as $prise){
        $prise->_id = "";
        $prise->object_id = $_line->_id;
        $prise->object_class = $_line->_class_name;
        if($prise->decalage_intervention != null){
          $time_operation = "";
          if($date_operation){
            $time_operation = mbTime($date_operation); 
          } elseif ($operation->_id) {
            $time_operation = $hour_operation;
          }
          $signe_decalage_intervention = ($prise->decalage_intervention >= 0) ? "+" : "";
          if($time_operation){
            $prise->heure_prise = mbTime("$signe_decalage_intervention $prise->decalage_intervention HOURS", $time_operation);
          }
        }
        if($prise->urgence_datetime){
          $prise->urgence_datetime = mbDateTime();
        }
        $msg = $prise->store();
        CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");  
      }   
    }
  }
  
  // Permet d'appliquer un protocole à une prescription
  function applyProtocole($protocole_id, $praticien_id, $date_sel, $operation_id, $debut_sejour="", $fin_sejour="", $date_operation="") {
    global $AppUI;
    
    // Chargement du protocole
    $protocole = new CPrescription();
    $protocole->load($protocole_id);
    
    // Creation d'un protocole de pré-admission s'il n'y en a pas
    if ($this->object_class === 'CSejour') {
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
    $protocole->loadRefsPrescriptionLineMixes();
    
		foreach($protocole->_ref_prescription_line_mixes as &$_prescription_line_mix){
      $_prescription_line_mix->loadRefsLines(); 
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
        // Chargement des lignes de substitutions de la ligne de protocole
        $_line_med->loadRefsSubstitutionLines();
        $_substitutions = $_line_med->_ref_substitution_lines;
      
      // Creation et modification de la ligne en fonction des dates
      $this->applyDateProtocole($_line_med, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                $date_operation, $hour_operation, $operation, $sejour);
                                
      // Creation d'une nouvelle ligne de substitution qui pointe vers la ligne qui vient d'etre crée
      foreach($_substitutions as $_line_subst_by_chap){
        foreach($_line_subst_by_chap as $_line_subst){
          $_line_subst->substitute_for_id = $_line_med->_id;
          $_line_subst->substitute_for_class = $_line_med->_class_name;
          $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                    $date_operation, $hour_operation, $operation, $sejour);
        }
      }
    }
    
    // Parcours des lignes d'elements
    foreach($protocole->_ref_prescription_lines_element_by_cat as &$elements_by_chap){
      foreach($elements_by_chap as &$elements_by_cat){
        foreach($elements_by_cat as &$_lines){
          foreach($_lines as $_line_elt){
            $this->applyDateProtocole($_line_elt, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                      $date_operation, $hour_operation, $operation, $sejour);
          } 
        }
      }
    }
  
    // Parcours des prescription_line_mixes
    foreach($protocole->_ref_prescription_line_mixes as &$_prescription_line_mix){
      $_prescription_line_mix->loadRefsSubstitutionLines();
      $_substitutions_perf = $_prescription_line_mix->_ref_substitution_lines;
      

      $this->applyDateProtocole($_prescription_line_mix, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                $date_operation, $hour_operation, $operation, $sejour);
      
  
      foreach($_substitutions_perf as $_line_subst_by_chap){
        foreach($_line_subst_by_chap as $_line_subst){
          $_line_subst->substitute_for_id = $_prescription_line_mix->_id;
          $_line_subst->substitute_for_class = $_prescription_line_mix->_class_name;
          $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                    $date_operation, $hour_operation, $operation, $sejour);
        }
      }                  
    }
  
    // Parcours des lignes de commentaires
    foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
      $line_comment->_id = "";
      $line_comment->prescription_id = $this->_id;
      $line_comment->praticien_id = $praticien_id;
      $line_comment->creator_id = $AppUI->user_id;
      $msg = $line_comment->store();
      CAppUI::displayMsg($msg, "CPrescriptionLineComment-msg-create");
    
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
        if($this->type != 'traitement'){
          $this->praticien_id = $AppUI->_ref_user->isPraticien() ? $AppUI->_ref_user->_id : $object->_praticien_id;
        }
      }
      else {
         $this->praticien_id = $object->_praticien_id;
      }
    }
  }
  
  
	function loadView(){
	  parent::loadView();
		
		// Chargement de toutes les lignes
    $this->loadRefsLinesMed("1","1");
    $this->loadRefsLinesElementByCat("1");
    $this->loadRefsPrescriptionLineMixes();
	}
	
	
  function store(){   
    if(!$this->_id){
      $this->calculPraticienId(); 
    }
		
		if($msg = parent::store()){
			return $msg;
		}
		
		if($this->_purge_planifs_systemes && $this->type == "sejour"){
			$this->_purge_planifs_systemes = false;
			$this->completeField("object_id");
			$this->removeAllPlanifSysteme();
			$this->calculAllPlanifSysteme();
		}
  }
  
  static function getAllProtocolesFor($praticien_id = null, $function_id = null, $group_id = null, $object_class = null, $type = null) {
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
      foreach($_protocoles as $owner => $protocoles_by_type){
        foreach($protocoles_by_type as $protocole_id => $_protocole){
          $protocoles[$owner][$_protocole->type][$_protocole->_id] = $_protocole;
        }
      }
    }
    else {
      // Classement de tous les protocoles par object_class
      foreach($_protocoles as $owner => $protocoles_by_type){
        foreach($protocoles_by_type as $protocole_id => $_protocole){
          $protocoles[$owner][$_protocole->object_class][$_protocole->type][$_protocole->_id] = $_protocole;
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
   * Chargement des prescription_line_mixes
   */
  function loadRefsPrescriptionLineMixes($chapitre = "", $with_child = 0, $with_subst_active = 1){
    if($this->_ref_prescription_line_mixes){
    	return;
    }
    $prescription_line_mix = new CPrescriptionLineMix();
		$where = array();
    $where["prescription_id"] = " = '$this->_id'";
		if($chapitre){
			$where["type_line"] = " = '$chapitre'";
		}
		if($with_child != 1){
      $where["next_line_id"] = "IS NULL";
    }
    // Permet de ne pas afficher les lignes de substitutions
    $where["substitution_active"] = " = '$with_subst_active'";
    
    $this->_ref_prescription_line_mixes = $prescription_line_mix->loadList($where);
		
		if(count($this->_ref_prescription_line_mixes)){
			foreach($this->_ref_prescription_line_mixes as $_line_mix){
			  $this->_ref_prescription_line_mixes_by_type[$_line_mix->type_line][] = $_line_mix;
		  }
		}
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
      if($this->_ref_object->_class_name == "CSejour"){
        $this->_ref_object->loadRefPraticien(1);
      } else {
        $this->_ref_object->loadRefPraticien();
      }
      $this->_ref_current_praticien = $this->_ref_object->_ref_praticien;
    }
    $this->_ref_current_praticien->loadRefFunction();
    $this->_current_praticien_id = $this->_ref_current_praticien->_id;
  }
  
  /*
   * Chargement de l'objet de la prescription
   */ 
  function loadRefObject(){
    if($this->object_class){
      $this->_ref_object = new $this->object_class;
      $this->_ref_object = $this->_ref_object->getCached($this->object_id);
    }
  }
  
  /*
   * Chargement du patient
   */
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    if($this->object_class == "CDossierMedical"){
      $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->object_id);  
    } else {
      if($this->_ref_object){
        $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->patient_id); 
      }
    }
  }
  
  /*
   * Forward Refs
   */
  function loadRefsFwd() {
    if($this->object_class != 'CDossierMedical'){
      $this->loadRefPraticien();
			$this->loadRefPatient();
    }
    $this->loadRefObject();
  }
  
  /*
   * Chargement des transmissions liées aux lignes de la prescription
   */
  function loadAllTransmissions(){
    $transmission = new CTransmissionMedicale();
    $where = array();
    $where[] = "(object_class = 'CCategoryPrescription') OR 
                (object_class = 'CPrescriptionLineElement') OR 
                (object_class = 'CPrescriptionLineMedicament') OR 
                (object_class = 'CPrescriptionLineMix') OR libelle_ATC IS NOT NULL";
    $where["sejour_id"] = " = '$this->object_id'";
    $transmissions_by_class = $transmission->loadList($where);
    
    foreach($transmissions_by_class as $_transmission){
      $_transmission->loadRefsFwd();
      if($_transmission->object_class && $_transmission->object_id){
        $this->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
      }
      if($_transmission->libelle_ATC){
        $this->_transmissions["ATC"][$_transmission->libelle_ATC][$_transmission->_id] = $_transmission;
      }
    }
  }
  
  /*
   * Calcul du nombre de produits dans la prescription, permet de calculer les rowspan
   * Les lignes doivent etre prealablement chargées
   */
  function calculNbProduit($chapitre = ""){
    $types = array("med","inj");
    foreach($types as $_type_med){
      $produits = ($_type_med == "med") ? $this->_ref_lines_med_for_plan : $this->_ref_injections_for_plan;
      if($produits){
        foreach($produits as $_code_ATC => $_cat_ATC){
          if(!isset($this->_nb_produit_by_cat[$_code_ATC])){
            $this->_nb_produit_by_cat[$_type_med][$_code_ATC] = 0;
          }
          foreach($_cat_ATC as $line_id => $_line) {
            foreach($_line as $unite_prise => $line_med){
              if(!isset($this->_nb_produit_by_chap[$_type_med])){
                $this->_nb_produit_by_chap[$_type_med] = 0;
              }
              $this->_nb_produit_by_chap[$_type_med]++;
              $this->_nb_produit_by_cat[$_type_med][$_code_ATC]++;
            }
          }
        }
      }
    }
    // Calcul du rowspan pour les elements
    if($this->_ref_lines_elt_for_plan){
      foreach($this->_ref_lines_elt_for_plan as $name_chap => $elements_chap){
        foreach($elements_chap as $name_cat => $elements_cat){
          if(!isset($this->_nb_produit_by_cat[$name_cat])){
            $this->_nb_produit_by_cat[$name_cat] = 0;
          }
          foreach($elements_cat as $_element){
            foreach($_element as $element){
              $element->loadRefLogSignee();
              if(!isset($this->_nb_produit_by_chap[$name_chap])){
                $this->_nb_produit_by_chap[$name_chap] = 0;  
              }
              $this->_nb_produit_by_chap[$name_chap]++;
              $this->_nb_produit_by_cat[$name_cat]++;
            }
          }
        }
      }     
    }
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
    
    $prescription_line_mix_item  = new CPrescriptionLineMixItem();
    $ljoinPerf["prescription_line_mix"] = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";
    $wherePerf["prescription_line_mix.prescription_id"] = " = '$this->_id'";
    $wherePerf["prescription_line_mix.next_line_id"] = " IS NULL";
    $wherePerf["prescription_line_mix.substitution_active"] = " = '1'";
    $this->_counts_by_chapitre["med"] += $prescription_line_mix_item->countList($wherePerf, null, null, null, $ljoinPerf);
    $wherePerf["signature_prat"] = " = '0'";
    $this->_counts_by_chapitre_non_signee["med"] += $prescription_line_mix_item->countList($wherePerf, null, null, null, $ljoinPerf);
    
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
    
    if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
      $this->loadRefsLinesDMI();
      $this->_counts_by_chapitre["dmi"] = count($this->_ref_lines_dmi);
    }
    
    $where["signee"] = " = '0'";
    foreach ($chapitres as $chapitre) {
      $where["category_prescription.chapitre"] = " = '$chapitre'";
      $nb_element = $line_element->countList($where, null, null, null, $ljoin_element);
      $nb_comment = $line_comment->countList($where, null, null, null, $ljoin_comment);
      $this->_counts_by_chapitre_non_signee[$chapitre] = $nb_element + $nb_comment;
    }
  }
  
  /*
   * Chargement des praticiens de la prescription
   */
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

    $sql = "SELECT DISTINCT prescription_line_mix.praticien_id
            FROM prescription_line_mix
            WHERE prescription_line_mix.prescription_id = '$this->_id'";
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
   * Calcul des chapitres modifiés récemments
   */
  function countRecentModif(){
  	$this->_count_recent_modif_presc = false;
    $this->_count_recent_modif["med"] = false;
    $this->_count_recent_modif["inj"] = false;
    
    // Parcours des lignes de medicaments
    if($this->_ref_prescription_lines_by_cat){
			foreach($this->_ref_prescription_lines_by_cat as $cat_atc => $_lines_med){
	      foreach($_lines_med as $_line_med){
	        $chapitre = $_line_med->_is_injectable ? "inj" : "med";
	        if($_line_med->_recent_modification){
	          $this->_count_recent_modif[$chapitre] = true;
	          $this->_count_recent_modif_presc = true;
					}
	      }
	    }
		}
		
    // Parcours des lignes de prescription_line_mixes
		if(is_array($this->_ref_prescription_line_mixes_by_type)){
	    foreach($this->_ref_prescription_line_mixes_by_type as $type_mix => $_prescription_line_mixes){
	    	$this->_count_recent_modif[$type_mix] = false;
	      foreach($_prescription_line_mixes as $_prescription_line_mix){
		      if($_prescription_line_mix->_recent_modification){
		        $this->_count_recent_modif[$type_mix] = true;
					  $this->_count_recent_modif_presc = true;
					}
				}
	    }
		}
		
    // Parcours des lignes d'elements
    foreach($this->_ref_prescription_lines_element_by_cat as $_chapitre_elt => $lines_by_cat){
      $this->_count_recent_modif[$_chapitre_elt] = false;
      foreach($lines_by_cat as $cat => $lines_by_type){
        foreach($lines_by_type as $lines_elt){
          foreach($lines_elt as $_line_elt){
            if($_line_elt->_recent_modification){
              $this->_count_recent_modif[$_chapitre_elt] = true;
              $this->_count_recent_modif_presc = true;
						}
          }
        }
      }
    }
  }
  
  /*
   * Calcul des chapitres qui possedent des prises urgentes 
   */
  function countUrgence($date){
    $this->_count_urgence["med"] = false;
    $this->_count_urgence["inj"] = false;
     
    // Parcours des lignes de medicaments
    foreach($this->_ref_prescription_lines_by_cat as $cat_atc => $_lines_med){
      foreach($_lines_med as $_line_med){
        $chapitre = $_line_med->_is_injectable ? "inj" : "med";
        if(is_array($_line_med->_dates_urgences) && array_key_exists($date, $_line_med->_dates_urgences)){
          $this->_count_urgence[$chapitre] = true;
        }
      }
    }
    
    // Parcours des lignes d'elements
    foreach($this->_ref_prescription_lines_element_by_cat as $_chapitre_elt => $lines_by_cat){
      $this->_count_urgence[$_chapitre_elt] = false;
      foreach($lines_by_cat as $cat => $lines_by_type){
        foreach($lines_by_type as $lines_elt){
          foreach($lines_elt as $_line_elt){
           if(is_array($_line_elt->_dates_urgences) && array_key_exists($date, $_line_elt->_dates_urgences)){
              $this->_count_urgence[$_chapitre_elt] = true;
            }
          }
        }
      }
    }
  }

  /*
   * Chargement des lignes de prescription de médicament
   */
  function loadRefsLinesMed($with_child = 0, $with_subst = 0, $emplacement="", $order="") {
		$line = new CPrescriptionLineMedicament();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    
    if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
    if($with_subst != "1"){
      $where["substitution_line_id"] = "IS NULL";
    }
    // Permet de ne pas afficher les lignes de substitutions
    $where["substitution_active"] = " = '1'";
    
    if(!$order){
      $order = "prescription_line_medicament_id DESC";
    }
    $this->_ref_prescription_lines = $line->loadList($where, $order);
  }
  
  /*
   * Chargement des lignes de prescription de médicament par catégorie ATC
   */
  function loadRefsLinesMedByCat($with_child = 0, $with_subst = 0, $emplacement = "") {
    $this->loadRefsLinesMed($with_child, $with_subst, $emplacement);
    $this->_ref_prescription_lines_by_cat = array();
    foreach($this->_ref_prescription_lines as &$_line){
      $produit =& $_line->_ref_produit;
      $produit->loadClasseATC();
      $this->_ref_prescription_lines_by_cat[$produit->_ref_ATC_2_code][$_line->_id] = $_line;
    }
  }
  
  /*
   * Chargement des lignes de medicaments (medicaments + commentaires)
   */
  function loadRefsLinesMedComments($withRefs = "1", $order=""){
    // Chargement des lignes de medicaments
    $this->loadRefsLinesMed(0,0,"",$order);
    // Chargement des lignes de commentaire du medicament
    $this->loadRefsLinesComment("medicament");
    
    // Initialisation du tableau de fusion
    $this->_ref_lines_med_comments["med"] = array();
    $this->_ref_lines_med_comments["comment"] = array();
    
    if(count($this->_ref_prescription_lines)){
      foreach($this->_ref_prescription_lines as &$line_med){
        if($withRefs){
          $line_med->loadRefsPrises();
        }
        $this->_ref_lines_med_comments["med"][] = $line_med;
      }
    }
    if(isset($this->_ref_prescription_lines_comment["medicament"][""]["comment"])){
      foreach($this->_ref_prescription_lines_comment["medicament"][""]["comment"] as &$comment_med){
        $this->_ref_lines_med_comments["comment"][] = $comment_med;
      }
    }
  }
  
  /*
   * Chargement des lignes d'element
   */
  function loadRefsLinesElement($chapitre = "", $withRefs = "1", $emplacement="", $order=""){
    $line = new CPrescriptionLineElement();
    $where = array();
    $ljoin = array();
    
    if($chapitre){
      $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
      $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = '$chapitre'";
    }
    $where["prescription_id"] = " = '$this->_id'";
    if(!$order){
      $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
      $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $order = "category_prescription.nom , element_prescription.libelle";
    }
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    
    if(count($this->_ref_prescription_lines_element)){
      foreach($this->_ref_prescription_lines_element as &$line_element){  
        $line_element->loadRefElement();
        if($withRefs){
          $line_element->loadRefsPrises();
          $line_element->loadRefExecutant();
          $line_element->loadRefPraticien();
        }
        $line_element->_ref_element_prescription->loadRefCategory();
      }
    }
  }
  
  /*
   * Chargement des lignes d'elements par catégorie
   */
  function loadRefsLinesElementByCat($withRefs = "1", $chapitre = "", $emplacement="", $order=""){
    $this->loadRefsLinesElement($chapitre, $withRefs, $emplacement, $order);
    $this->_ref_prescription_lines_element_by_cat = array();
    
    if(count($this->_ref_prescription_lines_element)){
      foreach($this->_ref_prescription_lines_element as $line){
        $line->_ref_element_prescription->loadRefCategory();
        $category =& $line->_ref_element_prescription->_ref_category_prescription;
        $this->_ref_prescription_lines_element_by_cat[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
        $this->_ref_lines_elements_comments[$category->chapitre]["$category->_id"]["element"][$line->_id] = $line;
      }
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
    
    if(count($commentaires)){
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
      }
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
  function loadRefsLinesElementsComments($withRefs = "1", $chapitre="", $order=""){
    $this->loadRefsLinesElementByCat($withRefs, $chapitre,"",$order);
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
  static function getFavorisPraticien($praticien_id, $chapitreSel){
    $listFavoris = array();
    if($chapitreSel == "med"){
      $listFavoris["medicament"] = CPrescription::getFavorisMedPraticien($praticien_id);
      $listFavoris["injectable"] = CPrescription::getFavorisInjectablePraticien($praticien_id);
    } else {
      $favoris[$chapitreSel] = CElementPrescription::getFavoris($praticien_id, $chapitreSel);
    }
    if(isset($favoris)){
      foreach($favoris as $key => $typeFavoris) {
        foreach($typeFavoris as $curr_fav){
          $element = new CElementPrescription();
          $element->load($curr_fav["element_prescription_id"]);
          $listFavoris[$key][] = $element;
        }
      }
    }
    return $listFavoris;    
  }
  
  /*
   * Controle des allergies
   */
  function checkAllergies($allergies, $code_cip) {
    if(!isset($this->_scores["allergie"])){
      $this->_scores["allergie"] = array();
    }
    if(!isset($this->_alertes["allergie"])){
      $this->_alertes["allergie"] = array();
    }
    $niveau_max = 0;
    foreach($allergies as $key => $all) {
      if($all->CIP == $code_cip) {
        $this->_alertes["allergie"][$code_cip][$key]["libelle"] = $all->LibelleAllergie;
      }
      $this->_scores["allergie"][$all->CIP] = $all;
    }
  }
  
  /*
   * Controle des interactions
   */
  function checkInteractions($interactions, $code_cip) {
    if(!isset($this->_scores["interaction"])){
      $this->_scores["interaction"] = array();
    }
    if(!isset($this->_alertes["interaction"])){
      $this->_alertes["interaction"] = array();
    }
    $niveau_max = 0;
    foreach($interactions as $key => $int) {
      if($int->CIP1 == $code_cip || $int->CIP2 == $code_cip) {
        $_interaction =& $this->_alertes["interaction"][$int->CIP1][$key];
        $_interaction["libelle"] = $int->Type;
        $_interaction["niveau"] = $int->Niveau;
        if(!isset($this->_scores["interaction"]["niv$int->Niveau"])){
          $this->_scores["interaction"]["niv$int->Niveau"] = 0;
        }
        $this->_scores["interaction"]["niv$int->Niveau"]++;
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
    if(!isset($this->_alertes["IPC"])){
      $this->_alertes["IPC"] = array();
    }
  }
  
  /*
   * Controle du profil du patient
   */
  function checkProfil($profils, $code_cip) {
    if(!isset($this->_scores["profil"])){
      $this->_scores["profil"] = array();
    }
    if(!isset($this->_alertes["profil"])){
      $this->_alertes["profil"] = array();
    }
    $niveau_max = 0;
    foreach($profils as $key => $profil) {
      if($profil->CIP == $code_cip) {
        $_profil =& $this->_alertes["profil"][$code_cip][$key];
        $_profil["libelle"] = $profil->LibelleMot;
        $_profil["niveau"] = $profil->Niveau;
        if(!isset($this->_scores["profil"]["niv$profil->Niveau"])){
          $this->_scores["profil"]["niv$profil->Niveau"] = 0;
        }
        $this->_scores["profil"]["niv$profil->Niveau"]++;
      }
      $niveau_max = max($profil->Niveau, $niveau_max);
    }
    if(count($this->_scores["profil"])){
      $this->_scores["profil"]["niveau_max"] = $niveau_max;
    }
  }
  
  /*
   * Controle des problèmes de posologie
   */
  function checkPoso($posologies, $code_cip) {
    if(!isset($this->_scores["posoqte"])){
      $this->_scores["posoqte"] = array();
    }
    if(!isset($this->_scores["posoduree"])){
      $this->_scores["posoduree"] = array();
    }
    if(!isset($this->_alertes["posoqte"])){
      $this->_alertes["posoqte"] = array();
    }
    if(!isset($this->_alertes["posoduree"])){
      $this->_alertes["posoduree"] = array();
    }

    $niveau_duree_max = 0;
    $niveau_qte_max   = 0;
    
    foreach($posologies as $key => $poso) {
      if($poso->Type == "Duree") {
        $tab = "posoduree";
      } else {
        $tab = "posoqte";
      }
      if($poso->CIP == $code_cip) {
        $_posologie =& $this->_alertes[$tab][$code_cip][$key];
        $_posologie["libelle"] = $poso->LibellePb;   
        $_posologie["niveau"]  = $poso->Niveau;
        if(!isset($this->_scores[$tab]["niv$poso->Niveau"])){
          $this->_scores[$tab]["niv$poso->Niveau"] = 0;
        }
        $this->_scores[$tab]["niv$poso->Niveau"]++;
      }
      if($poso->Type == "Duree") {
        $niveau_duree_max = max($poso->Niveau, $niveau_duree_max);
      } else {
        $niveau_qte_max   = max($poso->Niveau, $niveau_qte_max);
      }
    }
    if(count($this->_scores["posoduree"])){
      $this->_scores["posoduree"]["niveau_max"] = $niveau_duree_max;
    }
    if(count($this->_scores["posoqte"])){
      $this->_scores["posoqte"]["niveau_max"]   = $niveau_qte_max;
    }
  }
  
  /*
   * Creation de toutes les planifications systeme pour un sejour si celles-ci ne sont pas deja créées
   */
  function calculAllPlanifSysteme(){
  	$this->completeField("object_id");
		$planif = new CPlanificationSysteme();
		$planif->sejour_id = $this->object_id;
    
		if(!$this->object_id || ($this->type != "sejour") || $planif->countMatchingList()){
	   return;
    }

    // Chargement de toutes les lignes
    $this->loadRefsLinesMedByCat("1","1");
    $this->loadRefsLinesElementByCat("1");
    $this->loadRefsPrescriptionLineMixes();
		  
	  // Paroucrs des lignes de medicaments
    foreach($this->_ref_prescription_lines as &$_line_med){
      if(!$_line_med->_ref_prises){
        $_line_med->loadRefsPrises();
      }
      $planif = new CPlanificationSysteme();
      $planif->object_id = $_line_med->_id;
      $planif->object_class = $_line_med->_class_name;
      if(!$planif->countMatchingList()){
        $_line_med->calculPlanifSysteme();
      }
    }
      
		// Parcours des lignes d'elements
    foreach($this->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
      foreach($elements_chap as $name_cat => $elements_cat){
        foreach($elements_cat as &$_elements){
          foreach($_elements as &$_line_element){
            $planif = new CPlanificationSysteme();
            $planif->object_id = $_line_element->_id;
            $planif->object_class = $_line_element->_class_name;
            if(!$planif->countMatchingList()){
              $_line_element->calculPlanifSysteme();
            }
          }
        }
      }
    }
		
		// Parcours des prescription_line_mixes
		foreach($this->_ref_prescription_line_mixes as $_prescription_line_mix){
			$_prescription_line_mix->calculPlanifsPerf();
		}
  }
  
  /*
   * Suppression des planifications systemes
   */
  function removeAllPlanifSysteme(){
    $planifSysteme = new CPlanificationSysteme();
    $planifSysteme->sejour_id = $this->object_id;
    $planifs = $planifSysteme->loadMatchingList();
    foreach($planifs as $_planif){
      $_planif->delete();
    }
  }
	
  /*
   * Génération du Dossier/Feuille de soin
   */
  function calculPlanSoin($dates, $mode_feuille_soin = 0, $mode_semainier = 0, $mode_dispensation = 0, $code_cip = "", $with_calcul = true, $code_cis = ""){  
	  $this->calculAllPlanifSysteme();

    // Parcours des lignes de smedicaments
    if(count($this->_ref_prescription_lines)){
      foreach($this->_ref_prescription_lines as &$_line_med){
        if(!$_line_med->signee && !CAppUI::conf("dPprescription CPrescription show_unsigned_lines")){
          continue;  
        }
        // Filtre par code_cip
        if(($code_cip && ($code_cip != $_line_med->code_cip)) || ($code_cis && ($code_cis != $_line_med->code_cis))) {
          continue;
        }
        $_line_med->loadRefPraticien();
        // Mise à jour de la date de fin si celle-ci n'est pas indiquée
        if(!$_line_med->_fin_reelle){
          $_line_med->_fin_reelle = $_line_med->_ref_prescription->_ref_object->_sortie;
        }
            
        // Calcul des administrations
        if($with_calcul){
        	foreach($dates as $date){
            $_line_med->calculAdministrations($date, $mode_dispensation);
					}
        }
        
        // Si aucune prise
        $produit =& $_line_med->_ref_produit;
        $produit->loadClasseATC();
        $produit->loadRefsFichesATC();
        $code_ATC = $produit->_ref_ATC_2_code;
        
				foreach($dates as $date){
	        if(($date >= $_line_med->debut && $date <= mbDate($_line_med->_fin_reelle))){     
	          if ((count($_line_med->_ref_prises) < 1) && (!isset($this->_lines["med"][$code_ATC][$_line_med->_id]["aucune_prise"]))){
	            if($_line_med->_is_injectable){
	              $this->_ref_injections_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;  
	            } else { 
	              $this->_ref_lines_med_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;
	            }
	            continue;
	          }
	          $_line_med->calculPrises($this, $date, null, $with_calcul);
	        }
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
        if($with_calcul){
          $_line_med->removePrisesPlanif($mode_semainier);
        }         
      }
    }
    
		// Parcours des lignes d'elements
    if(!$mode_dispensation){
      if($this->_ref_prescription_lines_element_by_cat){
        foreach($this->_ref_prescription_lines_element_by_cat as $name_chap => $elements_chap){
          foreach($elements_chap as $name_cat => $elements_cat){
            foreach($elements_cat as &$_elements){
              foreach($_elements as &$_line_element){
                if(!$_line_element->signee && !CAppUI::conf("dPprescription CPrescription show_unsigned_lines")){
                  continue;  
                }
								
								if($_line_element->cip_dm){
									$_line_element->loadRefDM();
								}
								
                // Chargement des administrations et des transmissions
                if($with_calcul){
                	foreach($dates as $date){
                    $_line_element->calculAdministrations($date);
									}
                }
                
                // Pre-remplissage du plan de soin dans le cas des examens d'imagerie et des consultations spec.
                foreach($dates as $date){
	                // Pre-remplissage des prises prevues dans le dossier de soin
	                if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_fin_reelle))){
	                  // Si aucune prise  
	                  if ((count($_line_element->_ref_prises) < 1) && (!isset($this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))){
	                    $this->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
	                  }
	                  $_line_element->calculPrises($this, $date, $name_chap, $name_cat, $with_calcul);
	                }
								}
								
                // Stockage d'une ligne possedant des administrations ne faisant pas reference à une prise ou unite de prise
                if(!$mode_feuille_soin){
                  if(isset($_line_element->_administrations['aucune_prise']) && count($_line_element->_ref_prises) >= 1){
                    $this->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
                  }
                }
                
                // Suppression des prises prevues replanifiées
                if($with_calcul){
                  $_line_element->removePrisesPlanif($mode_semainier);
                }
              }
            }
          }
        }
      }
    }
  
	  // Parcours des prescription_line_mixes
    if($this->_ref_prescription_line_mixes){
      foreach($this->_ref_prescription_line_mixes as &$_prescription_line_mix){
          if(!$_prescription_line_mix->signature_prat && !CAppUI::conf("dPprescription CPrescription show_unsigned_lines")){
           continue;  
         }
				 $_prescription_line_mix->calculQuantiteTotal();
				 foreach($dates as $date){
	         if(($date >= mbDate($_prescription_line_mix->_debut)) && ($date <= mbDate($_prescription_line_mix->_fin))){
	           if($with_calcul){
	           	 $_prescription_line_mix->calculPrisesPrevues($date);
	           }
	           $this->_ref_prescription_line_mixes_for_plan[$_prescription_line_mix->_id] = $_prescription_line_mix;
	           $this->_ref_prescription_line_mixes_for_plan_by_type[$_prescription_line_mix->type_line][$_prescription_line_mix->_id] = $_prescription_line_mix;
					 }
				}
				if($with_calcul){
				  $_prescription_line_mix->calculAdministrations();
				}
			}
    }
  }
  
  // fillTemplate utilisé pour la consultation et le sejour (affichage des chapitres de la prescription)
  function fillLimitedTemplate(&$template) {
    $this->updateChapterView();
    foreach($this->_chapter_view as $_chapitre => $view_chapitre){
      $template->addProperty("Prescription ".CAppUI::tr("CPrescription.type.$this->type")." - ".CAppUI::tr("CPrescription._chapitres.$_chapitre"), $view_chapitre);
    }
  }
  
  function fillTemplate(&$template) {
    if(!($this->object_id && $this->object_class)){
      $this->_ref_selected_prat = new CMediusers();
      $this->_ref_patient = new CPatient();
    }
    $this->_ref_selected_prat->fillTemplate($template);
    $this->_ref_patient->fillTemplate($template);
  }
  
  /*
   * Retourne un template de prescription (header / footer)
   */
  static function getPrescriptionTemplate($type, $praticien){
    $modele = new CCompteRendu();
    if(!$praticien->_id){
      return $modele;
    }
    $modele->object_class = "CPrescription";
    $modele->chir_id = $praticien->_id;
    $modele->type = $type;
    $modele->loadMatchingObject();
    if(!$modele->_id){
      // Recherche du modele au niveau de la fonction
      $modele->chir_id = null;
      $modele->function_id = $praticien->function_id;
      $modele->loadMatchingObject();
      if(!$modele->_id){
        // Recherche du modele au niveau de l'etablissement
        $modele->function_id = null;
        $modele->group_id = $praticien->_ref_function->group_id;
        $modele->loadMatchingObject();
      }
    }
    return $modele;
  }
}

?>