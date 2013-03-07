<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$operation_id = CValue::get("operation_id");

$operation = new COperation();
$operation->load($operation_id);

$praticien = $operation->loadRefChir();

$email = $praticien->_user_email;

if (!$email) {
  CAppUI::js("alert('".addslashes(CAppUI::tr("alert-praticien_email"))."')");
  CApp::rip();
}

$operation->loadRefPlageOp();

$exchange_source = CExchangeSource::get("mediuser-" . CAppUI::$user->_id, "smtp");

$exchange_source->init();

try {
  $exchange_source->setRecipient($email);
  
  // Création du token
  $token = new CViewAccessToken();
  $token->ttl_hours = 24;
  $token->user_id = $praticien->_id;
  $token->params = "m=planningOp&a=vw_edit_urgence&operation_id=$operation_id";
  
  if ($msg = $token->store()) {
    CAppUI::displayAjaxMsg($msg, UI_MSG_ERROR);
  }
  
  $url = $token->getUrl();
  
  // Lien vers la DHE
  $subject = CAppUI::conf("reservation subject_mail");
  $content = CAppUI::conf("reservation text_mail");
  
  $from = array(
  "[URL]",
  "[PRATICIEN - NOM]",
  "[PRATICIEN - PRENOM]",
  "[DATE INTERVENTION]",
  "[HEURE INTERVENTION]");
  
  $to = array(
   $url,
   $praticien->_user_last_name,
   $praticien->_user_first_name,
   CMbDT::dateToLocale(CMbDT::date($operation->_datetime_best)),
   CMbDT::transform($operation->_datetime_best, null, CAppUI::conf("time"))
  );
  
  $subject = str_replace($from, $to, $subject);
  $exchange_source->setSubject($subject);
  
  $content = str_replace($from, $to, $content);
  $content = nl2br(utf8_encode($content));
  $exchange_source->setBody($content);
  
  $exchange_source->send();
  $operation->envoi_mail = CMbDT::dateTime();
  
  if ($msg = $operation->store()) {
    CAppUI::displayAjaxMsg($msg, UI_MSG_ERROR);
  }
  
  CAppUI::displayAjaxMsg("Message envoyé");
} catch(phpmailerException $e) {
    CAppUI::displayAjaxMsg($e->errorMessage(), UI_MSG_WARNING);
} catch(CMbException $e) {
    $e->stepAjax();
}