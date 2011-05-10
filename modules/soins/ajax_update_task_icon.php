<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_line_element_id = CValue::get("prescription_line_element_id");

$line = new CPrescriptionLineElement();
$line->load($prescription_line_element_id);

$line->loadRefTask();

$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("prescription", $line->_ref_prescription);
$smarty->display('inc_vw_task_icon.tpl');

?>