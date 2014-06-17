<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$task_id                      = CValue::get("task_id");
$sejour_id                    = CValue::get("sejour_id");
$prescription_line_element_id = CValue::get("prescription_line_element_id");

$task = new CSejourTask();
$task->load($task_id);

$task_element = false;
if ($prescription_line_element_id) {
  $task->prescription_line_element_id = $prescription_line_element_id;
  $task->loadMatchingObject();
  $task_element = true;
}

if (!$task->_id) {
  $task->author_id = Cuser::get()->_id;
  $task->date = CMbDT::dateTime();
}

$task->loadRefConsult()->loadRefsFwd();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("sejour_id"   , $sejour_id);
$smarty->assign("task"        , $task);
$smarty->assign("task_element", $task_element);

$smarty->display("inc_modal_task.tpl");
