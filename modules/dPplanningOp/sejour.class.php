<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Thomas Despoix
 */

global $AppUI;
require_once($AppUI->getModuleClass("dPccam", "codableCCAM"));

/**
 * Classe CSejour. 
 * @abstract Gère les séjours en établissement
 */
class CSejour extends CCodableCCAM {
  // DB Table key
  var $sejour_id = null;
  
  
  // DB Réference
  var $patient_id         = null; // remplace $op->pat_id
  var $praticien_id       = null; // clone $op->chir_id
  var $group_id           = null;

  // DB Fields
  var $type               = null; // remplace $op->type_adm
  var $modalite           = null;
  var $annule             = null; // complète $op->annule
  var $chambre_seule      = null; // remplace $op->chambre
  var $reanimation        = null; // Entrée en réanimation
  var $zt                 = null; // Entrée en zone de très courte durée

  var $entree_prevue      = null;
  var $sortie_prevue      = null;
  var $entree_reelle      = null;
  var $sortie_reelle      = null;

  var $venue_SHS          = null; // remplace $op->venue_SHS
  var $saisi_SHS          = null; // remplace $op->saisie
  var $modif_SHS          = null; // remplace $op->modifiee

  var $DP                 = null; // remplace $operation->CIM10_code
  var $pathologie         = null; // remplace $operation->pathologie
  var $septique           = null; // remplace $operation->septique
  var $convalescence      = null; // remplace $operation->convalescence

  var $rques              = null;
  var $ATNC               = null;
  var $hormone_croissance = null;
  var $lit_accompagnant   = null;
  var $isolement          = null;
  var $television         = null;
  var $repas_diabete      = null;
  var $repas_sans_sel     = null;
  var $repas_sans_residu  = null;
  var $mode_sortie        = null;
  var $prestation_id      = null;
  var $facturable         = null; 
  
  // Form Fields
  var $_entree             = null;
  var $_sortie             = null;
  var $_duree_prevue       = null;
  var $_duree_reelle       = null;
  var $_date_entree_prevue = null;
  var $_date_sortie_prevue = null;
  var $_hour_entree_prevue = null;
  var $_hour_sortie_prevue = null;
  var $_min_entree_prevue  = null;
  var $_min_sortie_prevue  = null;
  var $_venue_SHS_guess    = null;
  var $_at_midnight        = null;
  var $_couvert_cmu        = null;
  var $_num_dossier        = null;
  // Object References
  var $_ref_patient           = null;
  var $_ref_praticien         = null;
  var $_ref_operations        = null;
  var $_ref_last_operation    = null;
  var $_ref_affectations      = null;
  var $_ref_first_affectation = null;
  var $_ref_last_affectation  = null;
  var $_ref_GHM               = array();
  var $_ref_group             = null;

  // Filter Fields
  var $_date_min	 			= null;
  var $_date_max 				= null;
  var $_admission 			= null;
  var $_service 				= null;
  var $_type_admission  = null;
  var $_specialite 			= null;
  var $_date_min_stat		= null;
  var $_date_max_stat 	= null;
  var $_filter_type 		= null;
    
	function CSejour() {
    global $dPconfig;
    
    $this->CMbObject("sejour", "sejour_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_locked = $dPconfig["dPplanningOp"]["CSejour"]["locked"];
	}
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["affectations"] = "CAffectation sejour_id";
      $backRefs["factures"] = "CFacture sejour_id";
      $backRefs["GHM"] = "CGHM sejour_id";
      $backRefs["operations"] = "COperation sejour_id";
     return $backRefs;
  }

