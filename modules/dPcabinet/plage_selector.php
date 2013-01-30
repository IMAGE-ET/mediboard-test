<?php /** $Id$ **/

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id;

$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));
$periods         = array("day", "week", "month","weekly");
$chir_id         = CValue::get("chir_id");
$function_id     = $chir_id ? null : CValue::get("function_id");
$date            = CValue::get("date", mbDate());
$plageconsult_id = CValue::get("plageconsult_id");

// Vérification des droits sur les praticiens
$mediuser = new CMediusers();
if (CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);
}
else {
  $listPraticiens = $mediuser->loadProfessionnelDeSante(PERM_EDIT);
}

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date = $plage->date;
}

// Récupération de la periode précédente et suivante
$unit = $period;
if ($period == "weekly") {
  $unit = "week";
}

/* WARNING when using "next month", "last month", "+1 month", 
"-1 month" or any combination of +/-X months. It will give non-intuitive results on Jan 30th and 31st 
 * http://www.php.net/manual/fr/function.strtotime.php#107331
 * */

if ($period == "month" && phpversion() >= "5.3") {
  $ndate = mbDate("first day of next month"   , $date);
  $pdate = mbDate("last day of previous month", $date);
}
//5.1, 5.2 Case @TODO : toDelete if all MB instance are 5.3 compatible
elseif ($period == 'month') {
    $ndate = mbDate("+1 month"   , mbTransformTime(null, $date, "%Y-%m-01" ));
    $pdate = mbDate("-1 month"   , mbTransformTime(null, $date, "%Y-%m-01" ));
}
else {
  $ndate = mbDate("+1 $unit", $date);
  $pdate = mbDate("-1 $unit", $date);
}

if ($period == "weekly") {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_weekly");
}
else {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_classic");
}
