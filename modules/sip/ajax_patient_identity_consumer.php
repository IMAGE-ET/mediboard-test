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

$cn_receiver_guid = CValue::sessionAbs("cn_receiver_guid");

/** @var CReceiverHL7v2 $receiver_hl7v2 */
if ($cn_receiver_guid) {
  $receiver_hl7v2 = CStoredObject::loadFromGuid($cn_receiver_guid);
}
else {
  $receiver_hl7v2           = new CReceiverHL7v2();
  $receiver_hl7v2->actif    = 1;
  $receiver_hl7v2->group_id = CGroups::loadCurrent()->_id;
  $receiver_hl7v2->loadObject();
}

if (!$receiver_hl7v2 || !$receiver_hl7v2->_id) {
  CAppUI::stepAjax("No receiver", UI_MSG_WARNING);
  return;
}

CAppUI::stepAjax("From: ".$receiver_hl7v2->nom);

$profil      = "PIX";
$transaction = "ITI9";
$message     = "QBP";
$code        = "Q23";

$patient = new CPatient();

$iti_handler = new CITIDelegatedHandler();
if (!$iti_handler->isMessageSupported($transaction, $message, $code, $receiver_hl7v2)) {
  CAppUI::stepAjax("No receiver supports this", UI_MSG_WARNING);
  return;
}

$patient->_receiver                = $receiver_hl7v2;
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

$objects = array();
if ($ack_data) {
  $hl7_message = new CHL7v2Message;
  $hl7_message->parse($ack_data);

  $xml = $hl7_message->toXML();
  $xpath = new DOMXPath($xml);

  // Patient
  $_pids = $xpath->query("//PID");
  foreach ($_pids as $_element) {
    $ids = $xpath->query("PID.3", $_element);
    $_ids = array();
    foreach ($ids as $_id) {
      $_domain_parts = array();
      foreach ($xpath->query("CX.4/*", $_id) as $_domain_part) {
        $_domain_parts[] = $_domain_part->nodeValue;
      }

      $_ids[] = array(
        "id"     => $xpath->query("CX.1", $_id)->item(0)->nodeValue,
        "domain" => $_domain_parts,
      );
    }

    $objects[] = $_ids;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->display("inc_list_identifiers.tpl");


CApp::rip();
