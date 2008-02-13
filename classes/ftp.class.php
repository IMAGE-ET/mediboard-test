<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 16 $
 */
 
class CFTP {
  var $hostname = null;
  var $username = null;
  var $userpass = null;
  var $port     = 21;
  var $timeout  = 90;
  var $logs     = null;
  
  
  function logError($log) {
    $this->logs[] = "<strong>Erreur : </strong>$log";
  }

  function logStep($log) {
    $this->logs[] = "Etape : $log";
  }
  
  function testSocket() {
    $fp = fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);
    if (!$fp) {
      $this->logError("hote : $this->hostname, port : $this->port > $errstr ($errno)");
      return false;
    }
    return true;
  }
  
  function sendFile($source_file, $destination_file, $mode = FTP_BINARY, $passif_mode = false) {
    if(!function_exists("ftp_connect")) {
      $this->logError("Fonctions FTP non disponibles");
      return false;
    }
    
    $source_base = basename($source_file);
    
    // Set up basic connection
    $conn_id = ftp_connect($this->hostname, $this->port, $this->timeout);
    if (!$conn_id) {
      $this->logError("Impossible de se connecter au serveur $this->hostname");
      return false;
    }
    if($passif_mode) {
      $passif = ftp_pasv($conn_id, true);
      if (!$passif) {
        $this->logError("Impossible de passer en mode passif");
        return false;
      }
    }
    
    $this->logStep("Connect� au serveur $this->hostname");

    // Login with username and password
    $login_result = ftp_login($conn_id, $this->username, $this->userpass);
    if (!$login_result) {
      $this->logError("Impossible de s'authentifier en tant que $this->username");
      return false;
    } 
    
    $this->logStep("Authentifi� en tant que $this->username");
    
    //$this->logError("Phase de test, document non envoy�");
    //return false;
    
    // Upload the file
    $upload = ftp_put($conn_id, $destination_file, $source_file, $mode);
    //$this->logError("Phase de test, document non envoy�");
    //return false;
    if (!$upload) {
      $this->logError("Impossible de copier le fichier source $source_base en fichier cible $destination_file");
      return false;
    } 
    
    $this->logStep("Fichier source $source_base copi� en fichier cible $destination_file !!!");
    
    // close the FTP stream
    ftp_close($conn_id);
    return true;
  }
  
}

?>