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

$value_set_type = CValue::get("value_set_type", "ITI48");

$OID      = CValue::get("OID");
$version  = CValue::get("version");
$language = CValue::get("language");

$receiver_hl7v3           = new CReceiverHL7v3();
$receiver_hl7v3->actif    = 1;
$receiver_hl7v3->group_id = CGroups::loadCurrent()->_id;
/** @var CReceiverHL7v3[] $receivers */
$receivers = $receiver_hl7v3->loadMatchingList();

$profil      = "SVS";
$transaction = $value_set_type;

if ($transaction == "ITI48") {
  $message = "RetrieveValueSet";
}
else {
  $message = "RetrieveMultipleValueSets";
}

$iti_handler = new CITIDelegatedHandler();
foreach ($receivers as $_receiver) {
  if (!$iti_handler->isMessageSupported($transaction, $message, null, $_receiver)) {
    continue;
  }

  mbTrace($_receiver);
}