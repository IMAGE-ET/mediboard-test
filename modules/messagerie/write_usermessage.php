<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPportail
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkRead();
$user = CUser::get();
$usermessage = new CUserMessage();
$usermessage->from    = $user->_id;
$usermessage->to      = CValue::get("to");
$usermessage->subject = CValue::get("subject");
$usermessage->load(CValue::getOrSession("usermessage_id"));
$usermessage->loadRefsFwd();

// Vérifiction de la première lecture par le destinataire
if ($usermessage->to == $user->_id && $usermessage->date_sent && ! $usermessage->date_read) {
  $usermessage->date_read = CMbDT::dateTime();
  $usermessage->store();
}

if ($usermessage->to) {
  $usermessage->loadRefUserTo();
}

// Historique des messages avec le destinataire
$where = array();
$where[] = "(usermessage.from = '$usermessage->from' AND usermessage.to = '$usermessage->to')".
           "OR (usermessage.from = '$usermessage->to' AND usermessage.to = '$usermessage->from')";

$historique = $usermessage->loadList($where, "date_sent DESC", "20");
CMbObject::massLoadFwdRef($historique, "from");
CMbObject::massLoadFwdRef($historique, "to");

foreach ($historique as $_mail) {
  $_mail->loadRefUserFrom(1);
  $_mail->loadRefUserTo(1);
}

// Initialisation de CKEditor
$templateManager = new CTemplateManager();
$templateManager->editor = "ckeditor";
$templateManager->simplifyMode = true;

if ($usermessage->date_sent) {
  $templateManager->printMode = true;
}

$templateManager->initHTMLArea();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("usermessage"   , $usermessage);
$smarty->assign("historique", $historique);

$smarty->display("write_usermessage.tpl");

?>