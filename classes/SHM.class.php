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

$dir = dirname(__FILE__);
require_once "$dir/CMbPath.class.php";
require_once "$dir/shm/ISharedMemory.class.php";

/**
 * Shared memory container
 */
abstract class SHM {
  const GZ = "__gz__";

  /** @var ISharedMemory */
  static private $engine;

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

    $engine_name = $dPconfig['shared_memory'];

    // Must be the same here and in CApp
    // We don't use CApp because it can be called in /install
    $root_dir = $dPconfig['root_dir'];
    $prefix = preg_replace("/[^\w]+/", "_", $root_dir);

    if (!isset(self::$availableEngines[$engine_name])) {
      $engine_name = "disk";
    }

    $dir = dirname(__FILE__);

    $class_name = self::$availableEngines[$engine_name];
    include_once "$dir/shm/$class_name.class.php";

    /** @var ISharedMemory $engine */
    $engine = new $class_name;

    if (!$engine->init()) {
      $class_name = self::$availableEngines["disk"];
      include_once "$dir/shm/$class_name.class.php";

      $engine = new $class_name;
      $engine->init();
    }

    self::$prefix = "$prefix-";
    self::$engine = $engine;
  }

  /**
   * Get a value from the shared memory
   *
   * @param string $key The key of the value to get
   *
   * @return mixed
   */
  static function get($key) {
    $value = self::$engine->get(self::$prefix.$key);

    // If data is compressed
    if (is_array($value) && isset($value[self::GZ])) {
      $value = unserialize(gzuncompress($value[self::GZ]));
    }

    return $value;
  }

  /**
   * Save a value in the shared memory
   *
   * @param string $key      The key to pu the value in
   * @param mixed  $value    The value to put in the shared memory
   * @param bool   $compress Compress data
   *
   * @return bool
   */
  static function put($key, $value, $compress = false) {
    if ($compress) {
      $value = array(
        self::GZ => gzcompress(serialize($value))
      );
    }

    return self::$engine->put(self::$prefix.$key, $value);
  }

  /**
   * Remove a valur from the shared memory
   *
   * @param string $key The key to remove
   *
   * @return bool
   */
  static function rem($key) {
    return self::$engine->rem(self::$prefix.$key);
  }

  /**
   * List all the keys in the shared memory
   *
   * @return array
   */
  static function listKeys() {
    return self::$engine->listKeys(self::$prefix);
  }

  /**
   * Remove a list of keys corresponding to a pattern (* is a wildcard)
   *
   * @param string $pattern Pattern with "*" wildcards
   *
   * @return int The number of removed key/value pairs
   */
  static function remKeys($pattern) {
    $list = self::listKeys();

    $char = chr(255);
    $pattern = str_replace("*", $char, $pattern);
    $pattern = preg_quote($pattern);
    $pattern = str_replace($char, ".+", $pattern);
    $pattern = "/^$pattern$/";

    $n = 0;
    foreach ($list as $_key) {
      if (preg_match($pattern, $_key)) {
        self::rem($_key);
        $n++;
      }
    }

    return $n;
  }

  /**
   * @see parent::modDate()
   */
  static function modDate($key) {
    return self::$engine->modDate(self::$prefix.$key);
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
