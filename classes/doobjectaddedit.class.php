<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision$
 */

global $AppUI, $canRead, $canEdit, $m;

class CDoObjectAddEdit {
  var $className           = null;
  var $objectKeyGetVarName = null;
  var $createMsg           = null;
  var $modifyMsg           = null;
  var $deleteMsg           = null;
  var $redirect            = null;
  var $redirectStore       = null;
  var $redirectError       = null;
  var $redirectDelete      = null;
  var $isNotNew            = null;
  var $ajax                = null;
  var $callBack            = null;
  var $suppressHeaders     = null;
  var $_obj                = null;
  var $_objBefore          = null;
  var $_logIt              = null;

  function CDoObjectAddEdit($className, $objectKeyGetVarName) {
    global $m,$AppUI;

    $this->className           = $className;
    $this->objectKeyGetVarName = $objectKeyGetVarName;
    $this->redirect            = "m={$m}";
    $this->redirectStore       = null;
    $this->redirectError       = null;
    $this->redirectDelete      = null;

    $this->createMsg           = $AppUI->_("msg-".$className."-create");
    $this->modifyMsg           = $AppUI->_("msg-".$className."-modify");
    $this->deleteMsg           = $AppUI->_("msg-".$className."-delete");

    $this->_logIt              = true;
    $this->_obj                = new $this->className();
    $this->_objBefore          = new $this->className();
  }

  function doBind() {
    global $AppUI;

    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);

    // UTF8 issue for Ajax
    if ($this->ajax) {
      foreach($_POST as $key => $value) {
        if(is_scalar($value)){
          $_POST[$key] = utf8_decode($value);
        }
      }
    }

    // Object binding
    if (!$this->_obj->bind( $_POST )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }

    $this->_objBefore->load($this->_obj->_id);
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

  function doStore() {
    global $AppUI;
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } else {
      $id = $this->objectKeyGetVarName;
      mbSetValueToSession($id, $this->_obj->$id);
      $this->isNotNew = @$_POST[$this->objectKeyGetVarName];
      $this->doLog("store");
      $AppUI->setMsg($this->isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
    }
  }

  function doRedirect() {
    global $AppUI;

    if ($this->ajax) {
      $idName = $this->objectKeyGetVarName;
      $idValue = $this->_obj->$idName;
      $callBack = $this->callBack;
      echo $AppUI->getMsg();
      if ($callBack) {
        echo "\n<script type='text/javascript'>$callBack($idValue);</script>";
      }
      exit;
    }

    if ($this->redirect !== null) {
      $AppUI->redirect($this->redirect);
    }
  }

  function doLog($type) {
    global $AppUI;

    $fields = array();
    foreach ($this->_obj->getProps() as $propName => $propValue) {
      if ($propValue !== null) {
        $propValueBefore = $this->_objBefore->$propName;
        if ($propValueBefore != $propValue) {
          $fields[] = $propName;
        }
      }
    }
    
    if (!count($fields)) {
      return;
    }

    $object_id = $this->_obj->_id;
    $type = "store";
    if ($this->_objBefore->_id == null) {
      $type = "create";
      $fields = array();
    }

    if ($this->_obj->_id == null) {
      $type = "delete";
      $object_id = $this->_objBefore->_id;
      $fields = array();
    }

    if ($this->_logIt) {
      $log = new CUserLog;
      $log->user_id = $AppUI->user_id;
      $log->object_id = $object_id;
      $log->object_class = $this->className;
      $log->type = $type;
      $log->_fields = $fields;
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
