<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'échange spécifié", UI_MSG_ERROR);
}

/** @var CSourceMLLP $exchange_source */
$exchange_source = CExchangeSource::get($exchange_source_name, "mllp", true, null, false);

if (!$exchange_source) {
  CAppUI::stepAjax("Aucune source d'échange disponible pour ce nom : '$exchange_source_name'", UI_MSG_ERROR);
}

if (!$exchange_source->host) {
  CAppUI::stepAjax("Aucun hôte pour la source d'échange : '$exchange_source_name'", UI_MSG_ERROR);
}

try {
  $exchange_source->getSocketClient();
  CAppUI::stepAjax("Connexion au serveur MLLP réussi");
  if ($ack = $exchange_source->getData()) {
    echo "<pre>$ack</pre>";
  }
} catch (Exception $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
}




