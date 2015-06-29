<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Le dossier d'anesthésie est une liaison entre une intervention et une consultation pré-anesthésique
 * Le dossier contient toutes les informations nécessaires à l'impression de fiches d'anesthésie pour le bloc
 *
 * @todo Renommer en CDossierAnesthesie
 */
class CConsultAnesth extends CMbObject implements IPatientRelated, IIndexableObject {
  // DB Table key
  public $consultation_anesth_id = null;

  // DB References
  public $consultation_id;
  public $operation_id;
  public $sejour_id;
  public $chir_id;

  // DB fields
  public $date_interv;
  public $libelle_interv;

  // @todo A supprimer
  public $antecedents;
  public $traitements;
  public $tabac;
  public $oenolisme;

  // Intubation
  public $mallampati;
  public $bouche;
  public $distThyro;
  public $etatBucco;
  public $intub_difficile;
  public $mob_cervicale;

  // Criteres de ventilation
  public $plus_de_55_ans;
  public $imc_sup_26;
  public $edentation;
  public $ronflements;
  public $barbe;
  public $piercing;

  // Examen clinique
  public $examenCardio;
  public $examenPulmo;
  public $examenDigest;
  public $examenAutre;

  public $conclusion;
  public $premedication;
  public $prepa_preop;
  public $date_analyse;

  public $rai;
  public $hb;
  public $tp;
  public $tca;
  public $tca_temoin;
  public $creatinine;
  public $fibrinogene;
  public $na;
  public $k;
  public $tsivy;
  public $plaquettes;
  public $ecbu;
  public $ht;
  public $ht_final;
  public $result_ecg;
  public $result_rp;
  public $result_autre;
  public $result_com;

  // Check sur les codes cim10 de préfixe pour non-fumeur:
  //  F17 - T652 - Z720 - Z864 - Z587
  public $apfel_femme;
  public $apfel_non_fumeur;
  public $apfel_atcd_nvp;
  public $apfel_morphine;

  // Champs concernant l'intervention
  public $passage_uscpo;
  public $duree_uscpo;
  public $type_anesth;
  public $position;
  public $ASA;
  public $rques;
  public $strategie_antibio;

  // Form fields
  public $_date_consult;
  public $_date_op;
  public $_sec_tsivy;
  public $_min_tsivy;
  public $_sec_tca;
  public $_min_tca;
  public $_intub_difficile;
  public $_clairance;
  public $_psa;
  public $_score_apfel;
  public $_docitems_from_consult;
  
  // Object References
  /** @var  CConsultation */
  public $_ref_consultation;
  /** @var  CMediusers */
  public $_ref_chir;
  /** @var  CTechniqueComp[] */
  public $_ref_techniques;
  /** @var  CConsultAnesth */
  public $_ref_last_consultanesth;
  /** @var  COperation */
  public $_ref_operation;
  /** @var  CSejour */
  public $_ref_sejour;
  /** @var  CPlageconsult */
  public $_ref_plageconsult;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'consultation_anesth';
    $spec->key   = 'consultation_anesth_id';
    $spec->events = array(
      "examen" => array(
        "reference1" => array("COperation", "operation_id"),
        "reference2" => array("CSejour",    "sejour_id"),
      ),
    );
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["techniques"] = "CTechniqueComp consultation_anesth_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["consultation_id"]  = "ref notNull class|CConsultation cascade seekable show|0";
    $props["operation_id"]     = "ref class|COperation seekable";
    $props["sejour_id"]        = "ref class|CSejour seekable";
    $props["chir_id"]          = "ref class|CMediusers";

    $props["date_interv"]      = "date";
    $props["libelle_interv"]   = "str";
    
    // @todo: Supprimer ces quatre champs
    $props["antecedents"]      = "text confidential";
    $props["traitements"]      = "text confidential";
    $props["tabac"]            = "text helped";
    $props["oenolisme"]        = "text helped";

