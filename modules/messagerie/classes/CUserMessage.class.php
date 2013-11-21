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
  public $from;
  public $to;
  public $subject;
  public $source;
  public $date_sent;
  public $date_read;
  public $in_reply_to;      // origin message id in the case of an answer
  public $archived;
  public $starred;
  public $grouped;

  // Form Fields
  public $_from_state;
  public $_to_state;
  public $_is_draft;

  
  // References
  public $_ref_user_from;
  public $_ref_answer_from;
  public $_ref_users_to;
  public $_clean_subject;

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
    $backProps["user_message"]   = "CUserMessage in_reply_to";

    return $backProps;
  }

  /**
   * get props
   *
   * @return array
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["from"]        = "ref notNull class|CMediusers";
    $specs["to"]          = "ref notNull class|CMediusers";
    $specs["subject"]     = "str notNull";
    $specs["source"]      = "html";
    $specs["date_sent"]   = "dateTime";
    $specs["date_read"]   = "dateTime";
    $specs["in_reply_to"] = "ref class|CUserMessage";
    $specs["archived"]    = "bool default|0";
    $specs["starred"]     = "bool default|0";
    $specs["grouped"]     = "num";

    $specs["_from_state"] = "enum list|saved|sent|read";
    $specs["_to_state"]   = "enum list|pending|received|read|archived|starred";
    
    return $specs;
  }

  static function getLastGroupId() {
    $user = new self;
    $ds = $user->getDS();
    $sql = "SELECT MAX(`grouped`) FROM `usermessage`;";
    $num = $ds->loadResult($sql);
    return $num;
  }

  /**
   * Load all visible mails for current user
   * grouped by status (sent and starred only)
   *
   * @return array
   */
  function loadVisibleList() {
    // Module may not be visible
    if (!$this->_ref_module) {
      return null;
    }
    
    // Table may not be renamed yet.
    if ($this->_ref_module->mod_version < "0.11") {
      return null;
    }
    
    $user = CUser::get();
    $where["to"]        = "= '$user->_id'";
    $where["date_sent"] = "IS NOT NULL";
    $where[]            = "date_read IS NULL OR starred = '1'";
    $order = "date_sent";
    $mails = array();
    $list = $this->loadList($where, $order);
    /** @var $list CUserMessage[] */
    foreach ($list as $mail) {
      $mail->loadRefUserFrom();
      $mails[$mail->_to_state][$mail->_id] = $mail;
    }
    
    return $mails;
  }

  /**
   * function updateFormFields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = substr($this->subject, 0, 30);
    
    $this->_from_state = "saved";
    if ($this->date_sent) {
      $this->_from_state = "sent";
    }
    if ($this->date_read) {
      $this->_from_state = "read";
    }

    $this->_to_state = "pending";
    if ($this->date_sent) {
      $this->_to_state = "received";
    }
    if ($this->date_read) {
      $this->_to_state = "read";
    }
    if ($this->archived ) {
      $this->_to_state = "archived";
    }
    if ($this->starred  ) {
      $this->_to_state = "starred";
    }
    
    $this->_clean_subject = preg_replace("/^Re: /", "", $this->subject);
  }

  /**
   * load Ref User From
   *
   * @return CMediusers|null
   */
  function loadRefUserFrom() {
    /** @var CMediusers $user */
    $user = $this->loadFwdRef("from", true);
    $user->loadRefFunction();
    return $this->_ref_user_from = $user;
  }

  /**
   * @return CUserMessage|null
   */
  function loadOriginMessage() {
    return $this->_ref_answer_from =  $this->loadFwdRef("in_reply_to", true);
  }

  /**
   * load the list of destinataire
   *
   * @return array
   */
  function loadRefUsersTo() {
    $user = CMediusers::get();
    $listUser = array();

    $where = array();
    if ($this->grouped && $this->from && $this->from == $user->_id) {
      $where['grouped'] = " = '$this->grouped'";
    }
    elseif($this->in_reply_to) {
      $where['usermessage_id'] = " = '$this->in_reply_to'";
    }
    else {
      $where['usermessage_id'] = " = '$this->_id'";
    }
    foreach ($this->loadList($where) as $_usermessage) {
      /** @var CMediusers $user */
      $user = $_usermessage->loadFwdRef("to");
      $user->loadRefFunction();
      $listUser[] = $user;
    }

    return $this->_ref_users_to = $listUser;
  }

  /**
   * loadRef Fwd, load from user & to user
   *
   * @return int|void
   */
  function loadRefsFwd(){
    $this->loadRefUserFrom(); 
    $this->loadRefUsersTo();
  }
}