<?php

/**
 * Send a mail concerning the diskfull problem while backuping
 *  
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$source = CExchangeSource::get("system-message", "smtp", true, null, true);

try {
  // Source init
  $source->init();
  $source->addTo (CAppUI::conf("system CMessage default_email_to"  ));
  $source->addBcc(CAppUI::conf("system CMessage default_email_from"));
  $source->addRe (CAppUI::conf("system CMessage default_email_from"));
  
  // Email subject
  $page_title = CAppUI::conf("page_title");
  $message_subject = CAppUI::tr("system-msg-backup-diskfull");
  $source->setSubject("$page_title - $message_subject");
      
  // Email body
  $message_body = CAppUI::tr("system-msg-backup-diskfull-desc");
  $body = "<strong>$page_title</strong>";
  $body.= "<p>$message_body</p>";
  $source->setBody($body);
      
  // Do send
  $source->send();
}
catch (CMbException $e) {
  $e->stepAjax();
}

CAppUI::stepAjax("system-msg-email_sent");
?>