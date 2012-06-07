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
  var $source_file_system_id   = null;
  
  var $fileextension           = null;
  var $fileextension_write_end = null;
  
  // Form fields
  var $_path             = null;
  var $_file_path        = null;
  var $_files            = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "source_file_system";
    $spec->key   = "source_file_system_id";
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["fileextension"]           = "str";
    $specs["fileextension_write_end"] = "str";
    return $specs;
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
    
    if (!$handle = opendir($path)) {
      throw new CMbException("CSourceFileSystem-path-not-readable", $path);
    }  
    
    /* Loop over the directory 
     * $this->_files = CMbPath::getFiles($path); => pas optimisé pour un listing volumineux
     * */
    $i = 1;
    $this->_files = array();
    while (false !== ($entry = readdir($handle))) {
      /* Limite de 5000 fichiers */
      if ($i == 10000) {
        break;
      }
      
      /* Suppression des dossier */
      if (is_dir("$path/$entry")) {
        continue;
      } 
      
      
      $this->_files[] = $entry;
      
      $i++;
    }
    
    closedir($handle);
    
    return $this->_files;
  }
  
  function send($evenement_name = null) {
    $this->init();
    
    $path = rtrim($this->getFullPath($this->_path), "\\/");
    $file_path = "$path/$this->_file_path";
    
    if (!is_writable($path)) {
      throw new CMbException("CSourceFileSystem-path-not-writable", $path);
    }
    
    if ($this->fileextension_write_end) {
      file_put_contents($file_path, $this->_data);
      
      $pos = strrpos($file_path, ".");
      $file_path = substr($file_path, 0, $pos);
      
      return file_put_contents("$file_path.$this->fileextension_write_end", "");
    }
    else {
      return file_put_contents($file_path, $this->_data);
    }
  }
  
  function getData($path) {
    if (!is_readable($path)) {
      throw new CMbException("CSourceFileSystem-file-not-readable", $path);
    }
    
    return file_get_contents($path);
  }
  
  function setData($data, $argsList = false, $file_path) {
    parent::setData($data, $argsList);
    
    $this->_file_path = $file_path;
  }
  
  public function getFullPath($path = ""){
    $host = rtrim($this->host, "/\\");
    $path = ltrim($path, "/\\");
    $path = $host.($path ? "/$path" : "");
    return str_replace("\\", "/", $path);
  }
  
  function delFile($path) {
    if (unlink($path) === false) {
      throw new CMbException("CSourceFileSystem-file-not-deleted", $path);
    }    
  }
  
  function isReachableSource() {
    if (is_dir($this->host)) {
      return true;
    } else {
      $this->_reachable = 0;
      $this->_message   = CAppUI::tr("CSourceFileSystem-path-not-found", $this->host);
      return false;
    }
  }
  
  function isAuthentificate() {
    if (is_writable($this->host)) {
      return true;
    } else {
      $this->_reachable = 1;
      $this->_message   = CAppUI::tr("CSourceFileSystem-path-not-writable", $this->host);
      return false;
    }
  }
}
