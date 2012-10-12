<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$operation_id  = CValue::get("operation_id");
$sejour_id     = CValue::get("sejour_id");
$date_move     = CValue::get("date_move");
$callback      = CValue::get("callback");
$entree_prevue = CValue::get("entree_prevue");
$sortie_prevue = CValue::get("sortie_prevue");

if ($operation_id) {
  $operation = new COperation();
  $operation->load($operation_id);
  
  $sejour = $operation->loadRefSejour();
}
else {
  $sejour = new CSejour();
  $sejour->load($sejour_id);
}

if (!$date_move) {
  $date_move = "$operation->date $operation->time_operation";
}

if ($entree_prevue && $sortie_prevue) {
  $sejour->entree_prevue = $entree_prevue;
  $sejour->sortie_prevue = $sortie_prevue;
}

if (isset($operation)) {
  $nb_days = mbDaysRelative("$operation->date $operation->time_operation", $date_move);
}
else {
  $nb_days = mbDaysRelative($sejour->entree_prevue, $entree_prevue);
}

if ($nb_days > 0 ) {
  $sejour->entree_prevue = mbDateTime("+$nb_days day", $sejour->entree_prevue);
  $sejour->sortie_prevue = mbDateTime("+$nb_days day", $sejour->sortie_prevue);
} 
else {
  $sejour->entree_prevue = mbDateTime("$nb_days day", $sejour->entree_prevue);
  $sejour->sortie_prevue = mbDateTime("$nb_days day", $sejour->sortie_prevue);
}

$smarty = new CSmartyDP;

$smarty->assign("sejour"   , $sejour);
$smarty->assign("date_move", $date_move);
$smarty->assign("callback" , $callback);

$smarty->display("inc_edit_dates_sejour.tpl");
