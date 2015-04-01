<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$mediuser = CMediusers::get();

$showCount = 30;

// Chargement du patient sélectionné
$patient_id = CValue::getOrSession("patient_id");
$patient = new CPatient;
if ($new = CValue::get("new")) {
  $patient->load(null);
  CValue::setSession("patient_id", null);
  CValue::setSession("selClass", null);
  CValue::setSession("selKey", null);
}
else {
  $patient->load($patient_id);
}

// Récuperation des patients recherchés
$patient_nom         = trim(CValue::getOrSession("nom"));
$patient_prenom      = trim(CValue::getOrSession("prenom"));
$patient_ville       = CValue::get("ville");
$patient_cp          = CValue::get("cp");
$patient_day         = CValue::getOrSession("Date_Day");
$patient_month       = CValue::getOrSession("Date_Month");
$patient_year        = CValue::getOrSession("Date_Year");
$patient_naissance   = null;
$patient_ipp         = CValue::get("patient_ipp");
$patient_nda         = CValue::get("patient_nda");
$useVitale           = CValue::get("useVitale", CModule::getActive("fse") && CAppUI::pref('LogicielLectureVitale') != 'none' ? 1 : 0);
$prat_id             = CValue::get("prat_id");
$patient_sexe        = CValue::get("sexe");
$useCovercard         = CValue::get("usecovercard",  CModule::getActive("fse") && CModule::getActive("covercard") ? 1 : 0);

$patient_nom_search    = null;
$patient_prenom_search = null;

$patVitale = new CPatient();

if ($patient_ipp && CModule::getInstalled("dPsante400")) {
  // Recherche par IPP
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsLimited = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;

  $patient = new CPatient;
  $patient->_IPP = $patient_ipp;
  $patient->loadFromIPP();
  if ($patient->_id) {
    CValue::setSession("patient_id", $patient->_id);
    $patients[$patient->_id] = $patient;
  }
}
else {
  // Recheche par traits classiques
  if ($useVitale && CAppUI::pref('LogicielLectureVitale') == 'none' && CModule::getActive("fse")) {
    // Champs vitale
    $cv = CFseFactory::createCV();
    if ($cv) {
      $cv->getPropertiesFromVitale($patVitale);
      $patVitale->updateFormFields();
      $patient_nom    = $patVitale->nom   ;
      $patient_prenom = $patVitale->prenom;
      CValue::setSession("nom"   , $patVitale->nom   );
      CValue::setSession("prenom", $patVitale->prenom);
      $cv->loadFromIdVitale($patVitale);
    }
  }

  $where        = array();
  $whereLimited = array();
  $whereSoundex = array();
  $ljoin        = array();
  $soundexObj   = new soundex2();
  $group_by     = null;
  $lenSearchConfig = false; //not enought char in string to perform the limited search

  $patient_nom_search    = trim($patient_nom);
  $patient_prenom_search = trim($patient_prenom);

  //limitation de la recherche par config :
  $patient_nom_search_limited = $patient_nom_search;
  $patient_prenom_search_limited = $patient_prenom_search;
  if ($limit_char_search = $patient->conf("limit_char_search")) {
    //not enought characters
    if (strlen($patient_prenom_search) < $limit_char_search && strlen($patient_nom_search < $limit_char_search )) {
      $lenSearchConfig = true;
    }
    $patient_nom_search_limited     = substr($patient_nom_search   , 0, $limit_char_search);
    $patient_prenom_search_limited  = substr($patient_prenom_search, 0, $limit_char_search);
  }

  if ($patient_nom_search) {
    $patient_nom_soundex = $soundexObj->build($patient_nom_search);
    $where[]        = "`nom` LIKE '$patient_nom_search%'
      OR `nom_jeune_fille` LIKE '$patient_nom_search%'";
    $whereLimited[] = "`nom` LIKE '$patient_nom_search_limited%'
      OR `nom_jeune_fille` LIKE '$patient_nom_search_limited%'";
    $whereSoundex[] = "`nom_soundex2` LIKE '$patient_nom_soundex%'
      OR `nomjf_soundex2` LIKE '$patient_nom_soundex%'";
  }

  if ($patient_prenom_search) {
    $patient_prenom_soundex = $soundexObj->build($patient_prenom_search);
    $where["prenom"]                 = "LIKE '$patient_prenom_search%'";
    $whereLimited["prenom"]          = "LIKE '$patient_prenom_search_limited%'";
    $whereSoundex["prenom_soundex2"] = "LIKE '$patient_prenom_soundex%'";
  }

  if ($patient_year || $patient_month || $patient_day) {
    $patient_naissance =
      CValue::first($patient_year , "%") . "-" .
      CValue::first($patient_month, "%") . "-" .
      CValue::first($patient_day  , "%");
    $where["naissance"] = $whereSoundex["naissance"] = $whereLimited["naissance"] = "LIKE '$patient_naissance'";
  }

  if ($patient_sexe) {
    $where["sexe"] = $whereSoundex["sexe"] = $whereLimited["sexe"] = "= '$patient_sexe'";
  }

  if ($patient_ville) {
    $where["ville"] = $whereSoundex["ville"] = $whereLimited["ville"] ="LIKE '$patient_ville%'";
  }

  if ($patient_cp) {
    $where["cp"]    = $whereSoundex["cp"]    = $whereLimited["cp"] = "LIKE '$patient_cp%'";
  }

  if ($prat_id) {
    $ljoin["consultation"] = "`consultation`.`patient_id` = `patients`.`patient_id`";
    $ljoin["plageconsult"] = "`plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`";
    $ljoin["sejour"]       = "`sejour`.`patient_id` = `patients`.`patient_id`";

    $where[] = $whereLimited[] = "plageconsult.chir_id = '$prat_id' OR sejour.praticien_id = '$prat_id'";
    $whereSoundex[] = "plageconsult.chir_id = '$prat_id' OR sejour.praticien_id = '$prat_id'";

    $group_by = "patient_id";
  }

  if ($patient_nda) {
    $ljoin["sejour"]      = "`sejour`.`patient_id` = `patients`.`patient_id`";
    $ljoin["id_sante400"] = "`id_sante400`.`object_id` = `sejour`.`sejour_id`";

    $where[] = $whereLimited[]  = "`id_sante400`.`object_class` = 'CSejour'";
    $where["id_sante400.id400"] = " = '$patient_nda'";
    $where["id_sante400.tag"]   = " = '".CSejour::getTagNDA()."'";
  }

  /** @var CPatient[] $patients */
  $patients        = array();
  /** @var CPatient[] $patientsSoundex */
  $patientsSoundex = array();

  /** @var CPatient[] $patientsLimited */
  $patientsLimited = array();

  $order = "nom, prenom, naissance";
  $pat = new CPatient();

  // Chargement des patients
  if ($where) {

    // Séparation des patients par fonction
    if (CAppUI::conf('dPpatients CPatient function_distinct') && !CMediusers::get()->isAdmin()) {
      $function_id = CMediusers::get()->function_id;
      $where["function_id"] = $whereLimited["function_id"] = $whereSoundex["function_id"] = "= '$function_id'";
    }

    $patients = $pat->loadList($where, $order, $showCount, $group_by, $ljoin);
  }

  // par soundex
  if ($whereSoundex) {
    $patientsSoundex = $pat->loadList($whereSoundex, $order, $showCount, $group_by, $ljoin);
    $patientsSoundex = array_diff_key($patientsSoundex, $patients);
  }

  //par recherche limitée
  if ($whereLimited && $limit_char_search && !$lenSearchConfig) {
    $patientsLimited = $pat->loadList($whereLimited, $order, $showCount, $group_by, $ljoin);
    $patientsLimited = array_diff_key($patientsLimited, $patients);
  }

  // Sélection du premier de la liste si aucun n'est déjà sélectionné
  if (!$patient->_id && count($patients) == 1) {
    $patient = reset($patients);
  }

  // Patient vitale associé trouvé : prioritaire
  if ($patVitale->_id) {
    $patient = $patVitale;
    // Au cas où il n'aurait pas été trouvé grâce aux champs
    $patients[$patient->_id] = $patient;
  }
}

