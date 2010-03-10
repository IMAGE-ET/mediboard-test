<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExchangeSource extends CMbObject {
  // DB Fields
  var $name     = null;
  var $host     = null;
  var $user     = null;
  var $password = null;
  
  // Behaviour Fields
  var $_client      = null;
  var $_data        = null;
  var $_args_list   = false;
  var $_allowed_instances = null;
  var $_wanted_type = null;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["name"]     = "str notNull";
    $specs["host"]     = "text notNull";
    $specs["user"]     = "str";
    $specs["password"] = "password";
    
    return $specs;
  }
   
  static function getExchangeClasses() {
    CAppUI::getAllClasses();
      
    return getChildClasses("CExchangeSource");
  }
  
  static function getObjects($name, $type) {
    if ($type) {
      return null;
    }
    
    $exchange_objects = array();
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_objects[$_class] = new $_class;
      $exchange_objects[$_class]->name = $name;
    }
    
    return $exchange_objects;
  } 
  
  static function get($name, $type = null) {
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_source = new $_class;
      $exchange_source->name = $name;
      $exchange_source->loadMatchingObject();
      if ($exchange_source->_id) {
        $exchange_source->_wanted_type = $type;
        $exchange_source->_allowed_instances = self::getObjects($name, $type);
        return $exchange_source;
      }
    }
    $source = new CExchangeSource();
    if ($type == "soap") {
      $source = new CSourceSOAP();
    }
    if ($type == "ftp") {
      $source = new CSourceFTP();
    }
    $source->name = $name;
    $source->_wanted_type = $type;
    $source->_allowed_instances = self::getObjects($name, $type);
    return $source;
  }
  
  function check() {
    $source = self::get($this->name);
    if (!$this->_id && $source->_id) {
      return "Impossible de créer une nouvelle source"; // Mettre en traduction
    }
    
    return parent::check();
  }
  
  function setData($data, $argsList = false) {
    $this->_args_list = $argsList;
    $this->_data = $data;
  }
    
  function send($evenement_name = null) {}
  
  function receive() {}
}
?>