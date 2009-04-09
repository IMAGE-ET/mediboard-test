<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMomentUnitaire class
 */
class CPrisePosologie extends CMbMetaObject {
  
	// DB Table key
  var $prise_posologie_id = null;
  
  // DB Fields
  var $moment_unitaire_id    = null;
  var $quantite              = null;
  
  // Fois par
  var $nb_fois               = null;
  var $unite_fois            = null;
  
  // Tous les
  var $nb_tous_les           = null;
  var $unite_tous_les        = null;
  var $decalage_prise        = null;  // decalage de la prise J + $decalage (par defaut 0)
  var $unite_prise           = null;

  // I +/- X heures
  var $decalage_intervention = null;  // decalage en heures par rapport à l'intervention
  var $heure_prise           = null;  // heure calculée

  var $_type                 = null; // Type de prise
  var $_unite                = null; // Unite de la prise
  var $_heures               = null; // Heure de la prise
  var $_unite_temps          = null;
  var $_quantite_with_kg     = null;  // Permet d'eviter de recalculer plusieurs fois la quantite en fonction du poids
  var $_quantite_with_coef   = null;  // Permet d'eviter de recalculer plusieurs fois la quantite en fonction du coef
  var $_unite_sans_kg        = null;  // Unite sans le kg
  var $_quantite_dispensation = null;
  var $_quantite_administrable = null; // Quantité de produit en unité d'administration
  
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
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administration"] = "CAdministration prise_id";
    return $backProps;
  }
 
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefMoment();
    $this->_view = $this->quantite;

    if($this->object_class == "CPrescriptionLineElement"){
      $this->_view .= " ".$this->_ref_object->_unite_prise;
    } else {
      $this->_view .= " ".$this->unite_prise;	
    }
    
    if($this->moment_unitaire_id){
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
   
    if($this->nb_fois && $this->nb_fois <= 6 && $this->unite_fois == "jour"){  
      if(CAppUI::conf("dPprescription CPrisePosologie heures fois_par $this->nb_fois")){
	      $this->_heures = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures fois_par $this->nb_fois"));
	      foreach($this->_heures as &$_heure){
	      	$_heure .= ":00:00";
	      }
      }
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
    
	    if($this->unite_tous_les && (!$this->unite_fois || $this->unite_fois == "jour")){
	    	if(CAppUI::conf("dPprescription CPrisePosologie heures tous_les")){
	    	  $this->_heures[] = CAppUI::conf("dPprescription CPrisePosologie heures tous_les").":00:00";
	    	}
	    }   
    }
    
    if($this->heure_prise){
      $this->_view .= " à ". mbTransformTime(null, $this->heure_prise, "%Hh%M");
      $this->_short_view .= " à ". mbTransformTime(null, $this->heure_prise, "%Hh%M");
    }
    
    if($this->quantite && !$this->moment_unitaire_id && !$this->nb_fois && !$this->unite_fois && !$this->unite_tous_les && !$this->nb_tous_les){
      if(CAppUI::conf("dPprescription CPrisePosologie heures fois_par 1")){
	      $this->_heures = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures fois_par 1"));
	      foreach($this->_heures as &$_heure){
	      	$_heure .= ":00:00";
	      }
      }
    }    
  }
  
  /*
   * Chargement du moment unitaire
   */
  function loadRefMoment(){
    $moment = new CMomentUnitaire();
    $this->_ref_moment = $moment->getCached($this->moment_unitaire_id);
    if($this->_ref_moment->heure){
      $this->_heure = $this->_ref_moment->heure;
    }
  }
  
  /*
   * Calcul des prises d'un medicament à une date donnée
   */
  function calculDatesPrise($date){	
  	// Calcul de la premiere prise du medicament
    $date_temp = $this->_ref_object->debut;
    // Gestion du decalage entre les prises
    if($this->decalage_prise && $this->decalage_prise > 0){
    	$date_temp = mbDate("+ $this->decalage_prise DAYS", $date_temp);
    }
    $tabDates[] = $date_temp;
  	
    // Minute / Heure / demi-journee / jour
  	if($this->unite_tous_les == "minute" || $this->unite_tous_les == "heure" || $this->unite_tous_les == "demi_journee"){
  	  return true;
  	}
  	
  	// Jour
  	if($this->unite_tous_les == "jour"){
  		$increment = $this->nb_tous_les;
  	  $type_increment = "DAYS";
  	}
  	
  	// Semaine / Quinzaine
  	if($this->unite_tous_les == "semaine" || $this->unite_tous_les == "quinzaine"){	
  		if($this->unite_tous_les == "semaine"){
  			$increment = $this->nb_tous_les;
  		}
  		if($this->unite_tous_les == "quinzaine"){
  			$increment = 2 * $this->nb_tous_les;
  		}
  		$type_increment = "WEEKS";
  	}
  	
  	// Mois / Trimestre / Semestre
  	if($this->unite_tous_les == "mois" || $this->unite_tous_les == "trimestre" || $this->unite_tous_les == "semestre"){
  		if($this->unite_tous_les == "mois"){
  			$increment = $this->nb_tous_les;
  		}
  		if($this->unite_tous_les == "trimestre"){
  			$increment = 3 * $this->nb_tous_les;
  		}
  		if($this->unite_tous_les == "semestre"){
  			$increment = 6 * $this->nb_tous_les;
  		} 
  		$type_increment = "MONTHS";
  	}
  	
  	// Annee
    if($this->unite_tous_les == "an"){
    	$increment = $this->nb_tous_les;
    	$type_increment = "YEARS";  
    }
    
    while((mbDate($date_temp."+ $increment $type_increment")) <= $this->_ref_object->_fin_reelle){
      $date_temp = mbDate($date_temp."+ $increment $type_increment");
  	  $tabDates[] = $date_temp;
    }
    
    if(in_array($date, $tabDates)){
    	return true;
    } 
    return false;
  } 
  
  
  function calculQuantitePrise($borne_min, $borne_max){
  	$nb_hours = mbHoursRelative($borne_min, $borne_max);
  	$nb_days  = mbDaysRelative($borne_min, $borne_max);
  	$nb_minutes = mbMinutesRelative($borne_min, $borne_max);
 	  //$nb_days++;
 	  
  	switch($this->_unite_temps){
  		case 'minute':
  			$nb = $nb_minutes; 
  			break;
  		case 'heure':
  			$nb = $nb_hours;
  			break;
  	  case 'demi_journee':
  	  	$nb = $nb_hours / 12;
  		  break;		  
  		case 'jour':
  			$nb = $nb_days;
  		  break;  		
  		case 'semaine':
  			$nb = $nb_days / 7;
  		  break; 		
  		case 'quinzaine':
  			$nb = $nb_days / 14;
  		  break;		  
  		case 'mois' : 
  			$nb = $nb_days / 30;
  			break;			
  		case 'trimestre':
  			$nb = $nb_days / 90;
  			break;	
  		case 'semestre':
  			$nb = $nb_days / 180;
  			break;			
  		case 'an':
  			$nb = $nb_days / 360;
  			break;
  	}

  	if($this->moment_unitaire_id && $this->_ref_moment->heure){
  	  $nb_days++;
  		$heure = $this->_ref_moment->heure;
  		// Si une seule journée, on regarde si la prise est pendant la journée
  		if($nb_days == 0){
  			$day = mbDate($borne_min); // == mbDate($borne_max)	

  			$date_heure = "$day $heure";
				if($date_heure > $borne_min && $date_heure < $borne_max){
				  $nb = 1;
				} else {
				  $nb = 0;
				}
  		} else {
  			// On calcule combien de fois la prise sera effectuée pendant la ligne
  		  // Pour cela, on verifie si la prise est faite le 1er et dernier jour
				$first_prise = mbDate($borne_min)." $heure";
  		  $last_prise  = mbDate($borne_max)." $heure";
			  $nb = $nb_days - 2;
  		  if($first_prise > $borne_min){
				  $nb++;
				}
				if($last_prise < $borne_max){
					$nb++;
				}
  		}
  	}
		if($this->moment_unitaire_id && !isset($nb)){
		  $nb = 1;
		}
  	
  	// Cas d'une posologie de type moment (unite de prise: jour)
  	if($this->moment_unitaire_id && !$this->nb_tous_les){
    	$quantite = $this->quantite * $nb;	
    }
    // Cas "Fois par" (avec unite_prise en jour)
    if($this->nb_fois && $this->unite_fois){
    	$quantite = $this->quantite * $nb * $this->nb_fois;	
    }
    // Cas "Tous les" ...
    if($this->nb_tous_les){
    	$quantite = ceil($this->quantite * $nb / $this->nb_tous_les);	
    }
    if(!isset($quantite)){
    	$quantite = $this->quantite * $nb_days;
    }
    
    
    //---------------------
    // On reaffecte la quantite calculee
    $this->quantite = $quantite;
    
		$line =& $this->_ref_object;
		$produit =& $line->_ref_produit;
		$prescription =& $line->_ref_prescription;
		$sejour =& $prescription->_ref_object;
		
		$poids_ok = 1;
		
     if(!$this->_quantite_with_kg){
			  $_unite_prise = str_replace('/kg', '', $this->unite_prise);
			  if($_unite_prise != $this->unite_prise){
			    // On recupere le poids du patient pour calculer la quantite

	        if(!$sejour->_ref_patient){
	         $sejour->loadRefPatient();
	        }
	        $patient =& $sejour->_ref_patient;          
	        if(!$patient->_ref_constantes_medicales){
	          $patient->loadRefConstantesMedicales();
	        }
	        $poids = $patient->_ref_constantes_medicales->poids;
	        if($poids){
			      $this->quantite *= $poids;
			      $this->_quantite_with_kg = 1;  
			      $this->_unite_sans_kg = $_unite_prise;
	        } else {
	          $poids_ok = 0;
	          $this->quantite = 0;
	        }
        }
      }
      
		  if(!$this->_quantite_with_coef && $poids_ok){
		    $unite_prise = ($this->_unite_sans_kg) ? $this->_unite_sans_kg : $this->unite_prise;
		    $produit->loadConditionnement();
		    // Gestion des unites de prises exprimées en libelle de presentation (ex: poche ...)
		    if($this->unite_prise == $produit->libelle_presentation){		        
		      $this->quantite *= $produit->nb_unite_presentation;
		    }
		    
		    // Gestion des autres unite de prescription
		    if(!isset($produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation])) {
          $coef = 1;
        } else {
          $coef = $produit->rapport_unite_prise[$unite_prise][$produit->libelle_unite_presentation];
        }
        
        $this->_quantite_with_coef = 1;
		    $this->quantite *= $coef;
		    
		    $produit->_unite_administration = $this->_ref_object->_unite_administration = $produit->libelle_unite_presentation;
		    $this->_ref_object->_unite_dispensation = $produit->libelle_presentation ? $produit->libelle_presentation : $produit->libelle_unite_presentation;
		    $produit->_unite_dispensation = $this->_ref_object->_unite_dispensation;
		    
		    if($this->_ref_object->_unite_dispensation == $produit->libelle_unite_presentation){
		      $this->_ref_object->_ratio_administration_dispensation = 1;
		    } else {
		      $this->_ref_object->_ratio_administration_dispensation = 1 / $produit->nb_unite_presentation;
		    }
		  }
		  @$this->_ref_object->_quantite_administration += $this->quantite; 
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
  
  // countRefAdminByUnite
  function countRefsAdministrationsByUnite(){
   	$administration = new CAdministration();
  	$where = array();
  	$where["object_id"] = " = '$this->object_id'";
  	$where["object_class"] = " = '$this->object_class'";

  	// Pas d'unite de prise de stockée dans le cas d'un element
  	$this->completeField("unite_prise");
  	if($this->unite_prise){
  	  $where["unite_prise"] = " = '$this->unite_prise'";
  	}
  	$where["prise_id"] = "IS NULL";
  	$where["planification"] = " = '0'";
  	return $administration->countList($where);
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