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
$patient_nom       = mbGetValueFromGetOrSession("nom"       , ""       );
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , ""       );
$soundex           = mbGetValueFromGetOrSession("soundex"   , "off"    );
$patient_naissance = mbGetValueFromGetOrSession("naissance" , "off"    );
$patient_ville     = mbGetValueFromGetOrSession("ville"     , ""       );
$patient_cp        = mbGetValueFromGetOrSession("cp"        , ""       );
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , date("Y"));

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

if ($patient_naissance == "on"){
  $year =($patient_year)?"$patient_year-":"%-";
  $month =($patient_month)?"$patient_month-":"%-";
  $day =($patient_day)?"$patient_day":"%";
  $day = str_pad($day,2,"0",STR_PAD_LEFT);
  
  $naissance = $year.$month.$day;
  
  if($patient_year || $patient_month || $patient_day){
    $where["naissance"] = $whereSoundex["naissance"] = "LIKE '$naissance'";
  }
}

if ($patient_ville)             $where["ville"]     = $whereSoundex["ville"]     = "= '$patient_ville'";
if ($patient_cp)                $where["cp"]        = $whereSoundex["cp"]        = "= '$patient_cp'";

$patients        = array();
$patientsSoundex = array();

$order = "nom, prenom, naissance";
$pat = new CPatient();
if ($where && ($soundex == "off")) {
  $patients = $pat->loadList($where, $order, "0, 100");
}
if($whereSoundex && ($nbExact = (100 - count($patients)))) {
  $patientsSoundex = $pat->loadList($whereSoundex, $order, "0, $nbExact");
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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("nom"            , $patient_nom                               );
$smarty->assign("prenom"         , $patient_prenom                            );
$smarty->assign("soundex"        , $soundex                                   );
$smarty->assign("naissance"      , $patient_naissance                         );
$smarty->assign("ville"          , $patient_ville                             );
$smarty->assign("cp"             , $patient_cp                                );
$smarty->assign("datePat"        , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patients"       , $patients                                  );
$smarty->assign("patientsSoundex", $patientsSoundex                           );
$smarty->assign("patient"        , $patient                                   );
$smarty->assign("chir"           , $chir                                      );
$smarty->assign("anesth"         , $anesth                                    );
$smarty->assign("listPrat"       , $listPrat                                  );
$smarty->assign("board"          , 0                                          );

$smarty->display("vw_idx_patients.tpl");
?>