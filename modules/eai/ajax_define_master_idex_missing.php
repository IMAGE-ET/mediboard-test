<?php

/**
 * Define master idex missing
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

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

$smarty = new CSmartyDP();

$smarty->assign("exchange"          , $exchange);

$smarty->assign("patient"           , $patient);
$smarty->assign("master_IPP_missing", $master_IPP_missing);

$smarty->assign("sejour"            , $sejour);
$smarty->assign("master_NDA_missing", $master_NDA_missing);
$smarty->display("inc_define_master_idex_missing.tpl");