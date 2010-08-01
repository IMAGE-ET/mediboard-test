<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineMix extends CMbObject {
	// DB Table key
  var $prescription_line_mix_id = null;
  
  // DB Fields
  var $prescription_id  = null; // Prescription
  var $type             = null; // Type de prescription_line_mix: seringue electrique / PCA
  var $libelle          = null; // Libelle de la prescription_line_mix
  var $vitesse          = null; // Stockée en ml/h
  var $voie             = null; // Voie d'administration des produits
  var $interface        = null; // Interface d'administration
  var $date_debut       = null; // Date de debut
  var $time_debut       = null; // Heure de debut
  var $duree            = null; // Duree de la perf (en heures)
  var $unite_duree      = null; 
  
  var $next_line_id     = null; // Perfusion suivante (pour garder un historique lors de la modification de la prescription_line_mix)
  var $praticien_id     = null; // Praticien responsable de la prescription_line_mix
  var $creator_id       = null; // Createur de la prescription_line_mix
  var $signature_prat   = null; // Signature par le praticien responsable
  var $signature_pharma = null; // Signature par le pharmacien
  var $validation_infir = null; // Validation par l'infirmiere
  var $date_arret       = null; // Date d'arret de la perf (si arret anticipé) 
  var $time_arret       = null; // Heure d'arret de la perf (si arret anticipe)
  var $accord_praticien = null;
  var $decalage_interv  = null; // Nb heures de decalage par rapport à l'intervention (utilisé pour les protocoles de prescription_line_mixes)
  var $operation_id     = null;
  var $commentaire      = null;
	var $type_line        = null;
	
  var $date_pose   = null; // Date de la pose de la perf
  var $time_pose   = null; // Heure de la pose de la perf
  
  var $date_retrait     = null; // Date de retrait de la perf
  var $time_retrait     = null; // Heure de retrait de la perf
  var $emplacement      = null;
  var $nb_tous_les      = null;
  
	
	var $quantite_totale = null; // valeur en ml
	var $duree_passage   = null; // minutes
	var $unite_duree_passage = null;
	
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
	
	var $jour_decalage = null;
	
	// Fwd Refs
  var $_ref_prescription = null;
  var $_ref_praticien    = null;
  
  // Back Refs
  var $_ref_lines        = null;
  
  // Form fields
  var $_debut             = null; // Debut de la prescription_line_mix (dateTime)
  var $_fin               = null; // Fin de la prescription_line_mix (dateTime)
  var $_protocole         = null; // Perfusion de protocole
  var $_add_perf_contigue = null;
  var $_count_parent_line = null;
  var $_recent_modification = null;
  var $_count_substitution_lines = null;

  var $_pose = null;
  var $_retrait   = null;
  var $_voies = null;
  var $_active = null;
	var $_nb_gouttes = null;
	
  // Object references
  var $_ref_log_signature_prat = null;
  var $_ref_substitute_for = null; // ligne (med ou perf) que la ligne peut substituer

  var $_ref_variations = null;

  var $_short_view = null;

  // Can fields
  var $_perm_edit                        = null;
  var $_can_modify_prescription_line_mix             = null;
  var $_can_modify_prescription_line_mix_item        = null;
  var $_can_vw_form_signature_praticien  = null;
  var $_can_vw_form_signature_pharmacien = null;
  var $_can_vw_form_signature_infirmiere = null;
  var $_can_vw_signature_praticien       = null;
  var $_can_delete_prescription_line_mix             = null;
  var $_can_delete_prescription_line_mix_item        = null;
  var $_can_vw_form_add_perf_contigue    = null;
  var $_can_vw_form_stop_perf            = null;
  
  var $_quantite_totale                  = null;
  var $_prises_prevues                   = null;

  var $_frequence = null;
	var $_continuite = null;  // continue, discontinue
	var $_last_debit = null;
	var $_variations = null;
	var $_last_variation = null;
	
	static $type_by_line = array(
	  "perfusion"    => array("classique", "seringue", "PCA"),
		"oxygene"      => array("masque", "lunettes", "sonde"),
		"aerosol"      => array("nebuliseur_ultrasonique", "nebuliseur_pneumatique", "doseur", "inhalateur"),
		"alimentation" => array("")
	);
	
	static $interface_by_line = array(
		"aerosol" => array("buccal", "nasal", "bucco_nasal", "masque_facial")
	);
	
	static $unite_by_line = array(
	  "perfusion" => "ml/h",
		"oxygene"   => "l/min"
	);
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_mix';
    $spec->key   = 'prescription_line_mix_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
  	$specs["prescription_id"]        = "ref class|CPrescription cascade";
  	$specs["type_line"]              = "enum notNull list|perfusion|aerosol|oxygene|alimentation default|perfusion";
    $specs["type"]                   = "enum list|classique|seringue|PCA|masque|lunettes|sonde|nebuliseur_ultrasonique|nebuliseur_pneumatique|doseur|inhalateur";
		$specs["libelle"]                = "str";
    $specs["vitesse"]                = "num pos";
    $specs["voie"]                   = "str";
		$specs["interface"]              = "str";
    $specs["date_debut"]             = "date";
    $specs["time_debut"]             = "time";
    $specs["duree"]                  = "num pos";
		$specs["unite_duree"]            = "enum list|heure|jour default|heure";
    $specs["next_line_id"]           = "ref class|CPrescriptionLineMix"; 
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
    $specs["substitute_for_class"]   = "enum list|CPrescriptionLineMedicament|CPrescriptionLineMix default|CPrescriptionLineMix";
    $specs["substitution_active"]    = "bool";
    $specs["substitution_plan_soin"] = "bool";
    $specs["nb_tous_les"]            = "num";
    $specs["commentaire"]            = "str helped";
		$specs["conditionnel"]           = "bool";
    $specs["condition_active"]       = "bool";
    $specs["jour_decalage"]          = "enum list|I|N default|I"; // Permet de noter N comme jour de decalage
	  $specs["quantite_totale"]        = "num";
		$specs["duree_passage"]          = "num";
		$specs["unite_duree_passage"]    = "enum list|minute|heure default|minute";
		return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    
    // Calcul de la view
    $this->_view = ($this->libelle) ? "$this->libelle " : "";
    $this->_view .= ($this->type) ? " ".CAppUI::tr("CPrescriptionLineMix.type.$this->type").", " : "";
    $this->_view .= $this->voie;
    $this->_view .= ($this->vitesse) ? " à $this->vitesse ml/h" : "";
    $this->_view .= ($this->nb_tous_les) ? " toutes les $this->nb_tous_les h" : "";
    
    if($this->vitesse){
      $this->_frequence = "à $this->vitesse ml/h";
    }
    if($this->nb_tous_les){
      $this->_frequence = "toutes les $this->nb_tous_les h";
    }
    
    if($this->type == "PCA"){
      $this->_view .= ($this->mode_bolus) ? ", mode PCA: ".CAppUI::tr("CPrescriptionLineMix.mode_bolus.".$this->mode_bolus) : "";
      $this->_view .= ($this->dose_bolus) ? ", bolus de $this->dose_bolus mg" : "";
      $this->_view .= ($this->periode_interdite) ? ", période interdite de $this->periode_interdite min" : "";
    }
        
    if($this->date_pose && $this->time_pose){
      $this->_pose = "$this->date_pose $this->time_pose";
    }
    if($this->date_retrait && $this->time_retrait){
      $this->_retrait = "$this->date_retrait $this->time_retrait";
    }
    
    // Calcul du debut de la prescription_line_mix
    $this->_debut = ($this->date_pose) ? "$this->date_pose $this->time_pose" : "$this->date_debut $this->time_debut";

    // Calcul de la fin de la prescription_line_mix
		$increment = ($this->type_line == "aerosol") ? "DAYS" : "HOURS";
		
    $this->_date_fin = $this->duree ? mbDateTime("+ $this->duree $increment", "$this->_debut") : $this->_debut;
    $this->_fin = ($this->date_arret && $this->time_arret) ? "$this->date_arret $this->time_arret" 
                                                           : ($this->date_retrait ? "$this->date_retrait $this->time_retrait" : $this->_date_fin); 

    $this->loadRefPrescription();
    $this->_protocole = $this->_ref_prescription->object_id ? '0' : '1';
    $this->getRecentModification();    
    
    if($this->_protocole){
      $this->countSubstitutionsLines();
    }
		$this->_active = (!$this->conditionnel) ? 1 : $this->condition_active;
		
		if($this->vitesse){
		  $this->_continuite = "continue";
		}
		if(($this->nb_tous_les || $this->duree_passage) && $this->type_line != "oxygene"){
			$this->_continuite = "discontinue";
		}
  }
  	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["lines_mix"] = "CPrescriptionLineMixItem prescription_line_mix_id";
    $backProps["prev_line"] = "CPrescriptionLineMix next_line_id";
    $backProps["transmissions"] = "CTransmissionMedicale object_id";
    $backProps["substitutions_medicament"] = "CPrescriptionLineMedicament substitute_for_id";
    $backProps["substitutions_prescription_line_mix"]  = "CPrescriptionLineMix substitute_for_id";
		$backProps["variations"] = "CPrescriptionLineMixVariation prescription_line_mix_id";
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
    	$current_user = new CMediusers();
      $current_user->load($AppUI->user_id);
			
			if($mode_pharma && $this->signature_pharma){
				$perm_edit = 0;
			} else {
        $perm_edit = ($can->admin && !$mode_pharma) ||
				             ((!$this->signature_prat || $mode_pharma) && 
                     ($this->praticien_id == $AppUI->user_id || $is_praticien || $operation_id || $mode_pharma || ($current_user->isInfirmiere() && CAppUI::conf("dPprescription CPrescription droits_infirmiers_med"))));
			}
		}
    $this->_perm_edit = $perm_edit;
    
    // Modification de la prescription_line_mix et des lignes des prescription_line_mixes
    if($perm_edit){
    	$this->_can_modify_prescription_line_mix = 1;
    	$this->_can_modify_prescription_line_mix_item = 1;
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
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id)){
    	$this->_can_vw_form_signature_praticien = 1;
    }
    // View signature praticien
    if(!$this->_protocole){
    	$this->_can_vw_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
		/*
    if(!$mode_pharma && !$this->_protocole && !$is_praticien && !$this->signature_prat && $this->creator_id == $AppUI->user_id && !$this->signature_pharma){
    	$this->_can_vw_form_signature_infirmiere = 1;
    }*/
		
    // Suppression de la ligne
    if ($perm_edit || $this->_protocole){
      $this->_can_delete_prescription_line_mix = 1;
      $this->_can_delete_prescription_line_mix_item = 1;
  	}
	}
  
	function getRecentModification(){
	  $nb_hours = CAppUI::conf("dPprescription CPrescription time_alerte_modification");
    $this->_recent_modification = $this->hasRecentLog($nb_hours);
	}
	
	/*
	 * Duplication d'une prescription_line_mix
	 */
	function duplicatePerf(){
    $this->_add_perf_contigue = false;
    
	  // Creation de la nouvelle prescription_line_mix
	  $new_perf = new CPrescriptionLineMix();
    $new_perf->load($this->_id);
    $new_perf->loadRefsLines();
    $new_perf->_id = "";
    $new_perf->signature_pharma = 0;
    $new_perf->signature_prat = 0;
    if($msg = $new_perf->store()){
      return $msg;
    }
    
    // Copie des lignes dans la prescription_line_mix
    foreach($new_perf->_ref_lines as $_line){
      $_line->_id = "";
      $_line->prescription_line_mix_id = $new_perf->_id;
      if($msg = $_line->store()){
        return $msg;
      }
    }

    // Arret de la ligne et creation de l'historique
    $this->date_arret = mbDate();
    $this->time_arret = mbTime();
    $this->next_line_id = $new_perf->_id;
	}

	function loadRefsTransmissions(){
	  $this->_ref_transmissions = $this->loadBackRefs("transmissions");
	  foreach($this->_ref_transmissions as &$_trans){
	    $_trans->loadRefsFwd();
	  }
	}
	
	function loadRefsVariations(){
	  $this->_ref_variations = $this->loadBackRefs("variations", "dateTime");
		
		if(count($this->_ref_variations)){
		  $this->_last_variation = end($this->_ref_variations);
		} else {
  		$this->_last_variation = new CPrescriptionLineMixVariation();
			$this->_last_variation->debit = $this->vitesse;
			$this->_last_variation->prescription_line_mix_id = $this->_id;
  	}
	}
	
	function calculVariations(){
		$max_height = 2; // 2 em

		// Perfusion continue (avec eventuellement des changements de debit)
		if($this->_continuite == "continue"){
			// Calcul des modifications du debit
	    $this->loadRefsVariations();
	
		  $_date = mbTransformTime(null, $this->_debut, "%Y-%m-%d %H:00:00");
		  $current_debit = $this->vitesse;
		  $max_debit = $current_debit ? $current_debit : 1;
			
			$variation_id = "perf";
			
			if($this->_debut != $_date){
				$_variations[$_date][mbTime($_date)]["debit"] = '';
				$_variations[$_date][mbTime($_date)]["variation_id"] = "perf";
				
				$_variations[$_date][mbTime($this->_debut)]["debit"] = $this->vitesse;
        $_variations[$_date][mbTime($this->_debut)]["variation_id"] = "perf";
        
        $_date = mbDateTime("+ 1 hour", $_date);
			}
			
		  while($_date <= $this->_fin){
			  $_variations[$_date][mbTime($_date)]["debit"] = $current_debit;
				$_variations[$_date][mbTime($_date)]["variation_id"] = $variation_id;
			  foreach($this->_ref_variations as $_variation){
			 	  if($_variation->dateTime >= $_date && $_variation->dateTime < mbDateTime("+ 1 hour", $_date)){
		 	   	  $current_debit = $_variation->debit;
				   
						$_variations[$_date][mbTime($_variation->dateTime)]["debit"] = $current_debit;
						$variation_id = $_variations[$_date][mbTime($_variation->dateTime)]["variation_id"] = $_variation->_id;
				  }
					$max_debit = max($max_debit, $_variation->debit);
	      }
		    $_date = mbDateTime("+ 1 hour", $_date);
			}
		  
			foreach($_variations as $key => &$_variations_by_hour){
		    krsort($_variations_by_hour);
				
				if(count($_variations_by_hour) == 1){
					$_variations[$key][mbTime($key)]["pourcentage"] = "100";
					$_variations[$key][mbTime($key)]["height"] = round($_variations[$key][mbTime($key)]["debit"] * $max_height / $max_debit, 1);
				  $_variations[$key][mbTime($key)]["normale"] = round($this->vitesse * $max_height / $max_debit, 1);
	    	}
				else {
					$prev_hour_variation = 0;
					foreach($_variations_by_hour as $_hour_variation => $_debit_variation){
						if($prev_hour_variation){
							$_nb_min = mbTransformTime(null, $prev_hour_variation, "%M") - mbTransformTime(null, $_hour_variation, "%M");
						} else {
							$_nb_min = 60 - mbTransformTime(null, $_hour_variation, "%M");
						}
						$prev_hour_variation = $_hour_variation;
						$pourcentage = round($_nb_min * 100 / 60);
						$_variations[$key][$_hour_variation]["pourcentage"] = $pourcentage;
						$_variations[$key][$_hour_variation]["height"] = round($_debit_variation["debit"] * $max_height / $max_debit, 1);
						$_variations[$key][$_hour_variation]["variation_id"] = $_debit_variation["variation_id"];
						$_variations[$key][$_hour_variation]["normale"] = round($this->vitesse * $max_height / $max_debit, 1);
					}
				}
				ksort($_variations_by_hour);
			}
      $this->_variations = $_variations;
	  } 
		
	}
	
  function loadView() {
  	parent::loadView();
    $this->loadRefsLines();
    $this->loadRefsTransmissions();
  }
	
	function removePlanifSysteme(){
		if(!$this->_ref_lines){
		  $this->loadRefsLines();
		}
		foreach($this->_ref_lines as $_perf_line){
	    $planifSysteme = new CPlanificationSysteme();
	    $planifSysteme->object_id = $_perf_line->_id;
	    $planifSysteme->object_class = $_perf_line->_class_name;
	    $planifs = $planifSysteme->loadMatchingList();
	    foreach($planifs as $_planif){
	      $_planif->delete();
	    }
		}
  }
	
	/*
   * Calcul des prises prevues pour la prescription_line_mix
   */
  function calculPrisesPrevues($date){
  	if(!$this->_ref_lines){
  		return;
  	}
  	$line_perf = reset($this->_ref_lines);
		
		$planif = new CPlanificationSysteme();
		$where["object_id"] = " = '$line_perf->_id'";
		$where["object_class"] = " = '$line_perf->_class_name'";
		$where["dateTime"] = " LIKE '$date%'";
		$planifs = $planif->loadList($where, "dateTime ASC");
		
		foreach($planifs as $_planif){
			$date = mbDate($_planif->dateTime);
      $hour = mbTransformTime(null, $_planif->dateTime, "%H");
			$this->_prises_prevues[$date][$hour]["real_hour"][] = mbTime($_planif->dateTime);
			$this->_prises_prevues[$date][$hour]["plan_hour"] = "$hour:00:00";
    }
  }
	
	/*
   *  Calcul de la quantite totale de la perf en ml ($this->_quantite_totale)
   */
  function calculQuantiteTotal(){
    if(!$this->_ref_lines){
      $this->loadRefsLines();
    }
    $this->_quantite_totale = 0;
  
    foreach($this->_ref_lines as $_perf_line){
      $_perf_line->updateQuantiteAdministration();
    }
  }
	
	function calculPlanifsPerf(){
		// Calcul de la quantite totale de la perf en fonction des produits
    $this->calculQuantiteTotal();
		
		$volume_restant = 0;
		
		$dates_planif = array();
    if($this->date_debut && $this->time_debut && $this->duree){
      $date_time_temp = $this->_debut;
      $dates_planif[] = $date_time_temp;
      
      // Perfusion à la vitesse de x ml/h
      if($this->vitesse && $this->_quantite_totale){
      	
				// Chargement de toutes les variations
				$this->loadRefsVariations();
				
				// Initialisation au valeur de depart
				$prec_variation = $this->_debut;
				$prec_debit = $this->vitesse;
				
				$last_variation = new CPrescriptionLineMixVariation();
				$last_variation->debit = 0;
				$last_variation->dateTime = $this->_fin;
				$this->_ref_variations[] = $last_variation;
				
				// La quantite restante dans la perf est la quantite totale de la perf (quantite mise au depart)
				$qte_restante = $this->_quantite_totale;
				$volume_variation = 0;
				$current_date = $this->_debut;
        
				// Parcours des variations
				foreach($this->_ref_variations as $_variation){
          $duree_variation = mbHoursRelative($prec_variation, $_variation->dateTime);
					$volume_variation += round($prec_debit * $duree_variation);
          
					if($volume_restant){
						if($prec_debit){
						  $_duree_volume_restant = round(($volume_restant / $prec_debit) * 60);
					  	$current_date = mbDateTime("$_duree_volume_restant minutes", $prec_variation);
						  $dates_planif[] = $current_date;
						}
						$volume_restant = 0;
					}
				
					if($volume_variation < $qte_restante){
						$volume_restant = $qte_restante - $volume_variation;
						$_duree_variation = round($duree_variation * 60);
						$current_date = mbDateTime("$_duree_variation minutes", $current_date);
				  }
					
					// si le volume de la variation est plus grand que le contenu de la perf (plusieurs remplissages dans la meme variation)
					else {
					  while($volume_variation >= $qte_restante){
	            // Modification du volume total de la variation en fonction de ce qui est consommé
							$volume_variation = $volume_variation - $qte_restante;
							
							// Calcul de la duree de la consommation en fonction du debit de la prescription_line_mix
							$_duree = $qte_restante / $prec_debit;
							
							// Calcul de l'heure de la prise
	            if($_duree){
	              $increment = round($_duree * 60);
	              $current_date = mbDateTime("$increment minutes", $current_date);
							}
	            
							// Si la quantité restant dans la prescription_line_mix est superieure au volume de la variation, on calcule le volume restant 
							if($volume_variation <= $qte_restante){
								$volume_restant = $qte_restante - $volume_variation;                
							}
	            
							if($current_date < $_variation->dateTime){
							  $dates_planif[] = $current_date;
							}
            }
					}
					$prec_variation = $_variation->dateTime;
          $prec_debit = $_variation->debit;
				}
			}
			
			// Perfusion toutes les x heures
      if($this->nb_tous_les){
        while((mbDateTime("+ $this->nb_tous_les hours", $date_time_temp)) < $this->_fin){
          $date_time_temp = mbDateTime("+ $this->nb_tous_les hours", $date_time_temp);
          $dates_planif[] = $date_time_temp;
        }
      }
    }
		
    // Creation des planifications
		foreach($this->_ref_lines as $_perf_line){
      foreach($dates_planif as $_datetime){
        $new_planif = new CPlanificationSysteme();
        $new_planif->dateTime = $_datetime;
        $new_planif->object_id = $_perf_line->_id;
        $new_planif->object_class = $_perf_line->_class_name;
        $new_planif->sejour_id = $this->_ref_prescription->object_id;    
        $new_planif->store();
			}
    }
	}
  	
  function store(){
    if($this->_add_perf_contigue){
      if($msg = $this->duplicatePerf()){
        return $msg;
      }
    }
              
    $creation = !$this->_id;
    $calculPlanif =  ($this->fieldModified("vitesse") || 
		                  $this->fieldModified("nb_tous_les") || 
											$this->fieldModified("date_debut") || 
											$this->fieldModified("time_debut") ||
											$this->fieldModified("duree") ||
											$this->fieldModified("date_pose")||
											$this->fieldModified("time_pose")||
                      $this->fieldModified("date_retrait")||
                      $this->fieldModified("date_retrait")||
                      $this->fieldModified("date_arret")||
											$this->fieldModified("time_arret")||
											$this->fieldModified("conditionnel")||
											$this->fieldModified("condition_active")||
											$this->fieldModified("substitution_active"));
													
    if($msg = parent::store()){
  		return $msg;
    }

		if($calculPlanif){
			if($this->_ref_prescription->type == "sejour"){
			  $this->removePlanifSysteme();
				if($this->substitution_active && (!$this->conditionnel || ($this->conditionnel && $this->condition_active))){
					$this->calculPlanifsPerf();
				}
			}
		}
		
  	// On met en session le dernier guid créé
    if($creation){
  	  $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
    }
  }
  
  function delete(){
    // On supprime la ref vers la perf courante
    $prescription_line_mix = new CPrescriptionLineMix();
    $prescription_line_mix->next_line_id = $this->_id;
    $prescription_line_mix->loadMatchingObject();
    if($prescription_line_mix->_id){
      // On vide le child_id
      $prescription_line_mix->next_line_id = "";
      if($msg = $prescription_line_mix->store()){
        return $msg;
      }
    }
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
      $this->_ref_substitution_lines["CPrescriptionLineMix"] = $this->loadBackRefs("substitutions_prescription_line_mix");  
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
		foreach($this->_ref_substitution_lines["CPrescriptionLineMix"] as $_substitution_line){
      $_substitution_line->loadRefsLines();
    }
  }
  
  /*
   * Permet de connaitre le nombre de lignes de substitutions possibles
   */
  function countSubstitutionsLines(){
    if(!$this->substitute_for_id){
      $this->_count_substitution_lines = $this->countBackRefs("substitutions_medicament") + $this->countBackRefs("substitutions_prescription_line_mix");
    } else {
      $object = new $this->substitute_for_class;
      $object->load($this->substitute_for_id);
      $object->countSubstitutionsLines();    
      $this->_count_substitution_lines = $object->_count_substitution_lines;
    }
  }
  
  /*
   * Chargement récursif des parents d'une prescription_line_mix
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
   * Chargement des lignes de la prescription_line_mix
   */
  function loadRefsLines(){
    $this->_ref_lines = $this->loadBackRefs("lines_mix");  
		if(!$this->_short_view){
		  foreach($this->_ref_lines as $_perf_line){
		  	$this->_short_view .= $_perf_line->_ref_produit->libelle_abrege.", ";
		  }
			$this->_short_view .= "($this->voie - ".CAppUI::tr('CPrescriptionLineMix.type.'.$this->type).")";
		}
	}
  
  /*
   * Chargement des differentes voies disponibles pour la prescription_line_mix
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
	        $this->_voies[$_voie] = $_voie;
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
    $prescription_line_mix = new CPrescriptionLineMix();
    $prescription_line_mix->next_line_id = $this->_id;
    $this->_count_parent_line = $prescription_line_mix->countMatchingList(); 
  }
  
  /*
   * Chargement du log de signature du praticien
   */
  function loadRefLogSignaturePrat(){
    $this->_ref_log_signature_prat = $this->loadLastLogForField("signature_prat");
  }
	
	/*
	 * Creation d'une ligne d'oxygene a partir des infos du médicament
	 */
	function bindOxygene($values = array()){
		$prescription_id      = $values['prescription_id'];
	  $praticien_id         = $values['praticien_id'];
		$substitute_for_id    = $values['substitute_for_id'];
    $code_cip             = $values['code_cip'];

	  // Voir les types d'oxygene
	  $this->type = "masque";
	  $this->type_line = "oxygene";
		$this->unite_duree = "heure";
		$this->unite_duree_passage = "heure";
	  $this->prescription_id = $prescription_id;
	  $this->creator_id = CAppUI::$user->_id;
	  $this->praticien_id = $praticien_id;
	  $this->substitute_for_id = $substitute_for_id;

    if(isset($values['debut'])){
      $this->date_debut = $values['debut'];
    }
    if(isset($values['time_debut'])){
      $this->time_debut = $values['time_debut'];
    }
		if(isset($values['substitute_for_class'])){
			$this->substitute_for_class = $values['substitute_for_class'];
		}
	  if($this->substitute_for_id){
	    $this->substitution_active = 0;
	  }
	  
		// Sauvegarde de la voie lors de la creation de la ligne
    $produit = new CBcbProduit();
		$produit->load($code_cip);
    $produit->loadVoies();
		$this->voie = $produit->voies[0];
      
    $msg = $this->store();
	  CAppUI::stepAjax("CPrescriptionLineMix-msg-create");
    
	  $prescription_line_mix_item = new CPrescriptionLineMixItem();
	  $prescription_line_mix_item->prescription_line_mix_id = $this->_id;
	  $prescription_line_mix_item->code_cip = $code_cip;
	  
		$msg = $prescription_line_mix_item->store();
    CAppUI::stepAjax("CPrescriptionLineMixItem-msg-create");
	}
}
  
?>