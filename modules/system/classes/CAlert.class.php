<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CAlert Class
 */
class CAlert extends CMbMetaObject {
  public $alert_id;
  
  // DB Fields
  public $tag;
  public $level;
  public $comments;
  public $creation_date;
  public $handled;
  public $handled_date;
  public $handled_user_id;

  // Ref fields
  public $_ref_handled_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'alert';
    $spec->key   = 'alert_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["tag"]      = "str notNull";
    $props["level"]    = "enum list|low|medium|high default|medium notNull";
    $props["comments"] = "text";
    $props["creation_date"] = "dateTime";
    $props["handled"]  = "bool notNull default|0";
    $props["handled_date"] = "dateTime";
    $props["handled_user_id"] = "ref class|CMediusers";
    $props["object_id"] .= " cascade";
    return $props;
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("handled");

    if (!$this->creation_date) {
      $this->creation_date = CMbDT::dateTime();
      if ($this->_id) {
        $this->creation_date = $this->loadFirstLog()->date;
      }
    }

    if ($this->fieldModified("handled", "1") || ($this->handled && !$this->handled_date && !$this->handled_user_id)) {
      $this->handled_date = CMbDT::dateTime();
      $this->handled_user_id = CMediusers::get()->_id;
      if ($this->handled) {
        $last_log = $this->loadLastLog();
        $this->handled_date = $last_log->date;
        $this->handled_user_id = $last_log->user_id;
      }
    }

    if ($this->fieldModified("handled", "0")) {
      $this->handled_date = $this->handled_user_id = "";
    }

    if ($msg = parent::store()) {
      return $msg;
    }
  }

  function loadRefHandledUser() {
    return $this->_ref_handled_user = $this->loadFwdRef("handled_user_id");
  }
}
