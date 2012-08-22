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

$operation_id = CValue::get("operation_id");
$date_move    = CValue::get("date_move");
$callback     = CValue::get("callback");

$operation = new COperation;
$operation->load($operation_id);

$sejour = $operation->loadRefSejour();

if (!$date_move) {
  $date_move = "$operation->date $operation->time_operation";
}

$nb_days = mbDaysRelative("$operation->date $operation->time_operation", $date_move);

if ($nb_days > 0 ) {
  $sejour->entree_prevue = mbDateTime("+$nb_days day", $sejour->entree_prevue);
  $sejour->sortie_prevue = mbDateTime("+$nb_days day", $sejour->sortie_prevue);
} 
else {
  $sejour->entree_prevue = mbDateTime("$nb_days day", $sejour->entree_prevue);
  $sejour->sortie_prevue = mbDateTime("$nb_days day", $sejour->sortie_prevue);
}

$smarty = new CSmartyDP;

$smarty->assign("operation", $operation);
$smarty->assign("date_move", $date_move);
$smarty->assign("callback" , $callback);

$smarty->display("inc_edit_dates_sejour.tpl");
