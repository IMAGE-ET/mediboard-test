<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'échange spécifié", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

if (!$exchange_source) {
  CAppUI::stepAjax("Aucune source d'échange disponible pour ce nom : '$exchange_source_name'", UI_MSG_ERROR);
}

if (!$exchange_source->host) {
  CAppUI::stepAjax("Aucun hôte pour la source d'échange : '$exchange_source_name'", UI_MSG_ERROR);
}

$options = array(
  "encoding" => $exchange_source->encoding
);

$client = CMbSOAPClient::make($exchange_source->host, $exchange_source->user, $exchange_source->password, $exchange_source->type_echange, $options);
if (!$client || $client->soap_client_error) {
  CAppUI::stepAjax("Impossible de joindre la source de donnée : '$exchange_source_name'", UI_MSG_ERROR);
} 
else {
  CAppUI::stepAjax("Connecté à la source '$exchange_source_name'");
}