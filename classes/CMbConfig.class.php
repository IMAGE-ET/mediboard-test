<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
 
 
// Throws some E_STRICT errors
@require_once "Config.php";

class CMbConfig {
  var $options = array("name" => "dPconfig");
  var $configType = "phparray";
  var $values = array();
  var $sourcePath = "";
  var $targetPath = "";
  var $overloadPath = "";
  
  function CMbConfig() {
    global $mbpath;
    $this->sourcePath   = $mbpath."includes/config_dist.php";
    $this->targetPath   = $mbpath."includes/config.php";
    $this->overloadPath = $mbpath."includes/config_overload.php";
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
    
    if ($configContainer instanceof PEAR_Error) {
      return array();
    }
    
    $rootConfig = $configContainer->toArray();
    return $rootConfig["root"];
  }

  function load() {
    $this->values = array();
    $this->values = CMbArray::mergeRecursive($this->values, $this->loadValuesFromPath($this->sourcePath));
    $this->values = CMbArray::mergeRecursive($this->values, $this->loadValuesFromPath($this->targetPath));
    $this->values = CMbArray::mergeRecursive($this->values, $this->loadValuesFromPath($this->overloadPath));
    
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
      if (is_file($this->targetPath)) {
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
      if (!array_key_exists($part, $conf)) {
        $conf[$part] = array();
      }

      $conf = &$conf[$part];
    }
    $conf = $value;
    
    $this->values = $values;
  }
  
  function get($path) {
    $conf = $this->values;
    
    $items = explode(' ', $path);
    foreach ($items as $part) {
      if (!isset($conf[$part])) {
        return false;
      }
      $conf = $conf[$part];
    }
    return $conf;
  }

  static function loadConf($key, $value, &$config) {
    if (count($key) > 1) {
      $firstkey = array_shift($key);
      if (!isset($config[$firstkey])) {
        $config[$firstkey] = "";
      }
      self::loadConf($key, $value, $config[$firstkey]);
    }
    else {
      $config[$key[0]] = $value;
    }
  }

  static function buildConf(&$list, $array, $_key) {
    foreach ($array as $key => $value) {
      $_conf_key = ($_key ? "$_key " : "") . $key;
      if (is_array($value)) {
        self::buildConf($list, $value, $_conf_key);
        continue;
      }
      $list[$_conf_key] = $value;
    }
  }

  static function loadValuesFromDB() {
    global $dPconfig;
    $ds = CSQLDataSource::get("std");

    $request = "SELECT * FROM config_db;";

    $configs = $ds->loadList($request);

    foreach ($configs as $_value) {
      CMbConfig::loadConf(explode(" ", $_value['key']), $_value['value'], $dPconfig);
    }
  }
}
