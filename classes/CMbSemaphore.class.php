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
class CMbSemaphore {
  public $key;
  public $process;
  public $path;
  
  /**
   * CMbSemaphore Constructor
   *
   * @param string $key semaphore identifier
   */
  function __construct($key) {
    $this->path = CAppUI::conf("root_dir")."/tmp/locks";
    CMbPath::forceDir($this->path);
    $this->process = getmypid();
    $this->key = $key;
  }

  /**
   * Acquire the semaphore by putting a lock on it
   *
   * @param float $timeout The max time in seconds to acquire the semaphore (max 10s)
   * @param float $step    The step between each acquire attempt in seconds (max 10s)
   *
   * @return float Waiting time in seconds
   */
  function acquire($timeout = 10.0, $step = 0.1) {
    $i       = 0;
    $timeout = intval(min($timeout, 10) * 1000000);
    $step    = intval(min($step   , 10) * 1000000);
    
    $this->key = fopen("$this->path/$this->key", "w+");
    while (!flock($this->key, LOCK_EX + LOCK_NB) && $i < $timeout) {
      usleep($step);
      $i += $step;
    }
    
    return $i / 1000000;
  }
  
  /**
   * Release the lock on the semaphore
   *
   * @return boolean the job is done
   */
  function release() {
    return flock($this->key, LOCK_UN);
  }
}
