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
  static $current = null;

  /**
   * Inform whether formerly cached value of caller fonction is available
   * 
   * @return bool
   */
  static function exist() {
    list($function, $args) = self::trace();
    if (!isset(self::$data[$function][$args])) {
      return false;
    }
    
    self::$current = self::$data[$function][$args];
    self::$hits[$function][$args]++;
    return true;    
  }

  /**
   * Try to get a formerly cached value of caller fonction
   * Should always be called after an self::exist call
   * 
   * @return mixed Cached value, null if no cached value available
   */
  static function get() {
    return self::$current;
  }

  /**
   * Set the cached value for function caller with hashed arguments
   * 
   * @param mixed $value Value cache
   * 
   * @return mixed Cached value, useful for chaining returns
   */
  static function set($value) {
    list($function, $args) = self::trace();
    self::$data[$function][$args] = $value;
    self::$hits[$function][$args] = 0;
    return $value;
  }
  
  /**
   * Get the function-args component of backtrace
   * 
   * @return array function and hashed arguments
   */
  static private function trace() {
    $trace = debug_backtrace(false);
    $caller = $trace[2];
    $function = $caller["function"];
    if (isset($caller["class"])) {
      $function = $caller["class"] . "::" . $function;
    }
    
    $args = implode("-", $caller["args"]);
    return array($function, $args);
  }
}
