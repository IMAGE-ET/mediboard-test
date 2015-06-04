<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author Romain OLLIVIER	
 */

/**
 * The CUserMessage class
 */
  
class CUserMessage extends CMbObject {
  // DB Fields
  public $usermessage_id;
  public $creator_id;
  public $subject;
  public $content;
  public $in_reply_to;

  // Form Fields
  public $_abstract;
  public $_can_edit;
  public $_mode;

  // References
  public $_ref_user_creator;
  public $_ref_destinataires;
  public $_ref_dest_user;

  /**
   * Get specs
   *
   * @return CMbObjectSpec $spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "usermessage";
    $spec->key   = "usermessage_id";
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["usermessage_destinataires"]    = "CUserMessageDest user_message_id";
    $backProps["usermessage_in_reply"]         = "CUserMessage in_reply_to";
    return $backProps;
  }

  /**
   * get props
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["subject"]      = "str notNull";
    $props["content"]      = "html";
    $props["creator_id"]   = "ref class|CMediusers notNull";
    $props["in_reply_to"]  = "ref class|CUserMessage";

    /* Form fields */
    $props['_abstract']         = 'text';

    return $props;
  }

  /**
   * @see parent::uodateFormFields()
   */
  public function updateFormFields() {
    parent::updateFormFields();

    $this->_abstract = str_replace(array("\n", "\t", "\r"), ' ', substr(strip_tags($this->content), 0, 50)) . '...';
    $this->_view = ($this->subject ? "$this->subject  - " : "") . $this->_abstract;
  }

  /**
   * load the list of destinataires
   *
   * @return CUserMessageDest[]
   */
  function loadRefDests() {
    return $this->_ref_destinataires = $this->loadBackRefs("usermessage_destinataires");
  }

  /**
   * load the user_connected destinataire of a message
   *
   * @return CUserMessageDest
   */
  function loadRefDestUser() {
    $user = CMediusers::get();
    $dest = new CUserMessageDest();
    if ($this->_id) {
      $where = array();
      $where["user_message_id"] = " = '$this->_id'";
      $where["to_user_id"] = " = '$user->_id'";
      $where["datetime_sent"] = " IS NOT NULL";
      $dest->loadObject($where);
      if ($dest->_id) {
        $dest->_ref_user_to = $user;
        $dest->loadRefFrom();
      }
    }
    return $this->_ref_dest_user = $dest;
  }

  /**
   * load the creator
   *
   * @return CMediusers|null
   */
  function loadRefCreator() {
    return $this->_ref_user_creator = $this->loadFwdRef("creator_id");
  }
}