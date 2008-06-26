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
  
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prise_posologie';
    $spec->key   = 'prise_posologie_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "object_id"            => "notNull ref class|CMbObject meta|object_class cascade",
      "object_class"         => "notNull enum list|CPrescriptionLineMedicament|CPrescriptionLineElement",
      "moment_unitaire_id"   => "ref class|CMomentUnitaire",
      "quantite"             => "float",
      "nb_fois"              => "float",
      "unite_fois"           => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "nb_tous_les"          => "float",
      "unite_tous_les"       => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "decalage_prise"       => "num min|0",
      "unite_prise"          => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->quantite;
    if($this->object_class == "CPrescriptionLineElement"){
      $this->_view .= " ".$this->_ref_object->_view;
    } else {
      $this->_view .= " ".$this->unite_prise;	
    }
    if($this->moment_unitaire_id){
    	$this->_view .= " ".$this->_ref_moment->_view;
    }
    if($this->nb_fois){
    	$this->_view .= " ".$this->nb_fois." fois";
    }
    if($this->unite_fois && !$this->nb_tous_les){
    	$this->_view .= " par ".CAppUI::tr("CPrisePosologie.unite_fois.".$this->unite_fois);
    }
    if($this->nb_tous_les && $this->unite_tous_les){
    	$this->_view .= " tous les ".$this->nb_tous_les." ".CAppUI::tr("CPrisePosologie.unite_tous_les.".$this->unite_tous_les);
    	if($this->decalage_prise){
    		$this->_view .= "(J+$this->decalage_prise)";
    	}
    }   
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefMoment();
  }
  
  /*
   * Chargement du moment unitaire
   */
  function loadRefMoment(){
    $this->_ref_moment = new CMomentUnitaire();
    $this->_ref_moment->load($this->moment_unitaire_id);	
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
}
  
?>