<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSourceFileSystem extends CExchangeSource {
  // DB Table key
  public $source_file_system_id;
  
  public $fileextension;
  public $fileextension_write_end;
  public $fileprefix;
  public $sort_files_by;
  
  // Form fields
  public $_path;
  public $_file_path;
  public $_files            = array();
  public $_dir_handles      = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "source_file_system";
    $spec->key   = "source_file_system_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["fileextension"]           = "str";
    $props["fileextension_write_end"] = "str";
    $props["fileprefix"]              = "str";
    $props["sort_files_by"]           = "enum list|date|name|size default|name";
    
    return $props;
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
    
    while (true) {
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
     * $this->_files = CMbPath::getFiles($path); => pas optimis� pour un listing volumineux
     * */
    $i = 1;
    $files = array();

    $limit = 5000;
    while (false !== ($entry = readdir($handle))) {
      $entry = "$path/$entry";
      if ($i == $limit) {
        break;
      }
      
      /* We ignore folders */
      if (is_dir($entry)) {
        continue;
      }

      $files[] = $entry;

      $i++;
    }
    
    closedir($handle);

    switch ($this->sort_files_by) {
      default:
      case "name":
        sort($files);
        break;
      case "date":
        usort($files, array($this, "sortByDate"));
        break;
      case "size":
        usort($files, array($this, "sortBySize"));
        break;
    }

    if (isset($this->_limit)) {
      $files = array_slice($files, 0, $this->_limit);
    }
    
    return $this->_files = $files;
  }

  function sortByDate($a, $b) {
    return filemtime($a) - filemtime($b);
  }

  function sortBySize($a, $b) {
    return filesize($a) - filesize($b);
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
    
    $file_path = str_replace(array(" ", ":", "-"), array("_", "", ""), CMbDT::dateTime());
    
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

  function delFile($path, $current_directory = null) {
    if ($current_directory) {
      $path = $current_directory.$path;
    }
    if (file_exists($path) && unlink($path) === false) {
      throw new CMbException("CSourceFileSystem-file-not-deleted", $path);
    }
  }

  function renameFile($oldname, $newname, $current_directory = null) {
    if (rename($current_directory.$oldname, $current_directory.$newname) === false) {
      throw new CMbException("CSourceFileSystem-error-renaming", $oldname);
    }
  }

  function changeDirectory($directory_name) {
  }

  function getCurrentDirectory($directory = null) {
    if (!$directory) {
      $directory = $this->host;
      if (substr($directory, -1, 1) !== "/" && substr($directory, -1, 1) !== "\\") {
        $directory = "$directory/";
      }
    }

    return str_replace("\\", "/", $directory);
  }

  function getListDirectory($current_directory) {
    $contain = scandir($current_directory);

    $dir = array();
    foreach ($contain as $_contain) {
      $full_path = $current_directory.$_contain;
      if (is_dir($full_path) && "$_contain/" !== "./" && "$_contain/" !== "../") {
        $dir[] = "$_contain/";
      }
    }
    return $dir;
  }

  function getRootDirectory($current_directory) {
    $tabRoot = explode("/", $current_directory);
    array_pop($tabRoot);
    $tabRoot[0] = "/";
    $root = array();
    $i =0;
    foreach ($tabRoot as $_tabRoot) {

      if ($i === 0) {
        $path = $_tabRoot[0];
        if (!$path) {
          $path = "/";
        }
      }
      else {
        $path = $root[count($root)-1]["path"]."$_tabRoot/";
      }
      $root[] = array("name" => $_tabRoot,
                      "path" => $path);
      $i++;
    }
    return $root;
  }

  function getListFilesDetails($current_directory) {
    $contain = scandir($current_directory);
    $fileInfo = array();
    foreach ($contain as $_contain) {
      $full_path = $current_directory.$_contain;
      if (!is_dir($full_path) && @filetype($full_path) && !is_link($full_path)) {
        $fileInfo[] = array("type"  => "f",
                            "user"  => fileowner($full_path),
                            "size"  => CMbString::toDecaBinary(filesize($full_path)),
                            "date"  => strftime(CMbDT::ISO_DATETIME, filemtime($full_path)),
                            "name"  => $_contain,
                            "relativeDate" => CMbDT::daysRelative(fileatime($full_path), CMbDT::date()));
      }
    }
        return $fileInfo;
  }

  function addFile($file, $file_name, $current_directory) {
    if (copy($file, $current_directory.$file_name) === false) {
      throw new CMbException("CSourceFileSystem-error-add", $file);
    }
  }
  
  function isReachableSource() {
    if (is_dir($this->host)) {
      return true;
    }
    else {
      $this->_reachable = 0;
      $this->_message   = CAppUI::tr("CSourceFileSystem-path-not-found", $this->host);
      return false;
    }
  }
  
  function isAuthentificate() {
    if (is_writable($this->host)) {
      return true;
    }
    else {
      $this->_reachable = 1;
      $this->_message   = CAppUI::tr("CSourceFileSystem-path-not-writable", $this->host);
      return false;
    }
  }
}
