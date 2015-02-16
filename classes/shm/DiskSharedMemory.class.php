<?php 

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

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
   * Produce the path string based on key
   *
   * @param $key string
   *
   * @return string
   */
  private function __path($key) {
    return $this->dir . CMbPath::sanitizeBaseName($key);
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
    $path = $this->__path($key);
    if (file_exists($path)) {
      return unserialize(file_get_contents($path));
    }
    return null;
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    $path = $this->__path($key);
    return file_put_contents($path, serialize($value)) !== false;
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    $path = $this->__path($key);
    if (is_writable($path)) {
      return unlink($path);
    }

    return false;
  }

  /**
   * @see parent::exists()
   */
  function exists($key) {
    $path = $this->__path($key);
    return file_exists($path);
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

  /**
   * @see parent::modDate()
   */
  function modDate($key) {
    $path = $this->__path($key);
    clearstatcache(true, $path);
    if (!file_exists($path)) {
      return null;
    }

    return strftime(CMbDT::ISO_DATETIME, filemtime($path));
  }

  /**
   * @see parent::info()
   */
  function info($key) {
    $cache_info = array(
      "creation_date"     => null,
      "modification_date" => null,
      "num_hits"          => null,
      "mem_size"          => null,
      "compressed"        => null
    );

    $path = $this->__path($key);
    clearstatcache(true, $path);

    if (!file_exists($path)) {
      return false;
    }

    $stats = stat($path);

    $cache_info["modification_date"] = strftime(CMbDT::ISO_DATETIME, $stats["mtime"]);
    $cache_info["mem_size"]          = $stats["size"];

    return $cache_info;
  }
}