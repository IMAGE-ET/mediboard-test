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
  /** @var bool */
  public $read;

  /** @var bool */
  public $edit;

  /** @var bool */
  public $view;

  /** @var bool */
  public $admin;

  /** @var string  */
  public $context;

  /** @var  string|array Should not be used, find another redirection behavioural session mangagement */
  public $setValues;

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

    $action_params = "";
    foreach (array("wsdl", "info", "ajax", "raw", "dialog") as $_action_type) {
      $_action_flag = CValue::get($_action_type);
      if ($_action_flag) {
        $action_params .=  "&$_action_type=$_action_flag";
      }
    }

    $context_param = $this->context ? "&context=$this->context" : "";

    CAppUI::redirect("m=system&a=$action" . $context_param . $action_params . $params);
  }

  /**
   * Check if the connected user has READ rights on the current page
   *
   * @param null $setValues
   *
   * @return void
   */
  function needsRead($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->read) {
      $this->context .= " read permission";
      $this->redirect();
    }
  }
  
  /** 
   * Check if the connected user has EDIT rights on the current page
   *
   * @param null $setValues
   *
   * @return void
   */
  function needsEdit($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->edit) {
      $this->context .= " edit permission";
      $this->redirect();
    }
  }
  
  /** 
   * Check if the connected user has ADMIN rights on the current page
   *
   * @param null $setValues
   *
   * @return void
   */
  function needsAdmin($setValues = null) {
    $this->setValues = $setValues;
    if (!$this->admin) {
      $this->context .= " admin permission";
      $this->redirect();
    }
  }
  
  function needsObject(CMbObject $object, $setValues = null){
    $this->setValues = $setValues;
    if (!$object->_id) {
      $params = "&object_guid=$object->_guid";
      $this->redirect("object_not_found", $params);
    }
  }

  static function checkObject(CMbObject $object, $setValues = null){
    global $can;
    $can->needsObject($object, $setValues);
  }

  /** 
   * Check if the connected user has READ rights on the current page
   *
   * @return void
   */
  static function checkRead() {
    global $can;
    $can->needsRead();
  }

  /** 
   * Return the global READ permission
   *
   * @return bool
   */
  static function read() {
    global $can;
    return $can->read;
  }
  
  /** 
   * Check if the connected user has EDIT rights on the current page
   *
   * @return void
   */
  static function checkEdit() {
    global $can;
    $can->needsEdit();
  }

  /** 
   * Return the global EDIT permission
   *
   * @return bool
   */
  static function edit() {
    global $can;
    return $can->edit;
  }
  
  /** 
   * Check if the connected user has ADMIN rights on the current page
   *
   * @return void
   */
  static function checkAdmin() {
    global $can;
    $can->needsAdmin();
  }

  /** 
   * Return the global ADMIN permission
   *
   * @return bool
   */
  static function admin() {
    global $can;
    return $can->admin;
  }
  
  /**
   * Dummy check method with no control
   * Enables differenciation between no-check and undefined-check views
   *
   * @return void
   */
  static function check() {
  }
}
