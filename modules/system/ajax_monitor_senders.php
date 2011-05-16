<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Chargement des senders
$sender  = new CViewSender();
$sender->active = "1";
$senders = $sender->loadMatchingList("name");

// Détails des senders
foreach ($senders as $_sender) {
  $senders_source = $_sender->loadRefSendersSource();
  foreach ($senders_source as $_sender_source) {
  	$_sender_source->loadRefSender();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->display("inc_monitor_senders.tpl");
