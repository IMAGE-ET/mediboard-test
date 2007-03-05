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
      if($key[0] != "_"){
        if(isset($staticsProps[$key])){
          $object->$key = $staticsProps[$key];
        }else{
          $spec->sample($object);
        }
      }
    }
  }
  
  function testFunction($object, $function_name) {
    return true;
  }
  
}

?>