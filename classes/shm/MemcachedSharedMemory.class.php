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
 * Memcached based Shared Memory
 */
class MemcachedSharedMemory implements ISharedMemory {
  /** @var Memcached|\Xenzilla\Memcached */
  public $conn;

  /**
   * @see parent::init()
   */
  function init() {
    if (class_exists('Memcached', false)) {
      $conn = new Memcached();

      $servers = SHM::getServerAddresses();
      foreach ($servers as $_server) {
        $conn->addServer($_server[0], $_server[1]);
      }

      return (bool) $this->conn = $conn;
    }

    //include __DIR__."/../../lib/xenzilla-memcached/Memcached.php";

    //$conn = new \Xenzilla\Memcached();
    //$conn->addServer("127.0.0.1", 11211);
    //return (bool) $this->conn = $conn;
    return false;
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    $value = $this->conn->get($key);

    if (!$value) {
      return null;
    }

    $value = unserialize($value);

    if (isset($value["content"])) {
      return $value["content"];
    }

    return null;
  }

  /**
   * @see parent::put()
   */
  function put($key, $value) {
    $data = array(
      "content" => $value,
      "ctime"   => time(),
    );

    return $this->conn->set($key, serialize($data));
  }

  /**
   * @see parent::rem()
   */
  function rem($key) {
    return $this->conn->delete($key);
  }

  /**
   * @see parent::exists()
   */
  function exists($key) {
    return $this->conn->get($key);
  }

  /*function clear() {
    return $this->conn->flush();
  }*/

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix) {
    // Memcached 2.0+
    if (method_exists($this->conn, "getAllKeys")) {
      return $this->conn->getAllKeys();
    }

    return array();
  }

  /**
   * @see parent::modDate()
   */
  function modDate($key) {
    $data = self::get($key);

    if (empty($data["ctime"])) {
      return null;
    }

    return strftime(CMbDT::ISO_DATETIME, $data["ctime"]);
  }
}
