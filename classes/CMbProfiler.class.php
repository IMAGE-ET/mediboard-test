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
 * Mediboard profiling class
 *
 * Requires to install XHProf.io from https://github.com/staabm/xhprof.io/
 * Install instructions here : https://github.com/staabm/xhprof.io/blob/master/INSTALL.md
 */
class CMbProfiler {
  static $xhprof_path;
  static $config;

  protected $started = false;

  /**
   * Profiler constructor
   */
  public function __construct() {
    if (!extension_loaded("xhprof") || php_sapi_name() === "cli") {
      throw new Exception("The XHprof extension must be installed");
    }

    self::$xhprof_path = CAppUI::conf("dPdeveloppement profiling_xhprof_path");

    if (!self::$xhprof_path) {
      throw new Exception("You must configure the 'profiling_xhprof_path' config");
    }
  }

  /**
   * Start profiling
   *
   * @return bool
   */
  public function start(){
    // do not profile debugging sessions (ZendDebugger, XDebug)
    if (!empty($_COOKIE['start_debug'])) {
      return false;
    }

    self::$config = include self::$xhprof_path."/xhprof/includes/config.inc.php";

    // check the global enable switch
    if (isset(self::$config['profiler_enabled']) && !self::$config['profiler_enabled']) {
      return false;
    }

    $flags = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY;
    $options = array(
      'ignored_functions' => array(
        'call_user_func',
        'call_user_func_array',
      )
    );

    xhprof_enable($flags, $options);

    $this->started = true;

    return true;
  }

  /**
   * Stop profiling session
   *
   * @return void
   */
  public function stop(){
    if (!$this->started) {
      return;
    }

    $xhprof_data = xhprof_disable();

    require_once self::$xhprof_path."/xhprof/classes/data.php";

    $xhprof_data_obj = new \ay\xhprof\Data(self::$config['pdo']);
    $xhprof_data_obj->save($xhprof_data);
  }
}
