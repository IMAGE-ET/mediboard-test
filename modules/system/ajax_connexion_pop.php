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

switch ($type_action) {
  case 'connexion':
    try {
      if ($pop->open()) {
        CAppUI::stepAjax("CSourcePOP-info-connection-established", UI_MSG_OK, $exchange_source->host, $exchange_source->port);
      }
    } catch(CMbException $e) {
      $e->stepAjax(UI_MSG_WARNING);
    }
    break;

  case 'listBox':
    try {
      if ($pop->open()) {
        $boxes = imap_list($pop->_mailbox, $pop->_server, "*");
        foreach ($boxes as $_box) {
          echo str_replace($pop->_server, "", $_box).'<br/>';
        }
      }
    } catch(CMbException $e) {
      $e->stepAjax(UI_MSG_WARNING);
    }
    break;

  default:
    CAppUI::stepAjax("CExchange-unknown-test", UI_MSG_ERROR, $type_action);
    break;
}
