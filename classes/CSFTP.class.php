<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CAppUI::requireLibraryFile("phpseclib/phpseclib/Net/SFTP");
$root = CAppUI::conf("root_dir");
/**
 * PHPseclib test the inclusion of file with a relatif path
 */
set_include_path(get_include_path().PATH_SEPARATOR.$root.'/lib/phpseclib/phpseclib');
/**
 * Class SFTP
 */
class CSFTP {

  public $hostname;
  public $port;
  public $timeout;
  public $username;
  public $userpass;
  public $loggable;
  public $fileextension;
  public $fileextension_end;
  public $directory;

  /** @var Net_SFTP */
  public $connexion;

  private static $aliases = array(
  );

  /**
   * Magic method (do not call directly)
   *
   * @param string $name method name
   * @param array  $args arguments
   *
   * @return mixed
   *
   * @throws Exception
   * @throws CMbException
   */
  function __call($name, $args) {
    $name = strtolower($name);
    $silent = strncmp($name, 'try', 3) === 0;
    $function_name = $silent ? substr($name, 3) : $name;
    $function_name = '_' . (isset(self::$aliases[$function_name]) ? self::$aliases[$function_name] : $function_name);

    if (!method_exists($this, $function_name)) {
      throw new CMbException("CSourceSFTP-call-undefined-method", $name);
    }

    if ($function_name == "_init") {
      return call_user_func_array(array($this, $function_name), $args);
    }

    if (!$this->loggable) {
      try {
        return call_user_func_array(array($this, $function_name), $args);
      }
      catch(CMbException $fault) {
        throw $fault;
      }
    }

    $echange_ftp = new CExchangeFTP();
    $echange_ftp->date_echange = CMbDT::dateTime();
    $echange_ftp->emetteur     = CAppUI::conf("mb_id");
    $echange_ftp->destinataire = $this->hostname;

    $echange_ftp->function_name = $name;

    CApp::$chrono->stop();
    $chrono = new Chronometer();
    $chrono->start();
    $output = null;
    try {
      $output = call_user_func_array(array($this, $function_name), $args);
    }
    catch(CMbException $fault) {
      $echange_ftp->date_echange = CMbDT::dateTime();
      $echange_ftp->output      = $fault->getMessage();
      $echange_ftp->ftp_fault    = 1;
      $echange_ftp->store();

      CApp::$chrono->start();

      throw $fault;
    }
    $chrono->stop();
    CApp::$chrono->start();

    // response time
    $echange_ftp->response_time = $chrono->total;

    // Truncate input and output before storing
    $args = array_map_recursive(array("CFTP", "truncate"), $args);

    $echange_ftp->input = serialize($args);
    if ($echange_ftp->ftp_fault != 1) {
      if ($function_name == "_getlistfiles") {
        // Truncate le tableau des fichiers reçus dans le cas où c'est > 100
        $array_count = count($output);
        if ($array_count > 100) {
          $output          = array_slice($output, 0, 100);
          $output["count"] = "$array_count files";
        }
      }
      $echange_ftp->output = serialize(array_map_recursive(array("CSFTP", "truncate"), $output));
    }
    $echange_ftp->store();

    return $output;
  }

  /**
   * Truncate the string
   *
   * @param String $string String
   *
   * @return string
   */
  static public function truncate($string) {
    if (!is_string($string)) {
      return $string;
    }

    // Truncate
    $max = 1024;
    $result = CMbString::truncate($string, $max);

    // Indicate true size
    $length = strlen($string);
    if ($length > 1024) {
      $result .= " [$length bytes]";
    }

    return $result;
  }

  /**
   * Initialisation
   *
   * @param CSourceFTP $exchange_source source
   *
   * @throws CMbException
   * @return void
   */
  private function _init($exchange_source) {
    if (!$exchange_source->_id) {
      throw new CMbException("CSourceSFTP-no-source", $exchange_source->name);
    }

    $this->hostname          = $exchange_source->host;
    $this->username          = $exchange_source->user;
    $this->userpass          = $exchange_source->getPassword();
    $this->port              = $exchange_source->port;
    $this->timeout           = $exchange_source->timeout;
    $this->loggable          = $exchange_source->loggable;
    $this->fileextension     = $exchange_source->fileextension;
    $this->fileextension_end = $exchange_source->fileextension_write_end;
    $this->directory         = $exchange_source->fileprefix;
  }

  /**
   * Connect to the host
   *
   * @return bool
   * @throws CMbException
   */
  private function _connect() {
    if ($this->connexion) {
      return true;
    }
    define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX);
    if (!$sftp = new Net_SFTP($this->hostname, $this->port, $this->timeout)) {
      throw new CMbException("Connexion impossible");
    }

    if (!$sftp->login($this->username, $this->userpass)) {
      throw new CMbException("Authentification échoué");
    }

