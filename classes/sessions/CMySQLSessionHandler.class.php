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
  private $lock_timeout = 30;

  private $lifetime;

  private $mutex_type;

  /** @var CMbMutex */
  private $mutex;

  /**
   * @see parent::init()
   */
  function init() {
    $this->mutex_type = @CAppUI::conf("session_handler_mutex_type");

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
    if ($this->mutex) {
      $this->mutex->release();
    }
    else {
      $ds = self::$ds;
      $query = $ds->prepare("SELECT RELEASE_LOCK(%1)", $this->lock_name);

      if (!$ds->query($query)) {
        return false;
      }
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

    // Init the right mutex type
    $mutex = null;
    switch ($this->mutex_type) {
      case "files":
        $mutex = new CMbFileMutex($this->lock_name);
        break;

      case "system":
        $mutex = new CMbMutex($this->lock_name);
        break;

      default:
        $query = $ds->prepare("SELECT GET_LOCK(%1, %2)", $this->lock_name, $this->lock_timeout);
        $ds->query($query);
        break;
    }

    if ($mutex) {
      $mutex->acquire($this->lock_timeout);
      $this->mutex = $mutex;
    }

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

    $query = "INSERT INTO session (`session_id`, `user_id`, `user_ip`, `user_agent`, `expire`, `data`)
      VALUES (%1, %2, %3, %4, %5, %6)
      ON DUPLICATE KEY UPDATE
        `data`   = %7,
        `expire` = %8";

    $query = $ds->prepare($query, $session_id, CAppUI::$instance->user_id, $user_ip, $user_agent, $expire, $data, $data, $expire);

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