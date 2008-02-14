<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 16 $
 */
 
class CFTP {
  var $hostname  = null;
  var $username  = null;
  var $userpass  = null;
  var $connexion = null;
  var $port      = 21;
  var $timeout   = 90;
  var $logs      = null;
  
  
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
  
  function connect($passif_mode = false) {
    if(!function_exists("ftp_connect")) {
      $this->logError("Fonctions FTP non disponibles");
      return false;
    }
    
    // Set up basic connection
    $this->connexion = ftp_connect($this->hostname, $this->port, $this->timeout);
    if (!$this->connexion) {
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
    $this->logStep("Connecté au serveur $this->hostname");

    // Login with username and password
    $login_result = ftp_login($this->connexion, $this->username, $this->userpass);
    if (!$login_result) {
      $this->logError("Impossible de s'authentifier en tant que $this->username");
      return false;
    } 
    
    $this->logStep("Authentifié en tant que $this->username");
    return true;
  }
  
  function sendFile($source_file, $destination_file, $mode = FTP_BINARY) {
    
    if(!$this->connexion) {
      $this->logError("Non connecté au serveur, impossible de copier le fichier source $source_base");
    }
    
    $source_base = basename($source_file);
    
    // Upload the file
    $upload = ftp_put($this->connexion, $destination_file, $source_file, $mode);
    if (!$upload) {
      $this->logError("Impossible de copier le fichier source $source_base en fichier cible $destination_file");
      return false;
    } 
    
    $this->logStep("Fichier source $source_base copié en fichier cible $destination_file");
    return true;
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