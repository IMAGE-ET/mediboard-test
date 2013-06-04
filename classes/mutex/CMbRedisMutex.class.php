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

    $client = null;

    $list = $this->getServerAddresses();
    foreach ($list as $_server) {
      try {
        $client = new CRedisClient($_server[0], $_server[1]);
        $client->connect();
      }
      catch (Exception $e) {
        $client = null;
      }
    }

    if (!$client) {
      throw new Exception("No Redis server reachable");
    }

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
