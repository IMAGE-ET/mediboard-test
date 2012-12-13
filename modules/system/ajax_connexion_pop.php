<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("CExchangeSource-error-noSourceName", UI_MSG_ERROR);
}
if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("CExchangeSource-error-noTestDefined", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name, "pop", true, null, false);

if (!$exchange_source->_id) {
  CAppUI::stepAjax("CExchangeSource-error-unsavedParameters", UI_MSG_ERROR);
}
$pop = new CPop($exchange_source);

if ($type_action == "connexion") {
  try {
    if ($pop->open()) {
      CAppUI::stepAjax("Connecté au serveur $exchange_source->host sur le port $exchange_source->port");
    }
  } catch(CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
  }
} else {
  CAppUI::stepAjax("Type de test non supporté : $type_action", UI_MSG_ERROR);
}
