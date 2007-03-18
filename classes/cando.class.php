<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: $
 */
 
/**
 * CanDo class
 * Allow to check permissions on a module with redirect helpers
 */ 
class CCanDo {
  var $read = null;
  var $edit = null;
  var $view = null;
  var $admin = null;
  
  function redirect(){
    global $AppUI;
    $dialog = mbGetValueFromGet("dialog");
    $suppressHeaders = mbGetValueFromGet("suppressHeaders");
    $ajax = mbGetValueFromGet("ajax");
    $AppUI->redirect("m=system&a=access_denied&dialog=$dialog&ajax=$ajax&suppressHeaders=$suppressHeaders");
  }
  
  function needsRead() {
    if (!$this->read) {
      $this->redirect();
    }
  }

  function needsEdit() {
    if (!$this->edit) {
      $this->redirect();
    }
  }

  function needsAdmin() {
    if (!$this->admin) {
      $this->redirect();
    }
  }
}
?>