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
 * Redis based session handler
 */
class CRedisSessionHandler implements ISessionHandler {
  /** @var CRedisClient */
  private static $client;

  /** @var string */
  private static $prefix;

  private $lock_name;
  private $lock_timeout = 30;

  private $lifetime;

  /** @var CMbMutex */
  private $mutex;

  /** @var string */
  private $data_hash;

  /**
   * @see parent::init()
   */
  function init() {
    global $dPconfig;

    // Must be the same here and in CApp
    // We don't use CApp because it can be called in /install
    $root_dir = $dPconfig['root_dir'];
    $prefix = preg_replace("/[^\w]+/", "_", $root_dir);
    self::$prefix = "$prefix-session-";

    return ini_set("session.save_handler", "user");
  }

  /**
   * Get dictionary key
   *
   * @param string $session_id Session ID
   *
   * @return string
   */
  private function getKey($session_id) {
    return self::$prefix.$session_id;
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
    $client = null;

    $list = $this->getServerAddresses();
    foreach ($list as $_server) {
      try {
        $client = new CRedisClient($_server[0], $_server[1]);
        $client->connect();
        break;
      }
      catch (Exception $e) {
        $client = null;
      }
    }

    if (!$client) {
      return false;
    }

    self::$client = $client;

    return true;
  }

  /**
   * Get the list of server addresses
   *
   * @return array
   */
  private function getServerAddresses(){
    global $dPconfig;

    $conf = trim($dPconfig["mutex_drivers_params"]["CMbRedisMutex"]);

    $servers = preg_split("/\s*,\s*/", $conf);
    $list = array();
    foreach ($servers as $_server) {
      $list[] = explode(":", $_server);
    }
    return $list;
  }

  /**
   * @see parent::close()
   */
  function close() {
    $this->mutex->release();

    return true;
  }

  /**
   * @see parent::read()
   */
  function read($session_id) {
    $client = self::$client;

    $this->lock_name = "session_$session_id";
    $this->lifetime = ini_get('session.gc_maxlifetime');

    // Init the right mutex type
    $mutex = new CMbFileMutex($this->lock_name);
    $mutex->acquire($this->lock_timeout);
    $this->mutex = $mutex;

    $key = $this->getKey($session_id);

    if (!$client->has($key)) {
      return "";
    }

    $session = $client->get($key);

    if ($session) {
      $session = unserialize($session);
      $data = $session['data'];

      $this->data_hash = md5($data);

      return $data;
    }

    return "";
  }

  /**
   * @see parent::write()
   */
  function write($session_id, $data) {
    $client = self::$client;

    $address    = get_remote_address();
    $user_id    = CAppUI::$instance->user_id;
    $user_ip    = $address["remote"] ? inet_pton($address["remote"]) : null;

    $new_hash = md5($data);

    $key = $this->getKey($session_id);

    // If session is to be updated
    if ($this->data_hash || $this->data_hash !== $new_hash) {
      $session = array(
        "user_id" => $user_id,
        "user_ip" => $user_ip,
        "data"    => $data,
      );

      $client->set($key, serialize($session), $this->lifetime);
    }
    else {
      $client->expire($key, $this->lifetime);
    }

    return true;
  }

  /**
   * @see parent::destroy()
   */
  function destroy($session_id) {
    $key = $this->getKey($session_id);
    self::$client->remove($key);

    return true;
  }

  /**
   * @see parent::gc()
   */
  function gc($max) {
    // TTL is here for this ...
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
