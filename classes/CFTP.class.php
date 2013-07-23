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
 
class CFTP {
  public $hostname;
  public $username;
  public $userpass;
  public $connexion;
  public $port;
  public $timeout;
  public $ssl;
  public $passif_mode = false;
  public $mode;
  public $fileprefix;
  public $fileextension;
  public $filenbroll;
  public $loggable;
  
  private static $aliases = array(
    'sslconnect' => 'ssl_connect',
    'getoption' => 'get_option',
    'setoption' => 'set_option',
    'nbcontinue' => 'nb_continue',
    'nbfget' => 'nb_fget',
    'nbfput' => 'nb_fput',
    'nbget' => 'nb_get',
    'nbput' => 'nb_put',
  );

  static $month_to_number = array(
    'Jan' => '01',
    'Feb' => '02',
    'Mar' => '03',
    'Apr' => '04',
    'May' => '05',
    'Jun' => '06',
    'Jul' => '07',
    'Aug' => '08',
    'Sep' => '09',
    'Oct' => '10',
    'Nov' => '11',
    'Dec' => '12',
  );
  
  /**
   * Magic method (do not call directly).
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
      throw new CMbException("CSourceFTP-call-undefined-method", $name);
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
      $echange_ftp->output    = $fault->getMessage();
      $echange_ftp->ftp_fault = 1;
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
      $echange_ftp->output = serialize(array_map_recursive(array("CFTP", "truncate"), $output));
    }
    $echange_ftp->store();
    
    return $output;
  }
  
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
  
  private function _init($exchange_source) {   
    if (!$exchange_source->_id) {
      throw new CMbException("CSourceFTP-no-source", $exchange_source->name);
    }
       
    $this->hostname      = $exchange_source->host;
    $this->username      = $exchange_source->user;
    $this->userpass      = $exchange_source->getPassword();
    $this->port          = $exchange_source->port;
    $this->timeout       = $exchange_source->timeout;
    $this->ssl           = $exchange_source->ssl;
    $this->passif_mode   = $exchange_source->pasv;
    $this->mode          = $exchange_source->mode;
    $this->fileprefix    = $exchange_source->fileprefix;
    $this->fileextension = $exchange_source->fileextension;
    $this->filenbroll    = $exchange_source->filenbroll;
    $this->loggable      = $exchange_source->loggable;
  }
  
  private function _testSocket() {
    $fp = @fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);
    if (!$fp) {
      throw new CMbException("CSourceFTP-socket-connection-failed", $this->hostname, $this->port, $errno, $errstr);
    }
    
    return true;
  }
  
  private function _connect() {
    // If server provides SSL mode
    if ($this->ssl) {
      if (!function_exists("ftp_ssl_connect")) {
        throw new CMbException("CSourceFTP-function-not-available", "ftp_ssl_connect");
      }

      // Set up over-SSL connection
      $this->connexion = ftp_ssl_connect($this->hostname, $this->port, $this->timeout);
      if (!$this->connexion) {
        throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
      }
    }
    else {
      if (!function_exists("ftp_connect")) {
        throw new CMbException("CSourceFTP-function-not-available", "ftp_connect");
      }

      // Set up basic connection
      $this->connexion = @ftp_connect($this->hostname, $this->port, $this->timeout);
      if (!$this->connexion) {
        throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
      }
    }

    // Login with username and password
    if (!@ftp_login($this->connexion, $this->username, $this->userpass)) {
      throw new CMbException("CSourceFTP-identification-failed", $this->username);
    } 
    
    // Turn passive mode on
    if ($this->passif_mode && !@ftp_pasv($this->connexion, true)) {
      throw new CMbException("CSourceFTP-passive-mode-on-failed");
    }
    
    return true;
  }
  
  private function _getListFiles($folder = ".", $information = false) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    $files = ftp_nlist($this->connexion, $folder);

    if ($files === false) {
      throw new CMbException("CSourceFTP-getlistfiles-failed", $this->hostname);
    }
    
    foreach ($files as &$_file) {
      $_file = str_replace("\\", "/", $_file);
    }
    
    if ($folder && (substr($folder, -1) != "/")) {
      $folder = "$folder/";
    }

    $tabFileDir = array();
    foreach ($files as &$_file) {
      $tabFileDir[] = array("path" => $_file, "size" => ftp_size($this->connexion, $_file));
      // Some FTP servers do not retrieve whole paths
      if ($folder && $folder != "." && strpos($_file, $folder) !== 0) {
        $_file = "$folder/$_file";
        $tabFileDir[]["path"] = $_file;
      }
    }

    if ($information) {
      return $tabFileDir;
    }

    return $files;
  }

  private function _getListFilesDetails($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    $files = ftp_rawlist($this->connexion, $folder);

    if ($files === false) {
      throw new CMbException("CSourceFTP-getlistfiles-failed", $this->hostname);
    }

    $fileInfo = array();
    foreach ($files as $_file) {
      $pregInfo = preg_split("/[\s]+/", $_file, 9);
      if (strpos($pregInfo[0], "d") !== false || $pregInfo[0] == "total") {
        continue;
      }
      $month = self::$month_to_number[$pregInfo[5]];
      $day = $pregInfo[6];
      $year = $pregInfo[7];
      if (strpos($year, ":")) {
        $year = explode("-", CMbDT::date());
        $year = $year[0]." $pregInfo[7]";
      }
      $fileInfo[] = array("type"  => $pregInfo[0],
                          "user"  => $pregInfo[2]." ".$pregInfo[3],
                          "size"  => CMbString::toDecaBinary($pregInfo[4]),
                          "date"  => $day."-".$month."-".$year,
                          "name"  => $pregInfo[8],
                          "relativeDate" => CMbDT::daysRelative($day."-".$month."-".$year, CMbDT::date()));
    }
    return $fileInfo;
  }

  private function _getListDirectory($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    $files = ftp_rawlist($this->connexion, $folder);

    if ($files === false) {
      throw new CMbException("CSourceFTP-getlistfiles-failed", $this->hostname);
    }

    $fileInfo = array();
    foreach ($files as $_file) {
      $pregInfo = preg_split("/[\s]+/", $_file, 9);
      if (strpos($pregInfo[0], "d") === false) {
        continue;
      }
      $fileInfo[] = $pregInfo[8];
    }

    return $fileInfo;
  }

  private function _getCurrentDirectory() {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    $pwd = ftp_pwd($this->connexion);

    if ($pwd === false) {
      throw new CMbException("CSourceFTP-getlistfiles-failed", $this->hostname);
    }

    if ($pwd === "/") {
      return "$pwd";
    }

    return "$pwd/";
  }
  
  private function _delFile($file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Download the file
    if (!@ftp_delete($this->connexion, $file)) {
      throw new CMbException("CSourceFTP-delete-file-failed", $file);
    }    
    
    return true;
  }
  
  private function _getFile($source_file, $destination_file = null) {
    $source_base = basename($source_file);
    
    if (!$destination_file) {
      $destination_file = "tmp/$source_base";
    }
    $destination_info = pathinfo($destination_file);
    CMbPath::forceDir($destination_info["dirname"]);
    
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Download the file
    if (!@ftp_get($this->connexion, $destination_file, $source_file, constant($this->mode))) {
      throw new CMbException("CSourceFTP-download-file-failed", $source_file, $destination_file);
    }
    
    return $destination_file;
  }
  
  private function _sendContent($source_content, $destination_file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    $tmpfile = tempnam("", "");
    file_put_contents($tmpfile, $source_content);
    $result = $this->_sendFile($tmpfile, $destination_file);
    unlink($tmpfile);
    
    return $result;
  }

  private function _sendFile($source_file, $destination_file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    // Check for path, try to build it if needed
    // @todo Make it recursive
    $dir = dirname($destination_file);
    if ($dir != ".") {
      $pwd = ftp_pwd($this->connexion);
      if (!@ftp_chdir($this->connexion, $dir)) {
        @ftp_mkdir($this->connexion, $dir);
      }
      ftp_chdir($this->connexion, $pwd);
    }

    // Upload the file
    if (!@ftp_put($this->connexion, $destination_file, $source_file, constant($this->mode))) {
      throw new CMbException("CSourceFTP-upload-file-failed", $source_file, $destination_file);
    }

    return true;
  }

  private function _addFile($source_file, $file_name) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    // Upload the file
    if (!@ftp_put($this->connexion, $file_name, $source_file, constant($this->mode))) {
      throw new CMbException("CSourceFTP-upload-file-failed", $source_file);
    }

    return true;
  }

  private function _changeDirectory($directory) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    // Change the directory
    if (!@ftp_chdir($this->connexion, $directory)) {
      throw new CMbException("CSourceFTP-change-directory-failed", $directory);
    }

    return true;
  }
  
  private function _renameFile($oldname, $newname) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Rename the file
    if (!@ftp_rename($this->connexion, $oldname, $newname)) {
      throw new CMbException("CSourceFTP-rename-file-failed", $oldname, $newname);
    }
    
    return true;
  }
  
  private function _close() {
    // close the FTP stream
    if (!@ftp_close($this->connexion)) {
      throw new CMbException("CSourceFTP-close-connexion-failed", $this->hostname);
    }
    
    $this->connexion = null;
    return true;
  }
  
  private function _getSize($file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Rename the file
    $size = ftp_size($this->connexion, $file);
    if ($size == -1) {
      throw new CMbException("CSourceFTP-size-file-failed", $file);
    }
    
    return $size;
  }
  
  private function _createDirectory($directory) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    return @ftp_mkdir($this->connexion, $directory);
  }
}
