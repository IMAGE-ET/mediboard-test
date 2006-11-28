<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);
$new        = mbGetValueFromGet("new", 0);

$moduleCabinet = CModule::getInstalled("dPcabinet");

$canEditCabinet   = $moduleCabinet->canEdit();

$listPrat = array();

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

// Récuperation du patient sélectionné
$patient = new CPatient;
if($new) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
  mbSetValueToSession("selClass", null);
  mbSetValueToSession("selKey", null);
} else {
  $patient->load($patient_id);
}


if ($patient_id && !$new) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $patient->loadDossierComplet();
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

$where = null;
if($soundex == "on") {
  $soundex2 = new soundex2();
  if ($patient_nom   ) $where["nom_soundex2"]    = "= '".$soundex2->build($patient_nom)."'";
  if ($patient_prenom) $where["prenom_soundex2"] = "= '".$soundex2->build($patient_prenom)."'";
} else {
  if ($patient_nom   ) $where["nom"]    = "LIKE '$patient_nom%'";
  if ($patient_prenom) $where["prenom"] = "LIKE '$patient_prenom%'";
}
if ($patient_naissance == "on") {
  $where["naissance"] = "= '$patient_year-$patient_month-$patient_day'";
}
if($patient_ville) $where["ville"] = "= '$patient_ville'";
if($patient_cp) $where["cp"] = "= '$patient_cp'";

$patients = null;

if ($where) {
  $patients = new CPatient();
  $patients = $patients->loadList($where, "nom, prenom, naissance", "0, 100");
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("nom"           , $patient_nom                               );
$smarty->assign("prenom"        , $patient_prenom                            );
$smarty->assign("soundex"       , $soundex                                   );
$smarty->assign("naissance"     , $patient_naissance                         );
$smarty->assign("ville"         , $patient_ville                             );
$smarty->assign("cp"            , $patient_cp                                );
$smarty->assign("datePat"       , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patients"      , $patients                                  );
$smarty->assign("patient"       , $patient                                   );
$smarty->assign("chir"          , $chir                                      );
$smarty->assign("anesth"        , $anesth                                    );
$smarty->assign("listPrat"      , $listPrat                                  );
$smarty->assign("canEditCabinet", $canEditCabinet                            );
$smarty->assign("board"         , 0                                          );

$smarty->display("vw_idx_patients.tpl");
?>