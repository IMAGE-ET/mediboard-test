<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrisePosologie extends CMbMetaObject {
  
  // DB Table key
  var $prise_posologie_id = null;
  
  // DB Fields
  var $moment_unitaire_id    = null;
  var $quantite              = null;
  var $urgence_datetime      = null;
  
  // Fois par
  var $nb_fois               = null;
  var $unite_fois            = null;
  
  // Tous les
  var $nb_tous_les           = null;
  var $unite_tous_les        = null;
  var $decalage_prise        = null;  // decalage de la prise J + $decalage (par defaut 0)
  var $unite_prise           = null;

  // I ou A +/- X heures
  var $type_decalage = null;
	var $decalage_intervention = null;  // decalage en heures par rapport à l'intervention
  var $unite_decalage_intervention = null;
  var $heure_prise           = null;  // heure calculée
  
  
  var $condition             = null;
	var $datetime              = null;
	
  var $_type                 = null; // Type de prise
  var $_unite                = null; // Unite de la prise
  var $_heures               = null; // Heure de la prise
  var $_unite_temps          = null;
  var $_quantite_with_kg     = null;  // Permet d'eviter de recalculer plusieurs fois la quantite en fonction du poids
  var $_quantite_with_coef   = null;  // Permet d'eviter de recalculer plusieurs fois la quantite en fonction du coef
  var $_unite_sans_kg        = null;  // Unite sans le kg
  var $_quantite_dispensation = null;
  var $_quantite_administrable = null; // Quantité de produit en unité d'administration
  var $_urgent = null;
	var $_now    = null;
  var $_equivalence_unite_prise = null;
  var $_view_unite_prise = null;
	var $_short_view_unite_prise = null;
	var $_ref_planifications_systemes = null;
	var $_quantite_UI = null; // Quantite exprimée en Unité Internationale (UI)
	static $cache_produits = array();
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prise_posologie';
    $spec->key   = 'prise_posologie_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]          = "ref notNull class|CMbObject meta|object_class cascade";
    $specs["object_class"]       = "enum notNull list|CPrescriptionLineMedicament|CPrescriptionLineElement";
    $specs["moment_unitaire_id"] = "ref class|CMomentUnitaire";
    $specs["quantite"]           = "float";
    $specs["nb_fois"]            = "float";
    $specs["unite_fois"]         = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour";
    $specs["nb_tous_les"]        = "float";
    $specs["unite_tous_les"]     = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an|lundi|mardi|mercredi|jeudi|vendredi|samedi|dimanche default|jour";
    $specs["decalage_prise"]     = "num min|0";
    $specs["unite_prise"]        = "text";
    $specs["decalage_intervention"] = "num";
    $specs["heure_prise"]           = "time";
    $specs["urgence_datetime"]   = "dateTime";          
    
    $specs["unite_decalage_intervention"] = "enum list|minute|heure default|heure show|0";  
		$specs["type_decalage"] = "enum list|I|A default|I";
    $specs["condition"] = "str show|0";
    $specs["datetime"] = "dateTime show|0";
    $specs["_urgent"] = "bool";
		$specs["_now"] = "bool";
    
    $specs["_quantite_UI"]          = "float";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administration"] = "CAdministration prise_id";
		$backProps["planification_systeme"] = "CPlanificationSysteme prise_id";
    return $backProps;
  }
 
  function updateFormFields(){
    parent::updateFormFields();
    
    // Suppression de l'equivalence entre unite de prise à l'affichage
    if(!$this->_view_unite_prise){
      if(preg_match("/\(([0-9.,]+).*\)/i", $this->unite_prise, $matches)){
        $_quant = end($matches);
        $nb = $this->quantite * $_quant;

				// Suppression des parentheses de l'unite de prise
				//$unite_prise = preg_replace("/[(|)]/", '', $this->unite_prise);
        $this->_view_unite_prise = str_replace($_quant, "soit $nb", $this->unite_prise);
				$this->_short_view_unite_prise = str_replace(reset($matches), "", $this->unite_prise);
      }
    }
  }
  
  function updateQuantite(){
  	$this->loadTargetObject();
  	// Dans le cas d'un element, on affecte l'unite de prise prevu pour cet element
    if($this->_ref_object instanceof CPrescriptionLineElement){
      $this->unite_prise =  $this->_ref_object->_unite_prise;
    }
		
		if($this->_quantite_administrable){
  		return;
  	}
		
		$this->_quantite_administrable = $this->quantite;
    
    if($this->_ref_object instanceof CPrescriptionLineMedicament && !$this->_quantite_with_kg){
      $_unite_prise = str_replace('/kg', '', $this->unite_prise);
      if($_unite_prise != $this->unite_prise){
        // On recupere le poids du patient pour calculer la quantite
        $prescription =& $this->_ref_object->_ref_prescription;
				
        if(!$prescription->_ref_object->_ref_patient){
         $prescription->_ref_object->loadRefPatient();
        }
        $patient =& $prescription->_ref_object->_ref_patient;
        if(!$patient->_ref_constantes_medicales){
          $patient->loadRefConstantesMedicales();
        }
        $poids = $patient->_ref_constantes_medicales->poids;
        if(!$poids){
          $this->_quantite_administrable = 0;
          return;
        }
        $this->_quantite_administrable *= $poids;
        $this->_quantite_with_kg = 1;  
        $this->_unite_sans_kg = $_unite_prise;
      }
    }

    if($this->_ref_object instanceof CPrescriptionLineMedicament && !$this->_quantite_with_coef){
    	$unite_prise = ($this->_unite_sans_kg) ? $this->_unite_sans_kg : $this->unite_prise;
      $this->_ref_object->completeField("code_cip");
      if(!$this->_ref_object->_ref_produit){
			  $this->_ref_object->loadRefProduit();
			}
			$produit =& $this->_ref_object->_ref_produit; 

      // Systeme de cache local permettant de ne pas recalculer plusieurs fois $produit->rapport_unite_prise
			if(!isset(self::$cache_produits[$produit->code_cip])){
	      $produit->loadLibellePresentation();
	      $produit->loadUnitePresentation();
			  $produit->loadRapportUnitePriseByCIS();	
			  self::$cache_produits[$produit->code_cip] = $produit;
			} else {
				$produit = self::$cache_produits[$produit->code_cip];
			}
			   
      // Gestion des unites de prises exprimées en libelle de presentation (ex: poche ...)
      if($this->unite_prise == $produit->libelle_presentation){         
			   $this->_quantite_administrable *= $produit->nb_unite_presentation;
      }
			
      // Gestion des unite autres unite de prescription
      if(!isset($produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation])) {
				$coef=0;
      } else {
        $coef = $produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation];
      }
      
      $this->_quantite_with_coef = 1;
      $this->_quantite_administrable *= $coef;
      $this->_quantite_administrable = round($this->_quantite_administrable, 2);
      $this->_ref_object->_unite_administration = $produit->_unite_administration = $produit->libelle_unite_presentation;
      $this->_ref_object->_unite_dispensation = $produit->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;

      // Calcul du ration entre quantite d'administration et quantite de dispensation
      if($this->_ref_object->_unite_dispensation == $produit->libelle_unite_presentation){
        $this->_ref_object->_ratio_administration_dispensation = 1;
      } else {
      	if($produit->nb_unite_presentation){
          $this->_ref_object->_ratio_administration_dispensation = 1 / $produit->nb_unite_presentation;
				}
      }
      // Calcul de la quantite 
      $this->_quantite_dispensation = $this->_quantite_administrable * $this->_ref_object->_ratio_administration_dispensation;       
    }
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->_view = $this->quantite;

    if($this->_ref_object instanceof CPrescriptionLineElement){
      $this->_view .= " ".$this->_ref_object->_unite_prise;
    } else {
      if($this->_view_unite_prise){
      	if($this->_ref_object->_ref_prescription->type == "externe" && $this->_short_view_unite_prise){
      		$this->_view .= " ".$this->_short_view_unite_prise; 
      	} else {
      		$this->_view .= " ".$this->_view_unite_prise; 
      	}
      } else {
        $this->_view .= " ".$this->unite_prise; 
      }
    }
    
    if($this->urgence_datetime){
      if(!$this->_ref_object->_protocole){
        $this->_view .= " le ".mbTransformTime(null, $this->urgence_datetime, "%d/%m/%Y à %Hh%M")." (Urgence)";
      } else {
        $this->_view .= " (Urgence)";
      }
    }
		
    if($this->moment_unitaire_id){
      $this->loadRefMoment();
      $this->_view .= " ".$this->_ref_moment->_view;
      $this->_type = "moment";
      $this->_unite = $this->unite_prise;
      $this->_heures[] = $this->_ref_moment->heure;
    }
    
    $this->_short_view = $this->_view;
    
    if($this->unite_fois && !$this->nb_fois && !$this->moment_unitaire_id && !$this->nb_tous_les){
      $this->nb_fois = 1;
    }
    if($this->nb_fois){
      $this->_view .= " ".$this->nb_fois." fois";
      $this->_short_view .= " ".$this->nb_fois."X";
      $this->_type = "fois_par";
    }
   
    if($this->unite_fois && !$this->nb_tous_les && !$this->moment_unitaire_id){
      $this->_view .= " par ".CAppUI::tr("CPrisePosologie.unite_fois.".$this->unite_fois);
      $this->_short_view .= " / ".CAppUI::tr("CPrisePosologie.unite_fois.".$this->unite_fois);
      
      $this->_unite_temps = $this->unite_fois;
    }
    
    if($this->unite_tous_les && (!$this->unite_fois || ($this->unite_fois == "jour" && $this->nb_tous_les))){
      $this->_view .= " tous les ".$this->nb_tous_les." ".CAppUI::tr("CPrisePosologie.unite_tous_les.".$this->unite_tous_les);
      $this->_short_view .= " tous les ".$this->nb_tous_les." ".CAppUI::tr("CPrisePosologie.unite_tous_les.".$this->unite_tous_les);
      if($this->decalage_prise){
        $this->_view .= "(J+$this->decalage_prise)";
        $this->_short_view .= "(J+$this->decalage_prise)";
      }
      $this->_type = "tous_les";
      $this->_unite_temps = $this->unite_tous_les; 
    }
    
    if($this->heure_prise){
      $this->_view .= " à ". mbTransformTime(null, $this->heure_prise, "%Hh%M");
      $this->_short_view .= " à ". mbTransformTime(null, $this->heure_prise, "%Hh%M");
    }
		
		$this->_ref_object->loadRefPrescription();
		if(!$this->_ref_object->_ref_prescription->object_id){
			if($this->decalage_intervention){
				$signe_decalage = "";
				if($this->decalage_intervention >= 0){
					$signe_decalage = "+";
				}
				$this->_view .= " à $this->type_decalage $signe_decalage $this->decalage_intervention $this->unite_decalage_intervention(s)";
			}
		}
		
		if($this->condition){
			$this->_view .= " $this->condition";
			$this->_short_view .= " $this->condition";
		}
		
		if($this->datetime){
		  $this->_view .= " 1 fois";
			$this->_short_view .= " 1 fois";
		}
		
    $this->_view = stripslashes($this->_view);
		$this->_short_view = stripslashes($this->_short_view);
    
  }
  
  /*
   * Chargement du moment unitaire
   */
  function loadRefMoment($service_id = ""){
    $moment = new CMomentUnitaire();
    $this->_ref_moment = $moment->getCached($this->moment_unitaire_id);

    if(!$service_id){
      $service_id = "none";
    }
		
    if($this->_ref_moment->_id){
    	$group_id = $this->_ref_object->_ref_prescription->object_class == "CSejour" ? $this->_ref_object->_ref_prescription->_ref_object->group_id : "";	
      $configs = CConfigMomentUnitaire::getAllFor($service_id, $group_id);
      $this->_ref_moment->heure = $configs[$this->moment_unitaire_id];
      if($this->_ref_moment->heure){
        $this->_heure = $this->_ref_moment->heure;
      }
    }
  }
  
  // Chargement des administrations liées à l'object et à l'unite de prise
  function loadRefsAdministrationsByUnite(){
    $administration = new CAdministration();
    $where = array();
    $where["object_id"] = " = '$this->object_id'";
    $where["object_class"] = " = '$this->object_class'";
    $where["unite_prise"] = " = '$this->unite_prise'";
    $where["prise_id"] = "IS NULL";
    $where["planification"] = " = '0'";
    $this->_ref_administrations = $administration->loadList($where);
  }
  
	function loadRefsPlanificationsSystemes(){
	  $this->_ref_planifications_systemes = $this->loadBackRefs("planification_systeme");	
	}
	
  // countRefAdminByUnite
  function countRefsAdministrationsByUnite(){
    $administration = new CAdministration();
    $where = array();
    $where["object_id"] = " = '$this->object_id'";
    $where["object_class"] = " = '$this->object_class'";

    // Pas d'unite de prise de stockée dans le cas d'un element
    $this->completeField("unite_prise");
    if($this->unite_prise){
      $where["unite_prise"] = $this->_spec->ds->prepare("= %", $this->unite_prise);
    }
    $where["prise_id"] = "IS NULL";
    $where["planification"] = " = '0'";
    return $administration->countList($where);
  }
  
	function calculPlanifs(){
		if($this->condition){
			return;
		}
		
		// Chargement de la ligne de prescription
    $this->_ref_object = new $this->object_class;
    $this->_ref_object = $this->_ref_object->getCached($this->object_id);
		
		// Chargement de la prescription a partir de la ligne
    $this->_ref_object->loadRefPrescription();
		
		/*
		 * Fonctionnement:
		 *   - si aucune affectation, on utilise la config generale de l'etablissement (date du sejour)
		 *   - si 1 affectation, utilisation de la config du service (date de l'affectation)
		 *   - si plusieurs affectations, parcours des differents affectations
		 */
		
		$sejour =& $this->_ref_object->_ref_prescription->_ref_object;
    if(!$sejour->_id || $this->_ref_object->_ref_prescription->type != "sejour"){
			return;
		}

		$sejour->loadRefsAffectations();
  
    $dates = array();
    $bornes = array(); 
    
		if(count($sejour->_ref_affectations) < 2){
			if(count($sejour->_ref_affectations)){
				$_affectation = reset($sejour->_ref_affectations);
				$_affectation->loadRefLit();
				$_affectation->_ref_lit->loadRefChambre();
				$servicesId[$sejour->_id] = $_affectation->_ref_lit->_ref_chambre->service_id;
			} else {
        $servicesId[$sejour->_id] = "none";
			}
			$dates = array();
	    $date = mbDate($sejour->_entree);
	    $dates[$sejour->_id][] = $date;
			
			$prolongation_time = CAppUI::conf("dPprescription CPrescription prolongation_time");
			$sortie_sejour = ($sejour->sortie_reelle || !$prolongation_time) ?  $sejour->sortie : mbDateTime("+ $prolongation_time HOURS", $sejour->sortie);
			
	    while(($date = mbDate("+ 1 DAY", $date)) <= $sortie_sejour){
				$dates[$sejour->_id][] = $date;
	    }
			$bornes[$sejour->_id]["min"] = $sejour->entree;
      $bornes[$sejour->_id]["max"] = $sortie_sejour;
		} else {
			foreach($sejour->_ref_affectations as $curr_affectation){
				$curr_affectation->loadRefLit();
				$curr_affectation->_ref_lit->loadRefChambre();
        $servicesId[$curr_affectation->_id] = $curr_affectation->_ref_lit->_ref_chambre->service_id;
				
				$date = mbDate($curr_affectation->entree);
	      $dates[$curr_affectation->_id][] = $date;
				
				if($curr_affectation->sortie == $sejour->sortie){
					$prolongation_time = CAppUI::conf("dPprescription CPrescription prolongation_time");
					$curr_affectation->sortie = ($sejour->sortie_reelle || !$prolongation_time) ? $sejour->sortie : mbDateTime("+ $prolongation_time HOURS", $sejour->sortie);
				}
				
	      while(($date = mbDate("+ 1 DAY", $date)) <= $curr_affectation->sortie){
	        $dates[$curr_affectation->_id][] = $date;
	      }
				$bornes[$curr_affectation->_id]["min"] = $curr_affectation->entree;
				$bornes[$curr_affectation->_id]["max"] = $curr_affectation->sortie;
			}
		}
		
		$this->updateQuantite();
		
    $planifs = array();
		
		// Parcours des dates des affectations si plusieurs affectations
		foreach($dates as $key => $_dates){
      $planifs = array_merge($this->getPlanifs($_dates, $servicesId[$key], $bornes[$key]), $planifs);
		}

		// Heure de prises specifié (I+x heures)
    if($this->heure_prise ){
      $dateTimePrise = mbAddDateTime($this->heure_prise, mbDate($this->_ref_object->_debut_reel));
      if(($sejour->_entree <= $dateTimePrise) && ($sejour->_sortie >= $dateTimePrise)){
			  $planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $dateTimePrise);
      }
    }
  
    // Prise en urgence
    if($this->urgence_datetime){
      if((($this->_ref_object->_debut_reel <= $this->urgence_datetime) && ($this->_ref_object->_fin_reelle >= $this->urgence_datetime))){
        $planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $this->urgence_datetime);
		  }
    }
    
    if($this->datetime){
      if((($this->_ref_object->_debut_reel <= $this->datetime) && ($this->_ref_object->_fin_reelle >= $this->datetime))){
        $planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $this->datetime);
      }
    }
		
		// Seulement Tous les avec comme unite les heures
    if(!$this->moment_unitaire_id && ($this->unite_tous_les == "heure" || $this->unite_tous_les == "minute")){
    	if(!$this->nb_tous_les){
        $this->nb_tous_les = 1;
      }
			$date_time_temp = $this->_ref_object->_debut_reel;
      while($date_time_temp < $this->_ref_object->_fin_reelle){
      	if(($sejour->_entree <= $date_time_temp) && ($sejour->_sortie >= $date_time_temp)){
          $planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $date_time_temp);
				}
				$type_increment = $this->unite_tous_les == "heure" ? "hours" : "minutes";
			  $date_time_temp = mbDateTime("+ $this->nb_tous_les $type_increment", $date_time_temp);
      }
    }

    // Tous les avec comme unite les jours
    if($this->unite_tous_les == "jour"){
     	if(!$this->nb_tous_les){
     		$this->nb_tous_les = 1;
     	}
      if($this->_heures){
        $heure = reset($this->_heures);
				
			  $date_time_temp = mbDate($this->_ref_object->_debut_reel)." $heure";
        if($this->decalage_prise){
        	$date_time_temp = mbDateTime("+ $this->decalage_prise DAYS", $date_time_temp);
        }
          
				while($date_time_temp < $this->_ref_object->_fin_reelle){
	        if(($sejour->_entree <= $date_time_temp) && ($sejour->_sortie >= $date_time_temp)){
	          $planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $date_time_temp);
	        }
	        $date_time_temp = mbDateTime("+ $this->nb_tous_les DAYS", $date_time_temp);
	      }
			}
    }


    $is_element = $this->_ref_object instanceof CPrescriptionLineElement;
    
		
    // Recherche des planifs systemes non rattachées à une posologie (seulement pour les elements: planif sur l'heure de debut)
    if($is_element){
      $ds = CSQLDataSource::get("std");
      $query = "DELETE planification_systeme.* FROM planification_systeme 
                WHERE planification_systeme.object_id = '{$this->_ref_object->_id}'
                AND planification_systeme.object_class = '{$this->_ref_object->_class}'
							  AND planification_systeme.sejour_id = '{$this->_ref_object->_ref_prescription->object_id}'
							  AND planification_systeme.prise_id IS NULL;";
      $ds->exec($query);    	
    }

    // Sauvegarde des planifications systemes
		$_planifs_by_step = array();
		foreach($planifs as $_planification){
      $unite_prise = $is_element ?  $this->_ref_object->_chapitre : $_planification["unite_prise"];
      $_planifs_by_step[] = array("object_id" => "{$this->_ref_object->_id}",
					                            "object_class" => "{$this->_ref_object->_class}",
					                            "sejour_id" => "{$this->_ref_object->_ref_prescription->object_id}",
					                            "unite_prise" => $unite_prise,
					                            "prise_id" => $_planification["prise_id"],
					                            "dateTime" => $_planification["dateTime"]);
    }
    
		if(count($_planifs_by_step)){
			// Suppression des planification manuelles superieures à la date actuelle
		  $ds = CSQLDataSource::get("std");
		  $ds->insertMulti('planification_systeme', $_planifs_by_step, 1000);
			
			 // Chargement des planifications manuelles
      $where = array();
      $where["object_id"] = " = '$this->object_id'";
      $where["object_class"] = " = '$this->object_class'";
      $where["planification"] = " = '1'";
      $where["original_dateTime"] = " IS NOT NULL";
      
      $planification = new CAdministration();
      $planifications = $planification->loadList($where); 
      
      // Parcours des planifications et recherche des planifications systemes associées
      foreach($planifications as $_planification){
        $planification_systeme = new CPlanificationSysteme();
				
				$where = array();
				$where["dateTime"] = " LIKE '".mbTransformTime(null, $_planification->original_dateTime, "%Y-%m-%d %H")."%'";
				$where["object_id"] = " = '$_planification->object_id'";
        $where["object_class"] = " = '$_planification->object_class'";;
        if($planification_systeme->countList($where) < 1){
          $_planification->delete();
        }
      }
		}
  }
  
	function getPlanifs($dates, $service_id, $bornes){
		$group_id = $this->_ref_object->_ref_prescription->object_class == "CSejour" ? $this->_ref_object->_ref_prescription->_ref_object->group_id : "";
    $configs = CConfigService::getAllFor($service_id, $group_id);
	  
	  // Preparation des valeurs
	  if($this->nb_fois && $this->nb_fois <= 6 && $this->unite_fois == "jour"){  
      if($configs["$this->nb_fois fois par jour"]){
        $this->_heures = explode("|",$configs["$this->nb_fois fois par jour"]);
        foreach($this->_heures as &$_heure){
          $_heure .= ":00:00";
        }
      }
    }
    
    if( $this->unite_tous_les && (!$this->unite_fois || $this->unite_fois == "jour")){
      if($this->moment_unitaire_id){
        $this->loadRefMoment();
	      $this->_heures[] = $this->_ref_moment->heure;	
      } else {
	      if($configs["Tous les jours"]){
	        $this->_heures[] = $configs["Tous les jours"].":00:00";
	      }
      }
    }
       
    if(!$this->datetime && !$this->urgence_datetime && $this->quantite && !$this->moment_unitaire_id && !$this->nb_fois && !$this->unite_fois && !$this->unite_tous_les && !$this->nb_tous_les){
      if($configs["1 fois par jour"]){
        $this->_heures = explode("|",$configs["1 fois par jour"]);
        foreach($this->_heures as &$_heure){
          $_heure .= ":00:00";
        }
      }
    }   
		
		$_planifs = array();
		
    $jours = array("lundi"=>"1", "mardi"=>"2", "mercredi"=>"3", "jeudi"=>"4", "vendredi"=>"5", "samedi"=>"6", "dimanche"=>"0");
    
    if($this->_ref_object instanceof CPrescriptionLineMedicament){
       if(!$this->_ref_object->_fin_reelle){
       	 $prolongation_time = CAppUI::conf("dPprescription CPrescription prolongation_time");

       	 if($this->_ref_object->_ref_prescription->_ref_object->sortie_reelle || !$prolongation_time){
       	 	 $this->_ref_object->_fin_reelle = $this->_ref_object->_ref_prescription->_ref_object->sortie;
         } else {
       	   $this->_ref_object->_fin_reelle = mbDateTime("+ $prolongation_time HOURS", $this->_ref_object->_ref_prescription->_ref_object->sortie);
         }
      }
    }
				
    // Moment unitaire
    if($this->moment_unitaire_id && !array_key_exists($this->unite_tous_les, $jours) &&
      !in_array($this->unite_tous_les, array("jour", "semaine", "quinzaine", "mois", "trimestre", "semestre", "an"))) {
      foreach($dates as $_date){
      	$this->loadRefMoment($service_id);
        $dateTimePrise = mbAddDateTime(mbTime($this->_ref_moment->heure), $_date);				
				if(($bornes["min"] <= $dateTimePrise) && ($bornes["max"] >= $dateTimePrise)){
					if((($this->_ref_object->_debut_reel <= $dateTimePrise) && ($this->_ref_object->_fin_reelle >= $dateTimePrise))){
	          $_planifs[] = array("unite_prise" => $this->unite_prise, "prise_id" => $this->_id, "dateTime" => $dateTimePrise);
	        }
				}
      }
    }
    
		// Fois par avec comme unite jour
    if(($this->nb_fois && $this->unite_fois === 'jour' && !$this->unite_tous_les) || ($this->_quantite_administrable && !$this->moment_unitaire_id && 
        !$this->nb_fois && !$this->unite_fois && !$this->unite_tous_les && !$this->nb_tous_les && !$this->heure_prise)){
			if($this->_heures){
        foreach($this->_heures as $curr_heure){
          foreach($dates as $_date){
            $dateTimePrise = mbAddDateTime($curr_heure, $_date);
						if(($bornes["min"] <= $dateTimePrise) && ($bornes["max"] >= $dateTimePrise)){
	            if((($this->_ref_object->_debut_reel <= $dateTimePrise) && ($this->_ref_object->_fin_reelle >= $dateTimePrise))){
	              $_planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $dateTimePrise);                
	            }
						}
          }
        }
      }
    }
    
    // Nb tous les avec comme unite semaine / quinzaine
    if (in_array($this->unite_tous_les, array("semaine", "quinzaine"))) {
      if (!$this->nb_tous_les) {
        $this->nb_tous_les = 1;
      }
      $sejour =& $this->_ref_object->_ref_prescription->_ref_object;
      $dateTimePrise = $bornes["min"];
      if ($this->unite_tous_les == "semaine") {
        $nb_weeks = 7 * $this->nb_tous_les;
      }
      else {
        $nb_weeks = 14 * $this->nb_tous_les;
      }
      if($this->_heures){
        $heure = reset($this->_heures);
        $dateTimePrise = mbDate($bornes["min"])." $heure";
      }
      if ($this->moment_unitaire_id) {
        $this->loadRefMoment();
        $dateTimePrise = mbAddDateTime($this->_ref_moment->heure, mbDate($dateTimePrise));
      }
      if($this->decalage_prise){
          $dateTimePrise = mbDateTime("+ $this->decalage_prise DAYS", $dateTimePrise);
      }

      while ($dateTimePrise < $bornes["max"]) {
        if((($this->_ref_object->_debut_reel <= $dateTimePrise) && ($this->_ref_object->_fin_reelle) >= $dateTimePrise) &&
          (($sejour->entree <= $dateTimePrise) && ($sejour->sortie >= $dateTimePrise))){
          $_planifs[] = array("unite_prise" => $this->unite_prise, "prise_id" => $this->_id, "dateTime" => $dateTimePrise);
        }
        $dateTimePrise = mbDateTime("+ $nb_weeks DAYS", $dateTimePrise);
      }
    }

    // Fois par avec comme unite semaine
    if($this->nb_fois && $this->unite_fois === 'semaine' && $configs["$this->nb_fois fois par semaine"]){
      $list_jours = explode('|',$configs["$this->nb_fois fois par semaine"]);
			
      // Parcours des jours concernés
      foreach($list_jours as $_jour){
        foreach($dates as $_date){
          // Pour chaque jour, on regarde s'il correspond à la date courante
          if($jours[$_jour] == mbTransformTime(null, $_date, "%w")){
            $heure = $configs["1 fois par jour"].":00:00";
            $dateTimePrise = mbAddDateTime($heure, $_date);
            if(($bornes["min"] <= $dateTimePrise) && ($bornes["max"] >= $dateTimePrise)){
	            if((($this->_ref_object->_debut_reel <= $dateTimePrise) && ($this->_ref_object->_fin_reelle >= $dateTimePrise))){
	              $_planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $dateTimePrise);
	            }
						}
          }
        }
      }
    }		
		
		// Jour de prise
    if(array_key_exists($this->unite_tous_les, $jours)){
      foreach($dates as $_date){
        if($jours[$this->unite_tous_les] == mbTransformTime(null, $_date, "%w")){
          if($this->moment_unitaire_id){
            $heure = substr($this->_ref_moment->heure,0,2);
          } else {
            // On stocke l'heure de prise correspondant à 1 fois par jour
            $heure = ($conf = $configs["1 fois par jour"]) ? $conf : "10";
          }
					$heure .= ":00:00";
          $dateTimePrise = mbAddDateTime($heure, $_date);
				  if(($bornes["min"] <= $dateTimePrise) && ($bornes["max"] >= $dateTimePrise)){
	          if((($this->_ref_object->_debut_reel <= $dateTimePrise) && ($this->_ref_object->_fin_reelle >= $dateTimePrise))){
	            $_planifs[] = array("unite_prise" => "", "prise_id" => $this->_id, "dateTime" => $dateTimePrise);    
	          }
					}
        }
      }
    }
		return $_planifs;
	}
	
	function store(){
		$creation = !$this->_id;
		// Si l'unite prise est modifiée, on modifie les unites de prises 
    if($this->fieldModified("unite_prise")){
      $this->loadRefsPlanificationsSystemes();
      foreach($this->_ref_planifications_systemes as $_planif_system){
        $_planif_system->unite_prise = $this->unite_prise;
        $_planif_system->store();
      }
    }
		
		if($msg = parent::store()){
			return $msg;
		}
	
	  // Lors de la creation d'une posologie, on genere les planification systemes (sauf si la ligne n'est pas active)
	  if($creation){
	  	if(!$this->_ref_object){
	  		$this->loadTargetObject();
	  	}

		  if(!($this->_ref_object instanceof CPrescriptionLineMedicament && !$this->_ref_object->variante_active) && CPlanificationSysteme::$_calcul_planif){
			  $this->calculPlanifs();
			}
	  }
	}
	
	
	function delete(){
		if($msg = parent::delete()){
      return $msg;
    }
	
	  if($this->_ref_object instanceof CPrescriptionLineElement){
			// On genere une planif a la date et heure de debut si aucune poso n'est presente
			$date_debut = $this->_ref_object->debut;
	    $time_debut = $this->_ref_object->time_debut;
	     
	    if($date_debut && $time_debut && count($this->_ref_object->_ref_prises) == 0){
				$new_planif = new CPlanificationSysteme();
	      $new_planif->dateTime = "$date_debut $time_debut";
	      $new_planif->object_id = $this->_ref_object->_id;
	      $new_planif->object_class = $this->_ref_object->_class;
	      $new_planif->sejour_id = $this->_ref_object->_ref_prescription->object_id;    
	      $new_planif->store();
	    }
    }
	}
	
  /*
   * CanDelete
   */
  function canDeleteEx() {
    if($msg = parent::canDeleteEx()) {
      return $msg;
    }
    if($nb_administrations = $this->countRefsAdministrationsByUnite()){
      return "$nb_administrations administration(s) liée(s) à cette prise";
    }
  }
}
  
?>