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
  var $fileprefix              = null;
  var $sort_files_by           = null;
  
  // Form fields
  var $_path             = null;
  var $_file_path        = null;
  var $_files            = array();
  var $_dir_handles      = array();
  
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
    $specs["fileprefix"]              = "str";
    $specs["sort_files_by"]           = "enum list|date|name|size default|name";
    
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
  
  /**
   * Iterates through the files in the directory
   */
  function receiveOne() {
    $this->init();
    
    $path = $this->getFullPath($this->_path);
    $path = rtrim($path, "/\\");
    
    if (isset($this->_dir_handles[$path])) {
      $handle = $this->_dir_handles[$path];
    }
    else {
      if (!is_dir($path)) {
        throw new CMbException("CSourceFileSystem-path-not-found", $path);
      }
      
      if (!is_readable($path)) {
        throw new CMbException("CSourceFileSystem-path-not-readable", $path);
      }
      
      if (!$handle = opendir($path)) {
        throw new CMbException("CSourceFileSystem-path-not-readable", $path);
      }
      
      $this->_dir_handles[$path] = $handle;
    }
    
    while(true) {
      $file = readdir($handle);
      
      if ($file === false) {
        return;
      }
      
      if (!is_dir($filepath = "$path/$file")) {
        return $filepath;
      }
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
    
    /* Limite de 1000 fichiers par défaut */
    $limit = (isset($this->_limit) ? $this->_limit : 1000);
    while (false !== ($entry = readdir($handle))) {
      $entry = "$path/$entry";
            if ($i == $limit) {
        break;
      }
      
      /* Suppression des dossier */
      if (is_dir($entry)) {
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
  
  function setData($data, $argsList = false, CExchangeDataFormat $exchange = null) {
    parent::setData($data, $argsList);
    
    $file_path = str_replace(array(" ", ":", "-"), array("_", "", ""), mbDateTime());
    
    // Ajout du prefix si existant
    $file_path = $this->fileprefix.$file_path;
    
    if ($exchange) {
      $file_path = "$file_path-$exchange->_id";
    }
            
    $this->_file_path = "MB-$file_path.$this->fileextension";
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