    // Données examens complementaires
    $props["rai"]              = "enum list|?|NEG|POS default|? show|0";
    $props["hb"]               = "float min|0 show|0";
    $props["tp"]               = "float min|0 max|140 show|0";
    $props["tca"]              = "numchar maxLength|2 show|0";
    $props["tca_temoin"]       = "numchar maxLength|2 show|0";
    $props["creatinine"]       = "float show|0";
    $props["fibrinogene"]      = "float show|0";
    $props["na"]               = "float min|0 show|0";
    $props["k"]                = "float min|0 show|0";
    $props["tsivy"]            = "time show|0";
    $props["plaquettes"]       = "numchar maxLength|4 pos show|0";
    $props["ecbu"]             = "enum list|?|NEG|POS default|? show|0";
    $props["ht"]               = "float min|0 max|140 show|0";
    $props["ht_final"]         = "float min|0 max|140 show|0";
    $props["result_ecg"]       = "text helped";
    $props["result_rp"]        = "text helped";
    $props["result_autre"]     = "text helped";
    $props["result_com"]       = "text helped";
    $props["premedication"]    = "text helped";
    $props["prepa_preop"]      = "text helped";
    $props["date_analyse"]     = "date show|0";
    $props["apfel_femme"]      = "bool show|0";
    $props["apfel_non_fumeur"] = "bool show|0";
    $props["apfel_atcd_nvp"]   = "bool show|0";
    $props["apfel_morphine"]   = "bool show|0";
    
    // Champs pour les conditions d'intubation
    $props["mallampati"]       = "enum list|classe1|classe2|classe3|classe4";
    $props["bouche"]           = "enum list|m20|m35|p35";
    $props["distThyro"]        = "enum list|m65|p65";
    $props["etatBucco"]        = "text helped";
    $props["intub_difficile"]  = "bool";
    $props["mob_cervicale"]    = "enum list|m80|80m100|p100";
    $props["plus_de_55_ans"]   = "bool";
    $props["imc_sup_26"]       = "bool";
    $props["edentation"]       = "bool";
    $props["ronflements"]      = "bool";
    $props["barbe"]            = "bool";
    $props["piercing"]         = "bool";
    $props["examenCardio"]     = "text helped";
    $props["examenPulmo"]      = "text helped";
    $props["examenDigest"]     = "text helped";
    $props["examenAutre"]      = "text helped";
    
    $props["conclusion"]       = "text helped seekable";

    // Champs concernant l'intervention
    if (CAppUI::conf("dPplanningOp COperation show_duree_uscpo") == 2) {
      $props["passage_uscpo"]  = "bool notNull";
    }
    else {
      $props["passage_uscpo"]  = "bool";
    }
    $props["duree_uscpo"]      = "num min|0 max|10 default|0";
    $props['type_anesth']      = 'ref class|CTypeAnesth';
    $props['position']         = 'enum list|DD|DV|DL|GP|AS|TO|GYN';
    $props['ASA']              = 'enum list|1|2|3|4|5|6';
    $props['rques']            = 'text helped';
    $props['strategie_antibio']= 'text helped';

    // Champs dérivés
    $props["_intub_difficile"] = "";
    $props["_clairance"]       = "";
    $props["_psa"]             = "";
    $props["_score_apfel"]     = "";
    
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    // Vérification si intubation difficile
    $this->_intub_difficile =
       $this->intub_difficile == '1' || 
       (($this->mallampati === "classe3" || $this->mallampati === "classe4" || 
       $this->bouche === "m20" || $this->bouche === "m35" || 
       $this->distThyro === "m65" || $this->mob_cervicale === "m80" || $this->mob_cervicale === "80m100")
         && $this->intub_difficile != '0');
     
    $this->_sec_tsivy = intval(substr($this->tsivy, 6, 2));
    $this->_min_tsivy = intval(substr($this->tsivy, 3, 2));
    
    $this->_score_apfel = $this->apfel_femme + $this->apfel_non_fumeur + $this->apfel_atcd_nvp + $this->apfel_morphine;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    if ($this->_min_tsivy !== null && $this->_sec_tsivy !== null) {
      $this->tsivy  = '00:'.($this->_min_tsivy ? sprintf("%02d", $this->_min_tsivy):'00').':';
      $this->tsivy .=       ($this->_sec_tsivy ? sprintf("%02d", $this->_sec_tsivy):'00');
    }

