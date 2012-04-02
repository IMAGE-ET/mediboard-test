<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$operation_id = CValue::get("operation_id");

$operation = new COperation;
$operation->load($operation_id);

$sejour = $operation->loadRefSejour();

$grossesse = $sejour->loadRefGrossesse();

$naissances = $grossesse->loadRefsNaissances();

$sejours = CMbObject::massLoadFwdRef($naissances, "sejour_enfant_id");

CMbObject::massLoadFwdRef($sejours, "patient_id");

foreach ($naissances as $_naissance) {
  $_naissance->loadRefSejourEnfant()->loadRefPatient();
}

$smarty = new CSmartyDP;

$smarty->assign("grossesse", $grossesse);
$smarty->assign("operation", $operation);

$smarty->display("inc_vw_naissances.tpl");

?>