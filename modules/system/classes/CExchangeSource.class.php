<?php
/**
 * Exchange Source
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Exchange Source
 */
class CExchangeSource extends CMbObject {
  static $typeToClass = array(
    "sftp"        => "CSourceSFTP",
    "ftp"         => "CSourceFTP",
    "soap"        => "CSourceSOAP",
    "smtp"        => "CSourceSMTP",
    "pop"         => "CSourcePOP",
    "file_system" => "CSourceFileSystem",
    "http"        => "CSourceHTTP",
    "syslog"      => "CSyslogSource",
  );

  //multi instance sources (more than one can run at the same time)
  static $multi_instance = array(
    "CSourcePOP",
    "CSourceSMTP",
  );

  // DB Fields
  public $name;
  public $role;
  public $host;
  public $user;
  public $password;
  public $iv;
  public $type_echange;
  public $active;
  public $loggable;
  public $libelle;

  // Behaviour Fields
  public $_client;
  public $_data;
  public $_args_list = false;
  public $_allowed_instances;
  public $_wanted_type;
  public $_incompatible = false;
  public $_reachable;
  public $_message;
  public $_response_time;
  public $_all_source = array();
  public $_receive_filename;
  public $_acquittement;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                 = parent::getProps();
    $props["name"]         = "str notNull";
    $props["role"]         = "enum list|prod|qualif default|qualif notNull";
    $props["host"]         = "text notNull autocomplete";
    $props["user"]         = "str";
    $props["password"]     = "password show|0 loggable|0";
    $props["iv"]           = "str show|0 loggable|0";
    $props["type_echange"] = "str protected";
    $props["active"]       = "bool default|1 notNull";
    $props["loggable"]     = "bool default|1 notNull";
    $props["libelle"]      = "str";

    $props["_incompatible"]  = "bool";
    $props["_reachable"]     = "enum list|0|1|2 default|0";
    $props["_response_time"] = "float";

    return $props;
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
   * @param string $name         Name of the exchange source
   * @param null   $type         Always null
   * @param string $type_echange Exchange type
   *
   * @return array|null
   */
  static function getObjects($name, $type = null, $type_echange = null) {
    if ($type) {
      return null;
    }

    $exchange_objects = array();
    foreach (self::getExchangeClasses() as $_class) {
      /** @var CExchangeSource $object */
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
   * Get the exchange source
   *
   * @param string $name         Nom
   * @param string $type         Type de la source (FTP, SOAP, ...)
   * @param bool   $override     Charger les autres sources
   * @param string $type_echange Type de l'échange
   * @param bool   $only_active  Seulement la source active
   *
   * @return CExchangeSource
   */
  static function get($name, $type = null, $override = false, $type_echange = null, $only_active = true) {
    $cache = new Cache(__METHOD__, func_get_args(), Cache::INNER);
    if ($cache->exists()) {
      return $cache->get();
    }

    $exchange_classes = self::getExchangeClasses();
    foreach ($exchange_classes as $_class) {
      /** @var CExchangeSource $exchange_source */
      $exchange_source = new $_class;
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
        $exchange_source->_wanted_type       = $type;
        $exchange_source->_allowed_instances = self::getObjects($name, $type, $type_echange);
        if ($exchange_source->role != CAppUI::conf("instance_role")) {
          if (!$override) {
            $incompatible_source                = new $exchange_source->_class;
            $incompatible_source->name          = $exchange_source->name;
            $incompatible_source->_incompatible = true;
            CAppUI::displayAjaxMsg("CExchangeSource-_incompatible", UI_MSG_ERROR);

            return $incompatible_source;
          }
          $exchange_source->_incompatible = true;
        }

        return $cache->put($exchange_source, false);
      }
    }

    $source = new CExchangeSource();
    if (isset(self::$typeToClass[$type])) {
      $source = new self::$typeToClass[$type];
    }

    $source->name               = $name;
    $source->type_echange       = $type_echange;
    $source->_wanted_type       = $type;
    $source->_allowed_instances = self::getObjects($name, $type, $type_echange);

    return $cache->put($source, false);
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

  function updateEncryptedFields() {
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
      if ($this->fieldModified("password") || !$this->_id) {
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

        $keyAB = file($master_key_filepath . "/.mediboard.key");

        if (count($keyAB) == 2) {
          $cipher->setKey($keyAB[0] . $keyAB[1]);

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
      if (!$this->password) {
        return "";
      }
    }

    try {
      $master_key_filepath = CAppUI::conf("master_key_filepath");
      $master_key_filepath = rtrim($master_key_filepath, "/");

      if (CExchangeSource::checkMasterKeyFile($master_key_filepath)) {
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/AES");
        CAppUI::requireLibraryFile("phpseclib/phpseclib/Crypt/Random");

        $cipher = new Crypt_AES(CRYPT_AES_MODE_CTR);
        $cipher->setKeyLength(256);

        $keyAB = file($master_key_filepath . "/.mediboard.key");

        if (count($keyAB) == 2) {
          $cipher->setKey($keyAB[0] . $keyAB[1]);

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
    if (!is_readable($master_key_filepath . "/.mediboard.key")) {
      return false;
    }

    return true;
  }

  function setData($data, $argsList = false, CExchangeDataFormat $exchange = null) {
    $this->_args_list = $argsList;
    $this->_data      = $data;
  }

  function getData($path) {
  }

  function delFile($path) {
  }

  function send($evenement_name = null) {
  }

  function getACQ() {
    return $this->_acquittement;
  }

  function receiveOne() {
  }

  /**
   * Receive
   *
   * @return mixed
   */
  function receive() {
  }

  function renameFile($oldname, $newname) {
  }

  function changeDirectory($directory_name) {
  }

  function getCurrentDirectory($directory = null) {
  }

  function getListDirectory($current_directory) {
  }

  function getListFilesDetails($directory) {
  }

  function addFile($file, $file_name, $directory) {
  }

  /**
   * Source is reachable ?
   * @return boolean reachable
   */
  function isReachable() {
    $this->_reachable = 0;
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

  function isReachableSource() {
  }

  function isAuthentificate() {
  }

  function getResponseTime() {
  }

  /**
   * Get child exchanges
   *
   * @return string[] Data format classes collection
   */
  static function getAll() {
    $sources = CApp::getChildClasses("CExchangeSource", true);

    return array_filter($sources, function($v) {
      $s = new $v();
      return ($s->_spec->key);
    });
  }
}

if (CModule::getActive("hl7")) {
  CExchangeSource::$typeToClass["mllp"] = "CSourceMLLP";
}

if (CModule::getActive("dicom")) {
  CExchangeSource::$typeToClass["dicom"] = "CSourceDicom";
}

if (CModule::getActive("mssante")) {
  CExchangeSource::$typeToClass["mssante"] = "CSourceMSSante";
  CExchangeSource::$multi_instance[]       = "CSourceMSSante";
}