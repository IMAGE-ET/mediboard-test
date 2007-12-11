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
  var $_duree              = null;
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
  var $_curr_op_id         = null;
  var $_curr_op_date       = null;
  
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
  var $_ref_etabExterne       = null;
  var $_ref_dossier_medical   = null;
  
  // External objects
  var $_ext_diagnostic_principal = null;
  
  // Distant fields
  var $_dates_operations = null;
  
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
  var $_ccam_libelle    = null;
  
  // Object tool field
  var $_modifier_sortie = null;
  
  // BackRef
  var $etablissement_transfert_id = null;
    
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
    $specs["sortie_reelle"]       = "dateTime moreThan|entree_reelle";
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
    $specs["etablissement_transfert_id"] = "ref class|CEtabExterne";
    
    $specs["_entree"]         = "dateTime";
    $specs["_sortie"] 		    = "dateTime";
    $specs["_date_min"] 		  = "dateTime";
    $specs["_date_max"] 		  = "dateTime moreEquals|_date_min";
    $specs["_admission"] 		  = "text";
    $specs["_service"] 	      = "text";
    $specs["_type_admission"] = "text";
    $specs["_specialite"]     = "text";
    $specs["_date_min_stat"]  = "date";
    $specs["_date_max_stat"]  = "date moreEquals|_date_min_stat";
    $specs["_filter_type"]    = "enum list|comp|ambu|exte|seances|ssr|psy";
    $specs["_ccam_libelle"]   = "bool default|1";
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

    $oldSejour = new CSejour();
    if($this->_id) {
      $oldSejour->load($this->_id);
    }
    
    // Test de la pathologies
    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->_enums["categorie"]))) {
      $msg.= "Pathologie non disponible<br />";
    }
    
    // Test de coherence de date avec les interventions
    if($this->entree_prevue === null && $this->sortie_prevue === null) {
      $entree = $oldSejour->entree_prevue;
      $sortie = $oldSejour->sortie_prevue;
    } else {
      $entree = $this->entree_prevue;
      $sortie = $this->sortie_prevue;
    }

    if($entree !== null && $sortie !== null) {
      $this->loadRefsOperations();
      foreach($this->_ref_operations as $key => $operation){
        $operation->loadRefPlageop();
        $isCurrOp = $this->_curr_op_id == $operation->_id;
        if($isCurrOp) {
          $opInBounds = $this->_curr_op_date >= mbDate($entree) && $this->_curr_op_date <= mbDate($sortie);
        } else {
           $opInBounds = mbDate($operation->_datetime) >= mbDate($entree) && mbDate($operation->_datetime) <= mbDate($sortie);
        }
        if(!$opInBounds){
           $msg.= "Interventions en dehors des nouvelles dates du sejour";  
        }
      }
    }

    // Test de colision avec un autre sejour
    $patient = new CPatient;
    if($this->patient_id) {
      $patient->load($this->patient_id);
    } elseif($oldSejour->patient_id) {
      $patient->load($oldSejour->patient_id);
    }
    
    if($patient->_id) {
      $where = array("annule" => "= '0'");
      $patient->loadRefsSejours($where);

      // suppression de la liste des sejours le sejour courant
      $listSejour = array();
      foreach($patient->_ref_sejours as $key => $sejour){
        $listSejour[$key] = $sejour;
        if($key == $this->_id){
          unset($listSejour[$key]);
        }
      }
    
      foreach($listSejour as $key => $sejour){
        //Test sur les entree prevues du sejour
        if ((mbDate($sejour->entree_prevue) < mbDate($this->sortie_prevue) and mbDate($sejour->sortie_prevue) > mbDate($this->sortie_prevue))
          or(mbDate($sejour->entree_prevue) < mbDate($this->entree_prevue) and mbDate($sejour->sortie_prevue) > mbDate($this->entree_prevue))
          or(mbDate($sejour->entree_prevue) >= mbDate($this->entree_prevue) and mbDate($sejour->sortie_prevue) <= mbDate($this->sortie_prevue))) {
          $msg .= "Collision avec le sejour du $sejour->entree_prevue au $sejour->sortie_prevue<br />";
        }     
      }
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
        $firstAff->_no_synchro = 1;
        $firstAff->store();
      }
      $lastAff =& $this->_ref_last_affectation;
      if ($lastAff->affectation_id && ($lastAff->sortie != $this->sortie_prevue)) {
        $lastAff->sortie = $this->sortie_prevue;
        $lastAff->_no_synchro = 1;
        $lastAff->store();
      }
    }
    
    //si le sejour a une sortie ==> compléter le champ effectue de la derniere affectation
    if($this->mode_sortie){
      $this->_ref_last_affectation->effectue = 1;
        $this->_ref_last_affectation->store();  
    } 
    
    if($this->mode_sortie === ""){
      $this->_ref_last_affectation->effectue = 0;
      $this->sortie_reelle = "";
      $this->_ref_last_affectation->store();
    }
    
    if (null !== $this->mode_sortie) {
      if ("transfert" != $this->mode_sortie) {
        $this->etablissement_transfert_id = "";
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
    $this->_duree              = mbDaysRelative($this->_entree, $this->_sortie);

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
      $this->entree_prevue = "$this->_date_entree_prevue";
      $this->entree_prevue.= " ".str_pad($this->_hour_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":".str_pad($this->_min_entree_prevue, 2, "0", STR_PAD_LEFT);
      $this->entree_prevue.= ":00";
    }
    
    if ($this->_hour_sortie_prevue !== null and $this->_min_sortie_prevue !== null) {
      $this->sortie_prevue = "$this->_date_sortie_prevue";
      $this->sortie_prevue.= " ".str_pad($this->_hour_sortie_prevue, 2, "0", STR_PAD_LEFT);
      $this->sortie_prevue.= ":".str_pad($this->_min_sortie_prevue, 2, "0", STR_PAD_LEFT); 
      $this->sortie_prevue.= ":00";
    }
    
    // Synchro durée d'hospi / type d'hospi
    $this->_at_midnight = (mbDate(null, $this->entree_prevue) != mbDate(null, $this->sortie_prevue));
    if($this->_at_midnight && $this->type == "ambu") {
      $this->type = "comp";
    } elseif(!$this->_at_midnight && $this->type == "comp") {
      $this->type = "ambu";
    }
    
    // Signaler l'action de validation de la sortie
    if($this->_modifier_sortie){
      $this->sortie_reelle = mbDateTime();
    }
    
    if($this->_modifier_sortie === '0'){
      $this->sortie_reelle = "";
    }
    
  }
  
  // Calcul des droits CMU pour la duree totale du sejour
  function getDroitsCMU () {
     $this->_couvert_cmu = $this->_date_sortie_prevue <= $this->_ref_patient->cmu;
  }
  
  // Chargement du dossier medical du sejour
  function loadRefDossierMedical(){
    $this->_ref_dossier_medical = new CDossierMedical();
    $where["object_id"] = "= '$this->_id'";
    $where["object_class"] = "= 'CSejour'";
    $this->_ref_dossier_medical->loadObject($where);
  }
  
  function loadRefEtabExterne(){
    $this->_ref_etabExterne = new CEtabExterne();
    $this->_ref_etabExterne->load($this->etablissement_transfert_id);
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
  
  function loadRefDiagnosticPrincipal() {
    if($this->DP) {
      $this->_ext_diagnostic_principal = new CCodeCIM10($this->DP, 1);
    }
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
    $this->loadRefEtabExterne();
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
    
    $this->loadNumDossier();
    
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
    
  function getExecutantId($code_activite) {
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
    if(mbTime(null, $date) == "00:00:00") {
      $where["entree"] = $this->_spec->ds->prepare("< %", mbDate(null, $date)." 23:59:59");
      $where["sortie"] = $this->_spec->ds->prepare(">= %", mbDate(null, $date)." 00:00:01");
    } else {
      $where["entree"] = $this->_spec->ds->prepare("< %", $date);
      $where["sortie"] = $this->_spec->ds->prepare(">= %", $date);
    }
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
    $where["sejour_id"] = "= '$this->_id'";
    $order = "date ASC";

    $operations = new COperation;
    $this->_ref_operations = $operations->loadList($where, $order);
        
    if (count($this->_ref_operations) > 0) {
      $this->_ref_last_operation =& reset($this->_ref_operations);
    } else {
      $this->_ref_last_operation =& new COperation;
    }
  }
  
  function makeDatesOperations() {
    $this->_dates_operations = array();
    
    // On s'assure d'avoir les opérations
    if (!$this->_ref_operations) {
      $this->loadRefsOperations();
    }
    
    foreach ($this->_ref_operations as &$operation) {
      // On s'assure d'avoir les plages op
      if (!$operation->_ref_plageop) {
        $operation->loadRefPlageOp();
      }

      $this->_dates_operations[] = mbDate($operation->_datetime);
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
  
  function fillTemplate(&$template) {
    $dateFormat = "%d / %m / %Y";
    $timeFormat = "%Hh%M";
    
    $template->addProperty("Admission - Date"                 , mbTranformTime(null, $this->entree_prevue, $dateFormat));
    $template->addProperty("Admission - Heure"                , mbTranformTime(null, $this->entree_prevue, $timeFormat));
    $template->addProperty("Hospitalisation - Durée"          , $this->_duree_prevue);
    $template->addProperty("Hospitalisation - Date sortie"    , mbTranformTime(null, $this->sortie_prevue, $dateFormat));
    
    $this->loadRefDossierMedical();
    // Dossier médical
    $this->_ref_dossier_medical->fillTemplate($template, "Sejour");
  }
  
}
?>