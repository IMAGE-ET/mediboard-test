<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$board = mbGetValueFromGet("board", 0);

// Patients
$patient_nom       = mbGetValueFromGetOrSession("nom"       , ""       );
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , ""       );
$patient_naissance = mbGetValueFromGetOrSession("naissance" , "off"    );
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , date("Y"));

$where = null;
if ($patient_nom   ) $where["nom"]    = "LIKE '".addslashes($patient_nom)."%'";
if ($patient_prenom) $where["prenom"] = "LIKE '".addslashes($patient_prenom)."%'";
if ($patient_naissance == "on") {
  $where["naissance"] = "= '$patient_year-$patient_month-$patient_day'";
}

$patients = null;
if ($where) {
  $patients = new CPatient();
  $patients = $patients->loadList($where, "nom, prenom, naissance", "0, 100");
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("board"    , $board                                     );
$smarty->assign("nom"      , $patient_nom                               );
$smarty->assign("prenom"   , $patient_prenom                            );
$smarty->assign("naissance", $patient_naissance                         );
$smarty->assign("datePat"  , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patients" , $patients                                  );

$smarty->display("inc_list_patient.tpl");