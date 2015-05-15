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
$lock_file = CAppUI::getTmpPath("search_snapshot");
CMbPath::forceDir($lock_file);

$lock_file .= "/search_snapshot.lock";
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
$name_repository = CValue::get("name_repository");
$dir_repository  = CValue::get("dir_repository");
$name_snapshot   = CValue::get("name_snapshot");
try {
  $snapshot = new CSearchSnapshot();
  //create a client
  $snapshot->createClient();
  $snapshot->loadIndex();
  $snapshot->createSnapshot();
  //$snapshot->deleteIndexAndRestore($name_repository, $name_snapshot, true, true);
  $snapshot->snapshot($name_repository, CAppUI::getTmpPath($dir_repository), $name_snapshot);
  CAppUI::displayAjaxMsg("La création du snapshot de l'index s'est bien déroulée", UI_MSG_OK);
}
catch (Exception $e) {
  mbLog($e->getMessage());
  CAppUI::displayAjaxMsg("La création du snapshot de l'index a recontré un problème", UI_MSG_WARNING);
}