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
  var $chir_id         = null;

  // DB fields
  var $date_interv    = null;
  var $libelle_interv = null;

  var $groupe         = null;
  var $rhesus         = null;

  // @todo A supprimer
  var $antecedents    = null;
  var $traitements    = null;
  var $tabac          = null;
  var $oenolisme      = null;

  var $ASA            = null;
  var $mallampati     = null;
  var $bouche         = null;
  var $distThyro      = null;
  var $etatBucco      = null;
  var $examenCardio   = null;
  var $examenPulmo    = null;
  var $conclusion     = null;
  var $position       = null;
  var $premedication  = null;
  var $prepa_preop    = null;
  var $date_analyse   = null;	
	
  var $rai            = null;
  var $hb             = null;
  var $tp             = null;
  var $tca            = null;
  var $tca_temoin     = null;
  var $creatinine     = null;
  var $na             = null;
  var $k              = null;
  var $tsivy          = null;
  var $plaquettes     = null;
  var $ecbu           = null;
  var $ht             = null;
  var $ht_final       = null;

  // Form fields
  var $_date_consult    = null;
  var $_date_op         = null;
  var $_sec_tsivy       = null;
  var $_min_tsivy       = null;
  var $_sec_tca         = null;
  var $_min_tca         = null;
  var $_intub_difficile = null;
  var $_clairance       = null;
  var $_psa             = null;

  // Object References
  var $_ref_consultation       = null;
  var $_ref_chir               = null;
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

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["techniques"] = "CTechniqueComp consultation_anesth_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();

    $specs["consultation_id"]  = "ref notNull class|CConsultation cascade seekable show|0";
    $specs["operation_id"]     = "ref class|COperation seekable";
    $specs["sejour_id"]        = "ref class|CSejour seekable";
    $specs["chir_id"]          = "ref class|CMediusers";

    $specs["date_interv"]      = "date";
    $specs["libelle_interv"]   = "str";
    $specs["groupe"]           = "enum list|?|O|A|B|AB default|? show|0";
    $specs["rhesus"]           = "enum list|?|NEG|POS default|? show|0";
    
    // @todo: Supprimer ces quatre champs
    $specs["antecedents"]      = "text confidential";
    $specs["traitements"]      = "text confidential";
    $specs["tabac"]            = "text helped";
    $specs["oenolisme"]        = "text helped";
    
    $specs["ASA"]              = "enum list|1|2|3|4|5 default|1";

    // Données examens complementaires
    $specs["rai"]              = "enum list|?|NEG|POS default|? show|0";
    $specs["hb"]               = "float min|0 show|0";
    $specs["tp"]               = "float min|0 max|100 show|0";
    $specs["tca"]              = "numchar maxLength|2 show|0";
    $specs["tca_temoin"]       = "numchar maxLength|2 show|0";
    $specs["creatinine"]       = "float show|0";
    $specs["na"]               = "float min|0 show|0";
    $specs["k"]                = "float min|0 show|0";
    $specs["tsivy"]            = "time show|0";
    $specs["plaquettes"]       = "numchar maxLength|4 pos show|0";
    $specs["ecbu"]             = "enum list|?|NEG|POS default|? show|0";
    $specs["ht"]               = "float min|0 max|100 show|0";
    $specs["ht_final"]         = "float min|0 max|100 show|0";
    $specs["premedication"]    = "text helped";
    $specs["prepa_preop"]      = "text helped";
    $specs["date_analyse"]     = "date show|0";

    // Champs pour les conditions d'intubation
    $specs["mallampati"]       = "enum list|classe1|classe2|classe3|classe4";
    $specs["bouche"]           = "enum list|m20|m35|p35";
    $specs["distThyro"]        = "enum list|m65|p65";
    $specs["etatBucco"]        = "text helped";
    $specs["examenCardio"]     = "text helped";
    $specs["examenPulmo"]      = "text helped";
    $specs["conclusion"]       = "text helped seekable";
    $specs["position"]         = "enum list|DD|DV|DL|GP|AS|TO|GYN";

    // Champs dérivés
    $specs["_intub_difficile"] = "";
    $specs["_clairance"]       = "";
    $specs["_psa"]             = "";

    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    // Vérification si intubation difficile
    $this->_intub_difficile = 
       $this->mallampati === "classe3" || $this->mallampati === "classe4" || 
       $this->bouche === "m20" || $this->bouche === "m35" || 
       $this->distThyro === "m65";
     
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
    $this->_ref_consultation = $this->loadFwdRef("consultation_id", false);
    $this->_view = $this->_ref_consultation->_view;
  }
  
  function loadRefChir() {
    $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }

  function loadRefOperation() {
    $this->_ref_operation = $this->loadFwdRef("operation_id", false);
    
    // Chargement du séjour associé
    if ($this->_ref_operation->_id) {
      $this->_ref_operation->loadRefSejour();
      $this->_ref_operation->loadRefPlageOp();
      $this->_ref_sejour =& $this->_ref_operation->_ref_sejour;
    } 
    else {
      $this->loadRefSejour();
    }
  }

  function loadRefSejour() {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
    $this->_ref_sejour->loadRefsFwd(true);
  }

  function loadRefsFiles(){
    if(!$this->_ref_consultation){
      $this->loadRefConsultation();
    }
    $this->_ref_consultation->loadRefsFiles();
    $this->_ref_files =& $this->_ref_consultation->_ref_files;
  }

  function loadView() {
  	parent::loadView();
    $this->_ref_consultation = $this->_fwd["consultation_id"];
    $this->_ref_consultation->loadView();
  }
   
  function loadComplete(){
    parent::loadComplete();
    
    $this->_ref_consultation->loadExamsComp();
    $this->_ref_consultation->loadRefsExamNyha();
    $this->_ref_consultation->loadRefsExamPossum();
    $this->_ref_consultation->loadRefsExamIgs();
    
    $this->loadRefOperation();    
    
    $this->_ref_sejour->loadRefDossierMedical();
    $this->_ref_sejour->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_sejour->_ref_dossier_medical->loadRefstraitements();
    
		// Chargement des actes CCAM 
		$this->_ref_consultation->loadRefsActesCCAM();
    foreach ($this->_ref_consultation->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }

  function loadRefsFwd() {
    $this->loadRefChir();
    
		// Chargement operation/sejour
		$this->loadRefOperation();
    $this->_ref_operation->loadRefsFwd();
    $this->_date_op =& $this->_ref_operation->_datetime;
		
		// Chargement consultation
    $this->loadRefConsultation();
    $this->_ref_consultation->loadRefsFwd();
    $this->_ref_plageconsult =& $this->_ref_consultation->_ref_plageconsult;
    $this->_date_consult =& $this->_ref_consultation->_date;

    // Calcul de la Clairance créatinine
		$patient =& $this->_ref_consultation->_ref_patient;
    $patient->loadRefConstantesMedicales();
    $const_med = $patient->_ref_constantes_medicales;
    $const_med->updateFormFields();
    $age = intval($patient->_age);
    if ($const_med->poids && $this->creatinine && 
        $age && $age >= 18 && $age <= 110 && 
        $const_med->poids >= 35 && $const_med->poids <= 120 && 
        $this->creatinine >= 6 && $this->creatinine <= 70) {
          $this->_clairance = $const_med->poids * (140-$age) / (7.2 * $this->creatinine);
      if ($patient->sexe != 'm') {
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
    $techniques = new CTechniqueComp;
    $where = array(
      "consultation_anesth_id" => "= '$this->consultation_anesth_id'"
    );
    return $this->_ref_techniques = $techniques->loadList($where, "technique");
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
    return $canOper || $canSej || $this->_ref_consultation->getPerm($permType);
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
    $template->addProperty("Anesthésie - Tabac"                  , $this->tabac);
    $template->addProperty("Anesthésie - Oenolisme"              , $this->oenolisme);
    
    $this->updateFormFields();
    $template->addProperty("Anesthésie - Groupe sanguin"         , "$this->groupe $this->rhesus");
    $template->addProperty("Anesthésie - RAI"                    , $this->rai);
    $template->addProperty("Anesthésie - Hb"                     , "$this->hb g/dl");
    $template->addProperty("Anesthésie - Ht"                     , "$this->ht %");
    $template->addProperty("Anesthésie - Ht final"               , "$this->ht_final %");
    $template->addProperty("Anesthésie - PSA"                    , "$this->_psa ml/GR");
    $template->addProperty("Anesthésie - Plaquettes"             , ($this->plaquettes*1000)."/mm3");
    $template->addProperty("Anesthésie - Créatinine"             , "$this->creatinine ml/l");
    $template->addProperty("Anesthésie - Clairance"              , "$this->_clairance ml/min");
    $template->addProperty("Anesthésie - Na+"                    , "$this->na mmol/l");
    $template->addProperty("Anesthésie - K+"                     , "$this->k mmol/l");
    $template->addProperty("Anesthésie - TP"                     , "$this->tp %");
    $template->addProperty("Anesthésie - TCA"                    , "$this->tca_temoin s / $this->tca s");
    $template->addProperty("Anesthésie - TS Ivy"                 , "$this->_min_tsivy min $this->_sec_tsivy s");
    $template->addProperty("Anesthésie - ECBU"                   , $this->ecbu);
    
    $template->addProperty("Anesthésie - ASA"                    , $this->ASA);
    $template->addProperty("Anesthésie - Préparation pré-opératoire", $this->prepa_preop);
    $template->addProperty("Anesthésie - Prémédication", $this->getFormattedValue('premedication'));
    $template->addProperty("Anesthésie - Position", $this->getFormattedValue('position'));

    $list = CMbArray::pluck($this->loadRefsTechniques(), 'technique');
    $template->addListProperty("Anesthésie - Techniques complémentaires", $list);
    
    $template->addProperty("Anesthésie - Examen cardiovasculaire", $this->examenCardio);
    $template->addProperty("Anesthésie - Examen pulmonaire"      , $this->examenPulmo);
    
    $template->addProperty("Anesthésie - Ouverture de la bouche",     $this->getFormattedValue('bouche'));
    $template->addProperty("Anesthésie - Distance thyro-mentonnière", $this->getFormattedValue('distThyro'));
    $template->addProperty("Anesthésie - Etat bucco-dentaire" ,       $this->etatBucco);
    $img = '';
    if ($this->mallampati) {
      $img = $this->mallampati.'<br /><img src="../../../images/pictures/'.$this->mallampati.'.png" alt="'.$this->mallampati.'" />';
    }
    $template->addProperty("Anesthésie - Mallampati", $img);
    $template->addProperty("Anesthésie - Remarques",  $this->conclusion);
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
