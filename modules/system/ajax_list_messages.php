<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
