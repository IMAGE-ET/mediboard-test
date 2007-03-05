<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Romain Ollivier
*/

// Test class for CMbObject class

class CMbObjectTest {
  
  function sample(&$object, $staticsProps = array()){
    foreach($object->_specs as $key => $spec){
      if(isset($staticsProps[$key])){
        $object->$key = $staticsProps[$key];
      }
      elseif($key[0] != "_"){
        $spec->sample($object);
      }
    }
  }
  
  function testFunction(&$object, $function_name, $params = array()) {
    global $AppUI;
    $str_params = implode(",", $params);
    $result = $object->$function_name($str_params);
    $log = get_class($object)."::$function_name($str_params) -> $result ()";
    $this->addLog($log);
    
  }
  
  function addLog($log) {
    return true;
  }
  
}

?>