<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id;

$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));
$periods         = array("day", "week", "month","weekly");
$chir_id         = CValue::get("chir_id");
$function_id     = $chir_id ? null : CValue::get("function_id");
$date            = CValue::get("date", CMbDT::date());
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
  $ndate = CMbDT::date("first day of next month"   , $date);
  $pdate = CMbDT::date("last day of previous month", $date);
}
//5.1, 5.2 Case @TODO : toDelete if all MB instance are 5.3 compatible
elseif ($period == 'month') {
    $ndate = CMbDT::date("+1 month"   , CMbDT::transform(null, $date, "%Y-%m-01" ));
    $pdate = CMbDT::date("-1 month"   , CMbDT::transform(null, $date, "%Y-%m-01" ));
}
else {
  $ndate = CMbDT::date("+1 $unit", $date);
  $pdate = CMbDT::date("-1 $unit", $date);
}

if ($period == "weekly") {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_weekly");
}
else {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_classic");
}
