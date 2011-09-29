<?php /* $Id: view_messages.php 10359 2010-10-12 16:30:43Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10359 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$_status = CValue::get("_status");

// Chargement des messages
$filter = new CMessage;
$filter->_status = $_status;
$messages = $filter->loadPublications($filter->_status);
foreach ($messages as $_message) {
  $_message->loadRefsNotes();
  $_message->loadRefModule();
  $_message->loadRefGroup();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter", $filter);
$smarty->assign("messages", $messages);
$smarty->display("inc_list_messages.tpl");
