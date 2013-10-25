<?php

/**
 * $Id$
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Source SFTP
 */
class CSourceSFTP extends CExchangeSource {

  /** @var integer Primary key */
  public $source_sftp_id;
  public $port;
  public $timeout;
  public $fileprefix;
  public $fileextension_write_end;
  public $fileextension;

  /** @var  CSFTP */
  public $_sftp;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "source_sftp";
    $spec->key    = "source_sftp_id";
    return $spec;  
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["port"]                    = "num default|22";
    $props["timeout"]                 = "num default|10";
    $props["fileprefix"]              = "str";
    $props["fileextension_write_end"] = "str";
    $props["fileextension"]           = "str";
    
    return $props;
  }

  /**
   * Init
   *
   * @return CSFTP
   */
  function init() {
    if ($this->_sftp) {
      return $this->_sftp;
    }

    $sftp = new CSFTP();
    $sftp->init($this);
    $this->_sftp = $sftp;

    return $sftp;
  }

  /**
   * @see parent::receive
   */
  function receive() {
      $sftp = $this->init();
      $sftp->connect();
      $path = $sftp->getCurrentDirectory();
      $path = $this->fileprefix ? "$path/$this->fileprefix" : $path;
      $files = $sftp->getListFiles($path);

    if (empty($files)) {
      throw new CMbException("Le répertoire ne contient aucun fichier");
    }

    return $files;
  }

  /**
   * @see parent::addFile
   */
  function addFile($file, $file_remote) {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->addFile($file, $file_remote);
  }

  /**
   * @see parent::changeDirectory
   */
  function changeDirectory($directory) {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->changeDirectory($directory);
  }

  /**
   * @see parent::getCurrentDirectory
   */
  function getCurrentDirectory() {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->getCurrentDirectory();
  }

  /**
   * @see parent::delFile
   */
  function delFile($file) {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->delFile($file);
  }

  /**
   * @see parent::renameFile
   */
  function renameFile($file, $new_name) {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->renameFile($file, $new_name);
  }

  /**
   * @see parent::getData
   */
  function getData($file) {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->getFile($file);
  }

  /**
   * @see parent::getListDirectory
   */
  function getListDirectory($directory = ".") {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->getListDirectory($directory);
  }

  /**
   * @see parent::getListFilesDetails
   */
  function getListFilesDetails($directory = ".") {
    $sftp = $this->init();
    $sftp->connect();
    return $sftp->getListFilesDetails($directory);
  }
}