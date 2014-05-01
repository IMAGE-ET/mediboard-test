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
 * Alternative PHP Cache (APC) based Memory class
 */
class APCSharedMemory implements ISharedMemory {
  protected $_cache_key = "info";

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

  /**
   * @see parent::exists()
   */
  function exists($key) {
    return apc_exists($key);
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
    $cache_key = $this->_cache_key;

    $keys = array();
    foreach ($cache_list as $_cache) {
      $_key = $_cache[$cache_key];

      if (strpos($_key, $prefix) === 0) {
        $keys[] = substr($_key, $len);
      }
    }

    sort($keys);
    return $keys;
  }

  /**
   * @see parent::modDate()
   */
  function modDate($key) {
    $info = apc_cache_info("user");
    $cache_list = $info["cache_list"];
    $cache_key = $this->_cache_key;

    foreach ($cache_list as $_cache) {
      $_key = $_cache[$cache_key];

      if ($_key === $key) {
        return strftime(CMbDT::ISO_DATETIME, $_cache["mtime"]);
      }
    }

    return null;
  }
}