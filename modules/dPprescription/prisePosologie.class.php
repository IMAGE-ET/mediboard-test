<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
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
  
  var $nb_fois               = null;
  var $unite_fois            = null;
  var $nb_tous_les           = null;
  var $unite_tous_les        = null;
  var $decalage_prise        = null;   // decalage de la prise J + $decalage (par defaut 0)
  var $unite_prise           = null;
  
  var $_type                 = null; // Type de prise
  var $_unite                = null; // Unite de la prise
  var $_heures                = null; // Heure de la prise
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prise_posologie';
    $spec->key   = 'prise_posologie_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["object_id"]          = "notNull ref class|CMbObject meta|object_class cascade";
    $specs["object_class"]       = "notNull enum list|CPrescriptionLineMedicament|CPrescriptionLineElement";
    $specs["moment_unitaire_id"] = "ref class|CMomentUnitaire";
    $specs["quantite"]           = "float";
    $specs["nb_fois"]            = "float";
    $specs["unite_fois"]         = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour";
    $specs["nb_tous_les"]        = "float";
    $specs["unite_tous_les"]     = "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour";
    $specs["decalage_prise"]     = "num min|0";
    $specs["unite_prise"]        = "text";
    return $specs;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["administration"] = "CAdministration prise_id";
    return $backRefs;
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
    
    $this->_short_view = $this->_view;
    
    if($this->moment_unitaire_id){
    	$this->_view .= " ".$this->_ref_moment->_view;
      $this->_type = "moment";
      $this->_unite = $this->unite_prise;
      $this->_heures[] = $this->_ref_moment->heure;
    }
    
    if($this->nb_fois && $this->nb_fois <= 3 && $this->unite_fois == "jour"){
    	$this->_view .= " ".$this->nb_fois." fois";
      $this->_type = "fois_par";
      
      $this->_heures = explode("|",CAppUI::conf("dPprescription CPrisePosologie heures fois_par $this->nb_fois"));
      foreach($this->_heures as &$_heure){
      	$_heure .= ":00:00";
      }
    }
    
    if($this->unite_fois && !$this->nb_tous_les){
    	$this->_view .= " par ".CAppUI::tr("CPrisePosologie.unite_fois.".$this->unite_fois);
    	$this->_unite = $this->unite_fois;
    }
    
    if($this->nb_tous_les && $this->unite_tous_les){
    	$this->_view .= " tous les ".$this->nb_tous_les." ".CAppUI::tr("CPrisePosologie.unite_tous_les.".$this->unite_tous_les);
    	if($this->decalage_prise){
    		$this->_view .= "(J+$this->decalage_prise)";
    	}
    	$this->_type = "tous_les";
    	$this->_unite = $this->unite_tous_les;
    	$this->_heures[] = CAppUI::conf("dPprescription CPrisePosologie heures tous_les").":00:00";
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
    
    while((mbDate($date_temp."+ $increment $type_increment")) <= $this->_ref_object->_fin){
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
  
  	switch($this->_unite){
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
  			$nb = $nb_hours / 24;
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
  		$heure = $this->_ref_moment->heure;
  
  		// Si une seule journée, on regarde si la prise est pendant la journée
  		if($nb_days == 0){
  			$day = mbDate($borne_min); // == mbDate($borne_max)	

  			$date_heure = "$day ".$heure;
				if($date_heure > $borne_min && $date_heure < $borne_max){
				  $nb = 1;
				}
				
  		} 
  		else {
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
  	/*
		if($this->moment_unitaire_id && !$this->_ref_moment->heure){
  	  $nb = 1;
		}*/
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
    @$this->_ref_object->_quantites[$this->unite_prise] += $quantite;
  }
  
  
  // Chargement des administrations liées à l'object et à l'unite de prise
  function loadRefsAdministrationsByUnite(){
    $administration = new CAdministration();
 	  $where = array();
 	  $where["object_id"] = " = '$this->object_id'";
 	  $where["object_class"] = " = '$this->object_class'";
    $where["unite_prise"] = " = '$this->unite_prise'";
    $where["prise_id"] = "IS NULL";
 	  $this->_ref_administrations = $administration->loadList($where);
  }
  
  
  /*
   * CanDelete
   */
  function canDeleteEx() {
    if($msg = parent::canDeleteEx()) {
      return $msg;
    }
  	
  	$this->completeField("unite_prise");
  	
  	$administration = new CAdministration();
  	$where = array();
  	$where["object_id"] = " = '$this->object_id'";
  	$where["object_class"] = " = '$this->object_class'";
  	// Pas d'unite de prise de stockée dans le cas d'un element
  	if($this->unite_prise){
  	  $where["unite_prise"] = " = '$this->unite_prise'";
  	}
  	$where["prise_id"] = "IS NULL";
  	$nb_administrations = $administration->countList($where);
  	
  	if($nb_administrations){
  		return "$nb_administrations administration(s) liée(s) à cette prise";
  	}
  }

}
  
?>