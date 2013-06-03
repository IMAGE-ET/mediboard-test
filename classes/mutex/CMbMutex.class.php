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
class CMbMutex implements IMbMutex {
  static $drivers = array(
    "CMbRedisMutex",
    "CMbAPCMutex",
    "CMbFileMutex",
  );

  /** @var CMbMutexDriver */
  private $driver;

  /**
   * @see parent::__construct()
   */
  function __construct($key, $label = null) {
    $driver = null;

    $config = CAppUI::conf("mutex_drivers");

    foreach (self::$drivers as $_driver_class) {
      if (empty($config[$_driver_class])) {
        continue;
      }

      try {
        /** @var IMbMutex $driver */
        $driver = new $_driver_class($key, $label);

        break;
      }
      catch (Exception $e) {
        continue;
      }
    }

    if ($driver) {
      $this->driver = $driver;
    }
    else {
      throw new CMbException("No mutex driver available");
    }
  }

  /**
   * Get driver object
   *
   * @return CMbMutexDriver|IMbMutex
   */
  function getDriver() {
    return $this->driver;
  }

  /**
   * @see parent::acquire()
   */
  function acquire($duration = self::DEFAULT_TIMEOUT, $poll_delay = self::DEFAULT_POLL_DELAY) {
    return $this->driver->acquire($duration, $poll_delay);
  }

  /**
   * @see parent::release()
   */
  function release() {
    $this->driver->release();
  }

  /**
   * Renders a failed acquisition message
   *
   * @return void
   */
  function failedMessage() {
    CAppUI::stepMessage(UI_MSG_OK, "CMbLock-failed-message", $this->key);
  }
}
