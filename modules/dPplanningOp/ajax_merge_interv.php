<?php 

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$date          = CValue::get("date_min_interv", mbDate());
$see_yesterday = CValue::getOrSession("see_yesterday", "1");

$date_min = $date;
$date_min = $see_yesterday ? mbDate("-1 day", $date) : $date;
$date_max = mbDate("+1 day", $date);

// Chargement des séjours concernés
$sejour = new CSejour;

$where  = array();
$where["sejour.entree"]   = "BETWEEN '$date_min' AND '$date_max'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "entree";

$sejours = $sejour->loadList($where, $order);
$count = 0;

$operations_merge = array();
foreach ($sejours as $_sejour) {
  $operations = $_sejour->loadRefCurrOperations($date);

  if (count($operations) != 2) {
    if (count($operations) > 2) {
      CAppUI::stepAjax("Il y a plus de deux opérations (".count($operations).") pour $_sejour->_view", UI_MSG_WARNING);
    }
    continue;
  }
  
  $count++;
  
  $operations_merge[] = $operations;
}

CAppUI::stepAjax("$count interventions sont à fusionner");

foreach ($operations_merge as $_operation_merge) {
  $op_merge = array_values($_operation_merge);
  $plageop = 0;
  foreach ($op_merge as $_operation) {
    if ($_operation->plageop_id) {
      $plageop++;
      
      $op_merge[0] = $_operation;
    }
    else {
      $op_merge[1] = $_operation;
    }
  }
  
  $continue = false;
  switch ($plageop) {
    case 0 :
      $op_merge = array_values($_operation_merge);
      
      break;
    case 2 :
      $continue = true;
      CAppUI::stepAjax("Deux interventions avec les plages, impossible de fusionner", UI_MSG_WARNING);
      
      break;
  }
  
  if ($continue) {
    continue;
  }

  $first_op  = $op_merge[0];
  $second_op = $op_merge[1];

  $first_op_id = $first_op->_id;
  
  $array_second_op = array($second_op);
  // Check merge
  if ($checkMerge = $first_op->checkMerge($array_second_op)) {
    CAppUI::stepAjax($checkMerge, UI_MSG_WARNING);
    
    continue;
  }
  
  // @todo mergePlainFields resets the _id 
  $first_op->_id = $first_op_id;
  
  $first_op->_merging = CMbArray::pluck($array_second_op, "_id");
  if ($msg = $first_op->merge($array_second_op)) {
    CAppUI::stepAjax($msg, UI_MSG_WARNING);
    
    continue;
  }
  
  CAppUI::stepAjax("Interventions fusionnées");
}
  