//classique
CStoredObject::massCountBackRefs($patients, "notes");
foreach ($patients as $_patient) {
  $_patient->loadView();
}

//soundEx
CStoredObject::massCountBackRefs($patientsSoundex, "notes");
foreach ($patientsSoundex as $_patient) {
  $_patient->loadView();
}

//limited
CStoredObject::massCountBackRefs($patientsLimited, "notes");
foreach ($patientsLimited as $_patient) {
  $_patient->loadView();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet"));

$smarty->assign("nom"                 , $patient_nom);
$smarty->assign("prenom"              , $patient_prenom);
$smarty->assign("naissance"           , $patient_naissance);
$smarty->assign("ville"               , $patient_ville);
$smarty->assign("cp"                  , $patient_cp);
$smarty->assign("nom_search"          , $patient_nom_search);
$smarty->assign("prenom_search"       , $patient_prenom_search);
$smarty->assign("covercard"           , CValue::get("covercard", ""));
$smarty->assign("sexe"                , $patient_sexe);
$smarty->assign("prat_id"             , $prat_id);

$smarty->assign("useVitale"           , $useVitale);
$smarty->assign("useCoverCard"        , $useCovercard);
$smarty->assign("patVitale"           , $patVitale);
$smarty->assign("patients"            , $patients);
$smarty->assign("patientsLimited"     , $patientsLimited);
$smarty->assign("patientsSoundex"     , $patientsSoundex);

$smarty->assign("patient"             , $patient);
$smarty->assign("board"               , CValue::get("board", 0));
$smarty->assign("patient_ipp"         , $patient_ipp);
$smarty->assign("patient_nda"         , $patient_nda);

$smarty->display("inc_search_patients.tpl");
