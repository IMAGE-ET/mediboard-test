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
class CMbFileMutex extends CMbMutexDriver {
  protected $process;
  protected $path;
  protected $filename;

  /**
   * @see parent::__construct()
   */
  function __construct($key, $label = null) {
    parent::__construct($key, $label);

    $this->path = CAppUI::conf("root_dir")."/tmp/locks";
    $this->process = getmypid();
    $this->filename = "$this->path/".$this->getLockKey();

    CMbPath::forceDir(dirname($this->filename));
  }

  /**
   * @see parent::release()
   */
  function release() {
    if ($this->canRelease()) {
      $this->removeFile();
    }
  }

  /**
   * @see parent::setLock()
   */
  protected function setLock($duration){
    $exists = $this->fileExists();

    if ($exists) {
      return false;
    }

    $this->touchFile();

    return true;
  }

  /**
   * @see parent::recover()
   */
  protected function recover($duration){
    if (!$this->fileExists()) {
      return true;
    }

    $timeout = $this->timeout($duration);

    if ($this->getFileMtime() + $timeout > $this->getTime()) {
      return false;
    }

    // Not really atomic :(
    $this->touchFile();

    $this->expire = $timeout;

    return true;
  }


  /**
   * Get file modification time
   *
   * @return int Unix timestamp
   */
  protected function getFileMtime(){
    @clearstatcache(true, $this->filename);
    return (int) @filemtime($this->filename); // Can fail if file was deleted, will return 0
  }

  /**
   * Tells if file exists
   *
   * @return bool
   */
  protected function fileExists(){
    @clearstatcache(true, $this->filename);
    return file_exists($this->filename);
  }

  /**
   * Update or create file
   *
   * @return void
   */
  protected function touchFile(){
    touch($this->filename);
  }

  /**
   * Remove file
   *
   * @return void
   */
  protected function removeFile(){
    if ($this->fileExists()) {
      unlink($this->filename);
    }
  }
}
