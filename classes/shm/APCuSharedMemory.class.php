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

require_once __DIR__."/APCSharedMemory.class.php";

/**
 * Alternative PHP User Cache (APCu) based Memory class
 */
class APCuSharedMemory extends APCSharedMemory {
  protected $_cache_key = "key";

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
        return strftime(CMbDT::ISO_DATETIME, isset($_cache["mtime"]) ? $_cache["mtime"] : $_cache["modification_time"]);
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

    $cache_info = array(
      "creation_date"     => null,
      "modification_date" => null,
      "num_hits"          => null,
      "mem_size"          => null,
      "compressed"        => null
    );

    foreach ($user_cache["cache_list"] as $_cache_info) {
      if ($_cache_info["key"] == $key) {
        $cache_info["creation_date"]     = strftime(CMbDT::ISO_DATETIME, isset($_cache_info["ctime"]) ? $_cache_info["ctime"] : $_cache_info["creation_time"]);
        $cache_info["modification_date"] = strftime(CMbDT::ISO_DATETIME, isset($_cache_info["mtime"]) ? $_cache_info["mtime"] : $_cache_info["modification_time"]);
        $cache_info["num_hits"]          = $_cache_info["nhits"];
        $cache_info["mem_size"]          = $_cache_info["mem_size"];

        break;
      }
    }

    return $cache_info;
  }
}