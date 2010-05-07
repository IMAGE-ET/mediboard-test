<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = CValue::getOrSession("date", mbDate());

$planning = new CPlanningWeek($date);
$next_week = mbDate("+1 week", $date);
$prev_week = mbDate("-1 week", $date);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->assign("next_week", $next_week);
$smarty->assign("prev_week" , $prev_week);
$smarty->display("inc_week_changer.tpl");

?>