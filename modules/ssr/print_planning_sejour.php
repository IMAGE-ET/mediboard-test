<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", mbDate());
$sejour_id = CValue::getOrSession("sejour_id");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("sejour_id", $sejour_id);
$smarty->display("print_planning_sejour.tpl");

?>