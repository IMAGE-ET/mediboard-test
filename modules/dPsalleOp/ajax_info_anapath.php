<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$operation_id = CValue::get("operation_id");

$op = new COperation();
$op->load($operation_id);

$smarty = new CSmartyDP;

$smarty->assign("op", $op);

$smarty->display("inc_info_anapath.tpl");
