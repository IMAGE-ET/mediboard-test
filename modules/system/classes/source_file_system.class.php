<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

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
    
    $this->_path = $this->host;
  }
  
  function init() {
    if (!$this->_id) {
      throw new CMbException("CSourceFileSystem-no-source", $this->name);
    }
            
    if (!is_dir($this->host)) {
      throw new CMbException("CSourceFileSystem-host-not-a-dir", $this->host);
    }
    
    if (!is_dir($this->_path)) {
      throw new CMbException("CSourceFileSystem-path-not-found", $this->_path);
    }
    
    if (!is_readable($this->_path)) {
      throw new CMbException("CSourceFileSystem-path-not-readable", $this->_path);
    }
  }
  
  function receive() {
    $this->init();
    
    $this->_path = "$this->host/$this->_path";
    
    return $this->_files = CMbPath::getFiles($this->_path);
  }
  
  function getData($path) {
    return file_get_contents($path);
  }
}
?>