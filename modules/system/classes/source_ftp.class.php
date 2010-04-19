<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSourceFTP extends CExchangeSource {
  // DB Table key
  var $source_ftp_id = null;
  
  // DB Fields
  var $port          = null;
  var $timeout       = null;
  var $pasv          = null;
  var $mode          = null;
  var $fileprefix    = null;
  var $fileextension = null;
  var $filenbroll    = null;
  var $fileextension_write_end = null;
  var $counter       = null;
  
  var $_source_file      = null;
  var $_destination_file = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_ftp';
    $spec->key   = 'source_ftp_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["port"]       = "num default|21";
    $specs["timeout"]    = "num default|90";
    $specs["pasv"]       = "bool default|0";
    $specs["mode"]       = "enum list|FTP_ASCII|FTP_BINARY default|FTP_ASCII";
    $specs["counter"]    = "str protected";
    $specs["fileprefix"] = "str";
    $specs["fileextension"] = "str";
    $specs["filenbroll"]    = "enum list|1|2|3|4";
    $specs["fileextension_write_end"] = "str";
    
    return $specs;
  }
  
  function send() {
    $ftp = new CFTP();
    $ftp->init($this);
    
    $this->counter++;
      
    $destination_basename = sprintf("%s%0".$this->filenbroll."d", $this->fileprefix, $this->counter % pow(10, $this->filenbroll));
  
    if($ftp->connect()) {
      $ftp->sendContent($this->_data, "$destination_basename.$this->fileextension");
      if ($this->fileextension_write_end) {
        $ftp->sendContent($this->_data, "$destination_basename.$this->fileextension_write_end");
      }
      $ftp->close();
      
      $this->store();
			
      return true;
    }
  }
  
  function receive() {}
}
?>