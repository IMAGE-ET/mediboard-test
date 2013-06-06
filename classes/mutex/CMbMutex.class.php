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

  /** @var self[] */
  static $open_mutexes = array();

  /** @var CMbMutexDriver */
  private $driver;

  /** @var string Key */
  private $key;

  /**
   * @see parent::__construct()
   */
  function __construct($key, $label = null) {
    $this->key = $key;

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
   * Get the mutext key
   *
   * @return string
   */
  function getKey(){
    return $this->key;
  }

  /**
   * @see parent::acquire()
   */
  function acquire($duration = self::DEFAULT_TIMEOUT, $poll_delay = self::DEFAULT_POLL_DELAY) {
    self::$open_mutexes[$this->key] = $this;

    return $this->driver->acquire($duration, $poll_delay);
  }

  /**
   * @see parent::release()
   */
  function release() {
    unset(self::$open_mutexes[$this->key]);

    $this->driver->release();
  }

  /**
   * Renders a failed acquisition message
   *
   * @return void
   */
  function failedMessage() {
    CAppUI::stepMessage(UI_MSG_OK, "CMbLock-failed-message", $this->getKey());
  }

  /**
   * Release all mutexes on script end
   *
   * @return void
   */
  static function releaseMutexes(){
    foreach (self::$open_mutexes as $_mutex) {
      trigger_error(sprintf("Mutex '%s' was not released properly", $_mutex->getKey()), E_USER_NOTICE);
      $_mutex->release();
    }
  }
}

register_shutdown_function(array("CMbMutex", "releaseMutexes"));
