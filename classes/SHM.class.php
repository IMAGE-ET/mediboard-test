<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

require_once __DIR__."/CMbPath.class.php";
require_once __DIR__."/shm/ISharedMemory.class.php";

/**
 * Shared memory container
 */
abstract class SHM {
  const GZ = "__gz__";

  /** @var ISharedMemory */
  static private $engine;

  /** @var ISharedMemory */
  static private $engineDistributed;

  /** @var string */
  static private $prefix;

  /**
   * Available engines
   *
   * @var array
   */
  static $availableEngines = array(
    "disk"      => "DiskSharedMemory",
    "apc"       => "APCSharedMemory",
    "apcu"      => "APCuSharedMemory",
    "memcached" => "MemcachedSharedMemory",
    "redis"     => "RedisSharedMemory",
  );

  /**
   * Initialize the shared memory
   *
   * @return void
   */
  static function init() {
    global $dPconfig;

    // Must be the same here and in CApp
    // We don't use CApp because it can be called in /install
    $root_dir = $dPconfig['root_dir'];
    $prefix = preg_replace("/[^\w]+/", "_", $root_dir);
    self::$prefix = "$prefix-";

    /* ----- Local shared memory ----- */
    $engine_name = $dPconfig['shared_memory'];
    if (!isset(self::$availableEngines[$engine_name])) {
      $engine_name = "disk";
    }

    $class_name = self::$availableEngines[$engine_name];
    include_once __DIR__."/shm/$class_name.class.php";

    /** @var ISharedMemory $engine */
    $engine = new $class_name;

    if (!$engine->init()) {
      $class_name = self::$availableEngines["disk"];
      include_once __DIR__."/shm/$class_name.class.php";

      $engine = new $class_name;
      $engine->init();
    }

    self::$engine = $engine;

    /* ----- Multi server shared memory ----- */
    $engine_name_distributed = $dPconfig['shared_memory_distributed'];
    if (!$engine_name_distributed || !isset(self::$availableEngines[$engine_name_distributed])) {
      $engine_name_distributed = $engine_name;
    }

    $class_name = self::$availableEngines[$engine_name_distributed];
    include_once __DIR__."/shm/$class_name.class.php";

    /** @var ISharedMemory $engine_distributed */
    $engine_distributed = new $class_name;

    if (!$engine_distributed->init()) {
      $class_name = self::$availableEngines["disk"];
      include_once __DIR__."/shm/$class_name.class.php";

      $engine_distributed = new $class_name;
      $engine_distributed->init();
    }

    self::$engineDistributed = $engine_distributed;
  }

  /**
   * Get a value from the shared memory
   *
   * @param bool   $distributed Distributed
   * @param string $key         Key to get
   *
   * @return mixed
   */
  protected static function _get($distributed, $key) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    $value = $engine->get(self::$prefix.$key);

    // If data is compressed
    if (is_array($value) && isset($value[self::GZ])) {
      $value = unserialize(gzuncompress($value[self::GZ]));
    }

