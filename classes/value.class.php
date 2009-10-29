<?php /* $Id: mb_functions.php 7046 2009-10-13 12:29:24Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: 7046 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

abstract class CValue {
  static function read(&$array, $name, $default = null) {
    return isset($array[$name]) ? $array[$name] : $default;
  }
	
  static function first(){
    foreach(func_get_args() as $v)
      if ($v) return $v;
  }

  static function get($name, $default = null) {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
  }
  
  static function post($name, $default = null) {
    return isset($_POST[$name]) ? $_POST[$name] : $default;
  } 
	
  static function request($name, $default = null) {
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
  }
	
  static function session($name, $default = null) {
    return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
  }
	
  static function cookie($name, $default = null) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
  }
  
  static function getOrSession($name, $default = null) {
    global $m;
  
    if (isset($_GET[$name])) {
      $_SESSION[$m][$name] = $_GET[$name];
    }
    
    return self::read($_SESSION[$m], $name, $default);
  }
  
  static function postOrSession($name, $default = null) {
    global $m;
  
    if (isset($_POST[$name])) {
      $_SESSION[$m][$name] = $_POST[$name];
    }
    
    return self::read($_SESSION[$m], $name, $default);
  }
	
  static function getOrSessionAbs($name, $default = null) {
    if (isset($_GET[$name])) {
      $_SESSION[$name] = $_GET[$name];
    }
    
    return self::read($_SESSION, $name, $default);
  }
  
  static function postOrSessionAbs($name, $default = null) {
    if (isset($_POST[$name])) {
      $_SESSION[$name] = $_POST[$name];
    }
    
    return self::read($_SESSION, $name, $default);
  }
	
  static function setSession($name, $value = null) {
    global $m;
    $_SESSION[$m][$name] = $value;
  }
	
  static function setSessionAbs($name, $value = null) {
    $_SESSION[$name] = $value;
  }
}
