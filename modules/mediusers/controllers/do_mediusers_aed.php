<?php

/**
 * Mediuser
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// we don't allow anybody to change his user type or profile
if ($_POST["user_id"] && !CAppUI::$user->isAdmin() && !CModule::getCanDo("admin")->admin) {
  unset($_POST['_user_type']);
  unset($_POST['_profile_id']);
}

/**
 * Class CDoMediuserAddEdit
 */
class CDoMediuserAddEdit extends CDoObjectAddEdit {
  /**
   * Construct
   *
   * @return void
   */
  function CDoMediuserAddEdit() {
    $this->CDoObjectAddEdit("CMediusers", "user_id");
  }

  /**
   * Store
   *
   * @return void
   */
  function doStore() {
    // keep track of former values for fieldModified below
    $obj = $this->_obj;
    $old = $obj->loadOldObject();

    if ($msg = $obj->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      if ($this->redirectError) {
        CAppUI::redirect($this->redirectError);
      }
    }
    else {
      // Keep trace for redirections
      CValue::setSession($this->objectKey, $obj->_id);
      
      // Insert new group and function permission
      if ($obj->fieldModified("function_id") || !$old->_id) {
        $obj->insFunctionPermission(); 
        $obj->insGroupPermission();
      }
      
      // Message
      CAppUI::setMsg($old->_id ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      
      // Redirection
      if ($this->redirectStore) {
        CAppUI::redirect($this->redirectStore);
      }
    }
  }
}

$do = new CDoMediuserAddEdit();
$do->doIt();
