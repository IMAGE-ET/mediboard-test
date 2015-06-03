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
    $value = apc_fetch($key);
    return $value !== false ? $value : null;
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
    $info       = apc_cache_info("user");
    $cache_list = $info["cache_list"];
    $len        = strlen($prefix);
    $cache_key  = $this->_cache_key;

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
    $info       = apc_cache_info("user");
    $cache_list = $info["cache_list"];
    $cache_key  = $this->_cache_key;

    foreach ($cache_list as $_cache) {
      $_key = $_cache[$cache_key];

      if ($_key === $key) {
        return strftime(CMbDT::ISO_DATETIME, $_cache["mtime"]);
      }
    }

    return null;
  }

  /**
   * @see parent::info()
   */
  function info($key) {
    $user_cache = apc_cache_info("user");

    if (!$user_cache) {
      return false;
    }

    $cache_key = $this->_cache_key;

    $cache_info = array(
      "creation_date"     => null,
      "modification_date" => null,
      "num_hits"          => null,
      "mem_size"          => null,
      "compressed"        => null
    );

    foreach ($user_cache["cache_list"] as $_cache_info) {
      if ($_cache_info[$cache_key] == $key) {
        $cache_info["creation_date"]     = strftime(CMbDT::ISO_DATETIME, $_cache_info["creation_time"]);
        $cache_info["modification_date"] = strftime(CMbDT::ISO_DATETIME, $_cache_info["mtime"]);
        $cache_info["num_hits"]          = $_cache_info["num_hits"];
        $cache_info["mem_size"]          = $_cache_info["mem_size"];

        break;
      }
    }

    return $cache_info;
  }
}