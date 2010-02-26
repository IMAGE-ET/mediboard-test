<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
/**
 * CanDo class
 * Allow to check permissions on a module with redirect helpers
 */ 
class CCanDo {
  var $read       = null;
  var $edit       = null;
  var $view       = null;
  var $admin      = null;
  var $setValues  = null;
  
  function redirect($a = "access_denied", $params = null){
    global $actionType;

    // on passe a null soit "tab" soit "a" selon ou l'on se trouve
    CValue::setSession($actionType);
    
    if($this->setValues){
      if(is_scalar($this->setValues)){
        CValue::setSession($this->setValues);
      }else{
        foreach($this->setValues as $key => $value){
          CValue::setSession($key, $value);
        }
      }
    }
    
    $dialog = CValue::get("dialog");
    $suppressHeaders = CValue::get("suppressHeaders");
    $ajax = CValue::get("ajax");
    CAppUI::redirect("m=system&a=$a&dialog=$dialog&ajax=$ajax&suppressHeaders=$suppressHeaders".$params);
  }
  
  function needsRead($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->read) {
      $this->redirect();
    }
  }

  function needsEdit($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->edit) {
      $this->redirect();
    }
  }

  function needsAdmin($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->admin) {
      $this->redirect();
    }
  }
  
  function needsObject($object, $setValues = null){
    $this->setValues = $setValues;
    if(!$object->_id){
      $params = "&object_guid=$object->_class_name-?";
      $this->redirect("object_not_found", $params);
    }
  }
	
  static function checkRead() {
    global $can;
    $can->needsRead();
  }

  static function checkEdit() {
    global $can;
    $can->needsEdit();
  }

  static function checkAdmin() {
    global $can;
    $can->needsAdmin();
  }

	
}
?>