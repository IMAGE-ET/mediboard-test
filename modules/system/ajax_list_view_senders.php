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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->display("inc_list_view_senders.tpl");
