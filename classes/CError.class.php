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

define("E_JS_ERROR", 0);

/**
 * Error manager
 */
class CError {
  const LOG_PATH = LOG_PATH;
  const LOG_SIZE_LIMIT = 5242880; // 1024*1024*5

  static $_excluded = array(
    E_STRICT,
    E_DEPRECATED,        // BCB
    E_RECOVERABLE_ERROR, // Thrown by bad type hinting, to be removed
  );

  /**
   * @var array
   */
  static $_types = array(
    "exception"         => "exception",
    E_ERROR             => "error",
    E_WARNING           => "warning",
    E_PARSE             => "parse",
    E_NOTICE            => "notice",
    E_CORE_ERROR        => "core_error",
    E_CORE_WARNING      => "core_warning",
    E_COMPILE_ERROR     => "compile_error",
    E_COMPILE_WARNING   => "compile_warning",
    E_USER_ERROR        => "user_error",
    E_USER_WARNING      => "user_warning",
    E_USER_NOTICE       => "user_notice",
    E_STRICT            => "strict",
    E_RECOVERABLE_ERROR => "recoverable_error",
    E_DEPRECATED        => "deprecated",
    E_USER_DEPRECATED   => "user_deprecated",
    E_JS_ERROR          => "js_error",
  );

  static $_classes = array (
    E_ERROR             => "big-error",   // 1
    E_WARNING           => "big-warning", // 2
    E_PARSE             => "big-info",    // 4
    E_NOTICE            => "big-info",    // 8
    E_CORE_ERROR        => "big-error",   // 16
    E_CORE_WARNING      => "big-warning", // 32
    E_COMPILE_ERROR     => "big-error",   // 64
    E_COMPILE_WARNING   => "big-warning", // 128
    E_USER_ERROR        => "big-error",   // 256
    E_USER_WARNING      => "big-warning", // 512
    E_USER_NOTICE       => "big-info",    // 1024
    E_STRICT            => "big-info",    // 2048
    E_RECOVERABLE_ERROR => "big-error",   // 4096
    E_DEPRECATED        => "big-info",    // 8192
    E_USER_DEPRECATED   => "big-info",    // 16384
    // E_ALL = 32767 (PHP 5.4)
    E_JS_ERROR          => "big-warning javascript",// 0
  );

  static $_categories = array (
    "exception"         => "warning",
    E_ERROR             => "error",
    E_WARNING           => "warning",
    E_PARSE             => "error",
    E_NOTICE            => "notice",
    E_CORE_ERROR        => "error",
    E_CORE_WARNING      => "warning",
    E_COMPILE_ERROR     => "error",
    E_COMPILE_WARNING   => "warning",
    E_USER_ERROR        => "error",
    E_USER_WARNING      => "warning",
    E_USER_NOTICE       => "notice",
    E_STRICT            => "notice",
    E_RECOVERABLE_ERROR => "error",
    E_DEPRECATED        => "notice",
    E_USER_DEPRECATED   => "notice",
    E_JS_ERROR          => "warning",
  );

  /**
   * Get error types by level : error, warning and notice
   *
   * @return array
   */
  static function getErrorTypesByCategory(){
    $categories = array(
      "error"   => array(),
      "warning" => array(),
      "notice"  => array(),
    );

    foreach (self::$_categories as $_type => $_category) {
      $categories[$_category][] = self::$_types[$_type];
    }

    return $categories;
  }

  /**
   * Create a link to open the file in an IDE
   *
   * @param string $file File to open in the IDE
   * @param int    $line Line number
   *
   * @return string
   */
  static function openInIDE($file, $line = null) {
    global $dPconfig;

    $url = null;

    $ide_url = (!empty($dPconfig["dPdeveloppement"]["ide_url"]) ? $dPconfig["dPdeveloppement"]["ide_url"] : false);
    if ($ide_url) {
      $url = str_replace("%file%", urlencode($file), $ide_url).":$line";
    }
    else {
      $ide_path = (!empty($dPconfig["dPdeveloppement"]["ide_path"]) ? $dPconfig["dPdeveloppement"]["ide_path"] : false);
      if ($ide_path) {
        $url = "ide:".urlencode($file).":$line";
      }
    }

    if ($url) {
      return "<a target=\"ide-launch-iframe\" href=\"$url\">$file</a>";
    }

    return $file;
  }
}
