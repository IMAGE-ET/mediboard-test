<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ("mbobject"));

require_once($AppUI->getModuleClass("dPcim10"      , "favoricim10"));
require_once($AppUI->getModuleClass("dPcim10"      , "codecim10"));
require_once($AppUI->getModuleClass("dPpatients"   , "patients"));
require_once($AppUI->getModuleClass("dPcabinet"    , "consultation"));
require_once($AppUI->getModuleClass("dPcabinet"    , "plageconsult"));
require_once($AppUI->getModuleClass("dPcabinet"    , "techniqueComp"));
require_once($AppUI->getModuleClass("dPfiles"      , "files"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

class CConsultAnesth extends CMbObject {
  // DB Table key
  var $consultation_anesth_id = null;

  // DB References
  var $consultation_id = null;
  var $operation_id    = null;

  // DB fields
  var $poid          = null;
  var $taille        = null;
  var $groupe        = null;
  var $rhesus        = null;
  var $antecedents   = null;
  var $traitements   = null;
  var $tabac         = null;
  var $oenolisme     = null;
  var $transfusions  = null;
  var $tasys         = null;
  var $tadias        = null;
  var $intubation    = null;
  var $biologie      = null;
  var $commande_sang = null;
  var $ASA           = null;
  var $mallampati    = null;
  var $bouche        = null;
  var $distThyro     = null;
  var $etatBucco     = null;
  var $conclusion    = null;
  var $position      = null;
  var $rai           = null;
  var $hb            = null;
  var $tp            = null;
  var $tca           = null;
  var $creatinine    = null;
  var $na            = null;
  var $k             = null;
  var $tsivy         = null;
  var $plaquettes    = null;
  var $ecbu          = null;
  var $ecbu_detail   = null;
  var $pouls         = null;
  var $spo2          = null;
  var $ht            = null;

  // Form fields
  var $_date_consult = null;
  var $_date_op      = null;
  var $_sec_tsivy    = null;
  var $_min_tsivy    = null;
  var $_sec_tca      = null;
  var $_min_tca      = null;

  // Object References
  var $_ref_consult            = null;
  var $_ref_techniques         = null;
  var $_ref_last_consultanesth = null;
  var $_ref_operation          = null;
  var $_ref_plageconsult       = null;
  var $_intub_difficile        = null;
  var $_clairance              = null;
  var $_imc                    = null;
  var $_vst                    = null;
  var $_psa                    = null;

  function CConsultAnesth() {
    $this->CMbObject("consultation_anesth", "consultation_anesth_id");

    $this->_props["consultation_id"] = "ref|notNull";
    $this->_props["operation_id"]    = "ref";
    // @todo : un type particulier pour le poid et la taille
    $this->_props["poid"]            = "currency|pos";
    $this->_props["taille"]          = "currency|min|0";
    $this->_props["groupe"]          = "enum|?|O|A|B|AB";
    $this->_props["rhesus"]          = "enum|?|-|+";
    $this->_props["antecedents"]     = "str|confidential";
    $this->_props["traitements"]     = "str|confidential";
    $this->_props["tabac"]           = "str";
    $this->_props["oenolisme"]       = "str";
    $this->_props["transfusions"]    = "enum|?|-|+";
    $this->_props["tasys"]           = "num";
    $this->_props["tadias"]          = "num";
    $this->_props["intubation"]      = "enum|?|dents|bouche|cou";
    $this->_props["biologie"]        = "enum|?|NF|COAG|IONO";
    $this->_props["commande_sang"]   = "enum|?|clinique|CTS|autologue";
    $this->_props["ASA"]             = "enum|1|2|3|4|5";
    
    // Données examens complementaires
    $this->_props["rai"]             = "currency";
    $this->_props["hb"]              = "currency|min|0";
    $this->_props["tp"]              = "currency|minMax|0|100";
    $this->_props["tca"]             = "time";
    $this->_props["creatinine"]      = "currency";
    $this->_props["na"]              = "currency|min|0";
    $this->_props["k"]               = "currency|min|0";
    $this->_props["tsivy"]           = "time";
    $this->_props["plaquettes"]      = "num|pos";
    $this->_props["ecbu"]            = "enum|?|NEG|POS";
    $this->_props["ecbu_detail"]     = "str";
    $this->_props["pouls"]           = "num|pos";
    $this->_props["spo2"]            = "currency|min|0";
    $this->_props["ht"]              = "currency|minMax|0|100";
    
    // Champs pour les conditions d'intubation
    $this->_props["mallampati"]      = "enum|classe1|classe2|classe3|classe4";
    $this->_props["bouche"]          = "enum|m20|m35|p35";
    $this->_props["distThyro"]       = "enum|m65|p65";
    $this->_props["etatBucco"]       = "str";
    $this->_props["conclusion"]      = "str";
    $this->_props["position"]        = "enum|DD|DV|DL|GP|AS|TO|GYN";
    
    $this->buildEnums();
    
    $this->_seek["chir_id"]         = "ref|CMediusers";
    $this->_seek["consultation_id"] = "ref|CConsultation";
    $this->_seek["operation_id"]    = "ref|COperation";
    $this->_seek["conclusion"]      = "like";
  }
  
  function updateFormFields() {
  	// Vérification si intubation difficile
  	if(
  	  ($this->mallampati && ($this->mallampati=="classe3" || $this->mallampati=="classe4"))
  	  || ($this->bouche && ($this->bouche=="m20" || $this->bouche=="m35"))
  	  || ($this->distThyro && $this->distThyro=="m65")
  	  ){
  	  $this->_intub_difficile = true;
  	}
  	// Calcul de l'Indice de Masse Corporelle
  	if($this->poid && $this->taille){
  	  $this->_imc = round($this->poid / ($this->taille * $this->taille * 0.0001),2);
  	}
  	
  	$this->_sec_tca = intval(substr($this->tca, 6, 2));
    $this->_min_tca  = intval(substr($this->tca, 3, 2));
    $this->_sec_tsivy = intval(substr($this->tsivy, 6, 2));
    $this->_min_tsivy  = intval(substr($this->tsivy, 3, 2));
    
    parent::updateFormFields();
  }
   
  function updateDBFields() {
    $this->tca = "00:";
    if($this->_min_tca){
      $this->tca .= $this->_min_tca.":";
    }else{$this->tca .= "00:";}
    if($this->_sec_tca){
      $this->tca .= $this->_sec_tca;
    }else{$this->tca .= "00";}
    
    $this->tsivy = "00:";
    if($this->_min_tsivy){
      $this->tsivy .= $this->_min_tsivy.":";
    }else{$this->tsivy .= "00:";}
    if($this->_sec_tsivy){
      $this->tsivy .= $this->_sec_tsivy;
    }else{$this->tsivy .= "00";}

    parent::updateDBFields();
  }

  function check() {
    // Data checking
    $msg = null;
    return $msg . parent::check();
  }
  
  function loadRefConsultation() {
    $this->_ref_consultation = new CConsultation;
    $this->_ref_consultation->load($this->consultation_id);
  }
  
  function loadRefOperation() {
    $this->_ref_operation = new COperation;
    $this->_ref_operation->load($this->operation_id);
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->loadRefConsultation();
    $this->_ref_consultation->loadRefsFwd();
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->loadRefOperation();
    $this->_ref_operation->loadRefsFwd();    
    $this->_date_consult =& $this->_ref_consultation->_date;
    $this->_date_op =& $this->_ref_operation->_ref_plageop->date;
    
    // Calcul de la Clairance créatinine
  	if($this->poid && $this->creatinine && $this->_ref_consultation->_ref_patient->_age
  	   && intval($this->_ref_consultation->_ref_patient->_age)>=18 && intval($this->_ref_consultation->_ref_patient->_age)<=110
  	   && $this->poid>=35 && $this->poid<=120
  	   && $this->creatinine>=6 && $this->creatinine<=70
  	   ){
  	  $this->_clairance = $this->poid * (140-$this->_ref_consultation->_ref_patient->_age) / (7.2 * $this->creatinine);
  	  if($this->_ref_consultation->_ref_patient->sexe!="m"){
  	    $this->_clairance = 0.85 * $this->_clairance;
  	  }
  	  $this->_clairance = round($this->_clairance,2);
  	}
  	// Calcul du Volume Sanguin Total
  	if($this->poid){
  	  if($this->_ref_consultation->_ref_patient->sexe!="m"){
  	    $this->_vst = 65 * $this->poid;
  	  }else{
  	    $this->_vst = 70 * $this->poid;
  	  }
  	}
  	// Calcul des Pertes Sanguines Acceptables
  	if($this->ht && $this->_vst){
      $this->_psa = $this->_vst * ($this->ht - 30) / 100;
  	}
  }
  
  function loadRefsBack() {
    // Backward references    
    $this->_ref_techniques = new CTechniqueComp;
    $where = array();
    $where["consultAnesth_id"] = "= '$this->consultation_anesth_id'";
    $order = "technique";
    $this->_ref_techniques = $this->_ref_techniques->loadList($where,$order);
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefConsultation();
      $this->loadRefOperation();
    }
    $this->_canRead = $this->_ref_consult->canRead() || $this->_ref_operation->canRead();
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefConsultation();
      $this->loadRefOperation();
    }
    $this->_canEdit = $this->_ref_consult->canEdit() || $this->_ref_operation->canEdit();
    return $this->_canEdit;
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_consultation->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    $this->_ref_operation->fillLimitedTemplate($template);
  }
  
  function fillLimitedTemplate(&$template) {
    $template->addProperty("Anesthésie - poids"          , $this->poid." kg");
    $template->addProperty("Anesthésie - taille"         , $this->taille." cm");
    $template->addProperty("Anesthésie - IMC"            , $this->_imc);
    $template->addProperty("Anesthésie - tabac"          , nl2br($this->tabac));
    $template->addProperty("Anesthésie - oenolisme"      , nl2br($this->oenolisme));
    $template->addProperty("Anesthésie - TA"             , $this->tasys." / ".$this->tadias);
    $template->addProperty("Anesthésie - Pouls"          , $this->pouls);
    $template->addProperty("Anesthésie - Groupe Sanguin" , $this->groupe." ".$this->rhesus);
    $template->addProperty("Anesthésie - ASA"            , $this->ASA);
  }
}

?>