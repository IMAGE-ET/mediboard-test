<?php

/**
 * Patient identity consumer
 *
 * @category SIP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$cn_receiver_guid = trim(CValue::getOrSessionAbs("cn_receiver_guid"));

$receiver  = new CReceiverHL7v2();
$objects = CReceiverHL7v2::getObjectsBySupportedEvents(array("CHL7EventQBPQ23"), $receiver);

/** @var CInteropReceiver[] $receivers */
$receivers = array();
foreach ($objects as $event => $_receivers) {
  if (!$_receivers) {
    continue;
  }

  /** @var CInteropReceiver[] $_receivers */
  foreach ($_receivers as $_receiver) {
    $_receiver->loadRefGroup();
    $receivers[$_receiver->_guid] = $_receiver;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("receivers"       , $receivers);
$smarty->assign("patient"         , new CPatient());
$smarty->assign("cn_receiver_guid", $cn_receiver_guid);

$smarty->display("patient_identity_consumer.tpl");