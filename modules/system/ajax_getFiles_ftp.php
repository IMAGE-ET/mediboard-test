<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;

$can->needsAdmin();

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'échange spécifié", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

$ftp = new CFTP();
$ftp->init($exchange_source);

if (!$ftp->testSocket()) {
  CAppUI::stepAjax("Connexion au serveur $ftp->hostname' échouée", UI_MSG_WARNING);
} else {
  CAppUI::stepAjax("Connecté au serveur $ftp->hostname sur le port $ftp->port");
}

if (!$ftp->connect()) {
  CAppUI::stepAjax("Impossible de se connecter au serveur $ftp->hostname", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Connecté au serveur $ftp->hostname et authentifié en tant que $ftp->username");
}

if($ftp->passif_mode) {
  CAppUI::stepAjax("Activation du mode passif");
}

$list = $ftp->getListFiles();
if (!is_array($list)) {
  CAppUI::stepAjax("Impossible de lister les fichiers", UI_MSG_ERROR);
} else {
  CAppUI::stepAjax("Liste des fichiers du dossier");
  mbTrace($list);
}

?>