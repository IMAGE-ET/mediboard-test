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

$receiver_hl7v3           = new CReceiverHL7v3();
$receiver_hl7v3->actif    = 1;
$receiver_hl7v3->group_id = CGroups::loadCurrent()->_id;

/** @var CReceiverHL7v3[] $receivers */
$receivers = $receiver_hl7v3->loadMatchingList();

$profil      = "SVS";
$transaction = CSVS::getTransaction($value_set_type);
$event_name  = CMbArray::get(CSVS::$evenements, $value_set_type);

/** @var CHL7v3Event $event */
$event       = new $event_name;
$event->_event_name = "ValueSetRepository_RetrieveValueSet";

$data = array(
  "OID"      => $OID,
  "version"  => $version,
  "language" => $language
);

$object = new CMbObject();
$object->_data = $data;

$headers = CSVSAdressing::createWSAddressing()->saveXML();

mbTrace($headers);
foreach ($receivers as $_receiver) {
  mbTrace($_receiver->sendEvent($event, $object, $headers, true));
}