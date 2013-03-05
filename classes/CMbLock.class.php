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
  public $lock_file;
  public $lock_lifetime;

  /**
   * @param $lock_file
   * @param $lock_lifetime (in seconds)
   */
  function __construct($lock_file, $lock_lifetime) {
    $this->lock_file     = $lock_file;
    $this->lock_lifetime = $lock_lifetime;
  }

  /**
   * Try to acquire a lock file
   *
   * @return bool
   */
  function acquire() {
    // No lock, we acquire
    if (!file_exists($this->lock_file)) {
      return touch($this->lock_file);
    }

    // File exists, we have to check lifetime
    $lock_mtime = filemtime($this->lock_file);

    // Lock file is not dead
    if ( (microtime(true) - $lock_mtime) <= $this->lock_lifetime ) {
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
    return unlink($this->lock_file);
  }
}
