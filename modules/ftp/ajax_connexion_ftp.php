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

$ftp = new CFTP();
$ftp->init($exchange_source);

try {
  $ftp->testSocket();
  CAppUI::stepAjax("Connecté au serveur $ftp->hostname sur le port $ftp->port");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
  return;
}

try {
  $ftp->connect();
  CAppUI::stepAjax("Connecté au serveur $ftp->hostname et authentifié en tant que $ftp->username");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
  return;
}

if ($ftp->passif_mode) {
  CAppUI::stepAjax("Activation du mode passif");
}

$local_file = CAppUI::conf('root_dir')."/offline.php";
$remote_file = "offline.php";

try {
  $ftp->sendFile($local_file, $remote_file);
  CAppUI::stepAjax("Fichier source $local_file copié en fichier cible $remote_file");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
  return;
}

$destination_file = "tmp/offline.php";
try {
  $ftp->getFile($remote_file, $destination_file);
  CAppUI::stepAjax("Fichier source $remote_file récupéré en fichier cible $destination_file");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
  return;
}

try {
  $ftp->delFile($remote_file);
  CAppUI::stepAjax("Fichier $remote_file supprimé");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
  return;
}