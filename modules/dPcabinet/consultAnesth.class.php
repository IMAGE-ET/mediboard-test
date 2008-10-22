<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author Romain Ollivier
 */

class CConsultAnesth extends CMbObject {
  // DB Table key
  var $consultation_anesth_id = null;

  // DB References
  var $consultation_id = null;
  var $operation_id    = null;
  var $sejour_id       = null;

  // DB fields
  var $groupe        = null;
  var $rhesus        = null;
  var $antecedents   = null;
  var $traitements   = null;
  var $tabac         = null;
  var $oenolisme     = null;
  var $intubation    = null;
  var $biologie      = null;
  var $commande_sang = null;
  var $ASA           = null;
  var $mallampati    = null;
  var $bouche        = null;
  var $distThyro     = null;
  var $etatBucco     = null;
  var $examenCardio  = null;
  var $examenPulmo   = null;
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
  var $_intub_difficile = null;
  var $_clairance       = null;
  var $_psa             = null;

  // Object References
  var $_ref_consultation       = null;
  var $_ref_techniques         = null;
  var $_ref_last_consultanesth = null;
  var $_ref_operation          = null;
  var $_ref_sejour             = null;
  var $_ref_plageconsult       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_anesth';
    $spec->key   = 'consultation_anesth_id';
    return $spec;
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
    $specs["sejour_id"]        = "ref class|CSejour";

    $specs["groupe"]           = "enum list|?|O|A|B|AB default|?";
    $specs["rhesus"]           = "enum list|?|NEG|POS default|?";
    $specs["antecedents"]      = "text confidential";
    $specs["traitements"]      = "text confidential";
    $specs["tabac"]            = "text";
    $specs["oenolisme"]        = "text";
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
    $specs["ht"]               = "float minMax|0|100";
    $specs["ht_final"]         = "float minMax|0|100";
    $specs["premedication"]    = "text";
    $specs["prepa_preop"]      = "text";

    // Champs pour les conditions d'intubation
    $specs["mallampati"]       = "enum list|classe1|classe2|classe3|classe4";
    $specs["bouche"]           = "enum list|m20|m35|p35";
    $specs["distThyro"]        = "enum list|m65|p65";
    $specs["etatBucco"]        = "text";
    $specs["examenCardio"]     = "text";
    $specs["examenPulmo"]      = "text";
    $specs["conclusion"]       = "text";
    $specs["position"]         = "enum list|DD|DV|DL|GP|AS|TO|GYN";

    // Champs dérivés
    $specs["_intub_difficile"] = "";
    $specs["_clairance"]       = "";
    $specs["_psa"]             = "";

