<?php

CCanDo::checkAdmin();

$exchange_guid = CValue::get("exchange_guid");

if (!$exchange_guid) {
  CAppUI::displayAjaxMsg("Pas d'objet passé en paramètre");
  CApp::rip();
}

/** @var CExchangeDataFormat $exchange */
$exchange = CMbObject::loadFromGuid($exchange_guid);

$master_IPP_missing = false;
$pattern = "===IPP_MISSING===";
if (!CValue::read($receiver->_configs, "send_not_master_IPP") && strpos($exchange->_message, $pattern) !== false) {
  $master_IPP_missing = true;
}

$master_NDA_missing = false;
$pattern = "===NDA_MISSING===";
if (!CValue::read($receiver->_configs, "send_not_master_NDA") && strpos($exchange->_message, $pattern) !== false) {
  $master_NDA_missing = true;
}

$patient = null;
$sejour  = null;
if ($exchange->object_class && $exchange->object_id) {
  $object = CMbObject::loadFromGuid("$exchange->object_class-$exchange->object_id");

  if ($object instanceof CPatient) {
    $patient = $object;
    $patient->loadIPP($exchange->group_id);
  }

  if ($object instanceof CSejour) {
    $sejour = $object;
    $sejour->loadNDA($exchange->group_id);
    $object->loadRefPatient()->loadIPP($exchange->group_id);

    $patient = $sejour->_ref_patient;
  }
}