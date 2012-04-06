<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_replanif = CValue::getOrSession("date_replanif");

$plage = new CPlageOp;

$where = array();
$where["date"] = "= '$date_replanif'";

$plages = $plage->loadList($where);

$plages_by_salle = array();
$salles = array();

foreach ($plages as $key => $_plage) {
  $salle = $_plage->loadRefSalle();
  
  if (!$salle->isLocked($date_replanif)) {
    unset($plages[$key]);
    continue;
  }
  $operations = $_plage->loadRefsOperations();
  foreach ($operations as $_operation) {
    $_operation->loadRefPatient();
  }
  //$_plage->updateFormFields();
  $_plage->loadRefChir();
  if (!isset($plages_by_salle[$salle->_id])) {
    $plages_by_salle[$salle->_id] = array();
  }
  $plages_by_salle[$salle->_id][] = $_plage;
  $salles[$salle->_id] = $salle;
}

$smarty = new CSmartyDP;

$smarty->assign("plages_by_salle", $plages_by_salle);
$smarty->assign("salles", $salles);
$smarty->assign("date_replanif", $date_replanif);
$smarty->assign("date_replanif_before", mbDate("-1 day", $date_replanif));
$smarty->assign("date_replanif_after" , mbDate("+1 day", $date_replanif));

$smarty->display("inc_vw_operations_replanif.tpl");

?>