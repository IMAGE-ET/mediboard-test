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
class CMbLock {
  public $key;
  public $process;
  public $path;
  public $filename;

  /**
   * Construct
   *
   * @param string $key lock identifier
   */
  function __construct($key) {
    $this->path = CAppUI::conf("root_dir")."/tmp/locks";
    $this->process = getmypid();

    $prefix = CApp::getAppIdentifier();
    $this->key = "$prefix-lock-$key";

    $this->filename = "$this->path/$this->key";
    CMbPath::forceDir(dirname($this->filename));
  }

  /**
   * Try to acquire a lock file
   *
   * @param float $lock_lifetime The lock life time in seconds
   *
   * @return bool
   */
  function acquire($lock_lifetime = 300.0) {
    // No lock, we acquire
    // @todo Delete the mute operator as soon as PHP 5.2 not maintained
    @clearstatcache(true, $this->filename);
    if (!file_exists($this->filename)) {
      return touch($this->filename);
    }

    // File exists, we have to check lifetime
    $lock_mtime = filemtime($this->filename);

    // Lock file is not dead
    if ( (microtime(true) - $lock_mtime) <= $lock_lifetime ) {
      return false;
    }

    // Lock file too old
    $this->release();

    return $this->acquire();
  }

  /**
   * Release (delete) a lock file
   *
   * @return bool
   */
  function release() {
    // @todo Delete the mute operator as soon as PHP 5.2 not maintained
    @clearstatcache(true, $this->filename);
    if (file_exists($this->filename)) {
      return unlink($this->filename);
    }

    return true;
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
