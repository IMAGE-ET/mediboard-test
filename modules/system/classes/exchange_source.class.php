<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExchangeSource extends CMbObject {
  static $typeToClass = array (
    "ftp"  => "CSourceFTP",
    "soap" => "CSourceSOAP"
  );
  
  // DB Fields
  var $name         = null;
  var $role         = null;
  var $host         = null;
  var $user         = null;
  var $password     = null;
  var $type_echange = null;
	
  // Behaviour Fields
  var $_client      = null;
  var $_data        = null;
  var $_args_list   = false;
  var $_allowed_instances = null;
  var $_wanted_type  = null;
  var $_incompatible = false;
  
  function getProps() {
    $specs = parent::getProps();
    $specs["name"]     = "str notNull";
    $specs["role"]     = "enum list|prod|qualif default|qualif notNull";
    $specs["host"]     = "text notNull";
    $specs["user"]     = "str";
    $specs["password"] = "password";
		$specs["type_echange"] = "str protected";
    
    $specs["_incompatible"] = "bool";
    
    return $specs;
  }
   
  static function getExchangeClasses() {
    CAppUI::getAllClasses();
      
    return getChildClasses("CExchangeSource");
  }
  
  static function getObjects($name, $type = null, $type_echange = null) {
    if ($type) {
      return null;
    }
    
    $exchange_objects = array();
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_objects[$_class] = new $_class;
      $exchange_objects[$_class]->name = $name;
			$exchange_objects[$_class]->type_echange = $type_echange;
    }
    
    return $exchange_objects;
  } 
    
  static function get($name, $type = null, $override = false, $type_echange = null) {
    foreach (self::getExchangeClasses() as $_class) {
      $exchange_source = new $_class;
      $exchange_source->name = $name;
      $exchange_source->loadMatchingObject();
      if ($exchange_source->_id) {
        $exchange_source->_wanted_type = $type;
        $exchange_source->_allowed_instances = self::getObjects($name, $type, $type_echange);
        if ($exchange_source->role != CAppUI::conf("instance_role")) {
          if (!$override) {
            $incompatible_source = new $exchange_source->_class_name;
            $incompatible_source->name = $exchange_source->name;
            $incompatible_source->_incompatible = true;
            CAppUI::displayAjaxMsg(CAppUI::tr("CExchangeSource-_incompatible"), UI_MSG_ERROR);
            return $incompatible_source;
          }
          $exchange_source->_incompatible = true;
        }
        return $exchange_source;
      }
    }
    $source = new CExchangeSource();
    if ($type) {
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
    if (!$this->_id && $source->_id) {
      return CAppUI::tr("CExchangeSource-already-exist");
    }
    
    return parent::check();
  }
  
  function store() {
    if ($this->password === "") {
      $this->password = null;
    }
    
    return parent::store();
  }
  
  function setData($data, $argsList = false) {
    $this->_args_list = $argsList;
    $this->_data = $data;
  }
    
  function send($evenement_name = null) {}
  
  function receive() {}
}
?>