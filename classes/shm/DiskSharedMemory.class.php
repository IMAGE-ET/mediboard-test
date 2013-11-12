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

  /**
   * @see parent::exists()
   */
  function exists($key) {
    return file_exists($this->dir.$key);
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
    $filename = $this->dir.$key;
    clearstatcache(true, $filename);

    if (!file_exists($filename)) {
      return null;
    }

    return strftime(CMbDT::ISO_DATETIME, filemtime($filename));
  }
}