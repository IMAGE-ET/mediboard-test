<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $g, $AppUI;
$can->needsRead();

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

$showCount = 30;
  
// Recherhche par IPP
if($patient_ipp && !$useVitale && CModule::getInstalled("dPsante400")){
  // Initialisation dans le cas d'une recherche par IPP
  $patients = array();
  $patientsSoundex = array();
  $patientsCount = 0;
  $patientsSoundexCount = 0;
  
  $idsante = new CIdSante400();
  $idsante->tag = str_replace('$g',$g, CAppUI::conf("dPpatients CPatient tag_ipp"));
  $idsante->id400 = $patient_ipp;
  $idsante->object_class = "CPatient";
  $idsante->loadMatchingObject();
  
  if($idsante->object_id){
   $patient = new CPatient();
   $patient->load($idsante->object_id);
   $patients[$patient->_id] = $patient; 
  }
} 

// Recherche par traits classiques
else {
  // Gestion du cas vitale
  if ($useVitale && CAppUI::pref('GestionFSE') && !CAppUI::pref('VitaleVision')) {
    $patVitale = new CPatient();  
    $patVitale->loadFromIdVitale();
    $patVitale->getValuesFromVitale();
    
    $name = $patVitale->nom;
    $firstName = $patVitale->prenom;
  }
  
  // Recherche sur valeurs exactes et phonétique
  $where        = array();
  $whereSoundex = array();
  $soundexObj   = new soundex2();
  
	// Limitation du nombre de caractères
  $patient_name_search    = trim($name);
  $patient_firstName_search = trim($firstName);
  if ($limit_char_search = CAppUI::conf("dPpatients CPatient limit_char_search")) {
    $patient_name_search    = substr($patient_name_search   , 0, $limit_char_search);
    $patient_firstName_search = substr($patient_firstName_search, 0, $limit_char_search);
  }
  
  if ($patient_name_search) {
    $patient_nom_soundex = $soundexObj->build($patient_name_search);
    $where[] = "`nom` LIKE '$patient_name_search%' OR `nom_jeune_fille` LIKE '$patient_name_search%'";
    $whereSoundex[] = "`nom_soundex2` LIKE '$patient_nom_soundex%' OR `nomjf_soundex2` LIKE '$patient_nom_soundex%'";
  }
  
  if ($patient_firstName_search) {
    $patient_prenom_soundex = $soundexObj->build($patient_firstName_search);
    $where["prenom"]                 = "LIKE '$patient_firstName_search%'";
    $whereSoundex["prenom_soundex2"] = "LIKE '$patient_prenom_soundex%'";
  }
         
  if ($patient_year || $patient_month || $patient_day) {
    $patient_naissance = 
      CValue::first($patient_year, "%") . "-" .
      CValue::first($patient_month, "%") . "-" .
      ($patient_day ? str_pad($patient_day, 2, "0", STR_PAD_LEFT) : "%");
    $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$patient_naissance'";
  }

  $limit = "0, $showCount";
  $order = "patients.nom, patients.prenom";
  
  $pat             = new CPatient();
  $patients        = array();
  $patientsSoundex = array();
	
  if($where){
    $patients = $pat->loadList($where, $order, $limit);
    if ($nbExact = ($showCount - count($patients))) {
      $limit = "0, $nbExact";
      $patientsSoundex = $pat->loadList($whereSoundex, $order, $limit);
      $patientsSoundex = array_diff_key($patientsSoundex, $patients);
    }
  }
	
  // Chargement des consultations du jour
  function loadConsultationsDuJour(&$patients) {
    $today = mbDate();
    $where = array();
    $where["plageconsult.date"] = "= '$today'";
    foreach ($patients as &$patient) {
      $patient->loadRefsConsultations($where);
      foreach ($patient->_ref_consultations as &$consult) {
        $consult->loadRefPlageConsult();
      }
    }
    
  }
  
  // Chargement des admissions du jour
  function loadAdmissionsDuJour(&$patients) {
    $today = mbDate();
    $where = array();
    $where["entree_prevue"] = "LIKE '$today __:__:__'";
    foreach ($patients as &$patient) {
      $patient->loadRefsSejours($where);
      foreach ($patient->_ref_sejours as &$sejour) {
        $sejour->loadRefPraticien();
      }
    }
  }
  
  loadConsultationsDuJour($patients);
  loadConsultationsDuJour($patientsSoundex);
  loadAdmissionsDuJour($patients);
  loadAdmissionsDuJour($patientsSoundex);
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
$smarty->assign("patientsSoundex"     , $patientsSoundex );
$smarty->assign("patient_ipp"         , $patient_ipp     );
$smarty->assign("datePat"             , "$patient_year-$patient_month-$patient_day");

$smarty->display("pat_selector.tpl");

?>