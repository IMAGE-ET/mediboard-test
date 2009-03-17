<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();
$ds = CSQLDataSource::get("std");
$date      = mbGetValueFromGetOrSession("date", mbDate());
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);
// S�lection du praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$selChir = mbGetValueFromGetOrSession("selChir", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPrat = new CMediusers();
$selPrat->load($selChir);

$selChirLogin   = null;
$specialite     = null;
$secondary_specs = array();
if ($selPrat->isPraticien()) {
  $selChirLogin = $selPrat->user_id;
  $specialite = $selPrat->function_id;
  $selPrat->loadBackRefs("secondary_functions");
  foreach($selPrat->_back["secondary_functions"] as  $curr_sec_spec) {
    $curr_sec_spec->loadRefsFwd();
    $curr_function = $curr_sec_spec->_ref_function;
    $secondary_specs[$curr_function->_id] = $curr_function;
  }
}

// Tous les praticiens
$mediuser = new CMediusers;
$listChir = $mediuser->loadPraticiens(PERM_EDIT);

// Planning du mois
$sql = "SELECT plagesop.*, plagesop.date AS opdate," .
		"\nSEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree," .
		"\nCOUNT(operations.operation_id) AS total," .
    "\nfunctions_mediboard.text AS nom_function, functions_mediboard.color as color_function" .
		"\nFROM plagesop" .
		"\nLEFT JOIN operations" .
		"\nON plagesop.plageop_id = operations.plageop_id" .
    "\nAND operations.annulee = '0'" .
		"\nLEFT JOIN functions_mediboard" .
		"\nON functions_mediboard.function_id = plagesop.spec_id" .
		"\nWHERE (plagesop.chir_id = '$selChirLogin' OR plagesop.spec_id = '$specialite' OR plagesop.spec_id ".$ds->prepareIn(array_keys($secondary_specs)).")" .
		"\nAND plagesop.date LIKE '".mbTransformTime("+ 0 day", $date, "%Y-%m")."-__'" .
		"\nGROUP BY plagesop.plageop_id" .
		"\nORDER BY plagesop.date, plagesop.debut, plagesop.plageop_id";
if($selChirLogin) {
  $listPlages = $ds->loadList($sql);
} else {
  $listPlages = array();
}

// Urgences du mois
$sql = "SELECT operations.*, operations.date AS opdate," .
		"\nSEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree," .
		"\nCOUNT(operations.operation_id) AS total" .
		"\nFROM operations" .
    "\nWHERE operations.annulee = '0'" .
		"\nAND operations.chir_id = '$selChirLogin'" .
		"\nAND operations.date LIKE '".mbTransformTime("+ 0 day", $date, "%Y-%m")."-__'" .
		"\nGROUP BY operations.date" .
		"\nORDER BY operations.date";
if($selChirLogin) {
  $listUrgences = $ds->loadList($sql);
} else {
  $listUrgences = array();
}

$listDays = array();
foreach($listPlages as $curr_ops) {
  $listDays[$curr_ops["opdate"]][$curr_ops["plageop_id"]] = $curr_ops;
}
foreach($listUrgences as $curr_ops) {
  $listDays[$curr_ops["opdate"]]["hors_plage"] = $curr_ops;
}

ksort($listDays);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"        , $date        );
$smarty->assign("lastmonth"   , $lastmonth   );
$smarty->assign("nextmonth"   , $nextmonth   );
$smarty->assign("listChir"    , $listChir    );
$smarty->assign("selChir"     , $selChir     );
$smarty->assign("listDays"    , $listDays    );

$smarty->display("vw_idx_planning.tpl");

?>