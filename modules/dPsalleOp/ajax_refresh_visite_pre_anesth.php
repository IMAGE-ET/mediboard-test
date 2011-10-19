<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$operation_id = CValue::get("operation_id");
$callback     = CValue::get("callback");

$operation = new COperation;
$operation->load($operation_id);

$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$user = CMediusers::get();
$user->isAnesth();
$user->isPraticien();

$smarty = new CSmartyDP;

$smarty->assign("selOp", $operation);
$smarty->assign("listAnesths", $listAnesths);
$smarty->assign("callback"   , $callback);
$smarty->assign("currUser"   , $user);
$smarty->display("inc_visite_pre_anesth.tpl");

?>