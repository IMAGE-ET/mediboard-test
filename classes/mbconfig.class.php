<?php /* CLASSES $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision$
 */
 
require_once("Config.php");

class CMbConfig {
  var $options = array("name" => "dPconfig");
  var $values = array();
  var $configType = "phparray";
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
    $this->values["site_domain"] = $_SERVER["HTTP_HOST"];     
  }
  
  function load() {
    
    $loadPath = is_file($this->targetPath) ? $this->targetPath : $this->sourcePath;
    
    $config = new Config;
    $configContainer = $config->parseConfig($loadPath, $this->configType, $this->options);

    $rootConfig = $configContainer->toArray();
    $this->values = $rootConfig["root"];
    
    if (!is_file($this->targetPath)) {
      $this->guessValues();
    }
  }
  
  function update($newValues = array()) {
    if (!count($newValues)) {
      return;
    }
    
    $this->load();
    $this->values = mbArrayMergeRecursive($this->values, $newValues);
    
    $config = new Config;
    $config->parseConfig($this->values, $this->configType, $this->options);
    $config->writeConfig($this->targetPath, $this->configType, $this->options);
  }
}

?>