<?php 
/**
 * Send message
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

// Chargement de l'objet
/** @var CExchangeDataFormat $exchange */
$exchange = CMbObject::loadFromGuid($exchange_guid);

try {
  $exchange->send();
}
catch (CMbException $e) {
  $e->stepAjax(UI_MSG_ERROR);
}

CAppUI::stepAjax("CExchangeDataFormat-confirm-exchange sent", UI_MSG_OK, CAppUI::tr("$exchange->_class"));

