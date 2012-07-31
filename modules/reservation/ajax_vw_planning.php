<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date_planning = CValue::getOrSession("date_planning");
$praticien_id  = CValue::getOrSession("praticien_id");
$scroll_top    = CValue::get("scroll_top", null);

// Récupération des opérations
$operation = new COperation;
$operation->date = $date_planning;
$operation->annulee = 0;
$operation->plageop_id = null;

if ($praticien_id) {
  $operation->chir_id = $praticien_id;
}

$operations = $operation->loadMatchingList();

$salles = CMbObject::massLoadFwdRef($operations, "salle_id");
$prats  = CMbObject::massLoadFwdRef($operations, "chir_id");
CMbObject::massLoadFwdRef($operations, "anesth_id");
CMbObject::massLoadFwdRef($prats, "function_id");

$planning = new CPlanningWeek(0, 0, count($salles), count($salles), false, "auto");
$planning->title = "Planning du ".mbDateToLocale($date_planning);
$planning->guid = "planning_interv";
$planning->hour_min  = mbTime(CAppUI::conf("dPplanningOp COperation hour_urgence_deb").":00");
$planning->hour_max  = mbTime(CAppUI::conf("dPplanningOp COperation hour_urgence_fin").":00");
$planning->dragndrop = CCanDo::edit();
$planning->hour_divider = 60 / intval(CAppUI::conf("dPplanningOp COperation min_intervalle"));

$i = 0;

foreach ($salles as $_salle) {
  $planning->addDayLabel($i, $_salle->_view);
  $i++;
}

$operations_by_salle = array();

foreach ($operations as $key => $_operation) {
  if (!$_operation->salle_id) {
    unset($operations[$key]);
    continue;
  }
  
  if (!isset($operations_by_salle[$_operation->salle_id])) {
    $operations_by_salle[$_operation->salle_id] = array();
  }
  $operations_by_salle[$_operation->salle_id][] = $_operation;
}

ksort($operations_by_salle);

$i = 0;

foreach ($operations_by_salle as $salle_id => $_operations) {
  foreach ($_operations as $_operation) {
    $_operation->_ref_salle = $_operation->loadFwdRef("salle_id");
    $chir   = $_operation->loadRefChir();
    $chir->loadRefFunction();
    $anesth = $_operation->_ref_anesth = $_operation->loadFwdRef("anesth_id");
    $sejour  = $_operation->loadRefSejour();
    $patient = $sejour->loadRefPatient();
    
    if (!$anesth->_id) {
      $anesth = $_operation->loadFwdRef("anesth_id", true);
    }
    if ($_operation->horaire_voulu) {
      $debut = "$i {$_operation->horaire_voulu}";
      $debut_op = $_operation->horaire_voulu;
      $fin_op = mbAddTime($_operation->temp_operation, $_operation->horaire_voulu);
      $duree = mbMinutesRelative($_operation->horaire_voulu, $fin_op);
    }
    else {
      $debut = "$i {$_operation->time_operation}";
      $debut_op = $_operation->time_operation;
      $fin_op = mbAddTime($_operation->temp_operation, $_operation->time_operation);
      $duree = mbMinutesRelative($_operation->time_operation, $fin_op);
    }
    
    $libelle = "$patient->nom $patient->prenom, ".$patient->getFormattedValue("naissance").
    "\n".mbTransformTime($debut_op, null, "%H:%M")." - ".mbTransformTime($fin_op, null, "%H:%M").
    "\n".$sejour->getFormattedValue("entree").
    "\n$_operation->libelle".
    "\n$chir->_shortview, $anesth->_shortview".
    "\n$_operation->rques";
    
    $color = $_operation->rank ? "#{$chir->_ref_function->color}" : "#fd3";
    
    $event = new CPlanningEvent($_operation->_guid, $debut, $duree, $libelle, $color, true, null, $_operation->_guid);
    
    if (CCanDo::edit()) {
      $event->addMenuItem("edit", "Modifier cette opération");
    }
    
    $event->plage["id"] = $_operation->_id;
    $event->type = "operation_horsplage";
    $event->draggable = CCanDo::edit();
    $planning->addEvent($event);
  }
  $i++;
}

$today_tomorrow = $date_planning == mbDate() || $date_planning = mbDate("+1 day");

$smarty = new CSmartyDP;

$smarty->assign("planning", $planning);
$smarty->assign("salles"  , $salles);
$smarty->assign("salles_ids", array_keys($salles));
$smarty->assign("date_planning", $date_planning);
$smarty->assign("scroll_top", $scroll_top);
$smarty->assign("today_tomorrow", $today_tomorrow);

$smarty->display("inc_vw_planning.tpl");
