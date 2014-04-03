<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

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
function rmLock($lock_file){
  @unlink($lock_file);
}

register_shutdown_function("rmLock", $lock_file);

// LOCK //
touch($lock_file);

set_time_limit(600);
set_min_memory_limit("1024M");

//TRAITEMENT
$client_index = new CSearch();
//create a client
$client_index->createClient();
$client_index->loadIndex();
$data = $client_index->getDataTemporaryTable("50", null);
$client_index->bulkIndexing($data);


 // UNLOCK //
unlink($lock_file);