<?php

/**
 * Patient identity consumer
 *
 * @category SIP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: ajax_refresh_exchange.php 15880 2012-06-15 08:14:36Z phenxdesign $
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$person_id_number            = CValue::request("person_id_number");
$person_namespace_id         = CValue::request("person_namespace_id");
$person_universal_id         = CValue::request("person_universal_id");
$person_universal_id_type    = CValue::request("person_universal_id_type");
$person_identifier_type_code = CValue::request("person_identifier_type_code");

$domains_returned_namespace_id      = CValue::request("domains_returned_namespace_id");
$domains_returned_universal_id      = CValue::request("domains_returned_universal_id");
$domains_returned_universal_id_type = CValue::request("domains_returned_universal_id_type");

$receiver_hl7v2           = new CReceiverHL7v2();
$receiver_hl7v2->actif    = 1;
$receiver_hl7v2->group_id = CGroups::loadCurrent()->_id;

/** @var CReceiverHL7v2[] $receivers */
$receivers = $receiver_hl7v2->loadMatchingList();

$profil      = "PIX";
$transaction = "ITI9";
$message     = "QBP";
$code        = "Q23";

$patient = new CPatient();

$iti_handler = new CITIDelegatedHandler();
foreach ($receivers as $_receiver) {
  if (!$iti_handler->isMessageSupported($transaction, $message, $code, $_receiver)) {
    continue;
  }

  $patient->_receiver                = $_receiver;
  $patient->_patient_identifier_list = array(
    "person_id_number"            => $person_id_number,
    "person_namespace_id"         => $person_namespace_id,
    "person_universal_id"         => $person_universal_id,
    "person_universal_id_type"    => $person_universal_id_type,
    "person_identifier_type_code" => $person_identifier_type_code
  );
  $patient->_domains_returned  = array(
    "domains_returned_namespace_id"      => $domains_returned_namespace_id,
    "domains_returned_universal_id"      => $domains_returned_universal_id,
    "domains_returned_universal_id_type" => $domains_returned_universal_id_type,
  );

  // Envoi de l'évènement
  $ack_data = $iti_handler->sendITI($profil, $transaction, $message, $code, $patient);
}

if ($ack_data) {

}