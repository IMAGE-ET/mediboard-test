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

class CDoObjectAddEdit {
  public $className;
  public $objectKey;
  public $objectKeys;
  public $createMsg;
  public $modifyMsg;
  public $deleteMsg;
  public $request;
  public $redirect;
  public $redirectStore;
  public $redirectError;
  public $redirectDelete;
  public $ajax;
  public $callBack;
  public $suppressHeaders;
  public $_logIt;

  public $_obj;
  public $_old;

  function CDoObjectAddEdit($className, $objectKey = null) {
    if (CAppUI::conf("readonly")) {
      CAppUI::stepAjax("Mode-readonly-title", UI_MSG_ERROR);
      return;
    }

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

    // @todo : à supprimer cf déplacement dans le doBind()
    $this->_obj                = new $this->className();
    $this->_old                = new $this->className();
    $this->onAfterInstanciation(); // Lancer ceci apres chaque instanciation de _obj et _old !!

    $this->objectKey = $objectKey ? $objectKey : $this->_obj->_spec->key;
    $this->objectKeys = $this->objectKey . "s";
  }

  function doBind($reinstanciate_objects = false) {
    $this->ajax            = CMbArray::extract($this->request, "ajax");
    $this->suppressHeaders = CMbArray::extract($this->request, "suppressHeaders");
    $this->callBack        = CMbArray::extract($this->request, "callback");
    $this->postRedirect    = CMbArray::extract($this->request, "postRedirect");

    if ($this->postRedirect) {
      $this->redirect = $this->postRedirect;
    }

    if ($reinstanciate_objects) {
      $this->_obj            = new $this->className();
      $this->_old            = new $this->className();
      $this->onAfterInstanciation(); // Lancer ceci apres chaque instanciation de _obj et _old !!
    }

    // Object binding
    $this->_obj->bind($this->request);

    // Old object
    $this->_old->load($this->_obj->_id);
  }

  function onAfterInstanciation(){

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
      CAppUI::setMsg($this->_old->_id ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
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

  function doCallback() {
    $messages = CAppUI::$instance->messages;

    echo CAppUI::getMsg();

    $fields = $this->_obj->getProperties();
    $fields["_guid"] = $this->_obj->_guid;
    $fields["_class"] = $this->_obj->_class;

    foreach ($messages as &$_level) {
      $_keys   = array_map("utf8_encode", array_keys($_level));
      $_values = array_map("utf8_encode", array_values($_level));
      $_level = array_combine($_keys, $_values);
    }

    $fields["_ui_messages"] = $messages;

    $id = $this->_obj->_id ? $this->_obj->_id : 0;

    if ($this->callBack) {
      CAppUI::callbackAjax($this->callBack, $id, $fields);
    }
    else {
      $guid = "$this->className-$id";
      CAppUI::callbackAjax("Form.onSubmitComplete", $guid, $fields);
    }

    if (!CAppUI::$mobile) {
      CApp::rip();
    }

  }

  function doIt() {
    // Multiple case
    if ($object_ids = CMbArray::extract($this->request, $this->objectKeys)) {
      $request = $this->request;
      foreach (explode("-", $object_ids) as $object_id) {
        $this->request = $request;
        $this->request[$this->objectKey] = $object_id;
        $this->doSingle(true);
      }

      $this->doRedirect();
    }

    $this->doSingle(false);
    $this->doRedirect();
  }

  function doSingle($reinstanciate_objects) {
    $this->doBind($reinstanciate_objects);

    if (CMbArray::extract($this->request, 'del')) {
      $this->doDelete();
    }
    else {
      $this->doStore();
    }
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
