<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$operation_id = CValue::getOrSession("operation_id");

$operation = new COperation();
$operation->load($operation_id);

$operation->loadRefsAnesthPerops();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->display("inc_list_anesth_perops.tpl");

?>