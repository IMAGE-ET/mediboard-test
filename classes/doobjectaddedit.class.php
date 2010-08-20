<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDoObjectAddEdit {
  var $className           = null;
  var $objectKey           = null;
  var $createMsg           = null;
  var $modifyMsg           = null;
  var $deleteMsg           = null;
  var $request             = null;
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

  function CDoObjectAddEdit($className, $objectKey = null) {
    global $m;

    $this->className           = $className;
    $this->postRedirect        = null;
    $this->redirect            = "m={$m}";
    $this->redirectStore       = null;
    $this->redirectError       = null;
    $this->redirectDelete      = null;

    $this->createMsg           = CAppUI::tr("$className-msg-create");
    $this->modifyMsg           = CAppUI::tr("$className-msg-modify");
    $this->deleteMsg           = CAppUI::tr("$className-msg-delete");
    
    $this->request             =& $_POST;

    $this->_logIt              = true;
    $this->_obj                = new $this->className();
    $this->_objBefore          = new $this->className();
    
    $this->objectKey = $objectKey ? $objectKey : $this->_obj->_spec->key;
  }

  function doBind() {
    $this->ajax            = CMbArray::extract($this->request, "ajax");
    $this->suppressHeaders = CMbArray::extract($this->request, "suppressHeaders");
    $this->callBack        = CMbArray::extract($this->request, "callback");
    $this->postRedirect    = CMbArray::extract($this->request, "postRedirect");
    if($this->postRedirect) {
      $this->redirect = $this->postRedirect;
    }
    
    // Object binding
    $this->_obj->bind($this->request);
    
    $this->_objBefore->load($this->_obj->_id);
  }

  function doDelete() {
    if ($this->_obj->_purge) {
      set_time_limit(120);
      if ($msg = $this->_obj->purge()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR );
        if ($this->redirectError) {
          $this->redirect =& $this->redirectError;
        }
      }
      else {
        CValue::setSession($this->objectKey);
        CAppUI::setMsg("msg-purge", UI_MSG_ALERT);
        if ($this->redirectDelete) {
          $this->redirect =& $this->redirectDelete;
        }
      }
      return;
    }
    
    if ($msg = $this->_obj->delete()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } 
    else {
      CValue::setSession($this->objectKey);
      CAppUI::setMsg($this->deleteMsg, UI_MSG_ALERT);
      if ($this->redirectDelete) {
        $this->redirect =& $this->redirectDelete;
      }
    }
  }

  function doStore() {
    if ($msg = $this->_obj->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } 
    else {
      $id = $this->objectKey;
      CValue::setSession($id, $this->_obj->_id);
      $this->isNotNew = CValue::read($this->request, $this->objectKey);
      CAppUI::setMsg($this->isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
    }
  }

  function doRedirect() {
    if ($this->redirect === null) {
      return;
    }
    
    // Cas ajax
    if ($this->ajax) {
      $this->doCallback();
    }

    // Cas normal
    CAppUI::redirect($this->redirect);
  }
  
  function doCallback(){
    echo CAppUI::getMsg();
    $fields = $this->_obj->getDBFields();
    $json = @json_encode($fields);
    $id = $this->_obj->_id ? $this->_obj->_id : 0;
    if ($this->callBack) {
      echo "\n<script type=\"text/javascript\">{$this->callBack}($id, $json)</script>";
    }
		else {
			$guid = "$this->className-$id";
      echo "\n<script type=\"text/javascript\">Form.onSubmitComplete('$guid', $json)</script>";
		}
    CApp::rip();
  }

  function doIt() {
    $this->doBind();
    if (intval(CValue::read($this->request, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    $this->doRedirect();
  }

  /**
   * Sets a error messages and redirects
   * @param string $msg 
   */
  function errorRedirect($msg) {
	  CAppUI::setMsg($msg, UI_MSG_ERROR);
	  $this->redirect =& $this->redirectError;
	  $this->doRedirect();
  }
}
