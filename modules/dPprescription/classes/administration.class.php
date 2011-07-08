<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("dPpatients", "IPatientRelated");

class CAdministration extends CMbMetaObject implements IPatientRelated {
  // DB Field
  var $administration_id = null;
  var $administrateur_id = null;  // Utilisateur effectuant l'administration
  var $dateTime          = null;  // Heure de l'administration
  var $quantite          = null;  // Info sur la prise
  var $unite_prise       = null;  // Info sur la prise
  var $commentaire       = null;  // Commentaire sur l'administration
  var $prise_id          = null;
  var $planification_systeme_id  = null;
	var $constantes_medicales_id   = null;
	
  // Gestion des replanifications
  var $planification     = null;  // Flag permettant de gerer les plannifications
  var $original_dateTime = null;  // Champ permettant de stocker la date d'origine de la prise prevue replanifiée
  
  // Form field
  var $_heure = null;
  
  var $_quantite_prevue = null;
  
  // Object references
  var $_ref_administrateur = null;
  var $_ref_transmissions  = null;
  var $_ref_log   = null;
  var $_ref_planification_systeme = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'administration';
    $spec->key   = 'administration_id';
    $spec->events = array(
      "validation" => array(
        "multiple"   => false,
        "reference1" => array("CSejour",  "object_id.prescription_id.object_id"),
        "reference2" => array("CPatient", "object_id.prescription_id.object_id.patient_id"),
        "hostfield_sugg" => array(
          "object_id.CPrescriptionLineElement-element_prescription_id",
          "object_id.CPrescriptionLineMedicament-code_ucd",
        ),
      ),
    );
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["transmissions"]  = "CTransmissionMedicale object_id";
    //$backProps["echanges_phast"] = "CPhastEchange object_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["object_id"]         = "ref notNull class|CMbObject meta|object_class";
    $specs["object_class"]      = "enum notNull list|CPrescriptionLineMedicament|CPrescriptionLineElement|CPrescriptionLineMixItem";
    $specs["administrateur_id"] = "ref notNull class|CMediusers";
    $specs["prise_id"]          = "ref class|CPrisePosologie";
    $specs["quantite"]          = "float";
    $specs["unite_prise"]       = "text";
    $specs["dateTime"]          = "dateTime";
    $specs["commentaire"]       = "text";
    $specs["planification"]     = "bool default|0";
    $specs["original_dateTime"] = "dateTime";
    $specs["planification_systeme_id"] = "ref class|CPlanificationSysteme show|0";
    $specs["constantes_medicales_id"] = "ref class|CConstantesMedicales show|0";
    return $specs;
  }
	
	function loadRelPatient(){
		return $this->loadTargetObject()->loadRelPatient();
	}

	function loadRefConstantesMedicales() {
	  $constantes_medicales = new CConstantesMedicales;
	  return $this->_ref_constantes_medicales = $constantes_medicales->load($this->constantes_medicales_id);
	}
	
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_heure = substr(mbTime($this->dateTime), 0, 2);
  	$this->_unite_prise = ($this->unite_prise !== "aucune_prise" ? $this->unite_prise : ""); // Parfois modifié par loadRefPrise
  }
  
  function loadRefsFwd(){
  	parent::loadRefsFwd();
  	$this->loadRefAdministrateur();
  	if($this->_ref_object){
      $this->_ref_object->loadRefsFwd();
  	}
    $dateFormat = "%d/%m/%Y à %Hh%M";
		
		$this->_view = $this->quantite ? "Administration du ".mbTransformTime(null, $this->dateTime, $dateFormat)." par {$this->_ref_administrateur->_view}"
		                               : "Annulation par {$this->_ref_administrateur->_view} de l'administration prévue le ".mbTransformTime(null, $this->dateTime, $dateFormat);
		
		if($this->object_class === "CPrescriptionLineMedicament") {
  		$this->_view .= " ({$this->_ref_object->_ucd_view})";
  	}
  }
  
  function loadRefsTransmissions(){
  	$this->_ref_transmissions = $this->loadBackRefs("transmissions");
		foreach($this->_ref_transmissions as &$_trans){
  	  $_trans->loadRefsFwd();
    }
  }
  
  function loadRefPrise(){
  	$this->_ref_prise = new CPrisePosologie();
  	$this->_ref_prise = $this->_ref_prise->getCached($this->prise_id);
  	$this->_unite_prise = $this->_ref_prise->_unite;
  }
  
  function loadRefAdministrateur(){
  	$this->_ref_administrateur = new CMediusers();
  	$this->_ref_administrateur = $this->_ref_administrateur->getCached($this->administrateur_id);
		$this->_ref_administrateur->loadRefFunction();
  }
  
  function loadRefLog(){
    $this->_ref_log = new CUserLog();
    $this->_ref_log->object_id = $this->_id;
    $this->_ref_log->object_class = $this->_class_name;
    $this->_ref_log->loadMatchingObject();
    $this->_ref_log->loadRefsFwd();
  }
	
	static function getTimingPlanSoins($date, $configs, $periods="", $nb_before="", $nb_after=""){
		// Recuperation de l'heure courante
		$time = mbTransformTime(null,null,"%H");
		
		// Postes pour affichage du plan de soins (stocké en config)
		if($periods == ""){
			$list_postes = array("Poste 1", "Poste 2", "Poste 3", "Poste 4");
	    foreach ($list_postes as $_hour_poste){
	      if($configs[$_hour_poste] && ($configs[$_hour_poste] != '#')){
	        $periods[] = $configs[$_hour_poste];
	      }
	    }	
		}
		
		if($nb_before == ""){
			$nb_before = $configs["Nombre postes avant"];
		}
		if($nb_after == ""){
      $nb_after = $configs["Nombre postes apres"];
    }
		
		$key_periods = array();
		foreach($periods as $key => $_period){
		  $key_periods[$_period] = $key; 
		}
		
		$original_periods = $periods;
		$original_date = $date;
		
		// Calcul de la cle de la periode courante
		$current_period = end($periods);
		$current_period_key = end(array_keys($periods));
		
		foreach($periods as $_k => $_period){
		  if($time >= $_period){
		    $current_period = $_period;
		    $current_period_key = $_k;
		  }
		}
		$current_period_key_before = count($periods) - ($current_period_key + 1);
		
		// Calcul des periodes precedentes
		if($nb_before){
		  $before_periods = $original_periods;
		  rsort($before_periods);
		  rsort($periods);
		
		  for($i=$current_period_key_before+1; $i < ($nb_before+$current_period_key_before+1); $i++){
		    if(!isset($periods[$i])){
		      $date = mbDate("- 1 DAY", $date);
		      $periods = array_merge($periods, $before_periods);
		    }
		    $_periods[$date][] = $periods[$i];
		  }
		}
		
		// Ajout du period courant
		$date = $original_date;
		$_periods[$date][] = $current_period;
		
		if($nb_after){
		  $periods = $original_periods;
		  for($i=$current_period_key+1; $i < ($nb_after+$current_period_key+1); $i++){
		    if(!isset($periods[$i])){
		      $date = mbDate("+ 1 DAY", $date);
		      $periods = array_merge($periods, $original_periods);
		    }
		    $_periods[$date][] = $periods[$i];
		  }
		}
		
		ksort($_periods);
		
		$_p = array();
		foreach($_periods as $_date => $_periods_by_date){
		  foreach($_periods_by_date as $_period){
		    $_postes[$_date][$key_periods[$_period]] = $_period;
		  }
		  ksort($_postes[$_date]);
		}
		
		$postes = array();
		foreach($_postes as $_date => $_poste_by_date){
		  foreach($_poste_by_date as $_key_period => $_poste){
		  	$_view_nb_period = $_key_period + 1;
				
		    $real_date = $_date;
		    if(isset($original_periods[$_key_period+1])){
		      $next_period = $original_periods[$_key_period+1];
		
		      for($i = $_poste; $i < $next_period; $i++){
		        $i = str_pad($i, 2, '0', STR_PAD_LEFT);
		        $postes[$_date]["poste-".$_view_nb_period][$real_date]["$i:00:00"] = $i;
		      }
		    } 
		    else {
		      for($i = $_poste; $i < 24; $i++){
		        $i = str_pad($i, 2, '0', STR_PAD_LEFT);
		        $postes[$_date]["poste-".$_view_nb_period][$real_date]["$i:00:00"] = $i;
		      }
		      $real_date = mbDate("+ 1 DAY", $_date);
		      for($i = 0; $i < $original_periods[0]; $i++){
		        $i = str_pad($i, 2, '0', STR_PAD_LEFT);
		        $postes[$_date]["poste-".$_view_nb_period][$real_date]["$i:00:00"] = $i;
		      }
		    }
		  }
		}
		return $postes;
	}
	
	function canDeleteEx() {
    if($msg = parent::canDeleteEx()) {
      return $msg;
    }
		$this->completeField("administrateur_id");
		$this->completeField("planification");
    
		if(($this->administrateur_id != CAppUI::$user->_id) && !$this->planification && !CCanDo::admin()){
			return "Seul l'utilisateur ayant validé l'administration peut la supprimer";
		}
  }
}

?>