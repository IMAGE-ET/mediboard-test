<?php /* $Id: ui.class.php 8520 2010-04-09 14:27:59Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 8520 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The true application class
 */
class CApp {
  static $inPeace = false;
  
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectedly
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      trigger_error("Application died unexpectedly", E_USER_ERROR);      
    }
  }
  
  /**
   * Make application die properly
   */
  static function rip() {
    self::$inPeace = true;
    die;
  }
  
  /**
   * Outputs JSON data after removing the Output Buffer, with a custom mime type
   * @param object $data The data to output
   * @param string $mimeType [optional] The mime type of the data, application/json by default
   * @return void
   */
  static function json($data, $mimeType = "application/json") {
    ob_clean();
    header("Content-Type: $mimeType");
    echo json_encode($data);
    self::rip();
  }
  
  /**
   * 
   * @param object $module The module name or the file path
   * @param object $file [optional] The file of the module, or null
   * @param object $arguments [optional] The GET arguments
   * @return string The fetched content
   */
  static function fetch($module, $file = null, $arguments = array()) {
    $save = array();
    foreach($arguments as $_key => $_value) {
      if (!isset($_GET[$_key])) continue;
      $save[$_key] = $_GET[$_key];
    }
    
    foreach($arguments as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    ob_start();
    if (isset($file)) {
      include("./modules/$module/$file.php");
    }
    else {
      include($module);
    }
    $output = ob_get_clean();
   
    foreach($save as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    return $output;
  }
}
?>