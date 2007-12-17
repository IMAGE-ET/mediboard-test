<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

$board = mbGetValueFromGet("board", 0);

$patient_id = mbGetValueFromGetOrSession("patient_id");

// Patients
$patient_nom       = mbGetValueFromGetOrSession("nom"       , ""       );
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , ""       );
$patient_jeuneFille= mbGetValueFromGetOrSession("jeuneFille"  , ""       );
$patient_naissance = mbGetValueFromGetOrSession("naissance" , "off"    );
$patient_ville     = mbGetValueFromGetOrSession("ville"     , ""       );
$patient_cp        = mbGetValueFromGetOrSession("cp"        , ""       );
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , date("Y"));

$patient = new CPatient;
if ($new = mbGetValueFromGet("new")) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $patient->load($patient_id);
}

// Champs vitale
$patVitale = new CPatient;
if ($useVitale = mbGetValueFromGet("useVitale")) {
  $patVitale->getValuesFromVitale();
  $patVitale->updateFormFields();
  $patient_nom    = $patVitale->nom;
  $patient_prenom = $patVitale->prenom;
  mbSetValueToSession("nom", $patVitale->nom);
  mbSetValueToSession("prenom", $patVitale->prenom);
//  $patient_day    = $patVitale->_jour;
//  $patient_month  = $patVitale->_mois;
//  $patient_year   = $patVitale->_annee;
  $patient_naissance = "on";
  mbSetValueToSession("naissance", "on");
  $patVitale->loadFromIdVitale();
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
if ($patient_naissance == "on") $where["naissance"] = $whereSoundex["naissance"] = "= '$patient_year-$patient_month-$patient_day'";
if ($patient_ville)             $where["ville"]     = $whereSoundex["ville"]     = "= '$patient_ville'";
if ($patient_cp)                $where["cp"]        = $whereSoundex["cp"]        = "= '$patient_cp'";

$patients        = array();
$patientsSoundex = array();

if (!function_exists('array_diff_key')) {
  function array_diff_key() {
    $argCount  = func_num_args();
    $argValues  = func_get_args();
    $valuesDiff = array();
    if ($argCount < 2) return false;
    foreach ($argValues as $argParam) {
      if (!is_array($argParam)) return false;
    }
    foreach ($argValues[0] as $valueKey => $valueData) {
      for ($i = 1; $i < $argCount; $i++) {
        if (isset($argValues[$i][$valueKey])) continue 2;
      }
      $valuesDiff[$valueKey] = $valueData;
    }
    return $valuesDiff;
  }
}

$pat = new CPatient();
if ($where) {
  $patients = $pat->loadList($where, "nom, prenom, naissance", "0, 100");
}
if($whereSoundex && ($nbExact = (100 - count($patients)))) {
  $patientsSoundex = $pat->loadList($whereSoundex, "nom, prenom, naissance", "0, $nbExact");
  $patientsSoundex = array_diff_key($patientsSoundex, $patients);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("board"          , $board                                     );
$smarty->assign("nom"            , $patient_nom                               );
$smarty->assign("prenom"         , $patient_prenom                            );
$smarty->assign("naissance"      , $patient_naissance                         );
$smarty->assign("ville"          , $patient_ville                             );
$smarty->assign("cp"             , $patient_cp                                );
$smarty->assign("datePat"        , "$patient_year-$patient_month-$patient_day");
$smarty->assign("useVitale"      , $useVitale                                 );
$smarty->assign("patVitale"      , $patVitale                                 );
$smarty->assign("patients"       , $patients                                  );
$smarty->assign("patientsCount"  , count($patients)                           );
$smarty->assign("patientsSoundexCount", count($patientsSoundex)               );
$smarty->assign("jeuneFille"     , $patient_jeuneFille                        );
$smarty->assign("patientsSoundex", $patientsSoundex                           );
$smarty->assign("patient"        , $patient                                   );

$smarty->display("inc_list_patient.tpl");