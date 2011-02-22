<?php

/**
 * LPR protocol
 *  
 * @category classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CLPR
 */

class CLPR {
  var $hostname     = null;
  var $username     = null;
  var $port         = null;
  var $printer_name = null;
  
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
      CAppUI::stepAjax("Impossible d'acc�der au PDF", UI_MSG_ERROR);
    }
    
    $printer = "";
    if ($this->printer_name) {
      $printer = "-P '$this->printer_name'";
    }

    $host = "$this->hostname";
    $u = "";
    
    if ($this->username) {
      $u = "-U $this->username";
    }
    if ($this->port) {
      $host .= ":$this->port";
    }
    
    $command = "lpr -H $host $u $printer '$file->_file_path'";
    
    exec($command, $res, $success);

    // La commande lpr retourne 0 si la transmission s'est bien effectu�e
    if ($success == 0) {
      CAppUI::stepAjax("Impression r�ussie", UI_MSG_OK);  
    }
    else {
      CAppUI::stepAjax("Impression �chou�e, v�rifiez la configuration", UI_MSG_ERROR);
    }
  }
}
?>