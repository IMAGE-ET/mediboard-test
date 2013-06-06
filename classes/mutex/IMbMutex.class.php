<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id$
 */

/**
 * Manage locking files to deal with concurrency
 */
interface IMbMutex {
  const DEFAULT_TIMEOUT    = 300.0;  // seconds
  const DEFAULT_POLL_DELAY = 100000; // milliseconds (0.1 second)

  /**
   * Constructor
   *
   * @param string $key Mutex identifier
   */
  function __construct($key, $label = null);

  /**
   * Acquire the semaphore by putting a lock on it
   *
   * @param float $duration   The max time in seconds to acquire the semaphore (max 10s)
   * @param int   $poll_delay Poll delay in microseconds
   *
   * @return float|bool Time spent waiting, in seconds, or true/false of $duration is 0
   */
  function acquire($duration = self::DEFAULT_TIMEOUT, $poll_delay = self::DEFAULT_POLL_DELAY);

  /**
   * Releases the lock
   *
   * @return void
   */
  function release();
}
