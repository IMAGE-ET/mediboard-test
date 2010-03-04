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
  var $logs          = array();
  
  function logError($log) {
    $this->logs[] = "<strong>Erreur : </strong>$log";
  }

  function logStep($log) {
    $this->logs[] = "Etape : $log";
  }
  
  function init($exchange_source) {   
    if (!$exchange_source) {
      trigger_error("Aucune source d'échange disponible pour ce nom : '$exchange_source_name'");
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
    $fp = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);
    if (!$fp) {
      trigger_error("Socket connection failed : ($errno) $errstr");
      return false;
    }
    return true;
  }
  
  function connect() {
    if(!function_exists("ftp_connect")) {
      $this->logError("Fonctions FTP non disponibles");
      return false;
    }
    
    // Set up basic connection
    $this->connexion = ftp_connect($this->hostname, $this->port, $this->timeout);
    if (!$this->connexion) {
      return false;
    }

    // Login with username and password
    if (!ftp_login($this->connexion, $this->username, $this->userpass)) {
      return false;
    } 
    
    // Turn passive mode on
    if($this->passif_mode && !ftp_pasv($this->connexion, true)) {
      return false;
    }
    
    return true;
  }
  
  function getListFiles($folder = ".") {
    if(!$this->connexion) {
      return false;
    }
        
    return ftp_rawlist($this->connexion, $folder, true);
  }
  
  function delFile($file) {
    if(!$this->connexion) {
      return false;
    }
    
    return ftp_delete($this->connexion, $file);
  }
  
  function getFile($source_file, $destination_file = null) {
    
    $source_base = basename($source_file);
    
    if(!$destination_file) {
      $destination_file = "tmp/$source_base";
    }
    $destination_info = pathinfo($destination_file);
    CMbPath::forceDir($destination_info["dirname"]);
    
    if(!$this->connexion) {
      return false;
    }
    
    // Download the file
    if (!ftp_get($this->connexion, $destination_file, $source_file, constant($this->mode))) {
      return false;
    }
    
    return $destination_file;
  }
  
  function sendContent($source_content, $destination_file) {
    if(!$this->connexion) {
      return false;
    }
    
    $tmpfile = tempnam("","");    
    file_put_contents($tmpfile, $source_content);
    $result = $this->sendFile($tmpfile, $destination_file);
    unlink($tmpfile);
    
    return $result;
  }

  function sendFile($source_file, $destination_file) {
    if(!$this->connexion) {
      return false;
    }

    // Upload the file
    return ftp_put($this->connexion, $destination_file, $source_file, constant($this->mode));
  }
  
  function renameFile($oldname, $newname) {
    if(!$this->connexion) {
      return false;
    }
    
    // Rename the file
    return ftp_rename($this->connexion, $oldname, $newname);
  }
  
  function close() {
    // close the FTP stream
    ftp_close($this->connexion);
    $this->logStep("Déconnecté du serveur $this->hostname");
    $this->connexion = null;
    return true;
  }
}

?>