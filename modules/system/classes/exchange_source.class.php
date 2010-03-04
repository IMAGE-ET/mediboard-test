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
  var $_client  = null;
  var $_data    = null;
  
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
  
  static function getObjects() {
    $exchange_objects = array();
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_objects[$_class] = new $_class;
    }
    
    return $exchange_objects;
  } 
  
  static function get($name, $type = null) {
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_source = new $_class;
      $exchange_source->name = $name;
      $exchange_source->loadMatchingObject();
      if ($exchange_source->_id) {
        return $exchange_source;
      }
    }
    if ($type) {
      if ($type == "soap") {
        return new CSourceSOAP();
      }
      if ($type == "ftp") {
        return new CSourceFTP();
      }
    }
  }
  
  function check() {
    if (!$this->_id && $source = self::get($this->name)) {
      return "Impossible de créer une nouvelle source"; // Mettre en traduction
    }
    
    return parent::check();
  }
  
  function setData($data) {
    $this->_data = $data;
  }
    
  function send($evenement_name = null) {}
  
  function receive() {}
}
?>