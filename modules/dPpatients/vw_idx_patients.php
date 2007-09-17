<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$showCount = 50;

// L'utilisateur est-il un chirurgien
$chir = $mediuser->isFromType(array("Chirurgien")) ? $mediuser : null;

// L'utilisateur est-il un anesthésiste
$anesth = $mediuser->isFromType(array("Anesthésiste")) ? $mediuser : null;

// Chargement du patient sélectionné
$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
if ($new = mbGetValueFromGet("new")) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $patient->load($patient_id);
}

// Récuperation des patients recherchés
$patient_nom         = mbGetValueFromGetOrSession("nom"         , ""       );
$patient_prenom      = mbGetValueFromGetOrSession("prenom"      , ""       );
$patient_jeuneFille  = mbGetValueFromGetOrSession("jeuneFille"  , ""       );
$soundex             = mbGetValueFromGetOrSession("soundex"     , "off"    );
$patient_ville       = mbGetValueFromGetOrSession("ville"       , ""       );
$patient_cp          = mbGetValueFromGetOrSession("cp"          , ""       );
$patient_day         = mbGetValueFromGet("Date_Day"    , "");
$patient_month       = mbGetValueFromGet("Date_Month"  , "");
$patient_year        = mbGetValueFromGet("Date_Year"   , "");
$patient_naissance   = null;

$useVitale = mbGetValueFromGet("useVitale", 0);
if($useVitale) {
  $vitaleValue = mbGetValueFromGet("vitale", array());
  $patient_nom = mbGetValue($vitaleValue["nom"], $patient_nom);
  mbSetValueToSession("nom", $patient_nom);
  $patient_prenom = mbGetValue($vitaleValue["prenom"], $patient_prenom);
  mbSetValueToSession("prenom", $patient_prenom);
  $patient_day = mbGetValue(mbTranformTime(null, $vitaleValue["naissance"], "%d"), $patient_day);
  mbSetValueToSession("Date_Day", $patient_day);
  $patient_month = mbGetValue(mbTranformTime(null, $vitaleValue["naissance"], "%m"), $patient_month);
  mbSetValueToSession("Date_Month", $patient_day);
  $patient_year = mbGetValue(mbTranformTime(null, $vitaleValue["naissance"], "%Y"), $patient_year);
  mbSetValueToSession("Date_Year", $patient_day);
  $patient_naissance = "on";
  mbSetValueToSession("naissance", "on");
}

$where        = array();
$whereSoundex = array();
$soundexObj   = new soundex2();

if ($patient_nom) {
  $where["nom"]                 = "LIKE '$patient_nom%'";
  $whereSoundex["nom_soundex2"] = "LIKE '".$soundexObj->build($patient_nom)."%'";
}
if ($patient_prenom) {
  $where["prenom"]                 = "LIKE '$patient_prenom%'";
  $whereSoundex["prenom_soundex2"] = "LIKE '".$soundexObj->build($patient_prenom)."%'";
}
if ($patient_jeuneFille) {
  $where["nom_jeune_fille"]        = "LIKE '$patient_jeuneFille%'";
  $whereSoundex["nomjf_soundex2"]  = "LIKE '".$soundexObj->build($patient_jeuneFille)."%'";
}


if(($patient_year) || ($patient_month) || ($patient_day)){
	$patient_naissance = "on";
}

if ($patient_naissance == "on"){
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

if ($patient_ville)             $where["ville"]     = $whereSoundex["ville"]     = "LIKE '$patient_ville%'";
if ($patient_cp)                $where["cp"]        = $whereSoundex["cp"]        = "= '$patient_cp'";

$patients        = array();
$patientsSoundex = array();

$order = "nom, prenom, naissance";
$pat = new CPatient();

// Patient counts
$patientsCount = $where ? $pat->countList($where) : 0;
$patientsSoundexCount = $whereSoundex ? $pat->countList($whereSoundex) : 0;
$patientsSoundexCount -= $patientsCount;

// Chargement des patients
if ($where && ($soundex == "off")) {
  $patients = $pat->loadList($where, $order, "0, $showCount");
}

if ($whereSoundex) {
  $patientsSoundex = $pat->loadList($whereSoundex, $order, "0, $showCount");
  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$patient->_id and count($patients) == 1) {
  $patient = reset($patients);
}

// Liste des praticiens disponibles
$listPrat = array();
if ($patient->_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $patient->loadDossierComplet();
  
}

if ($patient->_id) {
  foreach($patient->_ref_sejours as $key=>$sejour){
  	$sejour->loadNumDossier();
  }
}
// Module de carte vitale

$intermaxFunctions = array(
  "Lire Vitale",
);

// Chargement de l'IPP du patient
$patient->loadIPP();


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("nom"            , $patient_nom                               );
$smarty->assign("prenom"         , $patient_prenom                            );
$smarty->assign("jeuneFille"     , $patient_jeuneFille                        );
$smarty->assign("soundex"        , $soundex                                   );
$smarty->assign("naissance"      , $patient_naissance                         );
$smarty->assign("ville"          , $patient_ville                             );
$smarty->assign("cp"             , $patient_cp                                );
$smarty->assign("patients"       , $patients                                  );
$smarty->assign("patientsSoundex", $patientsSoundex                           );
$smarty->assign("patientsCount"  , $patientsCount                             );
$smarty->assign("patientsSoundexCount", $patientsSoundexCount                      );
$smarty->assign("patient"        , $patient                                   );
$smarty->assign("chir"           , $chir                                      );
$smarty->assign("anesth"         , $anesth                                    );
$smarty->assign("listPrat"       , $listPrat                                  );
$smarty->assign("board"          , 0                                          );

$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->assign("newLine"          , "---");

$smarty->display("vw_idx_patients.tpl");
?>