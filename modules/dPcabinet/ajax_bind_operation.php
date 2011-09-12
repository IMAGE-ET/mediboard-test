<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);

$operations = $sejour->loadRefsOperations();
CMbObject::massLoadFwdRef($operations, "plageop_id");

foreach($operations as $_operation) {
  $_operation->loadRefPlageOp();
}

$smarty = new CSmartyDP;

$smarty->assign("operations", $operations);
$smarty->assign("sejour_id" , $sejour_id);
$smarty->display("inc_bind_operations.tpl");

?>