<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Use $dPconfig for both application and install wizard to use it
global $dPconfig, $rootName;
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
  private $dir = null;

  function __construct() {
    global $dPconfig;
    $this->dir = "{$dPconfig['root_dir']}/tmp/shared/";
  }

  function isReady() {
    if (!CMbPath::forceDir($this->dir)) {
      trigger_error("Shared memory could not be initialized, ensure that '$this->dir' is writable");
      CApp::rip();
    }
    return true;
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
    if (is_writable($this->dir.$key))
      return unlink($this->dir.$key);
    return false;
  }

  /*function clear() {
  	$files = glob($this->dir);
  	$ok = true;
  	 
  	foreach ($files as $file)
  	  unlink($file);
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
    if ($get = eaccelerator_get($key)) {
      return unserialize($get);
    }
    return false;
  }

  function put($key, $value) {
    return eaccelerator_put($key, serialize($value));
  }

  function rem($key) {
    return eaccelerator_rm($key);
  }

  /*function clear() {
    eaccelerator_list_keys();
    eaccelerator_clear();
    return true;
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
    return apc_fetch($key);
  }

  function put($key, $value) {
    return apc_store($key, $value);
  }

  function rem($key) {
    return apc_delete($key);
  }

  /*function clear() {
  	return apc_clear_cache('user');
  }*/
}

/** Shared memory container */
abstract class SHM {
  static private $engine = null;
  static private $prefix = null;
  static $availableEngines = array(
    "disk"         => "DiskSharedMemory",
    "eaccelerator" => "EAcceleratorSharedMemory",
    "apc"          => "APCSharedMemory",
  );
  
  static function init($engine = "disk", $prefix = "") {
    if (!isset(self::$availableEngines[$engine])) {
      $engine = "disk";
    }
    $engine = new self::$availableEngines[$engine];
    if (!$engine->isReady()) {
      $engine = new self::$availableEngines["disk"];
      $engine->isReady();
    }

    self::$prefix = "$prefix-";
    self::$engine = $engine;
  }
  
  static function get($key) {
    return self::$engine->get(self::$prefix.$key);
  }
  
  static function put($key, $value) {
    return self::$engine->put(self::$prefix.$key, $value);
  }
  
  static function rem($key) {
    return self::$engine->rem(self::$prefix.$key);
  }
}

SHM::init($dPconfig['shared_memory'], $rootName);
