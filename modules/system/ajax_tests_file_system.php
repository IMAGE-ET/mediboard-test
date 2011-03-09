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
  CAppUI::stepAjax("Aucun nom de source spécifié", UI_MSG_ERROR);
}

if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("Aucun type de test spécifié", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name);

if ($type_action == "connexion") {
  try {
    $exchange_source->init();
  } catch (Exception $e) {
    CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("CSourceFileSystem-host-is-a-dir", UI_MSG_OK, $exchange_source->_path);
}
else if ($type_action == "getFiles") {
  try {
    $files = $exchange_source->receive();
  } catch (Exception $e) {
    CAppUI::stepAjax($e->getMessage(), UI_MSG_ERROR);
  }
  
  CAppUI::stepAjax("Le dossier '$exchange_source->_path' contient : ".CMbPath::countFiles($exchange_source->_path)." fichier(s)");
  
  mbTrace($files, "Fichiers : ");
}

?>