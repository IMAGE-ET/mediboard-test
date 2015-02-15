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

/**
 * Utility class for function calls cache
 *
 * @deprecated Use Cache object instead
 */
class CFunctionCache {
  /**
   * Inform whether formerly cached value of caller fonction is available
   *
   * @param array $context Context as (methode name, array of params)
   *
   * @return bool
   * @deprecated Use Cache::exists() non-static method instead
   */
  static function exist($context) {
    list($function, $args) = $context;
    $cache = new Cache($function, $args, Cache::INNER);
    return $cache->exists();
  }

  /**
   * Try to get a formerly cached value of caller fonction
   *
   * @param array $context Context as (methode name, array of params)
   *
   * @return mixed Cached value, null if no cached value available
   * @deprecated Use Cache::get() non-static method instead
   */
  static function get($context) {
    list($function, $args) = $context;
    $cache = new Cache($function, $args, Cache::INNER);
    return $cache->get();
  }

  /**
   * Set the cached value for function caller with hashed arguments
   *
   * @param array $context Context as (methode name, array of params)
   * @param mixed $value Value cache
   * 
   * @return mixed Cached value, useful for chaining returns
   * @deprecated Use Cache::put() non-static method instead
   */
  static function set($context, $value) {
    list($function, $args) = $context;
    $cache = new Cache($function, $args, Cache::INNER);
    return $cache->put($value);
  }
}
