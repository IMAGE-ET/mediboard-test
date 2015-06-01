<?php

/**
 * $Id$
 *
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
CCanDo::checkAdmin();
$lock_file = CAppUI::getTmpPath("search_indexing");
CMbPath::forceDir($lock_file);

$lock_file .= "/search_indexing.lock";
$lock_timeout = 100;

if (file_exists($lock_file)) {
  if (filemtime($lock_file) + $lock_timeout > time()) {
    echo "Script locked search_indexing\n";

    return;
  }
  else {
    echo "Lock file present, but too old, we continue search_indexing\n";
  }
}
/**
 * remove the lock
 *
 * @param string $lock_file The file which lock traitement
 *
 * @return void
 */
function rmLock($lock_file) {
  @unlink($lock_file);
}

register_shutdown_function("rmLock", $lock_file);

// LOCK //
touch($lock_file);

set_time_limit(600);
set_min_memory_limit("1024M");

//TRAITEMENT
try {
  $client_index = new CSearch();
  //create a client
  $client_index->createClient();
  $client_index->loadIndex();
  // Passage à l'indexation en tps réel pour améliorer la performance du bulk indexing
  $client_index->_index->setSettings(array("index" => array("refresh_interval" => "-1")));
  // récupère données de la table buffer avec le pas fournit en configuration
  $data = $client_index->getDataTemporaryTable(CAppUI::conf("search interval_indexing"), null);
  // on bulk index les data
  $client_index->bulkIndexing($data);
  CAppUI::displayAjaxMsg("L'indexation s'est correctement déroulée ", UI_MSG_OK);
  $error = "";

  // on remet le paramètre à défaut et on optimise l'index
  $client_index->_index->setSettings(array("index" => array("refresh_interval" => "1s")));
  $client_index->_index->optimize(array("max_num_segments" => "5"));
}
catch (Exception $e) {
  mbLog($e->getMessage());
  CAppUI::displayAjaxMsg("L'indexation a recontré un problème", UI_MSG_WARNING);
  $error = "index";
}


// UNLOCK //
unlink($lock_file);

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_configure_es.tpl");