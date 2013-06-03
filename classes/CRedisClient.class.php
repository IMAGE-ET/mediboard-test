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

CAppUI::requireLibraryFile("yampee-redis/autoloader", false);

if (!class_exists("Yampee_Redis_Client", false)) {
  return;
}

/**
 * Redis client
 */
class CRedisClient extends Yampee_Redis_Client {
  /**
   * Set a value without overwriting if it already exists
   *
   * @param string $key   Key
   * @param mixed  $value Value
   *
   * @return mixed
   */
  function setNX($key, $value) {
    return $this->send("SETNX", array($key, $value));
  }

  /**
   * Renames key to newkey if newkey does not yet exist.
   * It returns an error under the same conditions as RENAME.
   *
   * @param string $key     Key
   * @param string $new_key New key
   *
   * @return mixed
   */
  function renameNX($key, $new_key) {
    return $this->send("RENAMENX", array($key, $new_key));
  }

  /**
   * Set a timeout on key. After the timeout has expired, the key will automatically be deleted.
   *
   * @param string $key     Key
   * @param float  $seconds Seconds
   *
   * @return int 1 or 0
   */
  function expire($key, $seconds) {
    return $this->send("EXPIRE", array($key, $seconds));
  }

  /**
   * Atomic get / set
   *
   * @param string $key   Key
   * @param mixed  $value Value
   *
   * @return mixed The old value
   */
  function getSet($key, $value) {
    return $this->send("GETSET", array($key, $value));
  }

  /**
   * Start a transaction
   *
   * @return void
   */
  function multi() {
    $this->send("MULTI");
  }

  /**
   * Exec a transaction, atomically
   *
   * @return array An array of the results
   */
  function exec() {
    return $this->send("EXEC");
  }
}
