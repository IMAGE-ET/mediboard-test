<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$operation_id = CValue::get("operation_id");

$operation = new COperation;
$operation->load($operation_id);

/** @var CDailyCheckList[] $check_lists */
$check_lists = $operation->loadBackRefs("check_lists", "date");
foreach ($check_lists as $_check_list_id => $_check_list) {
  // Remove check lists not signed
  if (!$_check_list->validator_id) {
    unset($operation->_back["check_lists"][$_check_list_id]);
    unset($check_lists[$_check_list_id]);
    continue;
  }

  $_check_list->loadItemTypes();
  $_check_list->loadRefListType();
  $_check_list->loadBackRefs('items', "daily_check_item_id");
  foreach ($_check_list->_back['items'] as $_item) {
    $_item->loadRefsFwd();
  }
}

$operation->loadRefsFwd();
$operation->loadRefSejour();
$operation->_ref_sejour->loadRefCurrAffectation();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation" , $operation);
$smarty->assign("patient"   , $operation->_ref_patient);
$smarty->assign("sejour"    , $operation->_ref_sejour);
$smarty->display("print_check_list_operation.tpl");