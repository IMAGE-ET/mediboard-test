<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Use $dPconfig for both application and install wizard to use it
global $dPconfig;
require_once "{$dPconfig['root_dir']}/classes/mbpath.class.php";

/**
 * Shared Memory interface
 */
interface ISharedMemory {

  /**
   * Returns true if shared memory is available
   * @return bool
   */
  function isReady();

  /**
   * Get a variable from shared memory
   * @param string $key of value to retrieve
   * @return mixed the value, null if failed
   */
  function get($key);

  /**
   * Put a variable into shared memory
   * @param string $key of value to store
   * @param mixed $value the value
   */
  function put($key, $value);

  /**
   * Remove a variable from shared memory
   * @param string $key of value to remove
   * @return bool job-done
   */
  function rem($key);

  /**
   * Clears the shared memory
   * @return bool job-done
   */
  //function clear();
}


class DiskSharedMemory implements ISharedMemory {
  var $dir = null;

  function __construct() {
    global $dPconfig;
    $this->dir = "{$dPconfig['root_dir']}/tmp/shared/";
  }

  function isReady() {
    return CMbPath::forceDir($this->dir);
  }

  function get($key) {
    if (file_exists($this->dir.$key)) {
      return unserialize(file_get_contents($this->dir.$key));
    }
    return false;
  }

  function put($key, $value) {
    return file_put_contents($this->dir.$key, serialize($value));
  }

  function rem($key) {
    return unlink($this->dir.$key);
  }

  /*function clear() {
  	$files = glob($this->dir);
  	$ok = true;
  	 
  	foreach ($files as $file) {
  	$filename = basename($file);
  	 
  	if (!unlink($file)) {
  	echo "<div class='error'>Impossible de supprimer l'entréee <i>$filename</i></div>";
  	$ok = false;
  	} else
  	echo "<div class='message'>Entrée <i>$filename</i> supprimée</div>";
  	}
  	return $ok;
  	}*/
}


/**
 * EAccelerator based Memory class
 */
class EAcceleratorSharedMemory implements ISharedMemory {

  function isReady() {
    return (function_exists('eaccelerator_get') &&
    function_exists('eaccelerator_put') &&
    function_exists('eaccelerator_rm'));
  }

  function get($key) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('eaccelerator_get')) {
      if ($get = eaccelerator_get($key)) {
        return unserialize($get);
      }
    }

    return null;
  }

  function put($key, $value) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('eaccelerator_put')) {
      return eaccelerator_put($key, serialize($value));
    }

    return false;
  }

  function rem($key) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('eaccelerator_rm')) {
      return eaccelerator_rm($key);
    }

    return false;
  }

  /*function clear() {
   if (function_exists('eaccelerator_clear')) {
   eaccelerator_clear();
   return true;
   }
   return false;
   }*/
}


/**
 * Alternative PHP Cache (APC) based Memory class
 */
class APCSharedMemory implements ISharedMemory {

  function isReady() {
    return (function_exists('apc_fetch') &&
    function_exists('apc_store') &&
    function_exists('apc_delete'));
  }

  function get($key) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('apc_fetch')) {
      return apc_fetch($key);
    }

    return false;
  }

  function put($key, $value) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('apc_store')) {
      return apc_store($key, $value);
    }

    return false;
  }

  function rem($key) {
    global $rootName;
    $key = "$rootName-$key";

    if (function_exists('apc_delete')) {
      return apc_delete($key);
    }

    return false;
  }

  /*function clear() {
  	if (function_exists('apc_clear_cache')) {
  	return apc_clear_cache('user');
  	}
  	return false;
  	}*/
}


// Shared Memory instance factory
switch ($dPconfig['shared_memory']) {
  case 'none' :
    $shm = new DiskSharedMemory;
    break;

  case 'eaccelerator' :
    $shm = new EAcceleratorSharedMemory;
    break;

  case 'apc' :
    $shm = new APCSharedMemory;
    break;

  default:
    trigger_error('Mode de mémoire partagée non reconnu', E_USER_ERROR);
    $shm = new DiskSharedMemory;
}


?>