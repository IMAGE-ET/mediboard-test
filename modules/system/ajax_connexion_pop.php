<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("CExchangeSource-error-noSourceName", UI_MSG_ERROR);
}
if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("CExchangeSource-error-noTestDefined", UI_MSG_ERROR);
}

/** @var CSourcePOP $exchange_source */
$exchange_source = CExchangeSource::get($exchange_source_name, "pop", true, null, false);

if (!$exchange_source->_id) {
  CAppUI::stepAjax("CExchangeSource-error-unsavedParameters", UI_MSG_ERROR);
}
$pop = new CPop($exchange_source);

if ($type_action == "connexion") {
  try {
    if ($pop->open()) {
      CAppUI::stepAjax("CSourcePOP-info-connection-established", UI_MSG_OK, $exchange_source->host, $exchange_source->port);
    }
  } catch(CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
  }
}
else {
  CAppUI::stepAjax("CExchange-unknown-test", UI_MSG_ERROR, $type_action);
}
