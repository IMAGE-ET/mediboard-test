<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$patient_id    = CValue::get("patient_id" );
$patient = new CPatient;
if($patient_id) {
  $patient->load($patient_id);
}
$patient->loadIPP();
$findyear  = null;
$findmonth = null;
$findday   = null;

if($patient->naissance) {
  //$findyear  = mbTransformTime(null, $patient->naissance, "%Y");
  //$findmonth = mbTransformTime(null, $patient->naissance, "%m");
  //$findday   = mbTransformTime(null, $patient->naissance, "%d");
}

$name          = CValue::get("name"      , $patient->nom);
$firstName     = CValue::get("firstName" , $patient->prenom);
$nomjf         = CValue::get("nomjf"     , $patient->nom_jeune_fille);
$patient_year  = CValue::get("Date_Year" , $findyear);
$patient_month = CValue::get("Date_Month", $findmonth);
$patient_day   = CValue::get("Date_Day"  , $findday);
$IPP           = CValue::get("IPP"       , $patient->_ref_IPP ? $patient->_ref_IPP->id400 : null);

$showCount = 30;

// Recherche sur valeurs exactes et phonétique
$where        = array();
$whereSoundex = array();
$soundexObj   = new soundex2();

if($IPP) {
  $where["external_id"] = "= '$IPP'";
} else {
	  
	  
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
}
  
$limit = "0, $showCount";
$order = "hprim21_patient.nom, hprim21_patient.prenom";
  
$pat             = new CHprim21Patient();
$patients        = array();
$patientsSoundex = array();
  
if(count($where)){
  $patients = $pat->loadList($where, $order, $limit);
  foreach($patients as &$curr_pat) {
    $curr_pat->loadRefs();
  }
  if($nbExact = ($showCount - count($patients)) && count($whereSoundex)) {
    $limit = "0, $nbExact";
    $patientsSoundex = $pat->loadList($whereSoundex, $order, $limit);
    $patientsSoundex = array_diff_key($patientsSoundex, $patients);
    foreach($patientsSoundex as &$curr_pat) {
      $curr_pat->loadRefs();
    }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("name"             , $name            );
$smarty->assign("firstName"        , $firstName       );
$smarty->assign("nomjf"            , $nomjf           );
$smarty->assign("patients"         , $patients        );
$smarty->assign("patientsSoundex"  , $patientsSoundex );
$smarty->assign("datePat"          , "$patient_year-$patient_month-$patient_day");
$smarty->assign("IPP"              , $IPP );

$smarty->display("sejour_hprim_selector.tpl");

?>