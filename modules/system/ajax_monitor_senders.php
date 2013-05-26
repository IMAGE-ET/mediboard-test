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

// Chargement des senders
$sender  = new CViewSender();
$sender->active = "1";

/** @var CViewSender[] $senders */
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
