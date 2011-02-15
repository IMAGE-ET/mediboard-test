<?php

/**
 * SMB protocol
 *  
 * @category classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSMB
 */

class CSMB {
  var $hostname  = null;
  var $username  = null;
  var $port      = null;
  var $workgroup = null;
  
  function init($source) {   
    if (!$source) {
      throw new CMbException("CSourceFTP-no-source", $source->name);
    }
    
    $this->hostname  = $source->host;
    $this->username  = $source->user;
    $this->password  = $source->password;
    $this->port      = $source->port;
    $this->printer_name = $source->printer_name;
    $this->workgroup = $source->workgroup;
  }
  
  function printFile($file) {
    // Construction de l'uri
    $uri = "'//$this->hostname/$this->printer_name' "; 
    
    if ($this->password) {
      $uri .= $this->password;
    }
    
    $uri .= " -c 'print " . dirname(__DIR__) . "/$file->_file_path' ";
    
    if ($this->username) {
      $uri .= "-U $this->username ";
    }
    
    if ($this->workgroup) {
      $uri .= "-W '$this->workgroup' ";
    }
    
    $uri .= "-N 2";

    exec("smbclient $uri", $res);
    
    if (count($res)) {
      $mess = "";
      foreach($res as $_res) {
        $mess .= $_res . "\n";
      }
      CAppUI::stepAjax("Impression échouée \n" . $mess, UI_MSG_ERROR);
    }
    else {
      CAppUI::stepAjax("Impression réussie", UI_MSG_OK);
    }
  }
}
?>