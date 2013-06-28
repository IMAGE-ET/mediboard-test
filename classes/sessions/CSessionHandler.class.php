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

  static $availableEngines = array(
    "files"    => "CFilesSessionHandler",
    "memcache" => "CMemcacheSessionHandler",
    "mysql"    => "CMySQLSessionHandler",
  );

  /** @var Zebra_Session */
  static protected $session;

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
      CAppUI::requireLibraryFile("zebra_session/Zebra_Session");

      // Must use the MySQL connector (not MySQLi)
      $dataSource = new CMySQLDataSource();
      $dataSource->init("std");
      $link = $dataSource->link;

      // Auto add session_data table
      $query = <<<SQL
CREATE TABLE IF NOT EXISTS `session_data` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT '',
  `http_user_agent` VARCHAR(32) NOT NULL DEFAULT '',
  `session_data` LONGBLOB NOT NULL,
  `session_expire` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
      $dataSource->exec($query);

      self::$session = new Zebra_Session(
        null, // $session_lifetime
        null, // $gc_probability
        null, // $gc_divisor
        'mb', // $security_code, should be changed for UA spoofing
        'session_data',  // $table_name
        300,  // $lock_timeout
        $link // $link
      );

      self::$started = true;

      return;
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
   * Update the ZebraSession lifetime
   *
   * @param int $lifetime Session lifetime in seconds
   *
   * @return void
   */
  static function updateLifetime($lifetime) {
    if (CAppUI::conf("session_handler") == "zebra") {
      self::$session->session_lifetime = intval($lifetime);
      return;
    }

    self::$engine->setLifeTime($lifetime);
  }

  /**
   * Sets user defined session life time
   *
   * @return void
   */
  static function setUserDefinedLifetime() {
    // Update ZebraSession lifetime
    $prefSessionLifetime = intval(CAppUI::pref("sessionLifetime")) * 60;

    // If default pref, we use session.gc_maxlifetime php.ini value
    $session_gc_maxlifetime = intval(ini_get("session.gc_maxlifetime"));
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