    return $specs;
  }

  function getSeeks() {
    return array (
    //"chir_id"         => "ref|CMediusers",
      "consultation_id" => "ref|CConsultation",
      "operation_id"    => "ref|COperation",
      "sejour_id"       => "ref|CSejour",
      "conclusion"      => "like"
      );
  }

  function getHelpedFields(){
    return array(
      "tabac"         => null,
      "oenolisme"     => null,
      "etatBucco"     => null,
      "examenCardio"  => null,
      "examenPulmo"   => null,
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
     
    $this->_sec_tsivy = intval(substr($this->tsivy, 6, 2));
    $this->_min_tsivy = intval(substr($this->tsivy, 3, 2));
  }
   
  function updateDBFields() {
    if($this->_min_tsivy !== null && $this->_sec_tsivy !== null) {
      $this->tsivy  = '00:'.($this->_min_tsivy ? sprintf("%02d", $this->_min_tsivy):'00').':';
      $this->tsivy .=       ($this->_sec_tsivy ? sprintf("%02d", $this->_sec_tsivy):'00');
    }

    parent::updateDBFields();
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

  function loadRefSejour() {
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
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
    if(!$this->_ref_operation->_id) {
      $this->loadRefSejour();
    } else {
      $this->_ref_operation->loadRefSejour();
      $this->_ref_sejour =& $this->_ref_operation->_ref_sejour;
    }
    $this->_ref_sejour->loadRefDossierMedical();
    $this->_ref_sejour->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_sejour->_ref_dossier_medical->loadRefstraitements();
     
    foreach ($this->_ref_consultation->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }

  function loadRefsFwd() {
    $this->loadRefConsultation();
    $this->_ref_consultation->loadRefsFwd();
    $this->_ref_consultation->_ref_patient->loadRefConstantesMedicales();
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->loadRefOperation();
    if(!$this->_ref_operation->_id) {
      $this->loadRefSejour();
    } else {
      $this->_ref_operation->loadRefSejour();
      $this->_ref_sejour =& $this->_ref_operation->_ref_sejour;
    }
    $this->_ref_operation->loadRefsFwd();
    $this->_date_consult =& $this->_ref_consultation->_date;
    $this->_date_op =& $this->_ref_operation->_datetime;

    // Calcul de la Clairance créatinine
    $this->_ref_consultation->_ref_patient->loadRefConstantesMedicales();
    $const_med = $this->_ref_consultation->_ref_patient->_ref_constantes_medicales;
    $const_med->updateFormFields();
    $age = intval($this->_ref_consultation->_ref_patient->_age);
    if ($const_med->poids && $this->creatinine && 
        $age && $age >= 18 && $age <= 110 && 
        $const_med->poids >= 35 && $const_med->poids <= 120 && 
        $this->creatinine >= 6 && $this->creatinine <= 70) {
          $this->_clairance = $const_med->poids * (140-$age) / (7.2 * $this->creatinine);
      if ($this->_ref_consultation->_ref_patient->sexe != 'm') {
        $this->_clairance *= 0.85;
      }
      $this->_clairance = round($this->_clairance, 2);
    }
    
    // Calcul des Pertes Sanguines Acceptables
    if($this->ht && $this->ht_final && $const_med->_vst) {
      $this->_psa = $const_med->_vst * ($this->ht - $this->ht_final) / 100;
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

  function getTemplateClasses(){
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects liés à l'opération
    $tab['CConsultAnesth'] = $this->_id;
    $tab['CConsultation'] = $this->_ref_consultation->_id;
    $tab['CPatient'] = $this->_ref_consultation->_ref_patient->_id;
    $tab['COperation'] = $this->_ref_operation->_id;
    $tab['CSejour'] = $this->_ref_operation->_ref_sejour->_id;

    return $tab;
  }

  function getPerm($permType) {
    if(!$this->_ref_consultation){
      $this->loadRefConsultation();
    }
    // Droits sur l'opération
    if($this->operation_id){
      if(!$this->_ref_operation){
        $this->loadRefOperation();
      }
      $canOper = $this->_ref_operation->getPerm($permType);
    }else{
      $canOper = false;
    }
    // Droits sur le séjour
    if($this->sejour_id){
      if(!$this->_ref_sejour){
        $this->loadRefSejour();
      }
      $canSej = $this->_ref_sejour->getPerm($permType);
    }else{
      $canSej = false;
    }
    return $this->_ref_consultation->getPerm($permType) || $canOper || $canSej;
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_consultation->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    $this->_ref_sejour->fillLimitedTemplate($template);
    $this->_ref_operation->fillLimitedTemplate($template);
    
    $this->_ref_sejour->loadRefDossierMedical();
    $this->_ref_sejour->_ref_dossier_medical->fillTemplate($template, "Sejour");
  }

  function fillLimitedTemplate(&$template) {
    $template->addProperty("Anesthésie - tabac"                  , $this->tabac);
    $template->addProperty("Anesthésie - oenolisme"              , $this->oenolisme);
    $template->addProperty("Anesthésie - Groupe Sanguin"         , $this->groupe." ".$this->rhesus);
    $template->addProperty("Anesthésie - ASA"                    , $this->ASA);
    $template->addProperty("Anesthésie - Préparation pré-opératoire", $this->prepa_preop);
    
    $this->loadRefsTechniques();
    $str = '';
    foreach ($this->_ref_techniques as $tech) {
      $str .= "&bull; $tech->technique<br />";
    }
    $template->addProperty("Anesthésie - Techniques complémentaires", $str);
    $template->addProperty("Anesthésie - Etat bucco-dentaire"    , $this->etatBucco);
    $template->addProperty("Anesthésie - Examen cardiovasculaire", $this->examenCardio);
    $template->addProperty("Anesthésie - Examen pulmonaire"      , $this->examenPulmo);
    
    $img = '';
    if ($this->mallampati) {
      $img = $this->mallampati.'<br /><img src="../../../images/pictures/'.$this->mallampati.'.png" alt="'.$this->mallampati.'" />';
    }
    $template->addProperty("Anesthésie - Mallampati", $img);
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