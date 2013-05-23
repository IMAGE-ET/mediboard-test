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

// Use $dPconfig for both application and install wizard to use it
global $dPconfig, $rootName;
require_once dirname(__FILE__)."/CMbPath.class.php";

/**
 * Shared Memory interface
 */
interface ISharedMemory {

  /**
   * Initialize the shared memory
   * Returns true if shared memory is available
   *
   * @return bool
   */
  function init();

  /**
   * Get a variable from shared memory
   *
   * @param string $key Key of value to retrieve
   *
   * @return mixed the value, null if failed
   */
  function get($key);

  /**
   * Put a variable into shared memory
   *
   * @param string $key   Key of value to store
   * @param mixed  $value The value
   *
   * @return bool job-done
   */
  function put($key, $value);

  /**
   * Remove a variable from shared memory
   *
   * @param string $key Key of value to remove
   *
   * @return bool job-done
   */
  function rem($key);

  /**
   * Clears the shared memory
   *
   * @return bool job-done
   */
  //function clear();

  /**
   * Return the list of keys
   *
   * @param string $prefix The keys' prefix
   *
   * @return array Keys list
   */
  function listKeys($prefix);
}

/**
 * Disk based shared memory
 */
class DiskSharedMemory implements ISharedMemory {
  private $dir = null;

  /**
   * @see parent::__construct()
   */
  function __construct() {
    global $dPconfig;
    $this->dir = "{$dPconfig['root_dir']}/tmp/shared/";
  }

  /**
   * @see parent::init()
   */
  function init() {
    if (!CMbPath::forceDir($this->dir)) {
      trigger_error("Shared memory could not be initialized, ensure that '$this->dir' is writable");
      CApp::rip();
    }
    return true;
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    if (file_exists($this->dir.$key)) {
      return unserialize(file_get_contents($this->dir.$key));
    }
    return false;
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    return file_put_contents($this->dir.$key, serialize($value)) !== false;
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    if (is_writable($this->dir.$key)) {
      return unlink($this->dir.$key);
    }

    return false;
  }

  /*function clear() {
    $files = glob($this->dir);
    $ok = true;
     
    foreach ($files as $file)
      unlink($file);
  }*/

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix){
    $list = array_map("basename", glob($this->dir.$prefix."*"));
    $len = strlen($prefix);

    foreach ($list as &$_item) {
      $_item = substr($_item, $len);
    }

    return $list;
  }
}

/**
 * Alternative PHP Cache (APC) based Memory class
 */
class APCSharedMemory implements ISharedMemory {
  /**
   * @see parent::init()
   */
  function init() {
    return function_exists('apc_fetch') &&
           function_exists('apc_store') &&
           function_exists('apc_delete');
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    return apc_fetch($key);
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    return apc_store($key, $value);
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    return apc_delete($key);
  }

  /*function clear() {
    return apc_clear_cache('user');
  }*/

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix) {
    $info = apc_cache_info("user");
    $cache_list = $info["cache_list"];
    $len = strlen($prefix);

    $keys = array();
    foreach ($cache_list as $_cache) {
      $_key = $_cache["info"];
      if (strpos($_key, $prefix) === 0) {
        $keys[] = substr($_key, $len);
      }
    }

    return $keys;
  }
}

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
   * @param string $engine_name Engine type
   * @param string $prefix      Prefix to use
   *
   * @return void
   */
  static function init($engine_name = "disk", $prefix = "") {
    if (!isset(self::$availableEngines[$engine_name])) {
      $engine_name = "disk";
    }

    /** @var ISharedMemory $engine */
    $engine = new self::$availableEngines[$engine_name];
    if (!$engine->init()) {
      $engine = new self::$availableEngines["disk"];
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
}

/**
 * Memcached based Shared Memory
 */
class MemcachedSharedMemory implements ISharedMemory {
  /** @var Memcached|\Xenzilla\Memcached */
  public $conn;

  /**
   * Get Memcached servers addresses
   *
   * @return array
   */
  private function getServerAddresses(){
    global $dPconfig;

    $conf = $dPconfig["shared_memory_params"];

    $servers = preg_split("/\s*,\s*/", $conf);
    $list = array();
    foreach ($servers as $_server) {
      $list[] = explode(":", $_server);
    }
    return $list;
  }

  /**
   * @see parent::init()
   */
  function init() {
    if (class_exists('Memcached', false)) {
      $conn = new Memcached();

      $servers = $this->getServerAddresses();
      foreach ($servers as $_server) {
        $conn->addServer($_server[0], $_server[1]);
      }

      return (bool) $this->conn = $conn;
    }

    include dirname(__FILE__)."/../lib/xenzilla-memcached/Memcached.php";

    $conn = new \Xenzilla\Memcached();
    $conn->addServer("127.0.0.1", 11211);
    return (bool) $this->conn = $conn;
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    return $this->conn->get($key);
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    return $this->conn->set($key, $value);
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    return $this->conn->delete($key);
  }

  /*function clear() {
    return $this->conn->flush();
  }*/

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix) {
    // Memcached 2.0+
    if (method_exists($this->conn, "getAllKeys")) {
      return $this->conn->getAllKeys();
    }

    return array();
  }
}

/**
 * Redis based Shared Memory
 */
class RedisSharedMemory implements ISharedMemory {
  /** @var Yampee_Redis_Client */
  public $conn;

  /**
   * Get Redis server address
   *
   * @return array
   */
  private function getServerAddress(){
    global $dPconfig;

    $conf = $dPconfig["shared_memory_params"];
    return explode(":", $conf);
  }

  /**
   * @see parent::init()
   */
  function init() {
    include dirname(__FILE__)."/../lib/yampee-redis/autoloader.php";

    if (class_exists('Yampee_Redis_Client')) {
      $server = $this->getServerAddress();

      $this->conn = new Yampee_Redis_Client($server[0], $server[1]);
      $this->conn->connect();

      return true;
    }

    return false;
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    if ($this->conn->has($key)) {
      return unserialize($this->conn->get($key));
    }

    return null;
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    return $this->conn->set($key, serialize($value));
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    return $this->conn->remove($key);
  }

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix) {
    $cache_list = $this->conn->findKeys("*");
    $len = strlen($prefix);

    $keys = array();
    foreach ($cache_list as $_cache) {
      $_key = $_cache["info"];
      if (strpos($_key, $prefix) === 0) {
        $keys[] = substr($_key, $len);
      }
    }

    return $keys;
  }
}

SHM::init($dPconfig['shared_memory'], $rootName);