  function getSpecs() {
   	$specs = parent::getSpecs();
    $specs["patient_id"]          = "notNull ref class|CPatient";
    $specs["praticien_id"]        = "notNull ref class|CMediusers";
    $specs["group_id"]            = "notNull ref class|CGroups";
    $specs["type"]                = "notNull enum list|comp|ambu|exte|seances|ssr|psy default|ambu";
    $specs["modalite"]            = "notNull enum list|office|libre|tiers default|libre";
    $specs["annule"]              = "bool";
    $specs["chambre_seule"]       = "bool";
    $specs["reanimation"]         = "bool default|0";
    $specs["zt"]                  = "bool default|0";
    $specs["entree_prevue"]       = "notNull dateTime";
    $specs["sortie_prevue"]       = "notNull dateTime moreEquals|entree_prevue";
    $specs["entree_reelle"]       = "dateTime";
    $specs["sortie_reelle"]       = "dateTime";
    $specs["venue_SHS"]           = "numchar length|8 confidential";
    $specs["saisi_SHS"]           = "bool";
    $specs["modif_SHS"]           = "bool";
    $specs["DP"]                  = "code cim10";
    $specs["pathologie"]          = "str length|3";
    $specs["septique"]            = "bool";
    $specs["convalescence"]       = "text confidential";
    $specs["rques"]               = "text";
    $specs["ATNC"]                = "bool";
    $specs["hormone_croissance"]  = "bool";
    $specs["lit_accompagnant"]    = "bool";
    $specs["isolement"]           = "bool";
    $specs["television"]          = "bool";
    $specs["repas_diabete"]       = "bool";
    $specs["repas_sans_sel"]      = "bool";
    $specs["repas_sans_residu"]   = "bool";
    $specs["mode_sortie"]         = "enum list|normal|transfert|deces default|normal";
    $specs["prestation_id"]       = "ref class|CPrestation";
    $specs["facturable"]          = "bool notNull default|1";

    $specs["_entree"]             = "dateTime";
    $specs["_sortie"] 		        = "dateTime";
    $specs["_date_min"] 		      = "dateTime";
    $specs["_date_max"] 		      = "dateTime moreEquals|_date_min";
    $specs["_admission"] 		      = "text";
    $specs["_service"] 		        = "text";
    $specs["_type_admission"]     = "text";
    $specs["_specialite"] 	      = "text";
    $specs["_date_min_stat"]      = "date";
    $specs["_date_max_stat"] 	    = "date moreEquals|_date_min_stat";
    $specs["_filter_type"]        = "enum list|comp|ambu|exte|seances|ssr|psy";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "patient_id"    => "ref|CPatient",
      "praticien_id"  => "ref|CMediusers",
      "convalescence" => "like"
    );
  }

  function check() {
    $msg    = null;
    $pathos = new CDiscipline();

    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_enums["categorie"]))) {
      $msg.= "Pathologie non disponible<br />";
    }

    return $msg . parent::check();
  }
    
  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->annule) {
      $this->delAffectations();
      $this->cancelOperations();
    }

    // Cas où on a une premiere affectation différente de l'heure d'admission
    if ($this->entree_prevue) {
      $this->loadRefsAffectations();
      $firstAff =& $this->_ref_first_affectation;
      if ($firstAff->affectation_id && ($firstAff->entree != $this->entree_prevue)) {
        $firstAff->entree = $this->entree_prevue;
        $firstAff->store();
      }
      $lastAff =& $this->_ref_last_affectation;
      if ($lastAff->affectation_id && ($lastAff->sortie != $this->sortie_prevue)) {
        $lastAff->sortie = $this->sortie_prevue;
        $lastAff->store();
      }
    }
  }
  
  function delAffectations() {
    $this->loadRefsAffectations();
    foreach($this->_ref_affectations as $key => $value) {
      $this->_ref_affectations[$key]->deleteOne();
    }
    return null;
  }
  function cancelOperations(){
    $this->loadRefsOperations();
    foreach($this->_ref_operations as $key => $value) {
      $value->annulee = 1;
      $this->_ref_operations[$key]->store();
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_entree = mbGetValue($this->entree_reelle, $this->entree_prevue);
    $this->_sortie = mbGetValue($this->sortie_reelle, $this->sortie_prevue);
    
    $this->_duree_prevue       = mbDaysRelative($this->entree_prevue, $this->sortie_prevue);
    $this->_duree_reelle       = mbDaysRelative($this->entree_reelle, $this->sortie_reelle);

    $this->_date_entree_prevue = mbDate(null, $this->entree_prevue);
    $this->_date_sortie_prevue = mbDate(null, $this->sortie_prevue);
    $this->_hour_entree_prevue = mbTranformTime(null, $this->entree_prevue, "%H");
    $this->_hour_sortie_prevue = mbTranformTime(null, $this->sortie_prevue, "%H");
    $this->_min_entree_prevue  = mbTranformTime(null, $this->entree_prevue, "%M");
    $this->_min_sortie_prevue  = mbTranformTime(null, $this->sortie_prevue, "%M");

    $this->_venue_SHS_guess = mbTranformTime(null, $this->entree_prevue, "%y");
    $this->_venue_SHS_guess .= 
      $this->type == "exte" ? "5" :
      $this->type == "ambu" ? "4" : "0";
    $this->_venue_SHS_guess .="xxxxx";
    $this->_at_midnight = ($this->_date_entree_prevue != $this->_date_sortie_prevue);

    $this->_view = "Séjour de ";
    $this->_view .= mbTranformTime(null, $this->entree_prevue, "%d/%m/%Y");
    $this->_view .= " au ";
    $this->_view .= mbTranformTime(null, $this->sortie_prevue, "%d/%m/%Y");
    $this->_acte_execution = mbAddDateTime($this->entree_prevue);
    $this->_praticien_id = $this->praticien_id;   
  }
  
  function updateDBFields() {
    if ($this->_hour_entree_prevue !== null and $this->_min_entree_prevue !== null) {
      $time_entree_prevue = mbTime(null, "$this->_hour_entree_prevue:$this->_min_entree_prevue");
      $this->entree_prevue = mbAddDateTime($time_entree_prevue, $this->_date_entree_prevue);
    }
    
    if ($this->_hour_sortie_prevue !== null and $this->_min_sortie_prevue !== null) {
      $time_sortie_prevue = mbTime(null, "$this->_hour_sortie_prevue:$this->_min_sortie_prevue");
      $this->sortie_prevue = mbAddDateTime($time_sortie_prevue, $this->_date_sortie_prevue);
    }
    
    // Synchro durée d'hospi / type d'hospi
    $this->_at_midnight = (mbDate(null, $this->entree_prevue) != mbDate(null, $this->sortie_prevue));
    if($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    } elseif(!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
    }
  }
  
  // Calcul des droits CMU pour la duree totale du sejour
  function getDroitsCMU () {
     $this->_couvert_cmu = $this->_date_sortie_prevue <= $this->_ref_patient->cmu;
  }
  
  function loadRefPatient() {
    $where = array (
      "patient_id" => "= '$this->patient_id'"
    );
    
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->loadObject($where);
    $this->getDroitsCMU();
    $this->_view = "Séjour de ";
    $this->_view .= $this->_ref_patient->_view;
    $this->_view .= " du ";
    $this->_view .= mbTranformTime(null, $this->entree_prevue, "%d/%m/%Y");
    $this->_view .= " au ";
    $this->_view .= mbTranformTime(null, $this->sortie_prevue, "%d/%m/%Y");
  }
  
  function loadRefPraticien() {
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->praticien_id);
  }

  function loadRefPrestation() {
    $this->_ref_prestation = new CPrestation;
    $this->_ref_prestation->load($this->prestation_id);
  }
  
  function loadRefEtablissement(){
    // Chargement de l'établissement correspondant
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefPatient();
    $this->loadRefPraticien();
    $this->loadRefEtablissement();
    $this->loadRefsCodesCCAM();
  }
  
  
  function loadView() {
    $this->loadRefsFwd();
    $this->loadRefsActesCCAM();
  }
  
  
  function loadComplete() {
    parent::loadComplete();
    foreach ($this->_ref_operations as &$operation) {
      $operation->loadRefsFwd();
    }
    foreach ($this->_ref_affectations as &$affectation) {
      $affectation->loadRefLit();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    } 
  }
  
  
  function loadNumDossier(){
  	global $dPconfig, $g;
  	// Récuperation du tag de l'id Externe (ex: sherpa group:10)
    
  	// si pas de fichier de config ==> IPP = ""
  	if(!$dPconfig["dPplanningOp"]["CSejour"]["tag_dossier"]){
  		$this->_num_dossier = "";
    	return;
    }
    
    // sinon, $_num_dossier = valeur id400
    // creation du tag de l'id Externe
  	$tag = str_replace('$g',$g, $dPconfig["dPplanningOp"]["CSejour"]["tag_dossier"]);

  	// Recuperation de la valeur de l'id400
  	$id400 = new CIdSante400();
    $id400->loadLatestFor($this, $tag);
  	
    // Stockage de la valeur de l'id400
    $this->_num_dossier = $id400->id400;
    
    // Si pas d'id400 correspondant, on stocke "_"
    if(!$this->_num_dossier){
    	$this->_num_dossier = "-";
    }
  }
    
  function getExecutant_id($code) {
      return $this->praticien_id;
  }
  
  
  function getPerm($permType) {
    if(!$this->_ref_praticien) {
      $this->loadRefPraticien();
    }
    if(!$this->_ref_group) {
      $this->loadRefEtablissement();
    }
    return ($this->_ref_group->getPerm($permType) && $this->_ref_praticien->getPerm($permType));
  }
  
  function getCurrAffectation($date = null) {
    if(!$date) {
      $date = mbDateTime();
    }
    $curr_affectation = new CAffectation();
    $order = "entree";
    $where = array();
    $where["sejour_id"] = $this->_spec->ds->prepare("= %", $this->sejour_id);
    $where["entree"] = $this->_spec->ds->prepare("< %", $date);
    $where["sortie"] = $this->_spec->ds->prepare(">= %", $date);
    $curr_affectation->loadObject($where, $order);
    return $curr_affectation;
  }
  
  function loadRefsAffectations() {
    $where = array("sejour_id" => "= '$this->sejour_id'");
    $order = "sortie DESC";
    $this->_ref_affectations = new CAffectation();
    $this->_ref_affectations = $this->_ref_affectations->loadList($where, $order);
    if(count($this->_ref_affectations) > 0) {
      $this->_ref_first_affectation =& end($this->_ref_affectations);
      $this->_ref_last_affectation =& reset($this->_ref_affectations);
    } else {
      $this->_ref_first_affectation =& new CAffectation;
      $this->_ref_last_affectation =& new CAffectation;
    }
  }
  
  function loadRefsOperations($where = array()) {
    $where["sejour_id"] = "= '$this->sejour_id'";
    $order = "date ASC";

    $operations = new COperation;
    $this->_ref_operations = $operations->loadList($where, $order);
    
    if(count($this->_ref_operations) > 0) {
      $this->_ref_last_operation =& reset($this->_ref_operations);
    } else {
      $this->_ref_last_operation =& new COperation;
    }
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
    $this->loadRefsActesCCAM();
  }
  
  function loadRefGHM() {
    $this->_ref_GHM = new CGHM;
    $where["sejour_id"] = "= '$this->sejour_id'";
    $this->_ref_GHM->loadObject($where);
    if(!$this->_ref_GHM->ghm_id) {
      $this->_ref_GHM->sejour_id = $this->sejour_id;
      $this->_ref_GHM->loadRefsFwd();
      $this->_ref_GHM->bindInfos();
      $this->_ref_GHM->getGHM();
    }
  }
}
?>