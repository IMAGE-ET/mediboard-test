<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$name          = CValue::get("name"       );
$firstName     = CValue::get("firstName"  );
$patient_year  = CValue::get("Date_Year"  );
$patient_month = CValue::get("Date_Month" );
$patient_day   = CValue::get("Date_Day"   );
$patient_ipp   = CValue::get("patient_ipp");
$useVitale     = CValue::get("useVitale"  );
$patVitale = null;
$patient_name_search = null;
$patient_firstName_search = null;

$patients = array();
$patientsSoundex = array();
$patientsLimited = array();

$showCount = 30;

// Recherhche par IPP
if ($patient_ipp && !$useVitale && CModule::getInstalled("dPsante400")) {
  // Initialisation dans le cas d'une recherche par IPP
  $patientsCount = 0;
  $patientsSoundexCount = 0;

  $patient = new CPatient();
  $patient->_IPP = $patient_ipp;
  $patient->loadFromIPP();
  if ($patient->_id) {
    CValue::setSession("patient_id", $patient->_id);
    $patients[$patient->_id] = $patient; 
  }
}
else {
  // Recherche par traits classiques
  // Gestion du cas vitale
  if ($useVitale && CModule::getActive("fse") && CAppUI::pref('LogicielLectureVitale') == 'none') {
    $patVitale = new CPatient();  
    $cv = CFseFactory::createCV();
    if ($cv) {
      $cv->loadFromIdVitale($patVitale);
      $cv->getPropertiesFromVitale($patVitale);
      $name = $patVitale->nom;
      $firstName = $patVitale->prenom;
    }
  }

  // Recherche sur valeurs exactes et phonétique
  $where        = array();
  $whereLimited = array();
  $whereSoundex = array();
  $soundexObj   = new soundex2();
  $lenSearchConfig = false; //not enought char in string to perform the limited search

  // Limitation du nombre de caractères
  $patient_name_search    = trim($name);
  $patient_firstName_search = trim($firstName);

  //limitation de la recherche par config :
  $patient_nom_search_limited = $patient_name_search;
  $patient_prenom_search_limited = $patient_firstName_search;

  if ($limit_char_search = CAppUI::conf("dPpatients CPatient limit_char_search")) {
    //not enought characters
    if (strlen($patient_firstName_search) < $limit_char_search && strlen($patient_name_search < $limit_char_search )) {
      $lenSearchConfig = true;
    }
    $patient_nom_search_limited     = substr($patient_name_search   , 0, $limit_char_search);
    $patient_prenom_search_limited  = substr($patient_firstName_search, 0, $limit_char_search);
  }

  if ($patient_name_search) {
    $patient_nom_soundex = $soundexObj->build($patient_name_search);
    $where[] = "`nom` LIKE '$patient_name_search%' OR `nom_jeune_fille` LIKE '$patient_name_search%'";
    $whereLimited[] = "`nom` LIKE '$patient_nom_search_limited%' OR `nom_jeune_fille` LIKE '$patient_nom_search_limited%'";
    $whereSoundex[] = "`nom_soundex2` LIKE '$patient_nom_soundex%' OR `nomjf_soundex2` LIKE '$patient_nom_soundex%'";
  }

  if ($patient_firstName_search) {
    $patient_prenom_soundex = $soundexObj->build($patient_firstName_search);
    $where["prenom"]                 = "LIKE '$patient_firstName_search%'";
    $whereLimited["prenom"]          = "LIKE '$patient_prenom_search_limited%'";
    $whereSoundex["prenom_soundex2"] = "LIKE '$patient_prenom_soundex%'";
  }

  if ($patient_year || $patient_month || $patient_day) {
    $patient_naissance = 
      CValue::first($patient_year, "%") . "-" .
      CValue::first($patient_month, "%") . "-" .
      ($patient_day ? str_pad($patient_day, 2, "0", STR_PAD_LEFT) : "%");
    $where["naissance"] = $whereSoundex["naissance"] = $whereLimited["naissance"] = "LIKE '$patient_naissance'";
  }

  $limit = "0, $showCount";
  $order = "patients.nom, patients.prenom";

  $pat             = new CPatient();
  /** @var CPatient[] $patients */
  $patients        = array();
  /** @var CPatient[] $patientsSoundex */
  $patientsSoundex = array();

  /** @var CPatient[] $patientsLimited */
  $patientsLimited = array();

  if ($where) {

    // Séparation des patients par fonction
    if (CAppUI::conf('dPpatients CPatient function_distinct') && !CMediusers::get()->isAdmin()) {
      $function_id = CMediusers::get()->function_id;
      $where["function_id"] = $whereLimited["function_id"] = $whereSoundex["function_id"] = "= '$function_id'";
    }

    $patients = $pat->loadList($where, $order, $limit);
    if ($nbExact = ($showCount - count($patients))) {
      $limit = "0, $nbExact";
      $patientsSoundex = $pat->loadList($whereSoundex, $order, $limit);
      $patientsSoundex = array_diff_key($patientsSoundex, $patients);
    }

    //par recherche limitée
    if ($whereLimited && $limit_char_search && !$lenSearchConfig) {
      $patientsLimited = $pat->loadList($whereLimited, $order, $limit);
      $patientsLimited = array_diff_key($patientsLimited, $patients);
    }
  }



  /**
   * Chargement des consultations du jour pour une liste de patients donnés
   *
   * @param CPatient[] &$patients Liste des patients
   *
   * @return void
   */
  function loadConsultationsDuJour(&$patients) {
    $today = CMbDT::date();
    $where = array();
    $where["plageconsult.date"] = "= '$today'";
    foreach ($patients as &$patient) {
      $patient->loadRefsConsultations($where);
      foreach ($patient->_ref_consultations as $consult) {
        $consult->loadRefPraticien()->loadRefFunction();
      }
    }
  }

  /**
   * Chargement des admissions du jour
   *
   * @param CPatient[] &$patients Liste des patient
   *
   * @return void
   */
  function loadAdmissionsDuJour(&$patients) {
    $today = CMbDT::date();
    $where = array();
    $where["entree"] = "LIKE '$today __:__:__'";
    foreach ($patients as &$patient) {
      $patient->loadRefsSejours($where);
      foreach ($patient->_ref_sejours as $sejour) {
        $sejour->loadRefPraticien()->loadRefFunction();
      }
    }
  }

  loadConsultationsDuJour($patients);
  loadConsultationsDuJour($patientsSoundex);
  loadAdmissionsDuJour($patients);
  loadAdmissionsDuJour($patientsSoundex);
}

foreach ($patients as $_patient) {
  $_patient->loadView();
}

foreach ($patientsSoundex as $_patient) {
  $_patient->loadView();
}

foreach ($patientsLimited as $_patient) {
  $_patient->loadView();
}

$patient_id = CValue::get("patient_id");
$patient = null;
if ($patient_id) {
  $patient =  new CPatient();
  $patient->load($patient_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dPsanteInstalled"    , CModule::getInstalled("dPsante400"));
$smarty->assign("name"                , $name            );
$smarty->assign("firstName"           , $firstName       );
$smarty->assign("name_search"         , $patient_name_search);
$smarty->assign("firstName_search"    , $patient_firstName_search);
$smarty->assign("useVitale"           , $useVitale       );
$smarty->assign("patVitale"           , $patVitale       );
$smarty->assign("patients"            , $patients        );
$smarty->assign("patientsLimited"     , $patientsLimited );
$smarty->assign("patientsSoundex"     , $patientsSoundex );
$smarty->assign("patient_ipp"         , $patient_ipp     );
$smarty->assign("datePat"             , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patient"             , $patient);

$smarty->display("pat_selector.tpl");
