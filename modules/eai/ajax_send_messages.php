<?php

/**
 * Send message
 *
 * @category HprimXML
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: ajax_send_message.php 20185 2013-08-16 15:31:17Z lryo $
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_class = CValue::get("exchange_class");
$count          = CValue::get("count", 20);
$date_min       = CValue::get('date_min');
$date_max       = CValue::get('date_max');

if (!$date_min) {
  $date_min = CMbDT::dateTime("-1 day");
}

if (!$date_max) {
  $date_max = CMbDT::dateTime("+1 day");
}

$where = array();
if (!($limit = CAppUI::conf("eai max_files_to_process"))) {
  return;
}

/** @var CExchangeDataFormat $exchange */
$exchange = new $exchange_class;

$where['sender_id']               = "IS NULL";
$where['receiver_id']             = "IS NOT NULL";

$where['statut_acquittement']     = "IS NULL";
$where['message_valide']          = "= '1'";
$where['acquittement_valide']     = "!= '1'";
$where['acquittement_content_id'] = "IS NULL";
$where['statut_acquittement']     = "IS NULL";

$where['date_echange']            = "IS NULL";
$where["date_production"]         = "BETWEEN '$date_min' AND '$date_max'";

$where[] = "master_idex_missing = '0' OR master_idex_missing IS NULL";

$order = $exchange->_spec->key . " ASC";
$notifications = $exchange->loadList($where, $order, $limit);

// Effectue le traitement d'enregistrement des notifications sur lequel le cron vient de passer
// ce qui permet la gestion des doublons
foreach ($notifications as $notification) {
  /** @var CExchangeDataFormat $notification */
  $notification->date_echange = CMbDT::dateTime();
  $notification->store();
}

foreach ($notifications as $notification) {
  try {
    $notification->send();
  }
  catch (CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);

    $notification->date_echange = "";
    $notification->store();

    continue;
  }

  CAppUI::stepAjax("CExchangeDataFormat-confirm-exchange sent", UI_MSG_OK, CAppUI::tr("$notification->_class"));
}