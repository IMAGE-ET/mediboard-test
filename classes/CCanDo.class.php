<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * CanDo class
 * 
 * Allow to check permissions on a module with redirect helpers
 */ 
class CCanDo {
  var $read       = null;
  var $edit       = null;
  var $view       = null;
  var $admin      = null;
  var $setValues  = null;
  
  /**
   * Redirection facility
   *  
   * @param string $action Action view     
   * @param string $params HTTP GET styled paramters
   * 
   * @return void
   */
  function redirect($action = "access_denied", $params = null){
    global $actionType;

    // on passe a null soit "tab" soit "a" selon ou l'on se trouve
    CValue::setSession($actionType);
    
    if ($this->setValues) {
      if (is_scalar($this->setValues)) {
        CValue::setSession($this->setValues);
      } 
      else {
        foreach ($this->setValues as $key => $value) {
          CValue::setSession($key, $value);
        }
      }
    }
    
    $dialog          = CValue::get("dialog");
    $suppressHeaders = CValue::get("suppressHeaders");
    $ajax            = CValue::get("ajax");
    CAppUI::redirect("m=system&a=$action&dialog=$dialog&ajax=$ajax&suppressHeaders=$suppressHeaders".$params);
  }
  
  /** 
   * Check if the connected user has READ rights on the current page
   * 
   * @return void
   */
  function needsRead($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->read) {
      $this->redirect();
    }
  }
  
  /** 
   * Check if the connected user has EDIT rights on the current page
   * 
   * @return void
   */
  function needsEdit($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->edit) {
      $this->redirect();
    }
  }
  
  /** 
   * Check if the connected user has ADMIN rights on the current page
   * 
   * @return void
   */
  function needsAdmin($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->admin) {
      $this->redirect();
    }
  }
  
  function needsObject(CMbObject $object, $setValues = null){
    $this->setValues = $setValues;
    if (!$object->_id){
      $params = "&object_guid=$object->_class-?";
      $this->redirect("object_not_found", $params);
    }
  }

  static function checkObject(CMbObject $object, $setValues = null){
    global $can;
    $can->needsObject($object, $setValues);
  }

  /** 
   * Check if the connected user has READ rights on the current page
   * @return void
   */
  static function checkRead() {
    global $can;
    $can->needsRead();
  }

  /** 
   * Return the global READ permission
   * @return bool
   */
  static function read() {
    global $can;
    return $can->read;
  }
  
  /** 
   * Check if the connected user has EDIT rights on the current page
   * @return void
   */
  static function checkEdit() {
    global $can;
    $can->needsEdit();
  }

  /** 
   * Return the global EDIT permission
   * @return bool
   */
  static function edit() {
    global $can;
    return $can->edit;
  }
  
  /** 
   * Check if the connected user has ADMIN rights on the current page
   * @return void
   */
  static function checkAdmin() {
    global $can;
    $can->needsAdmin();
  }

  /** 
   * Return the global ADMIN permission
   * @return bool
   */
  static function admin() {
    global $can;
    return $can->admin;
  }
  
  /**
   * Dummy check method with no control
   * Enables differenciation between no-check and undefined-check views
   * @return void
   */
  static function check() {
  }
}
