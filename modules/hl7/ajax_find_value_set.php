<?php

/**
 * Find value set
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$value_set_type = CValue::get("value_set_type", "RetrieveValueSet");

$OID      = CValue::get("OID");
$version  = CValue::get("version");
$language = CValue::get("language");

if (!$OID) {
  return;
}

$receiver_hl7v3           = new CReceiverHL7v3();
$receiver_hl7v3->actif    = 1;
$receiver_hl7v3->group_id = CGroups::loadCurrent()->_id;

/** @var CReceiverHL7v3[] $receivers */
$receivers = $receiver_hl7v3->loadMatchingList();

$profil      = "SVS";
$transaction = CSVS::getTransaction($value_set_type);
$event_name  = CMbArray::get(CSVS::$evenements, $value_set_type);

/** @var CHL7v3Event $event */
$event              = new $event_name;
$event->_event_name = "ValueSetRepository_RetrieveValueSet";

$data = array(
  "OID"      => $OID,
  "version"  => $version,
  "language" => $language
);

$object = new CMbObject();
$object->_data = $data;

$headers = CHL7v3Adressing::createWSAddressing("urn:ihe:iti:2008:$value_set_type", "http://valuesetrepository/");

$ack       = null;
$error     = null;
$value_set = null;
foreach ($receivers as $_receiver) {
  if (!$_receiver->isMessageSupported($event_name)) {
    continue;
  }

  try {
    /** @var CHL7v3AcknowledgmentSVS $ack */
    $ack = $_receiver->sendEvent($event, $object, $headers, true);

    $value_set = $ack->getQueryAck();
  }
  catch (SoapFault $s) {
    $error = $s->getMessage();
  }
}

mbTrace($value_set);

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_result_find_value_set.tpl");