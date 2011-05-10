<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Chargement des senders sources
$sender_source = new CViewSenderSource();
$senders_source = $sender_source->loadList(null, "name");
foreach ($senders_source as $_sender_source) {
  $_sender_source->loadRefGroup();
  $_sender_source->loadRefSourceFTP();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders_source", $senders_source);
$smarty->display("inc_list_view_senders_source.tpl");
