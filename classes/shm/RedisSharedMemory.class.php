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
 * Redis based Shared Memory
 */
class RedisSharedMemory implements ISharedMemory {
  /** @var CRedisClient */
  public $conn;

  /**
   * @see parent::init()
   */
  function init() {
    // Don't use autloader
    include_once __DIR__ . "/../CRedisClient.class.php";

    $client = null;

    $list = SHM::getServerAddresses();
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

    if ($client) {
      $this->conn = $client;
      return true;
    }

    return false;
  }

  /**
   * @see parent::get()
   */
  function get($key) {
    if (!$this->conn->has($key)) {
      return null;
    }

    $value = unserialize($this->conn->get($key));

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
    return $this->conn->remove($key);
  }

  /**
   * @see parent::exists()
   */
  function exists($key) {
    return $this->conn->has($key);
  }

  /**
   * @see parent::listKeys()
   */
  function listKeys($prefix) {
    $cache_list = $this->conn->findKeys("*");
    $len        = strlen($prefix);

    $keys = array();
    foreach ($cache_list as $_key) {
      if (strpos($_key, $prefix) === 0) {
        $keys[] = substr($_key, $len);
      }
    }

    return $keys;
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

  /**
   * @see parent::info()
   */
  function info($key) {
    return false;
  }
}