<?php 
/**
 * $Id: CValue.class.php 16213 2012-07-24 14:16:26Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16213 $
 */

/**
 * Utility class for function calls cache
 * Will only work with functions and static method, no this context allowed
 * Function paramaters are also discremenent on cache key
 */
class CFunctionCache {
  static $data = array();
  static $hits = array();
  static $totals = array();
  static $total = 0;

  /**
   * Inform whether formerly cached value of caller fonction is available
   * 
   * @return bool
   */
  static function exist($context) {
    list($function, $args) = $context;
    $args = implode("-", $args);
    if (!isset(self::$data[$function][$args])) {
      return false;
    }
    
    return true;    
  }

  /**
   * Try to get a formerly cached value of caller fonction
   * 
   * @return mixed Cached value, null if no cached value available
   */
  static function get($context) {
    list($function, $args) = $context;
    $args = implode("-", $args);
    self::$total++;
    self::$totals[$function]++;
    self::$hits  [$function][$args]++;
    return self::$data[$function][$args];
  }

  /**
   * Set the cached value for function caller with hashed arguments
   * 
   * @param mixed $value Value cache
   * 
   * @return mixed Cached value, useful for chaining returns
   */
  static function set($context, $value) {
    list($function, $args) = $context;
    $args = implode("-", $args);
    self::$totals[$function] = 0;
    self::$hits  [$function][$args] = 0;
    self::$data  [$function][$args] = $value;
    return $value;
  }
}
