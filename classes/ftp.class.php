<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
class CFTP {
  var $hostname      = null;
  var $username      = null;
  var $userpass      = null;
  var $connexion     = null;
  var $port          = null;
  var $timeout       = null;
  var $passif_mode   = false;
  var $mode          = null;
  var $fileprefix    = null;
  var $fileextension = null;
  var $filenbroll    = null;
  
  function init($exchange_source) {   
    if (!$exchange_source) {
      throw new CMbException("CSourceFTP-no-source", $exchange_source->name);
    }
       
    $this->hostname      = $exchange_source->host;
    $this->username      = $exchange_source->user;
    $this->userpass      = $exchange_source->password;
    $this->port          = $exchange_source->port;
    $this->timeout       = $exchange_source->timeout;
    $this->passif_mode   = $exchange_source->pasv;
    $this->mode          = $exchange_source->mode;
    $this->fileprefix    = $exchange_source->fileprefix;
    $this->fileextension = $exchange_source->fileextension;
    $this->filenbroll    = $exchange_source->filenbroll;
  }
  
  function testSocket() {
    $fp = @fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);
    if (!$fp) {
      throw new CMbException("CSourceFTP-socket-connection-failed", $this->hostname, $this->port, $errno, $errstr);
    }
  }
  
  function connect() {
    if (!function_exists("ftp_connect")) {
      throw new CMbException("CSourceFTP-function-not-available", "ftp_connect");
    }
    
    // Set up basic connection
    $this->connexion = @ftp_connect($this->hostname, $this->port, $this->timeout);
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    // Login with username and password
    if (!@ftp_login($this->connexion, $this->username, $this->userpass)) {
      throw new CMbException("CSourceFTP-identification-failed", $this->username);
    } 
    
    // Turn passive mode on
    if ($this->passif_mode && !@ftp_pasv($this->connexion, true)) {
      throw new CMbException("CSourceFTP-passive-mode-on-failed");
    }
  }
  
  function getListFiles($folder = ".") {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
        
    return ftp_nlist($this->connexion, $folder);
  }
  
  function delFile($file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Download the file
    if (!@ftp_delete($this->connexion, $file)) {
      throw new CMbException("CSourceFTP-delete-file-failed", $file);
    }    
  }
  
  function getFile($source_file, $destination_file = null) {
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
  
  function sendContent($source_content, $destination_file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    $tmpfile = tempnam("","");    
    file_put_contents($tmpfile, $source_content);
    $result = $this->sendFile($tmpfile, $destination_file);
    unlink($tmpfile);
    
    return $result;
  }

  function sendFile($source_file, $destination_file) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }

    // Upload the file
    if (!@ftp_put($this->connexion, $destination_file, $source_file, constant($this->mode))) {
      throw new CMbException("CSourceFTP-upload-file-failed", $source_file, $destination_file);
    }
  }
  
  function renameFile($oldname, $newname) {
    if (!$this->connexion) {
      throw new CMbException("CSourceFTP-connexion-failed", $this->hostname);
    }
    
    // Rename the file
    if (!@ftp_rename($this->connexion, $oldname, $newname)) {
      throw new CMbException("CSourceFTP-rename-file-failed", $oldname, $newname);
    }
  }
  
  function close() {
    // close the FTP stream
    if (!@ftp_close($this->connexion)) {
      throw new CMbException("CSourceFTP-close-connexion-failed", $this->hostname);
    }
    
    $this->connexion = null;
    return true;
  }
}

?>