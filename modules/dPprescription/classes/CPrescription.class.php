<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescription extends CMbObject implements IPatientRelated {
  
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
  var $fast_access     = null;
	var $planif_removed  = null;
	var $score           = null;
	var $checked_lines   = null;
	var $QSP             = null;
	
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
  var $_ref_prescription_line_mixes           = null;
  var $_ref_lines_dmi                         = null;
  var $_ref_lines_inscriptions                = null;
	var $_count_inscriptions                    = null;
	
	// Distant Ref
	var $_ref_alertes = null;
	
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
	var $_ref_inscriptions_for_plan = null;
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
	var $_count_recent_modif = null;
	
	var $_count_alertes = null;
	var $_count_urgences = null;
	
	var $_nb_lines_plan_soins = null;
	var $_ref_prescription_lines_by_cat = null;
	var $_protocole_locked = null;
	
	var $_ids = null;
	
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
    "ds"       => "modules/soins/images/ds.png",
    "med_elt"  => "modules/soins/images/medicaments.png"
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
    $backProps["protocoles_med"]                   = "CPrescriptionLineMedicament protocole_id";
    $backProps["protocoles_element"]               = "CPrescriptionLineElement protocole_id";
    $backProps["protocoles_dmi"]                   = "CPrescriptionLineDMI protocole_id";
    $backProps["protocoles_mix"]                   = "CPrescriptionLineMix protocole_id";
    $backProps["protocoles_comment"]               = "CPrescriptionLineComment protocole_id";
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
    $specs["checked_lines"] = "bool default|0";
		$specs["planif_removed"]= "bool default|0";
		$specs["QSP"]           = "text";
		$specs["_type_sejour"]  = "enum notNull list|pre_admission|sejour|sortie";
    $specs["_dateTime_min"] = "dateTime";
    $specs["_dateTime_max"] = "dateTime";
    $specs["_owner"]        = "enum list|prat|func|group";
    $specs["_score_prescription"] = "enum list|0|1|2";
		$specs["score"] = "enum list|0|1|2";
    $specs["_date_plan_soin"] = "date";
    $specs["_type_alerte"] = "enum list|hors_livret|interaction|allergie|profil|IPC";
    $specs["_chapitres"] = "enum list|med|inj|perfusion|oxygene|alimentation|aerosol|anapath|biologie|consult|dmi|imagerie|kine|soin|dm|ds";
    return $specs;
  }
  
  function loadRelPatient(){
    return $this->loadRefPatient();
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
		
		// Si c'est un protocole de praticien, on verifie les droits
		if(!$this->object_id && $this->praticien_id){
			$is_praticien = CAppUI::$user->isPraticien();
			$this->_protocole_locked = ($is_praticien && CAppUI::$user->_id != $this->praticien_id) ? 1 : 0;		
		}
  }
  
  function updateChapterView(){
    // Initialisation du tableau par chapitre
    foreach($this->_specs["_chapitres"]->_list as $_chapitre){
      $this->_chapter_view[$_chapitre] = array();
    }
    unset($this->_chapter_view["inj"]);
    
    // Chargement des medicaments et des commentaires
    $this->loadRefsLinesMedComments();
    if($this->_ref_lines_med_comments){
      foreach($this->_ref_lines_med_comments as $lines){
        foreach($lines as $_line){
          if ($_line instanceOf CPrescriptionLineMedicament){
            $_line->updateLongView();
            $this->_chapter_view["med"][] = $_line->_long_view;
          } 
					else {
            $this->_chapter_view["med"][] = $_line->_view;
          }
        }
      }
    }
    
    // Chargement des elements
    $this->loadRefsLinesElementsComments();
    if($this->_ref_lines_elements_comments){
      foreach($this->_ref_lines_elements_comments as $chapitre => $_lines_by_cat){
        foreach($_lines_by_cat as $_lines){
          foreach ($_lines["element"] as $_line_element){
            $_line_element->updateLongView();
            $this->_chapter_view[$chapitre][] = $_line_element->_long_view;
          }
          foreach($_lines["comment"] as $_line_comment){
            $this->_chapter_view[$chapitre][] = $_line_comment->_view;
          }
        }
      }
    }
    
    // Chargement des prescription_line_mixes
    $this->loadRefsPrescriptionLineMixes();
    foreach($this->_ref_prescription_line_mixes as $_mix) {
    	$perf_view = "$_mix->_view : ";
      $_mix->loadRefsLines();
			$perf_view.= implode(", ", CMbArray::pluck($_mix->_ref_lines, "_view"));
      $this->_chapter_view[$_mix->type_line][] = $perf_view;
    }
	}
  
  function check() {    
    if ($msg = parent::check()) {
      return $msg;
    }
 
    // Test permettant d'eviter que plusieurs prescriptions identiques soient créées 
    if(!$this->_id && $this->object_id !== null && $this->object_class !== null && $this->praticien_id !== null && $this->type !== null){
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
  
  
  function applyDateProtocole(&$_line, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, $date_operation, 
                              $hour_operation, $operation, $sejour, $protocole_id, $datetime_now){
                              	
		if(!$datetime_now){
			$datetime_now = mbDateTime();
		}														
    if (!$_line->unite_duree) {
		  $_line->unite_duree = "jour";
    }
   
    $_line->protocole_id = $protocole_id;
		// Chargement des lignes ou des prises suivant le type d'objet    
		if($_line instanceof CPrescriptionLineMix){
      $_line->loadRefsLines();
		  $_line->date_debut = "";
    } else {
		  $_line->loadRefsPrises();
      $_line->debut = "";
		}
		 
    $_line->_id = "";
		
		$time_debut = "";
  
	  $_line->prescription_id = $this->_id;
    $_line->praticien_id = $praticien_id;
    $_line->creator_id = CAppUI::$user->_id;
		
    // Calcul de la date d'entree
    switch($_line->jour_decalage){
      case 'E': 
        $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->entree;
        break;
      case 'I': 
        $date_debut = "";
        if($date_operation){
          $date_debut = mbDate($date_operation);
          $time_debut = mbTime($date_operation); 
        } elseif ($operation->_id) {
          $date_debut = mbDate($operation->_datetime); 
          $time_debut = $hour_operation;
        }
        break;
      case 'S': $date_debut = ($debut_sejour) ? $debut_sejour : $sejour->sortie; break;
      case 'N': $date_debut = $datetime_now; break;
			case 'A':
				 $date_debut = mbDate($operation->_datetime); 
				 // Si l'heure d'induction n'est pas encore precisée, on utilise la date prévue de l'intervention
				 $time_debut = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
				 break;
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
          	$date_fin = mbDate($operation->_datetime); 
            $time_fin = $hour_operation;
          }
        }
        break;
			case 'A';
			  $date_fin = mbDate($operation->_datetime); 
        $time_fin = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
				break;
      case 'S': $date_fin = ($fin_sejour) ? $fin_sejour : $sejour->_sortie; break;
			case 'N': $date_fin = $datetime_now; break;
    }
    
    $unite_decalage_debut = $_line->unite_decalage === "heure" ? "HOURS" : "DAYS";
    $unite_decalage_fin   = $_line->unite_decalage_fin === "heure" ? "HOURS" : "DAYS";

    if(!$_line->jour_decalage){
      $date_debut = $date_sel;
			if($time_sel){
				$date_debut .= " $time_sel";
			}
    }
		
		 if(!$_line->decalage_line){
       $_line->decalage_line = 0;
     }
     if(!$_line->decalage_line_fin){
       $_line->decalage_line_fin = 0;
     }
		 
		$signe = ($_line->decalage_line >= 0) ? "+" : "";
    // Decalage du debut en jour   
		if($unite_decalage_debut === "DAYS"){
    	$_time_debut = mbTime($date_debut);
			
			/*
			if($_time_debut != "00:00:00" && !$_line->time_debut && $_line->decalage_line == 0){
		    $_line->time_debut = $_time_debut; 
      }*/
		  $date_debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut); 
    } 
		// Decalage du debut en heure
		else {  
		  $date_time_debut = mbDateTime("$signe $_line->decalage_line HOURS", "$date_debut $time_debut");
      $date_debut = mbDate($date_time_debut);
      $_line->time_debut = mbTime($date_time_debut);    
		}
  
    if($date_debut){
      if($_line instanceof CPrescriptionLineMix){
        $_line->date_debut = mbDate($date_debut);
      } else {
        $_line->debut = mbDate($date_debut);
      }
    }
		
	  // Decalage de la fin
    if($_line->jour_decalage_fin){
      $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
			
			// Decalage de la fin en jour
      if($unite_decalage_fin === "DAYS"){
        $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_fin); 
				
				$_debut = ($_line instanceof CPrescriptionLineMix) ? $_line->date_debut : $_line->debut;
				$_line->unite_duree = "jour";
				$_line->duree = mbDaysRelative($_debut, $date_fin);
				$_line->duree++;
			} 
			
			// Decalage de la fin en heure
			else {
      	$date_time_fin = mbDateTime("$signe_fin $_line->decalage_line_fin HOURS", "$date_fin $time_fin");
				$date_fin = mbDate($date_time_fin);
        $time_fin = mbTime($date_time_fin);
        
			  if($_line instanceof CPrescriptionLineMix){
			  	$duree_hours = mbHoursRelative("$_line->date_debut $_line->time_debut", $date_time_fin);
				} else {
				  $duree_hours = mbHoursRelative("$_line->debut $_line->time_debut", $date_time_fin);
        }
				
				if(($duree_hours <= 24) || ($unite_decalage_debut == "HOURS" && $unite_decalage_fin == "HOURS")){
          $_line->unite_duree = "heure";
          $_line->duree = $duree_hours;
				} else {
					$_line->unite_duree = "jour";
					
					if($_line instanceof CPrescriptionLineMix){
					  $_line->duree = mbDaysRelative($_line->date_debut, $date_fin);
					} else {
					  $_line->duree = mbDaysRelative($_line->debut, $date_fin);
          }
					
					$_line->duree++;
					if($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement){
	          $_line->time_fin = $time_fin;
	        }
				}
      }
    }
		
    // Permet d'eviter les durees negatives lors de l'application d'un protocole
    if($_line->duree < 0){
      $_line->duree = 0;
    }
    
    if($_line->jour_decalage === "I" || $_line->jour_decalage_fin === "I" || $_line->jour_decalage === "A" || $_line->jour_decalage_fin === "A"){
      if($operation_id){
        $_line->operation_id = $operation_id;
      } else {
      	if($_line instanceof CPrescriptionLineMix){
          $_line->date_debut = "";
				} else {
				 	$_line->debut = "";
					$_line->time_fin = "";
				}
        $_line->duree = "";
        $_line->time_debut = "";
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
    
		// On vide la reference, elle sera mauvaise car la ligne est appliquée à une nouvelle prescription
		$_line->_ref_prescription = null;
		
		// Store de la ligne
    $msg = $_line->store();
	
    CAppUI::displayMsg($msg, "{$_line->_class}-msg-create");  
		
		if($_line instanceof CPrescriptionLineMix){
			// Parcours des lignes
			foreach($_line->_ref_lines as $_line_perf){
        $_line_perf->_id = "";
        $_line_perf->prescription_line_mix_id = $_line->_id;
        $msg = $_line_perf->store();
        CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");
      } 
		} else {
			if(count($_line->_ref_prises) == 0){
				if($_line instanceof CPrescriptionLineElement && $_line->debut && $_line->time_debut){
          // On genere une planif a la date et heure de debut si aucune poso n'est presente
	        $new_planif = new CPlanificationSysteme();
	        $new_planif->dateTime = "$_line->debut $_line->time_debut";
	        $new_planif->object_id = $_line->_id;
	        $new_planif->object_class = $_line->_class;
	        $new_planif->sejour_id = $_line->_ref_prescription->object_id;    
	        $new_planif->store();
		    }
			} else {
				// Parcours des prises
	      foreach($_line->_ref_prises as $prise){
	        $prise->_id = "";
	        $prise->object_id = $_line->_id;
	        $prise->object_class = $_line->_class;
	        if($prise->decalage_intervention != null){
	          $time_operation = "";
	          if($date_operation){
	            $time_operation = mbTime($date_operation); 
	          } elseif ($operation->_id) {
	            if($prise->type_decalage == "I"){
	              $time_operation = $hour_operation;
              } else {
	              $time_operation = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
              }
						}
	          $signe_decalage_intervention = ($prise->decalage_intervention >= 0) ? "+" : "";
	          if($time_operation){
	            $unite_decalage_intervention = ($prise->unite_decalage_intervention == "heure") ? "HOURS" : "MINUTES";
	            $prise->heure_prise = mbTime("$signe_decalage_intervention $prise->decalage_intervention $unite_decalage_intervention", $time_operation);
	          }
	        }
	        if($prise->urgence_datetime){
	          $prise->urgence_datetime = mbDateTime();
	        }
					if($prise->datetime){
            $prise->datetime = $_line->_debut_reel;
          }
	        $msg = $prise->store();
	        CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");  
	      }
			}
		}   
  }
  
  // Permet d'appliquer un protocole à une prescription
  function applyProtocole($protocole_id, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour="", $fin_sejour="", $date_operation="", $datetime_now) {
    $user = CUser::get();
    // Chargement du protocole
    $protocole = new CPrescription();
    $protocole->load($protocole_id);
    
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
    }
  
    if($this->_ref_object instanceof CSejour){
      $sejour =& $this->_ref_object;
    }
		
    // Parcours des lignes de medicaments
    foreach($protocole->_ref_prescription_lines as &$_line_med){      
        // Chargement des lignes de substitutions de la ligne de protocole
        $_line_med->loadRefsVariantes();
        $_substitutions = $_line_med->_ref_variantes;
      
      // Creation et modification de la ligne en fonction des dates
      $this->applyDateProtocole($_line_med, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                $date_operation, $hour_operation, $operation, $sejour, $protocole_id, $datetime_now);
                                
      // Creation d'une nouvelle ligne de substitution qui pointe vers la ligne qui vient d'etre crée
      foreach($_substitutions as $_line_subst_by_chap){
        foreach($_line_subst_by_chap as $_line_subst){
          $_line_subst->variante_for_id = $_line_med->_id;
          $_line_subst->variante_for_class = $_line_med->_class;
          $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                    $date_operation, $hour_operation, $operation, $sejour, $protocole_id, $datetime_now);
        }
      }
    }
    
    // Parcours des lignes d'elements
    foreach($protocole->_ref_prescription_lines_element_by_cat as &$elements_by_chap){
      foreach($elements_by_chap as &$elements_by_cat){
        foreach($elements_by_cat as &$_lines){
          foreach($_lines as $_line_elt){
            $this->applyDateProtocole($_line_elt, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                      $date_operation, $hour_operation, $operation, $sejour, $protocole_id, $datetime_now);
          } 
        }
      }
    }
  
    // Parcours des prescription_line_mixes
    foreach($protocole->_ref_prescription_line_mixes as &$_prescription_line_mix){
      $_prescription_line_mix->loadRefsVariantes();
      $_substitutions_perf = $_prescription_line_mix->_ref_variantes;
      

      $this->applyDateProtocole($_prescription_line_mix, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                $date_operation, $hour_operation, $operation, $sejour, $protocole_id, $datetime_now);
      
  
      foreach($_substitutions_perf as $_line_subst_by_chap){
        foreach($_line_subst_by_chap as $_line_subst){
          $_line_subst->variante_for_id = $_prescription_line_mix->_id;
          $_line_subst->variante_for_class = $_prescription_line_mix->_class;
          $this->applyDateProtocole($_line_subst, $praticien_id, $date_sel, $time_sel, $operation_id, $debut_sejour, $fin_sejour, 
                                    $date_operation, $hour_operation, $operation, $sejour, $protocole_id, $datetime_now);
        }
      }                  
    }
  
    // Parcours des lignes de commentaires
    foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
      $line_comment->_id = "";
      $line_comment->prescription_id = $this->_id;
      $line_comment->praticien_id = $praticien_id;
      $line_comment->creator_id = $user->_id;
			$line_comment->protocole_id = $protocole_id;
			if($this->_ref_object instanceof CSejour && $this->_ref_object->type == "urg" && CAppUI::conf("dPprescription CPrescription prescription_suivi_soins")){
        $line_comment->debut = mbDate();
        $line_comment->time_debut = mbTime();  
      }
			
      $msg = $line_comment->store();
      CAppUI::displayMsg($msg, "CPrescriptionLineComment-msg-create");
    
    }
  }
  
  /*
   * Permet d'applique un protocole ou un pack à partir d'un identifiant (pack-$id ou prot-$id)
   */
  function applyPackOrProtocole($pack_protocole_id, $praticien_id, $date_sel, $time_sel, $operation_id, $datetime_now){
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
          
          // Si le pack_item a un type différent de la prescription, alors on cherche la prescription du type adéquat
          if ($_protocole->type != $this->type) {
            $prescription = new CPrescription();
            $prescription->type = $_protocole->type;
            $prescription->object_class = $this->object_class;
            $prescription->object_id    = $this->object_id;
            $prescription->loadMatchingObject();
            
            // Si elle n'est pas trouvée, on a la crée
            if (!$prescription->_id) {
              if ($msg = $prescription->store()) {
                CAppUI::setMsg($msg, UI_MSG_ERROR);
              }
            }
            
            // Et on applique le protocole
            $prescription->applyProtocole($_protocole->_id, $praticien_id, $date_sel, $time_sel, $operation_id, "", "", "", $datetime_now);
            $this->_ids[] = $prescription->_id;
          }
          else {
            // Sinon, application directe du protocole
            $this->applyProtocole($_protocole->_id, $praticien_id, $date_sel, $time_sel, $operation_id, "", "", "", $datetime_now);
          }
        }
      }
      if($protocole_id){
        $this->applyProtocole($protocole_id, $praticien_id, $date_sel, $time_sel, $operation_id, "", "", "", $datetime_now);
      }
    }
    // Suppression des doublons éventuels sur les ids des prescriptions des autres types
    // (lors de l'application d'un pack contenant des protocoles de types différents)
    // L'utilisation d'array_values permet de réindexer les clés de 0 à n
    if (is_array($this->_ids)) {
      $this->_ids = array_values(array_unique($this->_ids));
    }
    
  }
  
  /*
   * Calcul du praticien_id responsable de la prescription
   */
  function calculPraticienId(){
    $user = CMediusers::get();
    
    if ($this->object_id !== null && $this->object_class !== null && $this->type !== null && $this->object_id){
      // Chargement de l'object
      $object = new $this->object_class;
      $object->load($this->object_id);
      $object->loadRefsFwd();
      
      if($this->type !== "sejour"){
        if($this->type != 'traitement'){
          $this->praticien_id = $user->isPraticien() ? $user->_id : $object->_praticien_id;
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
    $this->loadRefsLinesElementByCat("1","1");
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
  function loadRefsPrescriptionLineMixes($chapitre = "", $with_child = 0, $variante_active = 1, $protocole_id = '', $in_progress=0){
    if($this->_ref_prescription_line_mixes){
    	return;
    }
    $prescription_line_mix = new CPrescriptionLineMix();
		$where = array();
    $where["prescription_id"] = " = '$this->_id'";
		if ($chapitre){
			$where["type_line"] = " = '$chapitre'";
		}
		if ($with_child != 1){
      $where["next_line_id"] = "IS NULL";
    }
    if ($protocole_id) {
      $where["protocole_id"] = " = '$protocole_id'";
    }
    // Permet de ne pas afficher les lignes de substitutions
    $where["variante_active"] = " = '$variante_active'";
    
    $this->_ref_prescription_line_mixes = $prescription_line_mix->loadList($where);
		
		if(count($this->_ref_prescription_line_mixes)){
		  $current_date = mbDateTime();
			foreach($this->_ref_prescription_line_mixes as $_key_line => $_line_mix){
			  $debut = $_line_mix->_debut;
			  $fin = $_line_mix->_fin;
	      if ($in_progress && ($_line_mix->_fin && $_line_mix->_fin < $current_date)) {
			    unset($this->_ref_prescription_line_mixes[$_key_line]);
			    continue;
			  }
			  $this->_ref_prescription_line_mixes_by_type[$_line_mix->type_line][] = $_line_mix;
		  }
		}
	}
	
	/*
	 * Chargement des inscriptions
	 */
  function loadRefsLinesInscriptions(){
    if ($this->_ref_lines_inscriptions) {
      return;
    }

  	// Chargement des inscriptions de medicament
		$line_med = new CPrescriptionLineMedicament();
		$line_med->prescription_id = $this->_id;
		$line_med->inscription = 1;
		$this->_ref_lines_inscriptions["med"] = $line_med->loadMatchingList();
		
		// Chargement des inscriptions d'element
		$line_elt = new CPrescriptionLineElement();
		$line_elt->prescription_id = $this->_id;
		$line_elt->inscription = 1;
		$this->_ref_lines_inscriptions["elt"] = $line_elt->loadMatchingList();
  
	  $this->_count_inscriptions = count($this->_ref_lines_inscriptions["med"]) + count($this->_ref_lines_inscriptions["elt"]);
	}
	
  /*
   * Chargement du praticien utilisé pour l'affichage des protocoles/favoris
   */
  function loadRefCurrentPraticien() {
    if ($this->_ref_current_praticien) {
      return;
    }

    $user = CMediusers::get();
    if ($user->isPraticien()) {
      $this->_ref_current_praticien = $user;
    }
    else {
      if($this->_ref_object->_class == "CSejour"){
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
  
  /**
   * Chargement du patient
   * 
   * @return CPatient
   */
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
		
    if($this->object_class == "CDossierMedical"){
      $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->object_id);  
    } 
		else {
      if($this->_ref_object){
        $this->_ref_patient = $this->_ref_patient->getCached($this->_ref_object->patient_id); 
      }
    }
		
		return $this->_ref_patient;
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
  function countNoValideLines($praticien_id = null){
    $this->_counts_no_valide = 0;
    if($this->_id){
      $line = new CPrescriptionLineMedicament();
      $where = array();
      $where["signee"] = " = '0'";
      $where["prescription_id"] = " = '$this->_id'";
      $where["child_id"] = "IS NULL";
      $where["substituted"] = " = '0'";
      $where["variante_for_id"] = "IS NULL";
      $where["variante_active"] = " = '1'";
      
      if ($praticien_id) {
        $where["praticien_id"] = " = '$praticien_id'";
      } 
      
      $this->_counts_no_valide = $line->countList($where);
      
      unset($where["substituted"]);
      unset($where["variante_for_id"]);
      unset($where["variante_active"]);
      
      $line = new CPrescriptionLineElement();
      $this->_counts_no_valide += $line->countList($where);
      
      $line = new CPrescriptionLineComment();
      $this->_counts_no_valide += $line->countList($where);
      
      $line = new CPrescriptionLineMix();
      $where = array();
      $where["variante_for_id"] = "IS NULL";
      $where["variante_active"] = " = '1'";
      $where["prescription_id"] = " = '$this->_id'";
      
      $where["signature_prat"] = " = '0'";
      if ($praticien_id) {
        $where["praticien_id"] = " = '$praticien_id'";
      } 
      $this->_counts_no_valide += $line->countList($where);
    }
  }
  
  /*
   * Chargement du nombre des medicaments et d'elements
   */
  function countLinesMedsElements($praticien_sortie_id = null, $operation_id = null, $protocole_id=""){
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
    $whereMed["substituted"] = " = '0'";
    $whereMed["variante_active"] = " = '1'";
    $whereMed["inscription"] = " = '0'";
		
    if ($praticien_sortie_id) {
      $where["praticien_id"]    = " = '$praticien_sortie_id'";
      $whereMed["praticien_id"] = " = '$praticien_sortie_id'";
    }
    
    if ($protocole_id) {
      $where["protocole_id"]    = " = '$protocole_id'";
      $whereMed["protocole_id"] = " = '$protocole_id'";
    }
    $this->_counts_by_chapitre["med"] = $line_med->countList($whereMed);
    $this->_counts_by_chapitre["med"] += $line_comment_med->countList($where, null, $ljoin_comment);
    
    $whereMed["signee"] = " = '0'";
    $where["signee"]  =" = '0'";
    $this->_counts_by_chapitre_non_signee["med"] = $line_med->countList($whereMed);
    $this->_counts_by_chapitre_non_signee["med"] += $line_comment_med->countList($where, null, $ljoin_comment);
    
    $prescription_line_mix_item  = new CPrescriptionLineMixItem();
    $ljoinPerf["prescription_line_mix"] = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";
    $wherePerf["prescription_line_mix.prescription_id"] = " = '$this->_id'";
    $wherePerf["prescription_line_mix.next_line_id"] = " IS NULL";
    $wherePerf["prescription_line_mix.variante_active"] = " = '1'";
    if ($protocole_id) {
      $wherePerf["protocole_id"] = " = '$protocole_id'";
    }
    $this->_counts_by_chapitre["med"] += $prescription_line_mix_item->countList($wherePerf, null, $ljoinPerf);
    $wherePerf["signature_prat"] = " = '0'";
    $this->_counts_by_chapitre_non_signee["med"] += $prescription_line_mix_item->countList($wherePerf, null, $ljoinPerf);
    
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
    $where["inscription"] = " = '0'";
		$where["child_id"] = "IS NULL";
		if ($praticien_sortie_id){
      $where["praticien_id"] = " = '$praticien_sortie_id'";
    }
    
    if ($protocole_id) {
      $where["protocole_id"] = " = '$protocole_id'";
    }
    foreach ($chapitres as $chapitre) {
      $where["category_prescription.chapitre"] = " = '$chapitre'";
      $nb_element = $line_element->countList($where, null, $ljoin_element);
      $nb_comment = $line_comment->countList($where, null, $ljoin_comment);
      $this->_counts_by_chapitre[$chapitre] = $nb_element + $nb_comment;
    }
    
    if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
      $this->loadRefsLinesDMI($operation_id);
      $this->_counts_by_chapitre["dmi"] = count($this->_ref_lines_dmi);
    }
    
    $where["signee"] = " = '0'";
    foreach ($chapitres as $chapitre) {
      $where["category_prescription.chapitre"] = " = '$chapitre'";
      $nb_element = $line_element->countList($where, null, $ljoin_element);
      $nb_comment = $line_comment->countList($where, null, $ljoin_comment);
      $this->_counts_by_chapitre_non_signee[$chapitre] = $nb_element + $nb_comment;
    }
		
		// Compteur d'inscription
		$line_med = new CPrescriptionLineMedicament();
    $line_med->prescription_id = $this->_id;
    $line_med->inscription = 1;
    $this->_counts_by_chapitre["inscription"] = $line_med->countMatchingList();
    
    // Chargement des inscriptions d'element
    $line_elt = new CPrescriptionLineElement();
    $line_elt->prescription_id = $this->_id;
    $line_elt->inscription = 1;
    $this->_counts_by_chapitre["inscription"] += $line_elt->countMatchingList();
		$this->_counts_by_chapitre_non_signee["inscription"] = $this->_counts_by_chapitre["inscription"];
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
      $prescription_pre_adm->loadRefsLinesElementsComments("0", "0");
      $historique["pre_admission"] = $prescription_pre_adm;
      
      if($this->type === "sortie"){
        $prescription_sejour =& $this->_ref_object->_ref_prescriptions["sejour"];
        $prescription_sejour->loadRefsLinesMedComments("0");
        $prescription_sejour->loadRefsLinesElementsComments("0", "0");
        $historique["sejour"] = $prescription_sejour;
      }
    }
    return $historique;
  }

	
  /**
   * Count recent prescription modifications
   * acording to service config
   * Counts by chapter available
   * NEEDS loaded lines
   * 
   * @return void
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
	
	/**
	 * Count recent prescription modifications
	 * acording to service config
	 * DOES NOT NEED loaded lines
	 * 
	 * @return int Recent modification count
	 */
	function countFastRecentModif(){
		$service_id = isset($_SESSION["soins"]["service_id"]) && $_SESSION["soins"]["service_id"] ? $_SESSION["soins"]["service_id"] : "none";
      
    if ($service_id == "NP") {
      $service_id = "none";
    }
    
    $configs = CConfigService::getAllFor($service_id);
    
    // modification recente si moins de $nb_hours heures
    $nb_hours = $configs["Affichage alertes de modifications"];

		$this->_count_fast_recent_modif = 0;
		
		$recent = mbDateTime("- $nb_hours HOURS");
    
		$classes = array();
		$classes["prescription_line_medicament"] = "CPrescriptionLineMedicament";
		$classes["prescription_line_element"] = "CPrescriptionLineElement";
    $classes["prescription_line_comment"] = "CPrescriptionLineComment";
    $classes["prescription_line_mix"] = "CPrescriptionLineMix";
    
		foreach($classes as $_backprop => $_object_class){
			$ids = $this->loadBackIds($_backprop);
			$this->_count_fast_recent_modif += CUserLog::countRecentFor($_object_class, $ids, $recent);
		}
		
		return $this->_count_fast_recent_modif;
	}
	
	function countAlertes($level = "medium"){
		
		$alert = new CAlert();
		$where = array();
		$where["handled"] = " = '0'";
		$where["level"] = " = '$level'";
		$where["prescription.prescription_id"] = " = '$this->_id'";
    
		$ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = alert.object_id) 
                                             AND (alert.object_class = 'CPrescriptionLineMedicament')";
                                             
    $ljoin["prescription_line_element"] = "(prescription_line_element.prescription_line_element_id = alert.object_id) 
                                           AND (alert.object_class = 'CPrescriptionLineElement')";
                                             
    $ljoin["prescription_line_mix"] = "(prescription_line_mix.prescription_line_mix_id = alert.object_id) 
                                       AND (alert.object_class = 'CPrescriptionLineMix')";                   
                                             
    $ljoin["prescription_line_comment"] = "(prescription_line_comment.prescription_line_comment_id = alert.object_id) 
                                           AND (alert.object_class = 'CPrescriptionLineComment')";      
    
		$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
                          (prescription_line_mix.prescription_id = prescription.prescription_id) OR
													(prescription_line_comment.prescription_id = prescription.prescription_id)";
		
		return $alert->countList($where, null, $ljoin);											
	}
  
	function loadRefsAlertes($level = "medium"){
		$alert = new CAlert();
		$where = array();
    $where["handled"] = " = '0'";
		$where["level"] = " = '$level'";
    $where["prescription.prescription_id"] = " = '$this->_id'";
    
    $ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = alert.object_id) 
                                             AND (alert.object_class = 'CPrescriptionLineMedicament')";
                                             
    $ljoin["prescription_line_element"] = "(prescription_line_element.prescription_line_element_id = alert.object_id) 
                                           AND (alert.object_class = 'CPrescriptionLineElement')";
                                             
    $ljoin["prescription_line_mix"] = "(prescription_line_mix.prescription_line_mix_id = alert.object_id) 
                                       AND (alert.object_class = 'CPrescriptionLineMix')";                   
                                             
    $ljoin["prescription_line_comment"] = "(prescription_line_comment.prescription_line_comment_id = alert.object_id) 
                                           AND (alert.object_class = 'CPrescriptionLineComment')";      
    
    $ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
                          (prescription_line_mix.prescription_id = prescription.prescription_id) OR
                          (prescription_line_comment.prescription_id = prescription.prescription_id)";
	
	  $this->_ref_alertes = $alert->loadList($where, null, null, null, $ljoin);                
    
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
  function loadRefsLinesMed($with_child = 0, $with_subst = 0, $emplacement="", $order="", $protocole_id = "", $in_progress=0) {
    if ($this->_ref_prescription_lines) {
    	foreach($this->_ref_prescription_lines as $_line_med){
		    if($with_subst != "1" && $_line_med->substituted){
		    	unset($this->_ref_prescription_lines[$_line_med->_id]);
		    }
				if($with_child != "1" && $_line_med->child_id){
          unset($this->_ref_prescription_lines[$_line_med->_id]);
        }
    	}
      return;
    }
		$line = new CPrescriptionLineMedicament();
    $where = array();
    $where["prescription_id"] = " = '$this->_id'";
    $where["inscription"] = " = '0'";
    if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
    //if($with_subst != "1"){
      $where["substituted"] = " = '0'";
    //}
    // Permet de ne pas afficher les lignes de substitutions
    $where["variante_active"] = " = '1'";
    
    if ($protocole_id) {
      $where["protocole_id"] = " = '$protocole_id'";
    }
    
    if(!$order){
      $order = "prescription_line_medicament_id DESC";
    }
    $this->_ref_prescription_lines = $line->loadList($where, $order);
    
    if ($in_progress) {
      $current_date = mbDateTime();
      foreach ($this->_ref_prescription_lines as $_key_line => $_line) {
        if ($_line->_fin_reelle && $_line->_fin_reelle < $current_date) {
          unset($this->_ref_prescription_lines[$_key_line]);
        }
      }
    }
    
  }
  
  /*
   * Chargement des lignes de prescription de médicament par catégorie ATC
   */
  function loadRefsLinesMedByCat($with_child = 0, $with_subst = 0, $emplacement = "", $in_progress=0) {
    if ($this->_ref_prescription_lines_by_cat) {
      return;
    }
    $this->loadRefsLinesMed($with_child, $with_subst, $emplacement, "", "", $in_progress);
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
  function loadRefsLinesMedComments($withRefs = "1", $order="", $protocole_id="", $in_progress=0){
    // Chargement des lignes de medicaments
    $this->loadRefsLinesMed(0, 0, "", $order, $protocole_id, $in_progress);
    // Chargement des lignes de commentaire du medicament
    $this->loadRefsLinesComment("medicament", "1", $protocole_id);
    
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
  function loadRefsLinesElement($with_child = "0", $chapitre = "", $withRefs = "1", $emplacement="", $order="", $protocole_id = "", $in_progress=0){
    $line = new CPrescriptionLineElement();
    $where = array();
    $ljoin = array();
    
		if($with_child != "1"){
      $where["child_id"] = "IS NULL";
    }
		
    if($chapitre){
      $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
      $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $where["category_prescription.chapitre"] = " = '$chapitre'";
    }
    $where["prescription_id"] = " = '$this->_id'";
		$where["inscription"] = " = '0'";
    if(!$order){
      $ljoin["element_prescription"] = "prescription_line_element.element_prescription_id = element_prescription.element_prescription_id";
      $ljoin["category_prescription"] = "element_prescription.category_prescription_id = category_prescription.category_prescription_id";
      $order = "category_prescription.nom , element_prescription.libelle";
    }
    if ($protocole_id) {
      $where["protocole_id"] = " = '$protocole_id'";
    }
    
    $this->_ref_prescription_lines_element = $line->loadList($where, $order, null, null, $ljoin);
    
    if(count($this->_ref_prescription_lines_element)){
      $current_date = mbDateTime();

      foreach($this->_ref_prescription_lines_element as $key_line => &$line_element){
        if ($in_progress && ($line_element->_fin_reelle && $line_element->_fin_reelle < $current_date)) {
          unset($this->_ref_prescription_lines_element[$key_line]);
          continue;
        }
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
  function loadRefsLinesElementByCat($with_child = "0", $withRefs = "1", $chapitre = "", $emplacement="", $order="", $protocole_id = "", $in_progress=0){
    if ($this->_ref_prescription_lines_element_by_cat) {
      return;
    }
    
    $this->loadRefsLinesElement($with_child, $chapitre, $withRefs, $emplacement, $order, $protocole_id, $in_progress);
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
  function loadRefsLinesComment($chapitre = null, $withRefs = "1", $protocole_id = ""){
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
    if ($protocole_id) {
      $where["protocole_id"] = " = '$protocole_id'";
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
  function loadRefsLinesElementsComments($with_child = "0", $withRefs = "1", $chapitre="", $order="", $protocole_id="", $in_progress=0){
    $this->loadRefsLinesElementByCat($with_child, $withRefs, $chapitre, "", $order, $protocole_id, $in_progress);
    $this->loadRefsLinesComment("", $withRefs, $protocole_id);
    
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
  function loadRefsLinesDMI($operation_id = null, $protocole_id = null){
    $line_dmi = new CPrescriptionLineDMI();
    $line_dmi->prescription_id = $this->_id;
    $line_dmi->operation_id = $operation_id;
    $line_dmi->protocole_id = $protocole_id;
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
			$produit->_count = $_fav["total"];
      $listFavoris[$produit->ucd_view] = $produit;
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
			$produit->_count = $_fav_inj["total"];
      $listFavoris[$produit->ucd_view] = $produit;
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
      $listFavoris["med"] = CPrescription::getFavorisMedPraticien($praticien_id);
      $listFavoris["inj"] = CPrescription::getFavorisInjectablePraticien($praticien_id);
    } else {
      $favoris[$chapitreSel] = CElementPrescription::getFavoris($praticien_id, $chapitreSel);
    }
    if(isset($favoris)){
      foreach($favoris as $key => $typeFavoris) {
        foreach($typeFavoris as $curr_fav){
          $element = new CElementPrescription();
          $element->load($curr_fav["element_prescription_id"]);
					$element->_count = $curr_fav["total"];
          $listFavoris[$key][$element->_view] = $element;
        }
				if(isset($listFavoris[$key])){
				  ksort($listFavoris[$key]);
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
  function calculAllPlanifSysteme($perop = false){
		if (!$this->_id) {
  		return;
  	}
	
  	$this->completeField("planif_removed");
		
  	// Si les planifications ont ete supprimées de la prescriptions, on force le calcul en les supprimant toutes
		if($this->planif_removed){
  		$this->removeAllPlanifSysteme();
		}
		
  	$this->completeField("object_id", "type");
		
		$planif = new CPlanificationSysteme();
		$where = array();
		$where["sejour_id"] = " = '$this->object_id'";
		
		$ljoin = array();
		$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = planification_systeme.object_id AND 
		                                          planification_systeme.object_class = 'CPrescriptionLineMedicament'";
																							
    $ljoin["prescription_line_element"] = "prescription_line_element.prescription_line_element_id = planification_systeme.object_id AND 
                                              planification_systeme.object_class = 'CPrescriptionLineElement'";																							
		
    $ljoin["prescription_line_mix_item"] = "prescription_line_mix_item.prescription_line_mix_item_id = planification_systeme.object_id AND 
                                              planification_systeme.object_class = 'CPrescriptionLineMixItem'";		
		
		
		$ljoin["prescription_line_mix"] = "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";   
		
		$where[] = "prescription_line_medicament.perop = '0' OR  prescription_line_medicament.perop IS NULL";
		$where[] = "prescription_line_element.perop = '0' OR  prescription_line_element.perop IS NULL";
    $where[] = "prescription_line_mix.perop = '0' OR  prescription_line_mix.perop IS NULL";
    
		if(!$this->object_id || ($this->type != "sejour") || ($planif->countList($where, null, $ljoin) && !$perop)){
	   return;
    }
		
    // Chargement de toutes les lignes
    $this->loadRefsLinesMedByCat("1","1");
    $this->loadRefsLinesElementByCat("1","1");
    $this->loadRefsPrescriptionLineMixes(); 
		  
	  // Paroucrs des lignes de medicaments
    foreach($this->_ref_prescription_lines as &$_line_med){
      if(!$_line_med->_ref_prises){
        $_line_med->loadRefsPrises();
      }
      $planif = new CPlanificationSysteme();
      $planif->object_id = $_line_med->_id;
      $planif->object_class = $_line_med->_class;

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
            $planif->object_class = $_line_element->_class;
            if(!$planif->countMatchingList()){
              $_line_element->calculPlanifSysteme();
            }
          }
        }
      }
    }
		
		// Parcours des prescription_line_mixes
		foreach($this->_ref_prescription_line_mixes as $_prescription_line_mix){
      $_prescription_line_mix->calculPlanifsPerf(true);
		}
		
		// On vide planif_removed
		$this->planif_removed = 0;
    $this->store();
  }
  
  /*
   * Suppression des planifications systemes
   */
  function removeAllPlanifSysteme(){
    if(!$this->_id){
      return;
    }
		$ds = CSQLDataSource::get("std");
		$query = "DELETE planification_systeme.* FROM planification_systeme 
		          WHERE planification_systeme.sejour_id = '$this->object_id';";
		$ds->exec($query);
		
		// Sauvegarde de planif_removed pour indiquer que les planifs doivent etre re-calculer
		$this->planif_removed = 1;
		$this->store();
	}
	
  /*
   * Génération du Dossier/Feuille de soin
   */
  function calculPlanSoin($dates, $mode_feuille_soin = 0, $mode_semainier = 0, $mode_dispensation = 0, $code_cip = "", $with_calcul = true, $code_cis = ""){
		$manual_planif = $mode_dispensation ? 0 : CAppUI::conf("dPprescription CPrescription manual_planif");
		 
	  $alert_handler = @CAppUI::conf("object_handlers CPrescriptionAlerteHandler");
		
		$this->calculAllPlanifSysteme();
		
		// Chargement des lignes d'inscriptions
		$this->loadRefsLinesInscriptions();
		if($this->_count_inscriptions){
			foreach($this->_ref_lines_inscriptions as $_lines_inscription_by_type){
				foreach($_lines_inscription_by_type as $_inscription){
					if($with_calcul){
            foreach($dates as $date){
              $_inscription->calculAdministrations($date, $mode_dispensation);
            }
          }
					
					if ($_inscription instanceof CPrescriptionLineElement && $_inscription->_ref_element_prescription->rdv){
            $_inscription->loadRefTask();
          }
					
          $this->_ref_inscriptions_for_plan[$_inscription->_id] = $_inscription;
				}
			}
		}

    // Parcours des lignes de smedicaments
		$this->_nb_lines_plan_soins["med"] = 0;
    $this->_nb_lines_plan_soins["inj"] = 0;
		if($alert_handler){
	    $this->_count_recent_modif["med"] = false;
			$this->_count_recent_modif["inj"] = false;
	    $this->_count_urgence["med"] = false;
	    $this->_count_urgence["inj"] = false;
		}
		
    if(count($this->_ref_prescription_lines)){
      
      // La variable count permet d'incrément une seule fois le nombre de lignes
      // dans le cas de plusieurs journées
      foreach($this->_ref_prescription_lines as &$_line_med){
        $count = 0;
      	if($_line_med->perop){
      		continue;
      	}
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
	          $count ++;
	        	$type_med = $_line_med->_is_injectable ? "inj" : "med";
					  if ($count == 1) $this->_nb_lines_plan_soins[$type_med]++;
						
						if($alert_handler){
							if($_line_med->_recent_modification){
	              if($_line_med->_urgence){
								  $this->_count_urgence[$type_med] = true;
								} else {
								  $this->_count_recent_modif[$type_med] = true;
                }
							}
						}
						
	          if ((count($_line_med->_ref_prises) < 1) && (!isset($this->_lines["med"][$code_ATC][$_line_med->_id]["aucune_prise"]))){
	            if($_line_med->_is_injectable){
	              $this->_ref_injections_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;  
	            } else { 
	              $this->_ref_lines_med_for_plan[$code_ATC][$_line_med->_id]["aucune_prise"] = $_line_med;
	            }
	            continue;
	          }
						
	          $_line_med->calculPrises($this, $date, null, null, $with_calcul, $manual_planif);
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
          $this->_nb_lines_plan_soins[$name_chap] = 0;
					if($alert_handler){
            $this->_count_recent_modif[$name_chap] = false;
					  $this->_count_urgence[$name_chap] = false;
					}
          foreach($elements_chap as $name_cat => $elements_cat){
            foreach($elements_cat as &$_elements){
              foreach($_elements as &$_line_element){
              	if($_line_element->perop){
				          continue;
				        }
                if(!$_line_element->signee && !CAppUI::conf("dPprescription CPrescription show_unsigned_lines")){
                  continue;  
                }
								
								if($_line_element->cip_dm){
									$_line_element->loadRefDM();
								}
								
								if($_line_element->_ref_element_prescription->rdv){
									$_line_element->loadRefTask();
								}
								
								// Incrément du nombre de lignes seulement quand count vaudra 1
								// (cas de plusieurs journées)
								$count = 0;
								
                // Chargement des administrations et des transmissions
                if($with_calcul){
                	foreach($dates as $date){
                    $_line_element->calculAdministrations($date);
									}
                }
                
                foreach($dates as $date){
	                // Pre-remplissage des prises prevues dans le dossier de soin
	                if(($date >= $_line_element->debut && $date <= mbDate($_line_element->_fin_reelle))){
	                  $count ++;
	                  if ($count == 1) {
	                	  $this->_nb_lines_plan_soins[$name_chap]++;
	                  }
										if($alert_handler){
										  if($_line_element->_recent_modification){
				                if($_line_element->_urgence){
				                	$this->_count_urgence[$name_chap] = true;
				                } else {
				                  $this->_count_recent_modif[$name_chap] = true;
                        }
											}
										}
	                  // Si aucune prise  
	                  if ((count($_line_element->_ref_prises) < 1) && (!isset($this->_lines["elt"][$name_chap][$name_cat][$_line_element->_id]["aucune_prise"]))){
	                    $this->_ref_lines_elt_for_plan[$name_chap][$name_cat][$_line_element->_id]["aucune_prise"] = $_line_element;
	                  }
	                  $_line_element->calculPrises($this, $date, $name_chap, $name_cat, $with_calcul, $manual_planif);
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
    	if($alert_handler){
				$this->_count_recent_modif["aerosol"] = false;
				$this->_count_recent_modif["oxygene"] = false;
	      $this->_count_recent_modif["perfusion"] = false;
	      $this->_count_urgence["aerosol"] = false;
	      $this->_count_urgence["oxygene"] = false;
	      $this->_count_urgence["perfusion"] = false;
			}
      foreach($this->_ref_prescription_line_mixes as &$_prescription_line_mix){
         if($_prescription_line_mix->perop){
           continue;
         }
         if(!$_prescription_line_mix->signature_prat && !CAppUI::conf("dPprescription CPrescription show_unsigned_lines")){
           continue;  
         }
				 $count = 0;
				 $_prescription_line_mix->calculQuantiteTotal();
				 foreach($dates as $date){
	         if(($date >= mbDate($_prescription_line_mix->_debut)) && ($date <= mbDate($_prescription_line_mix->_fin))){
	         	 $count ++;
	           if ($count == 1) {
	             @$this->_nb_lines_plan_soins[$_prescription_line_mix->type_line]++;
	           }
						
						if($_prescription_line_mix->_recent_modification){
							if($alert_handler){
							  $this->_count_recent_modif[$_prescription_line_mix->type_line] = true;
							}
						}

	           if($with_calcul){
	           	 $_prescription_line_mix->calculPrisesPrevues($date, $manual_planif);
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
    foreach($this->_chapter_view as $_chapitre => $list_chapitre){
    	$loc_type = CAppUI::tr("CPrescription.type.$this->type");
			$loc_chapitre = CAppUI::tr("CPrescription._chapitres.$_chapitre");
      $template->addListProperty("Prescription $loc_type - $loc_chapitre", $list_chapitre);
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
    $modele->user_id = $praticien->_id;
    $modele->type = $type;
    $modele->loadMatchingObject();
    if(!$modele->_id){
      // Recherche du modele au niveau de la fonction
      $modele->user_id = null;
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
  
  function docsEditable() {
    return true;
  }
	
	function delete(){
	  // Suppression des references aux protocoles
		$this->completeField("object_id");
		if (!$this->object_id) {
			$query = "UPDATE `prescription_line_medicament` 
			          SET `protocole_id` = NULL
								WHERE `protocole_id` = '$this->_id';";
			$this->_spec->ds->exec($query);

			$query = "UPDATE `prescription_line_element` 
                SET `protocole_id` = NULL
                WHERE `protocole_id` = '$this->_id';";
      $this->_spec->ds->exec($query);
      
			$query = "UPDATE `prescription_line_comment` 
                SET `protocole_id` = NULL
                WHERE `protocole_id` = '$this->_id';";
      $this->_spec->ds->exec($query);
      
			$query = "UPDATE `prescription_line_mix`
                SET `protocole_id` = NULL
                WHERE `protocole_id` = '$this->_id';";
      $this->_spec->ds->exec($query);
		}
	
 	  return parent::delete();	
	}
}

?>