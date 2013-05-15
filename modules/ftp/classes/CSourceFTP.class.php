<?php

/**
 * Source FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSourceFTP extends CExchangeSource {
  // DB Table key
  public $source_ftp_id;

  // DB Fields
  public $port;
  public $timeout;
  public $pasv;
  public $mode;
  public $fileprefix;
  public $fileextension;
  public $filenbroll;
  public $fileextension_write_end;
  public $counter;

  // Form fields
  public $_source_file;
  public $_destination_file;
  public $_path;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_ftp';
    $spec->key   = 'source_ftp_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["port"]       = "num default|21";
    $specs["timeout"]    = "num default|5";
    $specs["pasv"]       = "bool default|0";
    $specs["mode"]       = "enum list|FTP_ASCII|FTP_BINARY default|FTP_ASCII";
    $specs["counter"]    = "str protected";
    $specs["fileprefix"] = "str";
    $specs["fileextension"] = "str";
    $specs["filenbroll"]    = "enum list|1|2|3|4";
    $specs["fileextension_write_end"] = "str";

    return $specs;
  }

  function init() {
    $ftp = new CFTP();
    $ftp->init($this);

    return $ftp;
  }

  function send($evenement_name = null, $destination_basename = null) {
    $ftp = $this->init($this);

    $this->counter++;

    if (!$destination_basename) {
      $destination_basename = sprintf("%s%0".$this->filenbroll."d", $this->fileprefix, $this->counter % pow(10, $this->filenbroll));
    }

    if ($ftp->connect()) {
      $ftp->sendContent($this->_data, "$destination_basename.$this->fileextension");
      if ($this->fileextension_write_end) {
        $ftp->sendContent($this->_data, "$destination_basename.$this->fileextension_write_end");
      }
      $ftp->close();

      $this->store();

      return true;
    }
  }

  function getACQ() {}

  function receive() {
    $ftp = $this->init();

    try {
      $ftp->connect();

      $files = array();
      $path = "$ftp->fileprefix/$this->_path";

      $files = $ftp->getListFiles($path);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    if (empty($files)) {
      throw new CMbException("Le répertoire '$path' ne contient aucun fichier");
    }

    $ftp->close();

    return $files;
  }

  function getData($path) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();

      $file = null;
      $temp = tempnam(sys_get_temp_dir(), "mb_");

      $file = $ftp->getFile($path, $temp);
    } catch (CMbException $e) {
      $e->stepAjax();
    }
    $ftp->close();

    $file_get_content = file_get_contents($file);

    unlink($temp);

    return $file_get_content;
  }

  function delFile($path, $directory) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();
      $ftp->changeDirectory($directory);

      $ftp->delFile($path);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();
  }

  function renameFile($oldname, $newname, $directory) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();
      $ftp->changeDirectory($directory);

      $ftp->renameFile($oldname, $newname);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();
  }

  function changeDirectory($directory_name) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();

      $ftp->changeDirectory($directory_name);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();
  }

  function getCurrentDirectory($directory = null) {
    $ftp = $this->init($this);
    if (!$directory) {
      $directory = $this->fileprefix;
    }
    try {
      $ftp->connect();
      if ($directory) {
        $ftp->changeDirectory($directory);
      }
      $curent_directory = $ftp->getCurrentDirectory();
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();

    return $curent_directory;
  }

  function getListFilesDetails($directory) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();

      $files = $ftp->getListFilesDetails($directory);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();

    return $files;
  }

  function addFile($file, $file_name, $directory) {
    $ftp = $this->init($this);

    try {
      $ftp->connect();
      $ftp->changeDirectory($directory);

      $ftp->addFile($file, $file_name);
    } catch (CMbException $e) {
      throw $e;
    }

    $ftp->close();

    return true;
  }

  function getListDirectory($current_directory = null) {
    $ftp = $this->init($this);
    if (!$current_directory) {
      $current_directory = $this->fileprefix;
    }
    try {
      $ftp->connect();

      $directories = $ftp->getListDirectory($current_directory);
    } catch (CMbException $e) {
      $e->stepAjax();
    }

    $ftp->close();

    return $directories;
  }

  function isReachableSource() {
    $ftp = new CFTP();
    $ftp->init($this);

    try {
      $ftp->testSocket();
    } 
    catch (CMbException $e) {
      $this->_reachable = 0;
      $this->_message   = $e->getMessage();
      return false;
    }
    return true;
  }

  function isAuthentificate() {
    $ftp = new CFTP();
    $ftp->init($this);

    try {
      $ftp->connect();
    } 
    catch (CMbException $e) {
      $this->_reachable = 0;
      $this->_message   = $e->getMessage();
      return false;
    }

    $ftp->close();

    return true;
  }

  function getResponseTime() {
    $this->_response_time = url_response_time($this->host, $this->port);
  }
}