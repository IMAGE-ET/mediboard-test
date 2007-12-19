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
  var $logs     = null;
  
  
  function logError($log) {
    $this->logs[] = "<strong>Erreur : </strong>$log";
  }

  function logStep($log) {
    $this->logs[] = "Etape : $log";
  }
  
  function sendFile($source_file, $destination_file, $mode = FTP_BINARY) {
    if(!function_exists("ftp_connect")) {
      $this->logError("Fonctions FTP non disponibles");
      return false;
    }
    
    $source_base = basename($source_file);
    
    // Set up basic connection
    $conn_id = ftp_connect($this->hostname, 21, 90);
    if (!$conn_id) {
      $this->logError("Impossible de se connecter au serveur $this->hostname");
      return false;
    } 
    
    $this->logStep("Connecté au serveur $this->hostname");

    // Login with username and password
    $login_result = ftp_login($conn_id, $this->username, $this->userpass);
    if (!$login_result) {
      $this->logError("Impossible de s'authentifier en tant que $this->username");
      return false;
    } 
    
    $this->logStep("Authentifié en tant que $this->username");
    
    // Upload the file
    $upload = ftp_put($conn_id, $destination_file, $source_file, $mode);
    if (!$upload) {
      $this->logError("Impossible de copier le fichier source $source_base en fichier cible $destination_file");
      return false;
    } 
    
    $this->logStep("Fichier source $source_base copié en fichier cible $destination_file !!!");
    
    // close the FTP stream
    ftp_close($conn_id);
    return true;
  }
  
}

?>