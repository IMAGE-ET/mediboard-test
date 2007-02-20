<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Thomas Despoix
 */

// @todo: Put the following in $config_dist;
global $dPconfig;

/**
 * Classe CSejour. 
 * @abstract Gère les séjours en établissement
 */
class CSejour extends CMbObject {
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
  
  // Form Fields
  var $_duree_prevue       = null;
  var $_date_entree_prevue = null;
  var $_date_sortie_prevue = null;
  var $_hour_entree_prevue = null;
  var $_hour_sortie_prevue = null;
  var $_min_entree_prevue  = null;
  var $_min_sortie_prevue  = null;
  var $_venue_SHS_guess    = null;
  var $_at_midnight        = null;

  // Object References
  var $_ref_patient           = null;
  var $_ref_praticien         = null;
  var $_ref_operations        = null;
  var $_ref_last_operation    = null;
  var $_codes_ccam            = null;
  var $_ref_affectations      = null;
  var $_ref_first_affectation = null;
  var $_ref_last_affectation  = null;
  var $_ref_GHM               = array();
  var $_ref_group             = null;

	function CSejour() {
    $this->CMbObject("sejour", "sejour_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}
  
  function getSpecs() {
    return array (
      "patient_id"         => "notNull refMandatory",
      "praticien_id"       => "notNull refMandatory",
      "group_id"           => "notNull refMandatory",
      "type"               => "notNull enum list|comp|ambu|exte|seances|ssr|psy",
      "modalite"           => "notNull enum list|office|libre|tiers",
      "annule"             => "bool",
      "chambre_seule"      => "bool",
      "entree_prevue"      => "notNull dateTime",
      "sortie_prevue"      => "notNull dateTime moreEquals|entree_prevue",
      "entree_reelle"      => "dateTime",
      "sortie_reelle"      => "dateTime",
      "venue_SHS"          => "numchar length|8 confidential",
      "saisi_SHS"          => "bool",
      "modif_SHS"          => "bool",
      "DP"                 => "code cim10",
      "pathologie"         => "str length|3",
      "septique"           => "bool",
      "convalescence"      => "text confidential",
      "rques"              => "text",
      "ATNC"               => "bool",
      "hormone_croissance" => "bool",
      "lit_accompagnant"   => "bool",
      "isolement"          => "bool",
      "television"         => "bool",
      "repas_diabete"      => "bool",
      "repas_sans_sel"     => "bool",
      "repas_sans_residu"  => "bool"
    );
  }
  
  function getSeeks() {
    return array (
      "patient_id"    => "ref|CPatient",
      "praticien_id"  => "ref|CMediusers",
      "convalescence" => "like"
    );
  }

  function check() {
    $msg = null;
    static $pathos = null;
    if (!$pathos) {
      $pathos = new CPathologies();
    }

    if ($this->pathologie != null && (!in_array($this->pathologie, $pathos->dispo))) {
      $msg.= "Pathologie non disponible<br />";
    }

    return $msg . parent::check();
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "opérations", 
      "name"      => "operations", 
      "idfield"   => "operation_id", 
      "joinfield" => "sejour_id"
    );
    
    return CMbObject::canDelete($msg, $oid, $tables);
  }
  
  function store() {
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->annule) {
      $this->delAffectations();
      $this->delOperations();
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
  
  function delete() {
    $msg = parent::delete();
    if($msg == null) {
      // Suppression des affectations
      $this->delAffectations();
    }
    return $msg;
  }
  
  function delAffectations() {
    $this->loadRefsAffectations();
    foreach($this->_ref_affectations as $key => $value) {
      $this->_ref_affectations[$key]->delete();
    }
  }
  function delOperations(){
    $this->loadRefsOperations();
    foreach($this->_ref_operations as $key => $value) {
      $value->annulee = 1;
      $this->_ref_operations[$key]->store();
    }
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_duree_prevue       = mbDaysRelative($this->entree_prevue, $this->sortie_prevue);
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
    $this->_view = "Séjour du ";
    $this->_view .= mbTranformTime(null, $this->entree_prevue, "%d/%m/%Y");
    $this->_view .= " au ";
    $this->_view .= mbTranformTime(null, $this->sortie_prevue, "%d/%m/%Y");
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
  
  function loadRefPatient() {
    $where = array (
      "patient_id" => "= '$this->patient_id'"
    );

    $this->_ref_patient = new CPatient;
    $this->_ref_patient->loadObject($where);
  }
  
  function loadRefPraticien() {
    $this->_ref_praticien = new CMediusers;
    $this->_ref_praticien->load($this->praticien_id);
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
      $this->_codes_ccam = $this->_ref_last_operation->codes_ccam;
    } else {
      $this->_ref_last_operation =& new COperation;
    }
  }
  
  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsAffectations();
    $this->loadRefsOperations();
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