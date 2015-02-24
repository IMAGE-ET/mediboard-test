<?php

/**
 * Actor domain aed
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$actor_guid    = CValue::post("actor_guid");
$domain_id     = CValue::post("domain_id");
$disassociated = CValue::post("disassociated");
  
list($actor_class, $actor_id) = explode('-', $actor_guid);
  
$domain = new CDomain();
$domain->load($domain_id);

if ($disassociated == 1) {
  $domain->actor_id    = "";
  $domain->actor_class = "";  
}
else {
  $domain->actor_id    = $actor_id;
  $domain->actor_class = $actor_class;
}

if ($msg = $domain->store()) {
  CAppUI::stepAjax(CAppUI::tr("CDomain") . CAppUI::tr("CMbObject-msg-store-failed") . $msg, UI_MSG_ERROR);
}
else {
  ($disassociated == 1) ? CAppUI::stepAjax("CDomain-actor-disassociated-desc") : CAppUI::stepAjax("CDomain-actor-associated");
}

CApp::rip();