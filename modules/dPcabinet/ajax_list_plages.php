<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::check();

global $listPraticiens;


$ds = CSQLDataSource::get("std");

$period           = CValue::get("period", CAppUI::pref("DefaultPeriod"));
$periods          = array("day", "week", "month","weekly");
$chir_id          = CValue::get("chir_id");
$function_id      = $chir_id ? null : CValue::get("function_id");
$multipleMode     = CValue::get("multipleMode", false);
$date             = CValue::get("date", CMbDT::date());
$plageconsult_id  = CValue::get("plageconsult_id");
$hour             = CValue::get("hour");
$hide_finished    = CValue::get("hide_finished", true);
$_line_element_id = CValue::get("_line_element_id");


// Vérification des droits sur les praticiens
$listPraticiens = CConsultation::loadPraticiens(PERM_EDIT);
$listPrat = CConsultation::loadPraticiens(PERM_EDIT, $function_id, null, true);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$plage = new CPlageconsult;
$where = array();

if ($_line_element_id) {
  $where["pour_tiers"] = "= '1'";
}

$chir_sel = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);
$where[] = "chir_id $chir_sel OR remplacant_id $chir_sel";

// Filtres
if ($hour) {
  $where["debut"] = "<= '$hour:00'";
  $where["fin"] = "> '$hour:00'";
}

if ($hide_finished) {
  $where[] = $ds->prepare("`date` >= %", CMbDT::date());
}

$minDate = $maxDate = $refDate = CMbDT::date(null, $date);



if ($period == "weekly") {
  CAppUI::requireModuleFile("dPcabinet", "inc_plage_selector_weekly");
  return;
}


switch ($period) {
  case "day":
    $minDate = $maxDate = $refDate = CMbDT::date(null, $date);
    $ndate = CMbDT::date("+1 day", $date);
    $pdate = CMbDT::date("-1 day", $date);
    break;

  case "week":
    $minDate = CMbDT::date("last sunday", $date);
    $maxDate = CMbDT::date("next saturday", $date);
    $refDate = CMbDT::date("+1 day", $minDate);
    $ndate = CMbDT::date("+1 week", $date);
    $pdate = CMbDT::date("-1 week", $date);
    break;

  case "4weeks":
    $minDate = CMbDT::date("last sunday", $date);
    $maxDate = CMbDT::date("+ 3 weeks", CMbDT::date("next saturday", $date));
    $refDate = CMbDT::date("+1 day", $minDate);
    $ndate = CMbDT::date("+4 week", $date);
    $pdate = CMbDT::date("-4 week", $date);
    break;

  case "month":
    $minDate = CMbDT::format($date, "%Y-%m-01");
    $maxDate = CMbDT::transform("+1 month", $minDate, "%Y-%m-01");
    $maxDate = CMbDT::date("-1 day", $maxDate);
    $refDate = $minDate;
    if (phpversion() >= "5.3") {
      $ndate = CMbDT::date("first day of next month"   , $date);
      $pdate = CMbDT::date("last day of previous month", $date);
    }
    else {
      $ndate = CMbDT::date("+1 month"   , CMbDT::format($date, "%Y-%m-01" ));
      $pdate = CMbDT::date("-1 month"   , CMbDT::format($date, "%Y-%m-01" ));
    }
    break;

  default:
    $ndate = CMbDT::date("+1 day", $date);
    $pdate = CMbDT::date("-1 day", $date);
    trigger_error("Période '$period' inconnue");
    break;
}

$bank_holidays = array_merge(CMbDate::getHolidays($minDate), CMbDate::getHolidays($maxDate));

$where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
$where[] = "libelle != 'automatique' OR libelle IS NULL";

$ljoin["users"] = "users.user_id = plageconsult.chir_id OR users.user_id = plageconsult.remplacant_id ";

$order = "date, user_last_name, user_first_name, debut";

// Chargement des plages disponibles
/** @var CPlageconsult[] $listPlage */
$listPlage = $plage->loadList($where, $order, null, null, $ljoin);

if (!array_key_exists($plageconsult_id, $listPlage)) {
  $plage->_id = $plageconsult_id = null;
}
$currPlage = new CPlageconsult();
foreach ($listPlage as $currPlage) {
  if (!$plageconsult_id && $date == $currPlage->date) {
    $plageconsult_id = $currPlage->_id;
  }

  $currPlage->_ref_chir = $listPrat[$currPlage->chir_id];
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
$smarty->assign("_line_element_id", $_line_element_id);
$smarty->assign("multiple"      , $multipleMode);

$smarty->display("inc_list_plages.tpl");