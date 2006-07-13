<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients"  , "patients"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPcabinet"   , "consultation"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// L'utilisateur est-il un chirurgien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Chirurgien"))) {
  $chir = $mediuser;
} else {
  $chir = null;
}

// L'utilisateur est-il un anesthésiste
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Anesthésiste"))) {
  $anesth = $mediuser;
} else {
  $anesth = null;
}

$patient_id = mbGetValueFromGetOrSession("id", 0);

// Récuperation du patient sélectionné
$patient = new CPatient;
if(dPgetParam($_GET, "new", 0)) {
  $patient->load(NULL);
  mbSetValueToSession("id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->patient_id) {
  $patient->loadDossierComplet();
}

// Récuperation des patients recherchés
$patient_nom       = mbGetValueFromGetOrSession("nom"       , ""       );
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , ""       );
$patient_naissance = mbGetValueFromGetOrSession("naissance" , "off"    );
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , date("Y"));

$where = null;
if ($patient_nom   ) $where[] = "nom LIKE '".addslashes($patient_nom)."%'";
if ($patient_prenom) $where[] = "prenom LIKE '".addslashes($patient_prenom)."%'";
if ($patient_naissance == "on") {
  $where["naissance"] = "= '$patient_year/$patient_month/$patient_day'";
}

$patients = null;
if ($where) {
  $patients = new CPatient();
  $patients = $patients->loadList($where, "nom, prenom, naissance", "0, 100");
}

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$canEditCabinet = !getDenyEdit("dPcabinet");


// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("nom"           , $patient_nom                               );
$smarty->assign("prenom"        , $patient_prenom                            );
$smarty->assign("naissance"     , $patient_naissance                         );
$smarty->assign("date"          , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patients"      , $patients                                  );
$smarty->assign("patient"       , $patient                                   );
$smarty->assign("chir"          , $chir                                      );
$smarty->assign("anesth"        , $anesth                                    );
$smarty->assign("listPrat"      , $listPrat                                  );
$smarty->assign("canEditCabinet", $canEditCabinet                            );

$smarty->display("vw_idx_patients.tpl");
?>