    return $value;
  }

  /**
   * Get a value from the shared memory, locally
   *
   * @param string $key The key of the value to get
   *
   * @return mixed
   */
  static function get($key) {
    return self::_get(false, $key);
  }

  /**
   * Save a value in the shared memory
   *
   * @param bool   $distributed Distributed
   * @param string $key         The key to pu the value in
   * @param mixed  $value       The value to put in the shared memory
   * @param bool   $compress    Compress data
   *
   * @return bool
   */
  protected static function _put($distributed, $key, $value, $compress = false) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;

    if ($compress) {
      $value = array(
        self::GZ => gzcompress(serialize($value))
      );
    }

    return $engine->put(self::$prefix.$key, $value);
  }

  /**
   * Save a value in the shared memory, locally
   *
   * @param string $key      The key to pu the value in
   * @param mixed  $value    The value to put in the shared memory
   * @param bool   $compress Compress data
   *
   * @return bool
   */
  static function put($key, $value, $compress = false) {
    return self::_put(false, $key, $value, $compress);
  }

  /**
   * Remove a value from the shared memory
   *
   * @param bool   $distributed Distributed
   * @param string $key         The key to remove
   *
   * @return bool
   */
  protected static function _rem($distributed, $key) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    return $engine->rem(self::$prefix.$key);
  }

  /**
   * Remove a value from the local shared memory
   *
   * @param string $key The key to remove
   *
   * @return bool
   */
  static function rem($key) {
    return self::_rem(false, $key);
  }

  /**
   * Check if given key exists
   *
   * @param bool   $distributed Is distributed?
   * @param string $key         Key to check
   *
   * @return bool
   */
  protected static function _exists($distributed, $key) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    return $engine->exists(self::$prefix.$key);
  }

  /**
   * Check if given key exists in shared memory
   *
   * @param string $key Key to check
   *
   * @return bool
   */
  static function exists($key) {
    return self::_exists(false, $key);
  }

  /**
   * List all the keys in the shared memory
   *
   * @param bool $distributed Distributed
   *
   * @return array
   */
  protected static function _listKeys($distributed) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    return $engine->listKeys(self::$prefix);
  }

  /**
   * List all the keys in the shared memory
   *
   * @return array
   */
  static function listKeys() {
    return self::_listKeys(false);
  }

  /**
   * Remove a list of keys corresponding to a pattern (* is a wildcard)
   *
   * @param bool   $distributed Distributed
   * @param string $pattern     Pattern with "*" wildcards
   *
   * @return int The number of removed key/value pairs
   */
  protected static function _remKeys($distributed, $pattern) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    $list = $engine->listKeys(self::$prefix);

    $char = chr(255);
    $pattern = str_replace("*", $char, $pattern);
    $pattern = preg_quote($pattern);
    $pattern = str_replace($char, ".+", $pattern);
    $pattern = "/^$pattern$/";

    $n = 0;
    foreach ($list as $_key) {
      if (preg_match($pattern, $_key)) {
        $engine->rem(self::$prefix.$_key);
        $n++;
      }
    }

    return $n;
  }

  /**
   * Remove a list of keys corresponding to a pattern (* is a wildcard)
   *
   * @param string $pattern Pattern with "*" wildcards
   *
   * @return int The number of removed key/value pairs
   */
  static function remKeys($pattern) {
    return self::_remKeys(false, $pattern);
  }

  /**
   * Get modification date
   *
   * @param bool   $distributed Distributed
   * @param string $key         The key to get the modification date of
   *
   * @return string
   */
  protected static function _modDate($distributed, $key) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    return $engine->modDate(self::$prefix.$key);
  }

  /**
   * Get modification date of a local key
   *
   * @param string $key The key to get the modification date of
   *
   * @return string
   */
  static function modDate($key) {
    return self::_modDate(false, $key);
  }

  /**
   * Get information about key
   *
   * @param bool   $distributed Distributed
   * @param string $key         The key to get information about
   *
   * @return array
   */
  protected static function _info($distributed, $key) {
    $engine = $distributed ? self::$engineDistributed : self::$engine;
    return $engine->info(self::$prefix.$key);
  }

  /**
   * Get information about key
   * Creation date, modification date, number of hits, size in memory, compressed or not
   *
   * @param string $key Key
   *
   * @return array Information
   */
  static function info($key) {
    return self::_info(false, $key);
  }

  /**
   * Get servers addresses
   *
   * @return array
   */
  static function getServerAddresses(){
    global $dPconfig;

    $conf = $dPconfig["shared_memory_params"];

    $servers = preg_split("/\s*,\s*/", $conf);
    $list = array();
    foreach ($servers as $_server) {
      $list[] = explode(":", $_server);
    }

    return $list;
  }
}

SHM::init();
