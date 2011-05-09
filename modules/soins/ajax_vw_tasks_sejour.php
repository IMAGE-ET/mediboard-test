<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::getOrSession("sejour_id");
$mode_realisation = CValue::get("mode_realisation");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefsTasks();

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("task", new CSejourTask());
$smarty->assign("mode_realisation", $mode_realisation);
$smarty->display("inc_vw_tasks_sejour.tpl");