    /*
    $key = $sftp->getServerPublicHostKey();
    $key = substr($key, strpos($key, " ")+1);
    //@todo : tester les cles dans la liste blanche
    //mbTrace(md5(base64_decode($key)));*/

    $this->connexion = $sftp;

    return true;
  }

  /**
   * Return the current directory
   *
   * @return String
   * @throws CMbException
   */
  private function _getCurrentDirectory() {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    if (!$pwd = $this->connexion->pwd()) {
      throw new CMbException("CSourceSFTP-pwd-failed", $this->hostname);
    }

    return $pwd;
  }

  /**
   * Change the directory to the target directory
   *
   * @param String $directory directory path
   *
   * @return bool
   * @throws CMbException
   */
  private function _changeDirectory($directory) {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    if (!$chdir = $this->connexion->chdir($directory)) {
      throw new CMbException("CSourceSFTP-change-directory-failed", $directory);
    }

    return true;
  }

  /**
   * return the list of directory
   *
   * @param string $folder directory path
   *
   * @return array
   * @throws CMbException
   */
  private function _getListDirectory($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    /**
     * group by directory
     * size - uid -gid - permissions - atime - mtime - type
     */
    if (!$files = $this->connexion->rawlist($folder)) {
      throw new CMbException("CSourceSFTP-getlistfiles-failed", $this->hostname);
    }

    CMbArray::extract($files, ".");
    CMbArray::extract($files, "..");

    $list = array();

    foreach ($files as $key => $_file) {
      if ($_file["type"] !== 2) {
        continue;
      }
      $list[$key] = $_file;
    }

    return $list;
  }

  /**
   * Return a simple list of files
   *
   * @param string $folder Directory path
   *
   * @return array
   * @throws CMbException
   */
  private function _getListFiles($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    if (!$files = $this->connexion->rawList($folder)) {
      throw new CMbException("CSourceSFTP-getlistfiles-failed", $this->hostname);
    }

    CMbArray::extract($files, ".");
    CMbArray::extract($files, "..");

    $array_file = array();

    foreach ($files as $key => $_file) {
      if ($_file["type"] === 2) {
        continue;
      }
      $array_file[] = $key;
    }

    return $array_file;
  }

  /**
   * return a detail list of directory
   *
   * @param string $folder Directory path
   *
   * @return array
   * @throws CMbException
   */
  private function _getListFilesDetails($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    if (!$files = $this->connexion->rawList($folder)) {
      throw new CMbException("CSourceSFTP-getlistfiles-failed", $this->hostname);
    }

    CMbArray::extract($files, ".");
    CMbArray::extract($files, "..");

    $list = array();

    foreach ($files as $key => $_file) {
      if ($_file["type"] === 2) {
        continue;
      }
      $list[$key] = $_file;
    }

    return $list;
  }

  /**
   * Delete the file
   *
   * @param String $file name or file path
   *
   * @return bool
   * @throws CMbException
   */
  private function _delFile($file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    // Download the file
    if (!$this->connexion->delete($file)) {
      throw new CMbException("CSourceSFTP-delete-file-failed", $file);
    }

    return true;
  }

  /**
   * Rename the file
   *
   * @param String $oldname the old name
   * @param String $newname the new name
   *
   * @return bool
   * @throws CMbException
   */
  private function _renameFile($oldname, $newname) {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    // Rename the file
    if (!$this->connexion->rename($oldname, $newname)) {
      throw new CMbException("CSourceSFTP-rename-file-failed", $oldname, $newname);
    }

    return true;
  }

  /**
   * Add a file
   *
   * @param String $file_name   name of the target file
   * @param String $source_file source path
   * @param Bool   $data_string The source file is a content or a path
   *
   * @return bool
   * @throws CMbException
   */
  private function _addFile($file_name, $source_file, $data_string = true) {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    // Upload the file
    if ($data_string) {
      if (!$this->connexion->put($file_name, $source_file)) {
        throw new CMbException("CSourceSFTP-upload-file-failed", $source_file);
      }
    }
    else {
      if (!$this->connexion->put($file_name, $source_file, NET_SFTP_LOCAL_FILE)) {
        throw new CMbException("CSourceSFTP-upload-file-failed", $source_file);
      }
    }

    return true;
  }

  /**
   * get a file
   *
   * @param String $source_file      the target file to download
   * @param bool   $destination_file the save path
   *
   * @return String
   * @throws CMbException
   */
  private function _getFile($source_file, $destination_file = false) {
    if (!$this->connexion) {
      throw new CMbException("CSourceSFTP-connexion-failed", $this->hostname);
    }

    // Download the file
    if (!$data = $this->connexion->get($source_file, $destination_file)) {
      throw new CMbException("CSourceSFTP-download-file-failed", $source_file, $destination_file);
    }

    return $data;
  }
}