<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("system", "exchange_source");

class CSourceFileSystem extends CExchangeSource {
  // DB Table key
  var $source_file_system_id = null;
   
  // Form fields
  var $_path  = null;
  var $_files = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_file_system';
    $spec->key   = 'source_file_system_id';
    return $spec;
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->host;
  }
  
  function init() {
    if (!$this->_id) {
      throw new CMbException("CSourceFileSystem-no-source", $this->name);
    }
            
    if (!is_dir($this->host)) {
      throw new CMbException("CSourceFileSystem-host-not-a-dir", $this->host);
    }
  }
  
  function receive() {
    $this->init();
    
    $path = $this->getFullPath($this->_path);

    if (!is_dir($path)) {
      throw new CMbException("CSourceFileSystem-path-not-found", $path);
    }
    
    if (!is_readable($path)) {
      throw new CMbException("CSourceFileSystem-path-not-readable", $path);
    }
    
    return $this->_files = CMbPath::getFiles($path);
  }
  
  function getData($path) {
    if (is_readable($path)) {
      return file_get_contents($path);
    }
    else {
      throw new CMbException("CSourceFileSystem-file-not-readable", $path);
    }
  }
  
  public function getFullPath($path = ""){
    $host = rtrim($this->host, "/\\");
    $path = ltrim($path, "/\\");
    $path = $host.($path ? "/$path" : "");
    return str_replace("\\", "/", $path);
  }
}