    parent::updatePlainFields();
  }
  
  /**
   * @see parent::loadRelPatient()
   */
  function loadRelPatient() {
    return $this->loadRefConsultation()->loadRefPatient();
  }

  /**
   * Charge le patient associé
   *
   * @return CPatient
   */
  function loadRefPatient(){
    return $this->loadRelPatient();
  }

  /**
   * Charge la consultation associée
   *
   * @return CConsultation
   */
  function loadRefConsultation() {
    $consultation = $this->loadFwdRef("consultation_id", true);
    $this->_view = $consultation->_view;
    return $this->_ref_consultation = $consultation;
  }

  /**
   * Charge la chirurgien associé
   *
   * @return CMediusers
   */
  function loadRefChir() {
    return $this->_ref_chir = $this->loadFwdRef("chir_id", true);
  }

  /**
   * Charge l'opération associée
   * Value également le séjour associé
   *
   * @return COperation
   */
  function loadRefOperation() {
    /** @var COperation $operation */
    $operation = $this->loadFwdRef("operation_id", true);
    
    // Chargement du séjour associé
    if ($operation->_id) {
      $operation->loadRefPlageOp();
      $this->_ref_sejour = $operation->loadRefSejour();
    }
    else {
      $this->loadRefSejour();
    }
    
    return $this->_ref_operation = $operation;
  }

  /**
   * Charge le séjour associé
   *
   * @return CSejour
   */
  function loadRefSejour() {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
    $this->_ref_sejour->loadRefsFwd(true);
    return $this->_ref_sejour;
  }

  /**
   * @see parent::loadRefsFiles()
   */
  function loadRefsFiles() {
    parent::loadRefsFiles();

    if (!$this->_docitems_from_consult) {
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $this->_ref_consultation->loadRefsFiles();
      $this->_nb_cancelled_files += $this->_ref_consultation->_nb_cancelled_files;
      $this->_nb_cancelled_docs += $this->_ref_consultation->_nb_cancelled_docs;
      $this->_ref_files = $this->_ref_files + $this->_ref_consultation->_ref_files;
    }
  }

  /**
   * @see parent::loadRefsDocs()
   */
  function loadRefsDocs(){
    parent::loadRefsDocs();

    if (!$this->_docitems_from_consult) {
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $this->_ref_consultation->loadRefsDocs();
      $this->_ref_documents = $this->_ref_documents + $this->_ref_consultation->_ref_documents;
    }
    return count($this->_ref_documents);
  }

  /**
   * @see parent::countDocs()
   */
  function countDocs(){
    $nbDocs = parent::countDocs();

    if (!$this->_docitems_from_consult) {
      // Ajout des documents des dossiers d'anesthésie
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $nbDocs += $this->_ref_consultation->countDocs();
    }

    return $this->_nb_docs = $nbDocs;
  }

  /**
   * @see parent::countFiles()
   */
  function countFiles(){
    $nbFiles = parent::countFiles();

    if (!$this->_docitems_from_consult) {
      // Ajout des fichiers des dossiers d'anesthésie
      if (!$this->_ref_consultation) {
        $this->loadRefConsultation();
      }
      $this->_ref_consultation->_docitems_from_dossier_anesth = true;
      $nbFiles += $this->_ref_consultation->countFiles();
    }

    return $this->_nb_files = $nbFiles;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->_ref_consultation = $this->_fwd["consultation_id"];
    $this->_ref_consultation->loadView();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete(){
    parent::loadComplete();
    
    $this->_ref_consultation->loadRefsExamsComp();
    $this->_ref_consultation->loadRefsExamNyha();
    $this->_ref_consultation->loadRefsExamPossum();
    
    $this->loadRefOperation();    
    
    $dossier_medical = $this->_ref_sejour->loadRefDossierMedical();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->loadRefstraitements();

    $patient = $this->loadRefPatient();
    $dossier_medical = $patient->loadRefDossierMedical();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->loadRefstraitements();

    
    // Chargement des actes CCAM
    foreach ($this->_ref_consultation->loadRefsActesCCAM() as $_acte) {
      $_acte->loadRefsFwd();
    }
  }

  /**
   * @deprecated
   * @see parent::loadRefsFwd()
   */
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
    $patient->loadRefLatestConstantes(null, null, $this->_ref_consultation, false);
    $const_med = $patient->_ref_constantes_medicales;
    $const_med->updateFormFields();
    $age = intval($patient->_annees);
    if (
        $const_med->poids && $this->creatinine &&
        $age && $age >= 18 && $age <= 110 &&
        $const_med->poids >= 35 && $const_med->poids <= 120 &&
        $this->creatinine >= 6 && $this->creatinine <= 70
    ) {
          $this->_clairance = $const_med->poids * (140-$age) / (7.2 * $this->creatinine);
      if ($patient->sexe == 'm') {
        $this->_clairance *= 1.04;
      }
      else {
        $this->_clairance *= 0.85;
      }
      $this->_clairance = round($this->_clairance, 2);
    }
    
    // Calcul des Pertes Sanguines Acceptables
    if ($this->ht && $this->ht_final && $const_med->_vst) {
      $this->_psa = $const_med->_vst * ($this->ht - $this->ht_final) / 100;
    }
  }

  /**
   * Chargement des techniques complémentaires
   *
   * @return CTechniqueComp[]
   */
  function loadRefsTechniques() {
    $techniques = new CTechniqueComp();
    $where = array(
      "consultation_anesth_id" => "= '$this->consultation_anesth_id'"
    );
    return $this->_ref_techniques = $techniques->loadList($where, "technique");
  }

  /**
   * @deprecated
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsTechniques();
  }

  /**
   * @see parent::getTemplateClasses
   */
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

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_consultation) {
      $this->loadRefConsultation();
    }
    
    switch ($permType) {
      case PERM_EDIT :
        return $this->_ref_consultation->getPerm($permType);
      default :
        // Droits sur l'opération
        if ($this->operation_id) {
          if (!$this->_ref_operation) {
            $this->loadRefOperation();
          }
          $canOper = $this->_ref_operation->getPerm($permType);
        }
        else {
          $canOper = false;
        }
        // Droits sur le séjour
        if ($this->sejour_id) {
          if (!$this->_ref_sejour) {
            $this->loadRefSejour();
          }
          $canSej = $this->_ref_sejour->getPerm($permType);
        }
        else {
          $canSej = false;
        }
        return $canOper || $canSej || $this->_ref_consultation->getPerm($permType);	
    }
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_consultation->fillTemplate($template);
    $this->fillLimitedTemplate($template);
    $this->_ref_operation->fillLimitedTemplate($template);
    $this->_ref_sejour->fillLimitedTemplate($template);

    // Dossier médical
    $this->_ref_sejour->loadRefDossierMedical()->fillTemplate($template, "Sejour");

    if (CModule::getActive("dPprescription")) {
      $sejour = $this->_ref_sejour;
      $sejour->loadRefsPrescriptions();
      $prescription = isset($sejour->_ref_prescriptions["pre_admission"]) ?
        $sejour->_ref_prescriptions["pre_admission"] :
        new CPrescription();
      $prescription->type = "pre_admission";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($sejour->_ref_prescriptions["sejour"]) ?
        $sejour->_ref_prescriptions["sejour"] :
        new CPrescription();
      $prescription->type = "sejour";
      $prescription->fillLimitedTemplate($template);
      $prescription = isset($sejour->_ref_prescriptions["sortie"]) ?
        $sejour->_ref_prescriptions["sortie"] :
        new CPrescription();
      $prescription->type = "sortie";
      $prescription->fillLimitedTemplate($template);
    }
  }

  /**
   * @see parent::fillLimitedTemplate
   */
  function fillLimitedTemplate(&$template) {
    global $rootName;
    $this->updateFormFields();

    $this->notify("BeforeFillLimitedTemplate", $template);
    
    $template->addProperty("Anesthésie - Tabac"                  , $this->tabac);
    $template->addProperty("Anesthésie - Oenolisme"              , $this->oenolisme);

    $dossier_medical = $this->loadRefPatient()->loadRefDossierMedical();
    $template->addProperty("Anesthésie - Groupe sanguin"         , $dossier_medical->groupe_sanguin." ".$dossier_medical->rhesus);
    $template->addProperty("Anesthésie - RAI"                    , $this->rai);
    $template->addProperty("Anesthésie - Hb"                     , "$this->hb g/dl");
    $template->addProperty("Anesthésie - Ht"                     , "$this->ht %");
    $template->addProperty("Anesthésie - Ht final"               , "$this->ht_final %");
    $template->addProperty("Anesthésie - PSA"                    , "$this->_psa ml/GR");
    $template->addProperty("Anesthésie - Plaquettes"             , ($this->plaquettes*1000)."/mm3");
    $template->addProperty("Anesthésie - Créatinine"             , "$this->creatinine mg/l");
    $template->addProperty("Anesthésie - Clairance"              , "$this->_clairance ml/min");
    $template->addProperty("Anesthésie - Na+"                    , "$this->na mmol/l");
    $template->addProperty("Anesthésie - K+"                     , "$this->k mmol/l");
    $template->addProperty("Anesthésie - TP"                     , "$this->tp %");
    $template->addProperty("Anesthésie - TCA"                    , "$this->tca_temoin s / $this->tca s");
    $template->addProperty("Anesthésie - TS Ivy"                 , "$this->_min_tsivy min $this->_sec_tsivy s");
    $template->addProperty("Anesthésie - ECBU"                   , $this->ecbu);
    $template->addProperty("Anesthésie - Commentaire"            , $this->result_com);
    
    $template->addProperty("Anesthésie - ASA"                    , $this->_ref_operation->ASA ? $this->_ref_operation->getFormattedValue("ASA") : $this->getFormattedValue("ASA"));
    $template->addProperty("Anesthésie - Préparation pré-opératoire", $this->prepa_preop);
    $template->addProperty("Anesthésie - Prémédication", $this->premedication);
    $template->addProperty("Anesthésie - Position"     , $this->_ref_operation->getFormattedValue('position'));

    $list = CMbArray::pluck($this->loadRefsTechniques(), 'technique');
    $template->addListProperty("Anesthésie - Techniques complémentaires", $list);
    
    $template->addProperty("Anesthésie - Résultats ECG"             , $this->result_ecg);
    $template->addProperty("Anesthésie - Résultats Radio pulmonaire", $this->result_rp);
    $template->addProperty("Anesthésie - Examen cardiovasculaire"   , $this->examenCardio);
    $template->addProperty("Anesthésie - Examen pulmonaire"         , $this->examenPulmo);
    $template->addProperty("Anesthésie - Examen digestif"           , $this->examenDigest);
    $template->addProperty("Anesthésie - Examen autre"              , $this->examenAutre);

    $template->addProperty("Anesthésie - Type d'anesthésie"         , $this->_ref_operation->type_anesth ? $this->_ref_operation->getFormattedValue("type_anesth") : $this->getFormattedValue("type_anesth"));
    $template->addProperty("Anesthésie - Ouverture de la bouche"    , $this->getFormattedValue('bouche'), null, false);
    $template->addProperty("Anesthésie - Intubation"                , CAppUI::tr("CConsultAnesth-_intub_" . ($this->_intub_difficile ? "difficile" : "pas_difficile")));

    $ventilation = $this->plus_de_55_ans ? "Plus de 55 ans, ": "";
    $ventilation.= $this->imc_sup_26 ? "IMC > 26Kg/m², ":"";
    $ventilation.= $this->edentation ? "Edentation, ": "";
    $ventilation.= $this->ronflements ? "Ronflements, ": "" ;
    $ventilation.= $this->barbe ? "Barbe": "" ;
    $ventilation.= $this->piercing ? "Piercing": "" ;
    $template->addProperty("Anesthésie - Critères de ventilation"   , $ventilation ? $ventilation : "Aucun", null, false);

    $template->addProperty("Anesthésie - Distance thyro-mentonnière", $this->getFormattedValue('distThyro'), null, false);
    $template->addProperty("Anesthésie - Mobilité cervicale"        , $this->getFormattedValue('mob_cervicale'), null, false);
    $template->addProperty("Anesthésie - Etat bucco-dentaire"       , $this->etatBucco);
    $img = "";
    if ($this->mallampati) {
      $img = $this->mallampati.'<br /> .
        <img src="/'.$rootName.'/images/pictures/'.$this->mallampati.'.png" alt="'.$this->mallampati.'" />';
    }
    $template->addProperty("Anesthésie - Mallampati", $img, null, false);
    $template->addProperty("Anesthésie - Mallampati (texte seul)", $this->getFormattedValue("mallampati"));
    $template->addProperty("Anesthésie - Remarques",  $this->conclusion);
    $template->addProperty("Anesthésie - Score APFEL", $this->_score_apfel);
    $template->addProperty("Anesthésie - Stratégie antibioprophylactique ", $this->strategie_antibio);

    // Constantes médicales dans le contexte de la consultation
    $this->loadRefConsultation();
    $patient = $this->loadRefPatient();
    $patient->loadRefLatestConstantes(null, null, $this->_ref_consultation, false);
    $const_dates = $patient->_latest_constantes_dates;
    $const_med = $patient->_ref_constantes_medicales;
    $const_med->updateFormFields();

    $grid_complet = CConstantesMedicales::buildGridLatest($const_med, $const_dates, true);
    $grid_minimal = CConstantesMedicales::buildGridLatest($const_med, $const_dates, false);
    $grid_valued  = CConstantesMedicales::buildGridLatest($const_med, $const_dates, false, true);

    $smarty = new CSmartyDP("modules/dPpatients");

    // Horizontal
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_horiz = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_complet_horiz = preg_replace('`([\\n\\r])`', '', $constantes_complet_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_horiz = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_minimal_horiz = preg_replace('`([\\n\\r])`', '', $constantes_minimal_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_horiz  = $smarty->fetch("print_constantes.tpl", '', '', 0);
    $constantes_valued_horiz  = preg_replace('`([\\n\\r])`', '', $constantes_valued_horiz);

    // Vertical
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_vert  = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_complet_vert  = preg_replace('`([\\n\\r])`', '', $constantes_complet_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_vert  = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_minimal_vert  = preg_replace('`([\\n\\r])`', '', $constantes_minimal_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_vert   = $smarty->fetch("print_constantes_vert.tpl", '', '', 0);
    $constantes_valued_vert   = preg_replace('`([\\n\\r])`', '', $constantes_valued_vert);

    $template->addProperty("Anesthésie - Constantes - mode complet horizontal", $constantes_complet_horiz, '', false);
    $template->addProperty("Anesthésie - Constantes - mode minimal horizontal", $constantes_minimal_horiz, '', false);
    $template->addProperty("Anesthésie - Constantes - mode avec valeurs horizontal", $constantes_valued_horiz, '', false);
    $template->addProperty("Anesthésie - Constantes - mode complet vertical"  , $constantes_complet_vert, '', false);
    $template->addProperty("Anesthésie - Constantes - mode minimal vertical"  , $constantes_minimal_vert, '', false);
    $template->addProperty("Anesthésie - Constantes - mode avec valeurs vertical"  , $constantes_valued_vert, '', false);

    if (CModule::getActive("forms")) {
      CExObject::addFormsToTemplate($template, $this, "Anesthésie");
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  /**
   * @see parent::canDeleteEx()
   */
  function canDeleteEx() {
    // Date dépassée
    $this->completeField("consultation_id");

    $consult = $this->loadRefConsultation();
    $consult->loadRefPlageConsult();

    if ($consult->_ref_plageconsult->date < CMbDT::date() && !$this->_ref_module->canDo()->edit) {
      return "Impossible de supprimer un dossier d'anesthésie d'une consultation passée";
    }

    return parent::canDeleteEx();
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("operation_id");

    if ($this->operation_id && $this->fieldModified("operation_id")) {
      $op = new COperation();
      $op->load($this->operation_id);
      $this->sejour_id = $op->sejour_id;
    }

    return parent::store();
  }

  /**
   * Get the patient_id of CMbobject
   *
   * @return CPatient
   */
  function getIndexablePatient () {
    return $this->loadRelPatient();
  }
  /**
   * Loads the related fields for indexing datum (patient_id et date)
   *
   * @return array
   */
  function getIndexableData () {
    $consult = $this->loadRefConsultation();
    $prat    = $this->getIndexablePraticien();
    $patient = $this->getIndexablePatient();
    $array["id"]          = $this->_id;
    $array["author_id"]   = $prat->_id;
    $array["prat_id"]     = $prat->_id;
    $array["title"]       = $consult->type;
    $array["body"]        = $this->getIndexableBody("");
    $array["date"]        = str_replace("-", "/", $consult->loadRefPlageConsult()->date);
    $array["function_id"] = $prat->function_id;
    $array["group_id"]    = $prat->loadRefFunction()->group_id;
    $array["patient_id"]  = $patient->_id;
    $sejour_id = $this->loadRefSejour()->_id;
    if ($sejour_id) {
      $array["object_ref_id"]  = $sejour_id;
      $array["object_ref_class"]  = $this->loadRefSejour()->_class;
    }
    else {
      $array["object_ref_id"]  = $this->_id;
      $array["object_ref_class"]  = $this->_class;
    }
    return $array;
  }

  /**
   * Redesign the content of the body you will index
   *
   * @param string $content The content you want to redesign
   *
   * @return string
   */
  function getIndexableBody ($content) {
    $this->loadRefConsultation();
    $fields = $this->_ref_consultation->getTextcontent();
    $fields_anesth = array();

    foreach ($this->_specs as $_name => $_spec) {
      if ($_spec instanceof CTextSpec) {
        $fields_anesth[] = $_name;
      }
    }

    foreach ($fields_anesth as $_field_anesth) {
      $content .= " ".  $this->$_field_anesth;
    }
    foreach ($fields as $_field) {
      $content .= " ".  $this->_ref_consultation->$_field;
    }

    return $content;
  }

  /**
   * Get the praticien_id of CMbobject
   *
   * @return CMediusers
   */
  function getIndexablePraticien () {
    $consult = $this->loadRefConsultation();
    $prat = $consult->loadRefPraticien();
    return $prat;
  }
}
