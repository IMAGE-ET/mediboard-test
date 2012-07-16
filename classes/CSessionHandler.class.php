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
  * Session handler interface
  */
interface ISessionHandler {
  
  /**
   * Init the session handler
   * @return bool
   */
  function init();
  
  /**
   * Check if this handler use user's functions
   * @return bool
   */
  function useUserHandler();
  
  /**
   * Open the session
   * @return bool
   */
  function open();
  
  /**
   * Close the session
   * @return bool
   */
  function close();
  
  /**
   * Read the session
   * @param int session id
   * @return string string of the session
   */
  function read($id);
  
  /**
   * Write the session
   * @param int session id
   * @param string data of the session
   */
  function write($id, $data);
  
  /**
   * Destroy the session
   * @param int session id
   * @return bool
   */
  function destroy($id);
  
  /**
   * Garbage Collector
   * @param int life time (sec.)
   * @return bool
   * @see session.gc_divisor      100
   * @see session.gc_maxlifetime 1440
   * @see session.gc_probability    1
   * @usage execution rate 1/100
   *        (session.gc_probability/session.gc_divisor)
   */
  function gc($max);
  
  /**
   * List current sessions ids
   * @return array of the ids
   */
  function listSessions();
}

class CFilesSessionHandler implements ISessionHandler {
  function init() { return ini_set("session.save_handler", "files"); }
  function useUserHandler() { return false; }
  function open() { return false; }
  function close() { return false; }
  function read($id) { return false; }
  function write($id, $data) { return false; }
  function destroy($id) { return false; }
  function gc($max) { return false; }
  function listSessions() { return array(); }
}

class CMemcacheSessionHandler implements ISessionHandler {
  function init() { return ini_set("session.save_handler", "memcache"); }
  function useUserHandler() { return false; }
  function open() { return false; }
  function close() { return false; }
  function read($id) { return false; }
  function write($id, $data) { return false; }
  function destroy($id) { return false; }
  function gc($max) { return false; }
  function listSessions() { return array(); }
}

class CMySQLSessionHandler implements ISessionHandler {
  private static $ds;
  
  function init() {
    return ini_set("session.save_handler", "user");
  }
  
  function useUserHandler() {
    return true;
  }
  
  function open() {
    if (self::$ds = CSQLDataSource::get("std")) {
      return true;
    }
    
    return false;
  }
  
  function close() {
    return true;
  }
  
  function read($id) {
    $id = mysql_real_escape_string($id);
    $query = sprintf("SELECT `data` FROM `session` WHERE `session_id` = '%s'", $id);
    $result = self::$ds->query($query);
    if ($record = self::$ds->fetchAssoc($result)) {
        return $record['data'];
    }
    return '';
  }
  
  function write($id, $data) {
    $address = get_remote_address();
    
    $id                = mysql_real_escape_string($id);
    $date_modification = mysql_real_escape_string(time());
    $user_id           = mysql_real_escape_string(CUser::get()->_id);
    $user_ip           = mysql_real_escape_string($address["remote"] ? inet_pton($address["remote"]) : null);
    $user_agent        = mysql_real_escape_string(CValue::read($_SERVER, "HTTP_USER_AGENT"));
    $data              = mysql_real_escape_string($data);
    
    $query = sprintf("SELECT * FROM `session` WHERE `session_id` = '%s'", $id);
    $result = self::$ds->query($query);
    if ($record = self::$ds->numRows($result)) {
      $replace = sprintf("UPDATE `session` SET
        `date_modification` = '%s',
        `user_id`           = '%s',
        `user_ip`           = '%s',
        `user_agent`        = '%s',
        `data`              = '%s'
        WHERE `session_id` = '%s'",
        $date_modification,
        $user_id,
        $user_ip,
        $user_agent,
        $data,
        $id);
    }
    else {
      $replace = sprintf("INSERT INTO `session`
        VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
        $id,
        $date_modification,
        $date_modification,
        $user_id,
        $user_ip,
        $user_agent,
        $data);
    }
    return self::$ds->query($replace);
  }

  function destroy($id) {
    $query = sprintf("DELETE FROM `session` WHERE `session_id` = '%s'", mysql_real_escape_string($id));
    return self::$ds->query($query);
  }
  
  function gc($max) {
    $query = sprintf("DELETE FROM `sessions` WHERE `date_modification` < '%s'",
      mysql_real_escape_string(time() - $max));
    return self::$ds->query($query);
  }
  
  function listSessions() { return array(); }
}

 /**
  * Session handler container
  */
abstract class CSessionHandler {
  static private $engine = null;
  static $availableEngines = array(
    "files"    => "CFilesSessionHandler",
    "memcache" => "CMemcacheSessionHandler",
    "mysql"    => "CMySQLSessionHandler",
  );
  
  /**
   * Init the correct session handler
   */
  static function setHandler($engine = "files") {
    if (!isset(self::$availableEngines[$engine])) {
      $engine = "files";
    }
    $engine = new self::$availableEngines[$engine];
    if (!$engine->init()) {
      $engine = new self::$availableEngines["files"];
      $engine->init();
    }
    if ($engine->useUserHandler()) {
      session_set_save_handler(
        array("CSessionHandler", "open"),
        array("CSessionHandler", "close"),
        array("CSessionHandler", "read"),
        array("CSessionHandler", "write"),
        array("CSessionHandler", "destroy"),
        array("CSessionHandler", "gc"));
    }
    self::$engine = $engine;
  }
  
  static function open() {
    return self::$engine->open();
  }
  
  static function close() {
    return self::$engine->close();
  }
  
  static function read($id) {
    return self::$engine->read($id);
  }
  
  static function write($id, $data) {
    return self::$engine->write($id, $data);
  }
  
  static function destroy($id) {
    return self::$engine->destroy($id);
  }
  
  static function gc($max) {
    return self::$engine->gc($max);
  }
}

global $dPconfig;
CSessionHandler::setHandler($dPconfig["session_handler"]);
//mbTrace(get_class(CSessionHandler::$engine));

?>