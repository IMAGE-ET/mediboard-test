<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$message_id = CValue::getOrSession("message_id");

// Récupération du message à ajouter/éditer
$message = new CMessage;
$message->deb = mbDateTime();
$message->load($message_id);
$message->loadRefsNotes();

$groups = CMediusers::loadEtablissements(PERM_EDIT);

$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->assign("groups", $groups);
$smarty->display("inc_form_message.tpl");
