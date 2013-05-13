<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$message_id = CValue::get("message_id");

$update_moment    = CValue::get("_update_moment");
$update_initiator = CValue::get("_update_initiator");
$update_benefits  = CValue::get("_update_benefits");

// Récupération du message à ajouter/éditer
$message = new CMessage;
$message->deb = CMbDT::dateTime();
$message->load($message_id);
$message->loadRefsNotes();

// Création du possible
if ($update_moment) {
  $message->deb = CMbDT::dateTime("-8 hours", $update_moment);
  $message->fin = CMbDT::dateTime("+15 minutes", $update_moment);
  $message->titre = CAppUI::tr("CMessage-create_update-titre");
  $message->corps = CAppUI::tr(
    "CMessage-create_update-corps",
    CMbDT::transform(null, $update_moment, CAppUI::conf("datetime"))
  );
  
  $details = CAppUI::tr(
    "CMessage-create_update-details",
    stripslashes($update_initiator), 
    stripslashes($update_benefits)
  );
  $message->_email_details = CMbString::br2nl($details);
}

// Etablissements disponisbles
$groups = CMediusers::loadEtablissements(PERM_EDIT);

// Source SMTP
$message_smtp = CExchangeSource::get("system-message", "smtp", true, null, false);
$message->_email_from = CAppUI::conf("system CMessage default_email_from");
$message->_email_to   = CAppUI::conf("system CMessage default_email_to"  );

$smarty = new CSmartyDP();
$smarty->assign("message", $message);
$smarty->assign("message_smtp", $message_smtp);
$smarty->assign("groups", $groups);
$smarty->display("inc_form_message.tpl");
