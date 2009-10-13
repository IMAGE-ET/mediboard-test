<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Test class for CMbObject class

class CMbObjectTest {
  
  var $log = "";
  
  function sample(&$object, $staticsProps = array()){
    foreach($object->_specs as $key => $spec){
      if(isset($staticsProps[$key])){
        $object->$key = $staticsProps[$key];
      }
      elseif($key[0] != "_"){
        $spec->sample($object, false);
      }
    }
  }
  
  function testFunction(&$object, $function_name, $params = array()) {
    $str_params = implode(",", $params);
    $result = $object->$function_name($str_params);
    $log = "<div class=\"message\">".get_class($object)."::$function_name($str_params) -> $result</div>".CAppUI::getMsg();
    $this->addLog($log);
  }
  
  function resetLog() {
    $this->log = "";
  }
  
  function addLog($log) {
    $this->log .= "$log<br />";
  }
  
  function getLog() {
    return $this->log;
  }
  
}

?>