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

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id, $consultation_id;

$period          = CValue::get("period", CAppUI::pref("DefaultPeriod"));
$periods         = array("day", "week", "month","weekly");
$chir_id         = CValue::get("chir_id");
$function_id     = $chir_id ? null : CValue::get("function_id");
$date            = CValue::get("date", CMbDT::date());
$plageconsult_id = CValue::get("plageconsult_id");
$consultation_id = CValue::get("consultation_id");

// Vérification des droits sur les praticiens
$listPraticiens = CConsultation::loadPraticiens(PERM_EDIT);

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

if ($period == "month") {
  $ndate = CMbDT::date("first day of next month"   , $date);
  $pdate = CMbDT::date("last day of previous month", $date);
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
