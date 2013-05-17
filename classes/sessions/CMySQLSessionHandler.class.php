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
 * MySQL based session handler
 */
class CMySQLSessionHandler implements ISessionHandler {
  /** @var CMySQLDataSource */
  private static $ds;

  private $lock_name;
  private $lock_timeout = 300;

  private $lifetime;

  /**
   * @see parent::init()
   */
  function init() {
    return ini_set("session.save_handler", "user");
  }

  /**
   * @see parent::useUserHandler()
   */
  function useUserHandler() {
    return true;
  }

  /**
   * @see parent::open()
   */
  function open() {
    if (self::$ds = CSQLDataSource::get("std")) {
      return true;
    }

    return false;
  }

  /**
   * @see parent::close()
   */
  function close() {
    $ds = self::$ds;

    $query = $ds->prepare("SELECT RELEASE_LOCK(%1)", $this->lock_name);

    if (!$ds->query($query)) {
      return false;
    }

    return true;
  }

  /**
   * @see parent::read()
   */
  function read($session_id) {
    $ds = self::$ds;

    $this->lock_name = "session_$session_id";
    $this->lifetime = ini_get('session.gc_maxlifetime');

    $query = $ds->prepare("SELECT GET_LOCK(%1, %2)", $this->lock_name, $this->lock_timeout);
    $ds->query($query);

    $query = $ds->prepare("SELECT `data` FROM `session` WHERE `session_id` = %1 AND `expire` > %2", $session_id, time());
    $result = $ds->exec($query);

    if ($record = $ds->fetchAssoc($result)) {
      return $record['data'];
    }

    return '';
  }

  /**
   * @see parent::write()
   */
  function write($session_id, $data) {
    $ds = self::$ds;

    $address = get_remote_address();
    $user_ip  = $address["remote"] ? inet_pton($address["remote"]) : null;
    $user_agent = CValue::read($_SERVER, "HTTP_USER_AGENT");
    $expire = time() + $this->lifetime;

    $query = "INSERT INTO session (`session_id`, `user_ip`, `user_agent`, `expire`, `data`)
      VALUES (%1, %2, %3, %4, %5)
      ON DUPLICATE KEY UPDATE
        `data`   = %6,
        `expire` = %7";

    $query = $ds->prepare($query, $session_id, $user_ip, $user_agent, $expire, $data, $data, $expire);

    if (!$ds->query($query)) {
      return false;
    }

    return true;
  }

  /**
   * @see parent::destroy()
   */
  function destroy($session_id) {
    $ds = self::$ds;

    $query = $ds->prepare("DELETE FROM `session` WHERE `session_id` = %", $session_id);

    if (!$ds->query($query)) {
      return false;
    }

    return true;
  }

  /**
   * @see parent::gc()
   */
  function gc($max) {
    $ds = self::$ds;

    $query = $ds->prepare("DELETE FROM `session` WHERE `expire` < %", time());

    if (!$ds->query($query)) {
      return false;
    }

    return true;
  }

  /**
   * @see parent::listSessions()
   */
  function listSessions() {
    return array();
  }

  /**
   * @see parent::setLifeTime()
   */
  function setLifeTime($lifetime) {
    $this->lifetime = $lifetime;
  }
}