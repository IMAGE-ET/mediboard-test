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

require_once __DIR__."/SHM.class.php";

/**
 * Distributed shared memory container
 */
abstract class DSHM extends SHM {
  /**
   * Get a value from the shared memory, from the distributed cvache
   *
   * @param string $key The key of the value to get
   *
   * @return mixed
   */
  static function get($key) {
    return self::_get(true, $key);
  }

  /**
   * Save a value in the shared memory, from the distributed cache
   *
   * @param string $key      The key to pu the value in
   * @param mixed  $value    The value to put in the shared memory
   * @param bool   $compress Compress data
   *
   * @return bool
   */
  static function put($key, $value, $compress = false) {
    return self::_put(true, $key, $value, $compress);
  }

  /**
   * Remove a value from the distributed shared memory
   *
   * @param string $key The key to remove
   *
   * @return bool
   */
  static function rem($key) {
    return self::_rem(true, $key);
  }

  /**
   * Check if given key exists in distributed shared memory
   *
   * @param string $key Key to check
   *
   * @return bool
   */
  static function exists($key) {
    return self::_exists(true, $key);
  }

  /**
   * List all the keys in the shared memory
   *
   * @return array
   */
  static function listKeys() {
    return self::_listKeys(true);
  }

  /**
   * Remove a list of keys corresponding to a pattern (* is a wildcard)
   *
   * @param string $pattern Pattern with "*" wildcards
   *
   * @return int The number of removed key/value pairs
   */
  static function remKeys($pattern) {
    return self::_remKeys(true, $pattern);
  }

  /**
   * Get modification date of a distributed key
   *
   * @param string $key The key to get the modification date of
   *
   * @return string
   */
  static function modDate($key) {
    return self::_modDate(true, $key);
  }

  /**
   * Get information about key
   * Creation date, modification date, number of hits, size in memory, compressed or not
   *
   * @param string $key The key to get information about
   *
   * @return array
   */
  static function info($key) {
    return self::_info(true, $key);
  }
}
