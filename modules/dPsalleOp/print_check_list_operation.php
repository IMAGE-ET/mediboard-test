<?php /* $Id: vw_operations.php 7351 2009-11-17 09:58:58Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: 7351 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$operation_id = CValue::get("operation_id");

$operation = new COperation;
$operation->load($operation_id);

$check_lists = $operation->loadBackRefs("check_lists", "date");
foreach($check_lists as $_check_list) {
  $_check_list->loadItemTypes();
  $_check_list->loadBackRefs('items', "daily_check_item_id");
  foreach($_check_list->_back['items'] as $_item) {
    $_item->loadRefsFwd();
  }
}

$operation->loadRefsFwd();
$operation->loadRefSejour();
$operation->_ref_sejour->loadRefCurrAffectation();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->assign("patient", $operation->_ref_patient);
$smarty->assign("sejour", $operation->_ref_sejour);
$smarty->display("print_check_list_operation.tpl");
