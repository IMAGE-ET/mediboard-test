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
  * Session handler container
  */
abstract class CSessionHandler {
  /** @var ISessionHandler */
  static private $engine;

  /** @var bool Is the session started ? */
  static private $started = false;

  /** @var int Session life time is seconds */
  static private $lifetime;

  static $availableEngines = array(
    "files"    => "CFilesSessionHandler",
    "memcache" => "CMemcacheSessionHandler",
    "mysql"    => "CMySQLSessionHandler",
    "redis"    => "CRedisSessionHandler",
  );

  /**
   * Init the correct session handler
   *
   * @param string $engine_name Engine name
   *
   * @return void
   */
  static function setHandler($engine_name = "files") {
    // TODO remove Zebra
    if ($engine_name == "zebra") {
      $engine_name = "mysql";
    }

    if (!isset(self::$availableEngines[$engine_name])) {
      $engine_name = "files";
    }

    /** @var ISessionHandler $engine */
    $engine = new self::$availableEngines[$engine_name];

    if (!$engine->init()) {
      $engine = new self::$availableEngines["files"];
      $engine->init();
    }

    if ($engine->useUserHandler()) {
      session_set_save_handler(
        array("CSessionHandler", "onOpen"),
        array("CSessionHandler", "onClose"),
        array("CSessionHandler", "onRead"),
        array("CSessionHandler", "onWrite"),
        array("CSessionHandler", "onDestroy"),
        array("CSessionHandler", "onGC")
      );
    }

    self::$engine = $engine;
  }

  /**
   * Update the session lifetime
   *
   * @param int $lifetime Session lifetime in seconds
   *
   * @return void
   */
  static function updateLifetime($lifetime) {
    self::$lifetime = $lifetime;

    self::$engine->setLifeTime($lifetime);
  }

  /**
   * Get session life time
   *
   * @return int
   */
  static function getLifeTime(){
    return self::$lifetime;
  }

  /**
   * Sets user defined session life time
   *
   * @return void
   */
  static function setUserDefinedLifetime() {
    // Update session lifetime
    $prefSessionLifetime = intval(CAppUI::pref("sessionLifetime")) * 60;

    // If default pref, we use the PHP default value
    $session_gc_maxlifetime = self::getPhpSessionLifeTime();
    $sessionLifetime = null;

    if (!$prefSessionLifetime) {
      $sessionLifetime = $session_gc_maxlifetime;
    }
    elseif ($prefSessionLifetime < $session_gc_maxlifetime) {
      $sessionLifetime = $prefSessionLifetime;
    }

    self::updateLifetime($sessionLifetime);
  }

  /**
   * Get PHP default session life time value
   *
   * @return int
   */
  static function getPhpSessionLifeTime(){
    return intval(ini_get("session.gc_maxlifetime"));
  }

  /**
   * Session Open handler
   *
   * @return bool
   */
  static function onOpen() {
    return self::$engine->open();
  }

  /**
   * Session Open handler
   *
   * @return bool
   */
  static function onClose() {
    return self::$engine->close();
  }

  /**
   * Session Read handler
   *
   * @param string $id Session ID
   *
   * @return bool
   */
  static function onRead($id) {
    return self::$engine->read($id);
  }

  /**
   * Session Write handler
   *
   * @param string $id   Session ID
   * @param string $data Session data
   *
   * @return bool
   */
  static function onWrite($id, $data) {
    return self::$engine->write($id, $data);
  }

  /**
   * Session Destroy handler
   *
   * @param string $id Session ID
   *
   * @return bool
   */
  static function onDestroy($id) {
    return self::$engine->destroy($id);
  }

  /**
   * Session GC handler
   *
   * @param int $max Max life time
   *
   * @return bool
   */
  static function onGC($max) {
    return self::$engine->gc($max);
  }

  /**
   * Start the session
   *
   * @return void
   */
  static function start() {
    if (self::$started) {
      return;
    }

    session_start();
    self::$started = true;
  }

  /**
   * Ends the session
   *
   * @param bool $destroy Destroy the session data
   *
   * @return void
   */
  static function end($destroy = false){
    if (!self::$started) {
      return;
    }

    // Free the session data
    session_unset();

    if ($destroy) {
      @session_destroy(); // Escaped because of an unknown error
    }

    self::$started = false;
  }

  /**
   * Saves session and closes it
   *
   * @return void
   */
  static function writeClose(){
    if (!self::$started) {
      return;
    }

    session_write_close();

    self::$started = false;
  }

  /**
   * Writes session data (in fact it writes, closes and starts it back)
   *
   * @return void
   */
  static function write(){
    if (!self::$started) {
      return;
    }

    self::writeClose();
    self::start();

    self::$started = false;
  }

  /**
   * Tells if the session is currently open
   *
   * @return bool
   */
  static function isOpen(){
    return self::$started;
  }
}
