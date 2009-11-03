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
$nomjf         = CValue::get("nomjf"      );
$patient_year  = CValue::get("Date_Year"  );
$patient_month = CValue::get("Date_Month" );
$patient_day   = CValue::get("Date_Day"   );
$patient_ipp   = CValue::get("patient_ipp");
$useVitale     = CValue::get("useVitale"  ); 

$patVitale = null;

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
} else {

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
  
  
  if($name){
    $name = trim($name);
    $where["nom"]                    = "LIKE '$name%'";
    $whereSoundex["nom_soundex2"]    = "LIKE '".$soundexObj->build($name)."%'";
  }
  
  if($firstName){
    $firstName = trim($firstName);
    $where["prenom"]                 = "LIKE '$firstName%'";
    $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($firstName)."%'";
  }
  
  if($nomjf){
    $nomjf = trim($nomjf);
    $where["nom_jeune_fille"]        = "LIKE '$nomjf%'";
    $whereSoundex["nomjf_soundex2"]    = "LIKE '".$soundexObj->build($nomjf)."%'";  
  }
     
  if(($patient_year) || ($patient_month) || ($patient_day)){
    $year =($patient_year)?"$patient_year-":"%-";
    $month =($patient_month)?"$patient_month-":"%-";
    $day =($patient_day)?"$patient_day":"%";
    if($day!="%"){
      $day = str_pad($day,2,"0",STR_PAD_LEFT);
    }
    $naissance = $year.$month.$day;
    
    if($patient_year || $patient_month || $patient_day){
      $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$naissance'";
    }
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

$smarty->assign("dPsanteInstalled", CModule::getInstalled("dPsante400"));
$smarty->assign("name"             , $name            );
$smarty->assign("firstName"        , $firstName       );
$smarty->assign("nomjf"            , $nomjf           );
$smarty->assign("useVitale"        , $useVitale       );
$smarty->assign("patVitale"        , $patVitale       );
$smarty->assign("patients"         , $patients        );
$smarty->assign("patientsSoundex"  , $patientsSoundex );
$smarty->assign("patient_ipp"      , $patient_ipp     );
$smarty->assign("datePat"          , "$patient_year-$patient_month-$patient_day");

$smarty->display("pat_selector.tpl");

?>