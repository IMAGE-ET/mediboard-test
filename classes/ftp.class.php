<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
class CFTP {
  var $ftpsn       = null;
  var $hostname    = null;
  var $username    = null;
  var $userpass    = null;
  var $connexion   = null;
  var $port        = null;
  var $timeout     = null;
  var $passif_mode = false;
  var $mode        = null;
  var $logs        = array();
  
  function logError($log) {
    $this->logs[] = "<strong>Erreur : </strong>$log";
  }

  function logStep($log) {
    $this->logs[] = "Etape : $log";
  }
  
  function init($ftpsn) {
    $this->ftpsn = $ftpsn;
    $this->config = CAppUI::conf("ftp $ftpsn");
    
    $this->hostname    = $this->config["ftphost"];
    $this->username    = $this->config["ftpuser"];
    $this->userpass    = $this->config["ftppass"];
    $this->port        = $this->config["port"];
    $this->timeout     = $this->config["timeout"];
    $this->passif_mode = $this->config["pasv"];
    $this->mode        = $this->config["mode"];
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
      $this->logError("Non connect� au serveur, impossible de lister le repertoire $folder");
      return false;
    }
    
    $list = ftp_nlist($this->connexion, $folder);
    
    if(!$list) {
      $this->logError("Impossible de lister le repertoire $folder");
      return false;
    }
    
    $this->logStep("Repertoire $folder list�");
    
    return $list;
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
  
  function sendFile($source_file, $destination_file) {
    if(!$this->connexion) {
      return false;
    }

    $source_base = basename($source_file);
    
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
    $this->logStep("D�connect� du serveur $this->hostname");
    $this->connexion = null;
    return true;
  }
}

?>