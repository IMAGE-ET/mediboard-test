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
 * Semaphore implementation to deal with concurrency
 */
class CMbRedisMutex extends CMbMutexDriver {
  /** @var CRedisClient */
  private $client;

  /**
   * @see parent::__construct()
   */
  function __construct($key, $label = null) {
    parent::__construct($key, $label);

    $client = new CRedisClient("127.0.0.1");
    $client->connect();

    $this->client = $client;
  }

  /**
   * @see parent::release()
   */
  public function release(){
    if ($this->canRelease()) {
      $this->client->remove($this->getLockKey());
    }
  }

  /**
   * @see parent::getLockKey()
   */
  protected function getLockKey(){
    global $rootName;
    return "$rootName-lock:{$this->key}";
  }

  /**
   * @see parent::setLock()
   */
  protected function setLock($duration){
    $key = $this->getLockKey();
    $tmp_key = uniqid("$key-");
    $client = $this->client;

    $client->multi(); // Start

    $client->setNX($tmp_key, 1);
    $client->expire($tmp_key, $duration);
    $client->renameNX($tmp_key, $key);
    $client->remove($tmp_key); // GC, if rename failed

    $ret = $client->exec();  // End

    return $ret[2] == 1; // renameNX result
  }

  /**
   * Never has to recover as keys are volatile
   *
   * @see parent::recover()
   */
  protected function recover($duration){
    return false;
  }
}
