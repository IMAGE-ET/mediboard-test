<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$sender = new CViewSender();
$senders = $sender->loadList(null, "name");
foreach ($senders as $_sender) {
	$_sender->makeHourPlan();
}

$hour_sum = array();
foreach (range(0, 59) as $min) {
	$hour_sum[$min] = 0;
	foreach ($senders as $_sender) {
	  $hour_sum[$min] += $_sender->_hour_plan[$min] ? 1 : 0;
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->assign("hour_sum", $hour_sum);
$smarty->display("inc_list_view_senders.tpl");
