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
  
  function needsRead() {
    if (!$this->read) {
      global $AppUI;
      $dialog = mbGetValueFromGet("dialog");
      $AppUI->redirect("m=system&a=access_denied&dialog=$dialog");
    }
  }

  function needsEdit() {
    if (!$this->edit) {
      global $AppUI;
      $dialog = mbGetValueFromGet("dialog");
      $AppUI->redirect("m=system&a=access_denied&dialog=$dialog");
    }
  }

  function needsAdmin() {
    if (!$this->admin) {
      global $AppUI;
      $dialog = mbGetValueFromGet("dialog");
      $AppUI->redirect("m=system&a=access_denied&dialog=$dialog");
    }
  }
}
?>