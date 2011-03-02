<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$task_id = CValue::get("task_id");
$sejour_id = CValue::get("sejour_id");

$task = new CSejourTask();
$task->load($task_id);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("task", $task);
$smarty->display("inc_modal_task.tpl");

?>