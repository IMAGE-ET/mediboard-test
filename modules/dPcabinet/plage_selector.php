<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");

// Initialisation des variables
$plageconsult_id = CValue::get("plageconsult_id");
$hour            = CValue::get("hour");
$chir_id         = CValue::get("chir_id");
$function_id     = CValue::get("function_id");
$date            = CValue::get("date", mbDate());
$hide_finished   = CValue::get("hide_finished", true);
$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date = $plage->date;
}

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();

// Praticiens sélectionnés
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_EDIT, $function_id);

$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);

// Filtres
if ($hour) {
  $where["debut"] = "<= '$hour:00'";
  $where["fin"] = "> '$hour:00'";
}

if ($hide_finished) {
  $where[] = $ds->prepare("`date` >= %", mbDate());
}

// Filtre de la période
$periods = array("day", "week", "month");
switch ($period) {
  case "day":
    $minDate = mbDate(null, $date);
    $maxDate = mbDate(null, $date);
    break;

  case "week":
    $minDate = mbDate("last sunday", $date);
    $maxDate = mbDate("next saturday", $date);
    break;

  case "month":
    $minDate = mbTransformTime(null, $date, "%Y-%m-01");
    $maxDate = mbTransformTime("+1 month", $minDate, "%Y-%m-01");
    break;

  default:
    trigger_error("Période '$period' inconnue");
    break;
}

$relDate = $date;
if ($period == 'month') {
	$relDate = $minDate;
}

$ndate = mbDate("+1 $period", $relDate);
$pdate = mbDate("-1 $period", $relDate);

$where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
$where[] = "libelle != 'automatique' OR libelle IS NULL";

$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);

if (!array_key_exists($plageconsult_id, $listPlage)) {
  $plage->_id = $plageconsult_id = null;
}

foreach ($listPlage as $keyPlage => &$currPlage) {
  if (!$plageconsult_id && $date == $currPlage->date) {
    $plageconsult_id = $currPlage->plageconsult_id;
  }

  $currPlage->_ref_chir =& $listPrat[$currPlage->chir_id];
  $currPlage->loadFillRate();
}

// Chargement des places disponibles
$listPlace = array();
if ($plageconsult_id) {
  if (!$plage->plageconsult_id) {
    $plage->load($plageconsult_id);
  }
  $plage->loadRefs(false);
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $listPlace[$i]["time"] = mbTime("+ $minutes minutes", $plage->debut);
    $listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient();
    // Chargement de la categorie
    $consultation->loadRefCategorie();
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($listPlace[($keyPlace + $i)])) {
        $listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("periods"        , $periods);
$smarty->assign("hour"           , $hour);
$smarty->assign("hours"          , CPlageconsult::$hours);
$smarty->assign("hide_finished"  , $hide_finished);
$smarty->assign("date"           , $date);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlage"      , $listPlage);
$smarty->assign("listPlace"      , $listPlace);
$smarty->assign("online"         , true);

$smarty->display("plage_selector.tpl");

?>