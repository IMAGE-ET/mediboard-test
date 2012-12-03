<?php

/**
 * Exchange Source
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeSource 
 * Exchange Source
 */
class CExchangeSource extends CMbObject {
  static $typeToClass = array (
    "ftp"         => "CSourceFTP",
    "soap"        => "CSourceSOAP",
    "smtp"        => "CSourceSMTP",
    "pop"         => "CSourcePOP",
    "file_system" => "CSourceFileSystem",
    "http"        => "CSourceHTTP",
  );
  
  // DB Fields
  var $name               = null;
  var $role               = null;
  var $host               = null;
  var $user               = null;
  var $password           = null;
  var $type_echange       = null;
  var $active             = null;
  var $loggable           = null;
  
  // Behaviour Fields
  var $_client            = null;
  var $_data              = null;
  var $_args_list         = false;
  var $_allowed_instances = null;
  var $_wanted_type       = null;
  var $_incompatible      = false;
  var $_reachable         = null;
  var $_message           = null;
  var $_response_time     = null;
  var $_all_source        = array();
  var $_receive_filename  = null;
  var $_acquittement      = null;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["name"]           = "str notNull";
    $specs["role"]           = "enum list|prod|qualif default|qualif notNull";
    $specs["host"]           = "text notNull";
    $specs["user"]           = "str";
    $specs["password"]       = "password revealable";
    $specs["type_echange"]   = "str protected";
    $specs["active"]         = "bool default|1 notNull";
    $specs["loggable"]       = "bool default|1 notNull";
    
    $specs["_incompatible"]  = "bool";
    $specs["_reachable"]     = "enum list|0|1|2 default|0";
    $specs["_response_time"] = "float";
    
    return $specs;
  }

  static function getExchangeClasses() {
    return self::$typeToClass;
  }
  
  static function getObjects($name, $type = null, $type_echange = null) {
    if ($type) {
      return null;
    }
    
    $exchange_objects = array();
    foreach (self::getExchangeClasses() as $_class) {
      $object = new $_class;
      if (!$object->_ref_module) {
        continue;
      }
      $object->name = $name;
      $object->loadMatchingObject();
      $object->type_echange = $type_echange;
      
      $exchange_objects[$_class] = $object;
    }
    
    return $exchange_objects;
  } 
    
  static function get($name, $type = null, $override = false, $type_echange = null, $only_active = true) {
    $exchange_classes = self::getExchangeClasses(); 
    foreach ($exchange_classes as $_class) {
      $exchange_source         = new $_class;
      if (isset(self::$typeToClass[$type])) {
        $classname = self::$typeToClass[$type];
        if ($classname != $exchange_source->_class) {
          continue;
        }
      }
    
      if ($only_active) {
        $exchange_source->active = 1;
      }
      
      $exchange_source->name = $name;
      $exchange_source->loadMatchingObject();
      
      if ($exchange_source->_id) {
        $exchange_source->_wanted_type = $type;
        $exchange_source->_allowed_instances = self::getObjects($name, $type, $type_echange);
        if ($exchange_source->role != CAppUI::conf("instance_role")) {
          if (!$override) {
            $incompatible_source       = new $exchange_source->_class;
            $incompatible_source->name = $exchange_source->name;
            $incompatible_source->_incompatible = true;
            CAppUI::displayAjaxMsg("CExchangeSource-_incompatible", UI_MSG_ERROR);
            return $incompatible_source;
          }
          $exchange_source->_incompatible = true;
        }
        return $exchange_source;
      }
    }
    
    $source = new CExchangeSource();
    if (isset(self::$typeToClass[$type])) {
      $source = new self::$typeToClass[$type];
    }

    $source->name = $name;
    $source->type_echange = $type_echange;
    $source->_wanted_type = $type;
    $source->_allowed_instances = self::getObjects($name, $type, $type_echange);

    return $source;
  }
  
  function check() {
    $source = self::get($this->name, null, true);

    if ($source->_id && ($source->_id != $this->_id)) {
      $this->active = 0;
    }
    
    return parent::check();
  }
  
  function store() {
    if ($this->password === "") {
      $this->password = null;
    }
    
    return parent::store();
  }
  
  function setData($data, $argsList = false, CExchangeDataFormat $exchange = null) {
    $this->_args_list = $argsList;
    $this->_data = $data;
  }
  
  function getData($path) {}

  function delFile($path) {}
  
  function send($evenement_name = null) {}
  
  function getACQ() {    
    return $this->_acquittement;
  }
  
  function receiveOne() {}
  
  function receive() {}
  
  /**
   * Source is reachable ?
   * @return boolean reachable
   */
  function isReachable() {
    if (!$this->active) {
      $this->_reachable = 1;
      $this->_message   = CAppUI::tr("CExchangeSource_no-active", $this->host);
      return;
    }
    
    if (!$this->isReachableSource()) {
      return;
    }

    if (!$this->isAuthentificate()) {
      return;
    }
    
    $this->_reachable = 2;
    $this->_message   = CAppUI::tr("$this->_class-reachable-source", $this->host);
  }
  
  function isReachableSource() {}
  
  function isAuthentificate() {}
  
  function getResponseTime() {}
}

if (CModule::getActive("hl7")) {
  CExchangeSource::$typeToClass = CExchangeSource::$typeToClass + array("mllp" => "CSourceMLLP");
}

if (CModule::getActive("dicom")) {
  CExchangeSource::$typeToClass = CExchangeSource::$typeToClass + array("dicom" => "CSourceDicom");
}
?>