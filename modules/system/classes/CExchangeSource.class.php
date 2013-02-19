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

  //multi instance sources (more than one can run at the same time)
  static $multi_instance = array(
    "CSourcePOP",
    "CSourceSMTP"
  );
  
  // DB Fields
  var $name               = null;
  var $role               = null;
  var $host               = null;
  var $user               = null;
  var $password           = null;
  var $iv                 = null;
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

  /**
   * db properties
   *
   * @return array
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["name"]           = "str notNull";
    $specs["role"]           = "enum list|prod|qualif default|qualif notNull";
    $specs["host"]           = "text notNull";
    $specs["user"]           = "str";
    $specs["password"]       = "password show|0 loggable|0";
    $specs["iv"]             = "str show|0 loggable|0";
    $specs["type_echange"]   = "str protected";
    $specs["active"]         = "bool default|1 notNull";
    $specs["loggable"]       = "bool default|1 notNull";
    
    $specs["_incompatible"]  = "bool";
    $specs["_reachable"]     = "enum list|0|1|2 default|0";
    $specs["_response_time"] = "float";
    
    return $specs;
  }

  /**
   * return the array of exchange classes
   *
   * @return array
   */
  static function getExchangeClasses() {
    return self::$typeToClass;
  }

  /**
   * return the exchange object
   *
   * @param string $name name of the exchange source
   * @param null   $type always null
   * @param strin  $type_echange
   * @return array|null
   */
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

  /**
   * get the exchange source
   *
   * @param $name
   * @param null $type
   * @param bool $override
   * @param null $type_echange
   * @param bool $only_active
   * @return CExchangeSource
   */
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

  /**
   * check before store
   *
   * @return string
   */
  function check() {
    $source = self::get($this->name, null, true);

    if ($source->_id && ($source->_id != $this->_id)) {
      $this->active = 0;
    }
    
    return parent::check();
  }

  function updateEncryptedFields(){

  }


  /**
   * store function
   *
   * @return null|string
   */
  function store() {
    if ($this->password === "") {
      $this->password = null;
    }
    else {
      if (!empty($this->password)) {
        $this->password = $this->encryptString();
      }
    }

    $this->updateEncryptedFields();

    return parent::store();
  }

  function encryptString($pwd = null, $iv_field = "iv") {
    if (is_null($pwd)) {
      $pwd = $this->password;
    }

    try {
      $master_key_filepath = CAppUI::conf("master_key_filepath");
      $master_key_filepath = rtrim($master_key_filepath, "/");

      if (CExchangeSource::checkMasterKeyFile($master_key_filepath)) {
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");
        $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
        // keys are null-padded to the closest valid size
        // longer than the longest key and it's truncated
        $cipher->setKeyLength(256);

        $keyAB = file($master_key_filepath."/.mediboard.key");

        if (count($keyAB) == 2) {
          $cipher->setKey($keyAB[0].$keyAB[1]);

          $iv = bin2hex(crypt_random_string(16));

          $this->{$iv_field} = $iv;

          $cipher->setIV($iv);

          $encrypted = rtrim(base64_encode($cipher->encrypt($pwd)), "\0\3");

          if ($encrypted) {
            return $encrypted;
          }
        }
      }
      else {
        // Key is not available
        $this->{$iv_field} = "";
      }
    }
    catch (Exception $e) {
      return $pwd;
    }

    return $pwd;
  }

  function getPassword($pwd = null, $iv_field = "iv") {
    if (is_null($pwd)) {
      $pwd = $this->password;
    }

    try {
      $master_key_filepath = CAppUI::conf("master_key_filepath");
      $master_key_filepath = rtrim($master_key_filepath, "/");

      if (CExchangeSource::checkMasterKeyFile($master_key_filepath)) {
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");

        $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
        $cipher->setKeyLength(256);

        $keyAB = file($master_key_filepath."/.mediboard.key");

        if (count($keyAB) == 2) {
          $cipher->setKey($keyAB[0].$keyAB[1]);

          $ivToUse = $this->{$iv_field};

          if (!$ivToUse) {
            $clear = $pwd;
            $this->store();

            return $clear;
          }

          $cipher->setIV($ivToUse);
          $decrypted = rtrim(base64_decode($pwd), "\0\3");
          $decrypted = $cipher->decrypt($decrypted);

          if ($decrypted) {
            return $decrypted;
          }
        }
      }
    }
    catch (Exception $e) {
      return $pwd;
    }

    return $pwd;
  }

  static function checkMasterKeyFile($master_key_filepath) {
    $master_key_filepath = rtrim($master_key_filepath, "/");
    if (!is_readable($master_key_filepath."/.mediboard.key")) {
      return false;
    }

    return true;
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