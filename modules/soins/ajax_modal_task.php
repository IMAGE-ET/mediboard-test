<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$task_id                      = CValue::get("task_id");
$sejour_id                    = CValue::get("sejour_id");
$prescription_line_element_id = CValue::get("prescription_line_element_id");

$task = new CSejourTask();
  
if($task_id){
  $task->load($task_id);
}

$chapitre = "";
if($prescription_line_element_id){
	$task->prescription_line_element_id = $prescription_line_element_id;
	$task->loadMatchingObject();
	
	// Chargement de la ligne
	$line_element = new CPrescriptionLineElement();
	$line_element->load($prescription_line_element_id);
	$chapitre = $line_element->_chapitre;
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("task", $task);
$smarty->assign("chapitre", $chapitre);
$smarty->display("inc_modal_task.tpl");

?>