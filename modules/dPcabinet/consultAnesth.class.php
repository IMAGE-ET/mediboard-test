<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

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
  var $tca_temoin    = null;
  var $creatinine    = null;
  var $na            = null;
  var $k             = null;
  var $tsivy         = null;
  var $plaquettes    = null;
  var $ecbu          = null;
  var $pouls         = null;
  var $spo2          = null;
  var $ht            = null;
  var $ht_final      = null;
  var $premedication = null;
  var $prepa_preop   = null;

  // Form fields
  var $_date_consult = null;
  var $_date_op      = null;
  var $_sec_tsivy    = null;
  var $_min_tsivy    = null;
  var $_sec_tca      = null;
  var $_min_tca      = null;

  // Object References
  var $_ref_consultation       = null;
  var $_ref_techniques         = null;
  var $_ref_last_consultanesth = null;
  var $_ref_operation          = null;
  var $_ref_plageconsult       = null;
  var $_intub_difficile        = null;
  var $_clairance              = null;
  var $_imc                    = null;
  var $_imc_valeur             = null;
  var $_vst                    = null;
  var $_psa                    = null;

  function CConsultAnesth() {
    $this->CMbObject("consultation_anesth", "consultation_anesth_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["techniques"] = "CTechniqueComp consultation_anesth_id";
     return $backRefs;
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["consultation_id"]  = "notNull ref class|CConsultation cascade";
    $specs["operation_id"]     = "ref class|COperation";

    // @todo : un type particulier pour le poid et la taille
    $specs["poid"]             = "float pos";
    $specs["taille"]           = "float min|0";
    $specs["groupe"]           = "enum list|?|O|A|B|AB default|?";
    $specs["rhesus"]           = "enum list|?|NEG|POS default|?";
    $specs["antecedents"]      = "text confidential";
    $specs["traitements"]      = "text confidential";
    $specs["tabac"]            = "text";
    $specs["oenolisme"]        = "text";
    $specs["tasys"]            = "num max|64";
    $specs["tadias"]           = "num max|64";
    $specs["intubation"]       = "enum list|?|dents|bouche|cou";
    $specs["biologie"]         = "enum list|?|NF|COAG|IONO";
    $specs["commande_sang"]    = "enum list|?|clinique|CTS|autologue";
    $specs["ASA"]              = "enum list|1|2|3|4|5 default|1";

    // Données examens complementaires
    $specs["rai"]              = "enum list|?|NEG|POS default|?";
    $specs["hb"]               = "float min|0";
    $specs["tp"]               = "float minMax|0|100";
    $specs["tca"]              = "numchar maxLength|2";
    $specs["tca_temoin"]       = "numchar maxLength|2";
    $specs["creatinine"]       = "float";
    $specs["na"]               = "float min|0";
    $specs["k"]                = "float min|0";
    $specs["tsivy"]            = "time";
    $specs["plaquettes"]       = "numchar maxLength|4 pos";
    $specs["ecbu"]             = "enum list|?|NEG|POS default|?";
    $specs["pouls"]            = "numchar maxLength|4 pos";
    $specs["spo2"]             = "float minMax|0|100";
    $specs["ht"]               = "float minMax|0|100";
    $specs["ht_final"]         = "float minMax|0|100";
    $specs["premedication"]    = "text";
    $specs["prepa_preop"]      = "text";

    // Champs pour les conditions d'intubation
    $specs["mallampati"]       = "enum list|classe1|classe2|classe3|classe4";
    $specs["bouche"]           = "enum list|m20|m35|p35";
    $specs["distThyro"]        = "enum list|m65|p65";
    $specs["etatBucco"]        = "text";
    $specs["conclusion"]       = "text";
    $specs["position"]         = "enum list|DD|DV|DL|GP|AS|TO|GYN";
    
    return $specs;
  }
  
  function getSeeks() {
    return array (
      //"chir_id"         => "ref|CMediusers",
      "consultation_id" => "ref|CConsultation",
      "operation_id"    => "ref|COperation",
      "conclusion"      => "like"
    );
  }
  
  function getHelpedFields(){
    return array(
      "tabac"         => null,
      "oenolisme"     => null,
      "etatBucco"     => null,
      "conclusion"    => null,
      "premedication" => null,
      "prepa_preop"   => null
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
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
  	
    $this->_sec_tsivy = intval(substr($this->tsivy, 6, 2));
    $this->_min_tsivy  = intval(substr($this->tsivy, 3, 2));

    // Hack for ZEROFILL issue
    $this->tasys  = intval($this->tasys);
    $this->tadias = intval($this->tadias);
  }
   
  function updateDBFields() {
    
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
    $this->_view = $this->_ref_consultation->_view;
    $this->_ref_consultation->loadRefsActesCCAM();
  }
  
  function loadRefOperation() {
    $this->_ref_operation = new COperation;
    $this->_ref_operation->load($this->operation_id);
  }
  
  function loadRefsFiles(){
  	if(!$this->_ref_consultation){
  		$this->loadRefConsultation();
  	}
    $this->_ref_consultation->loadRefsFiles();
    $this->_ref_files =& $this->_ref_consultation->_ref_files;
  }
    
  function loadView() {
  	$this->loadRefsFwd();
    $this->_ref_consultation->loadRefsActesCCAM();  
  }
   
  
  function loadComplete(){
   parent::loadComplete();
   $this->_ref_consultation->loadExamsComp();
   $this->_ref_consultation->loadRefsExamNyha();
   $this->_ref_consultation->loadRefsExamPossum();
   $this->_ref_consultation->loadRefsExamIgs();
   $this->loadRefOperation();
   $this->_ref_operation->loadRefSejour();
   $this->_ref_operation->_ref_sejour->loadRefDossierMedical();
   $this->_ref_operation->_ref_sejour->_ref_dossier_medical->loadRefsAntecedents();
   $this->_ref_operation->_ref_sejour->_ref_dossier_medical->loadRefsAddictions();
   $this->_ref_operation->_ref_sejour->_ref_dossier_medical->loadRefstraitements();
   
   foreach ($this->_ref_consultation->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }  
  
  function loadRefsFwd() {
    $this->loadRefConsultation();
    $this->_ref_consultation->loadRefsFwd();  	
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->loadRefOperation();
    $this->_ref_operation->loadRefsFwd();
    $this->_date_consult =& $this->_ref_consultation->_date;
    $this->_date_op =& $this->_ref_operation->_datetime;
    
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
   
   // Détermination valeur IMC
   if($this->poid && $this->taille){
     if($this->_ref_consultation->_ref_patient->sexe!="m"){
       $valeurImc = array("seuil_inf" => 19, "seuil_sup" => 24);
     }else{
       $valeurImc = array("seuil_inf" => 20, "seuil_sup" => 25);
     }
     if($this->_imc < $valeurImc["seuil_inf"]){
       $this->_imc_valeur = "Maigreur";
     }elseif($this->_imc > $valeurImc["seuil_sup"] && $this->_imc <=30){
       $this->_imc_valeur = "Surpoids";
     }elseif($this->_imc > 30 && $this->_imc <=40){
       $this->_imc_valeur = "Obésité";
     }elseif($this->_imc > 40){
       $this->_imc_valeur = "Obésité morbide";
     }
   }
 
  }
  
  function loadRefsTechniques() {
    $this->_ref_techniques = new CTechniqueComp;
    $where = array();
    $where["consultation_anesth_id"] = "= '$this->consultation_anesth_id'";
    $order = "technique";
    $this->_ref_techniques = $this->_ref_techniques->loadList($where,$order);
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsTechniques();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consultation){
      $this->loadRefConsultation();
    }
    if($this->operation_id){
      if(!$this->_ref_operation){
        $this->loadRefOperation();
      }
      $canOper = $this->_ref_operation->getPerm($permType);
    }else{
      $canOper = false;
    }
    return $this->_ref_consultation->getPerm($permType) || $canOper;
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
    $template->addProperty("Anesthésie - tabac"          , $this->tabac);
    $template->addProperty("Anesthésie - oenolisme"      , $this->oenolisme);
    $template->addProperty("Anesthésie - TA"             , $this->tasys." / ".$this->tadias);
    $template->addProperty("Anesthésie - Pouls"          , $this->pouls);
    $template->addProperty("Anesthésie - Groupe Sanguin" , $this->groupe." ".$this->rhesus);
    $template->addProperty("Anesthésie - ASA"            , $this->ASA);
  }
  
  function canDeleteEx() {
    // Date dépassée
    $this->loadRefConsultation();
    $consult =& $this->_ref_consultation;
    $consult->loadRefPlageConsult();
    if ($consult->_ref_plageconsult->date < mbDate()){
      return "Imposible de supprimer une consultation passée";
    }
    
    return parent::canDeleteEx();
  }
}


?>