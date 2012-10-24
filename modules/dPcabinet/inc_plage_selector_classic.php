<?php /* $Id: plage_selector.php 13572 2011-10-24 07:04:18Z flaviencrochard $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 13572 $
* @author Romain Ollivier
*/

$ds = CSQLDataSource::get("std");

// Initialisation des variables
global $period, $periods, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id;

$hour            = CValue::get("hour");
$hide_finished   = CValue::get("hide_finished", true);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$plage = new CPlageconsult;
$where = array();

// Praticiens sélectionnés
$listPrat = new CMediusers;
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $listPrat->loadPraticiens(PERM_EDIT, $function_id, null, true);
} else {
  $listPrat = $listPrat->loadProfessionnelDeSante(PERM_EDIT, $function_id, null, true);
}

$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);

// Filtres
if ($hour) {
  $where["debut"] = "<= '$hour:00'";
  $where["fin"] = "> '$hour:00'";
}

if ($hide_finished) {
  $where[] = $ds->prepare("`date` >= %", mbDate());
}

$minDate = $maxDate = $refDate = mbDate(null, $date);
// Filtre de la période
switch ($period) {
  case "day":
    $minDate = $maxDate = $refDate = mbDate(null, $date);
    break;

  case "week":
    $minDate = mbDate("last sunday", $date);
    $maxDate = mbDate("next saturday", $date);
    $refDate = mbDate("+1 day", $minDate);
    break;

  case "month":
    $minDate = mbTransformTime(null, $date, "%Y-%m-01");
    $maxDate = mbTransformTime("+1 month", $minDate, "%Y-%m-01");
    $maxDate = mbDate("-1 day", $maxDate);
    $refDate = $minDate;
    break;

  default:
    trigger_error("Période '$period' inconnue");
    break;
}

$bank_holidays = array_merge(mbBankHolidays($minDate), mbBankHolidays($maxDate));

$where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
$where[] = "libelle != 'automatique' OR libelle IS NULL";

$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);

if (!array_key_exists($plageconsult_id, $listPlage)) {
  $plage->_id = $plageconsult_id = null;
}
$currPlage = new CPlageconsult();
foreach ($listPlage as $keyPlage => &$currPlage) {
  if (!$plageconsult_id && $date == $currPlage->date) {
    $plageconsult_id = $currPlage->plageconsult_id;
  }

  $currPlage->_ref_chir =& $listPrat[$currPlage->chir_id];
  $currPlage->loadFillRate();
  $currPlage->loadCategorieFill();
  $currPlage->loadRefsNotes();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("periods"        , $periods);
$smarty->assign("hour"           , $hour);
$smarty->assign("hours"          , CPlageconsult::$hours);
$smarty->assign("hide_finished"  , $hide_finished);
$smarty->assign("date"           , $date);
$smarty->assign("refDate"        , $refDate);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("bank_holidays"  , $bank_holidays);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlage"      , $listPlage);
$smarty->assign("online"         , true);

$smarty->display("plage_selector.tpl");

?>