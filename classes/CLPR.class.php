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
 

/**
 * Class CLPR
 */

class CLPR {
  public $hostname;
  public $username;
  public $port;
  public $printer_name;
  
  function init($source) {   
    if (!$source) {
      throw new CMbException("CSourceFTP-no-source", $source->name);
    }
    
    $this->hostname = $source->host;
    $this->username = $source->user;
    $this->port     = $source->port;
    $this->printer_name = $source->printer_name;
  }
  
  function printFile($file) {
    // Test de la commande lpr
    exec("whereis lpr", $ret);
    if (preg_match("@\/lpr@", $ret[0]) == 0) {
       CAppUI::stepAjax("La commande lpr n'est pas disponible", UI_MSG_ERROR);
    }
    
    if (file_get_contents($file->_file_path) === false) {
      CAppUI::stepAjax("Impossible d'accéder au PDF", UI_MSG_ERROR);
    }
    
    $printer = "";
    if ($this->printer_name) {
      $printer = "-P " . escapeshellarg($this->printer_name);
    }

    $u = "";
    
    if ($this->username) {
      $u = "-U " . escapeshellarg($this->username);
    }
    /*if ($this->port) {
      $host .= ":$this->port";
    }*/
    
    // La commande lpr interprête mal le hostname pour établir la requête
    // $host = "$this->hostname";
    // $command = "lpr -H $host $u $printer '$file->_file_path'";
    
    // Ajout préalable de l'imprimante via cups du serveur web
    $command = "lpr $u $printer " . escapeshellarg($file->_file_path);
    
    exec($command, $res, $success);

    // La commande lpr retourne 0 si la transmission s'est bien effectuée
    if ($success == 0) {
      CAppUI::stepAjax("Impression réussie", UI_MSG_OK);  
    }
    else {
      CAppUI::stepAjax("Impression échouée, vérifiez la configuration", UI_MSG_ERROR);
    }
  }
}
