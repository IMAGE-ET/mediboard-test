<?php /* CLASSES $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision$
 */

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('system', 'user_log') );

class CDoObjectAddEdit {
  var $className = null;
  var $objectKeyGetVarName = null;
  var $createMsg = null;
  var $modifyMsg = null;
  var $deleteMsg = null;
  var $redirect = null;
  var $redirectStore  = null;
  var $redirectError  = null;
  var $redirectDelete = null;
  var $ajax = null;
  var $suppressHeaders = null;
  var $_obj = null;
  var $_logIt = null;
    
  function CDoObjectAddEdit($className, $objectKeyGetVarName) {
    global $m;
    
    $this->className = $className;
    $this->objectKeyGetVarName = $objectKeyGetVarName;
    $this->redirect = "m={$m}";
    $this->redirectStore  = null;
    $this->redirectError  = null;
    $this->redirectDelete = null;
    $this->createMsg = "Object of type $className created";
    $this->modifyMsg = "Object of type $className modified";
    $this->deleteMsg = "Object of type $className deleted";
    $this->_logIt = true;
  }
  
  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax", 0);
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders", 0);
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    
    //UTF8 issue for Ajax
    if($this->ajax) {
      foreach($_POST as $key => $value) {
        $_POST[$key] = utf8_decode($value);
      }
    }
    
    // Object binding
    $this->_obj = new $this->className();
    if (!$this->_obj->bind( $_POST )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }
  }
  
  function doDelete() {
    global $AppUI;

    if ($msg = $this->_obj->delete()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } else {
      mbSetValueToSession($this->objectKeyGetVarName);
      $this->doLog("delete");
      $AppUI->setMsg($this->deleteMsg, UI_MSG_ALERT);
      if ($this->redirectDelete) {
        $this->redirect =& $this->redirectDelete;
      }
    }
  }
  
  function doStore () {
    global $AppUI;

    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } else {
      $id = $this->objectKeyGetVarName;
      mbSetValueToSession($id, $this->_obj->$id);
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      $this->doLog("store");
      $AppUI->setMsg( $isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      $this->redirect =& $this->redirectStore;
    }
  }
  
  function doRedirect() {
    global $AppUI;
    if ($this->ajax) {
      $id = $this->objectKeyGetVarName;
      echo $AppUI->getMsg();
      echo "<script type='text/javascript'>$id = $this->_obj->$id</script>";
      exit;
    } elseif ($this->redirect !== null) {
      $AppUI->redirect($this->redirect);
    }
  }
  
  function doLog($type) {
    global $AppUI;
    if($this->_logIt) {
      $log = new CuserLog;
      $log->user_id = $AppUI->user_id;
      $objectKey = $this->_obj->_tbl_key;
      $log->object_id = $this->_obj->$objectKey;
      $log->object_class = $this->className;
      $log->type = $type;
      $log->date = mbDateTime();
      $log->store();
    }
  }

  function doIt() {
    $this->doBind();
    
    if (intval(dPgetParam($_POST, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    
    $this->doRedirect();
  }
  
}
