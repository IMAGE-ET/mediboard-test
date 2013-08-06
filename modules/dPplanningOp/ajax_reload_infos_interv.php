<?php 

/**
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$operation_id = CValue::get("operation_id");

$operation = new COperation();
$operation->load($operation_id);
$operation->canDo();
$operation->countAlertsNotHandled();

$smarty = new CSmartyDP();

$smarty->assign("operation", $operation);

$smarty->display("inc_reload_infos_interv.tpl");