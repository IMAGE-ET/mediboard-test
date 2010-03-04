<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Throws some E_STRICT errors
@require_once("Config.php");

class CMbConfig {
  var $options = array("name" => "dPconfig");
  var $configType = "phparray";
  var $values = array();
  var $sourcePath = "";
  var $targetPath = "";
  
  function CMbConfig() {
    global $mbpath;
    $this->sourcePath = $mbpath."includes/config_dist.php";
    $this->targetPath = $mbpath."includes/config.php";
  }
  
  function guessValues() {
    global $mbpath;

    $this->values["root_dir"] = strtr(realpath($mbpath), "\\", "/");
    $this->values["base_url"] = "http://" . $_SERVER["HTTP_HOST"] . dirname(dirname($_SERVER["PHP_SELF"]));
  }
  
  function loadValuesFromPath($path) {
    if (!is_file($path)) {
      return array();
    }
    
    $config = new Config;
    $configContainer = $config->parseConfig($path, $this->configType, $this->options);
    $rootConfig = $configContainer->toArray();
    return $rootConfig["root"];
  }

  function load() {
    $this->values = CMbArray::mergeRecursive(
      $this->loadValuesFromPath($this->sourcePath),
      $this->loadValuesFromPath($this->targetPath));
    
    
    if (!is_file($this->targetPath)) {
      $this->guessValues();
    }
  }
  
  function update($newValues = array(), $keepOld = true) {
    $newValues = array_map_recursive('stripslashes', $newValues);
    
    if ($keepOld) {
      $this->load();
      $newValues = CMbArray::mergeRecursive($this->values, $newValues);
    }
    
    if (!count($newValues)) {
      if(is_file($this->targetPath)){
        unlink($this->targetPath);
      }
      return;
    }
    
    $this->values = $newValues;
    
    // Throws many E_STRICT errors
    $config = @new Config;
    @$config->parseConfig($this->values, $this->configType, $this->options);
    return @$config->writeConfig($this->targetPath, $this->configType, $this->options);
  }
  
  function set($path, $value) {
    $conf = $this->values;
    $values = &$conf;
    
    $items = explode(' ', $path);
    foreach ($items as $part) {
      if (!array_key_exists($part, $conf))
        $conf[$part] = array();
      $conf = &$conf[$part];
    }
    $conf = $value;
    
    $this->values = $values;
  }
  
  function get($path) {
    $conf = $this->values;
    
    $items = explode(' ', $path);
    foreach ($items as $part) {
      $conf = $conf[$part];
    }
    return $conf;
  }
}

?>