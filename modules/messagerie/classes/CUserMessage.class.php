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
  public $archived;
  public $starred;
  
  // Form Fields
  public $_from_state;
  public $_to_state;
  
  // References
  public $_ref_user_from;
  public $_ref_user_to;
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
    $specs["archived"]    = "bool default|0";
    $specs["starred"]     = "bool default|0";

    $specs["_from_state"] = "enum list|saved|sent|read";
    $specs["_to_state"]   = "enum list|pending|received|read|archived|starred";
    
    return $specs;
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
   * @param int $cache using cache
   *
   * @return CMediusers|null
   */
  function loadRefUserFrom($cache = 0) {
    $this->_ref_user_from = $this->loadFwdRef("from", $cache);
    $this->_ref_user_from->loadRefFunction();
    return $this->_ref_user_from;
  }

  /**
   * load ref user to
   *
   * @param int $cache use cache
   *
   * @return CMbObject|null
   */
  function loadRefUserTo($cache = 0) {
    $this->_ref_user_to = $this->loadFwdRef("to", $cache);
    $this->_ref_user_to->loadRefFunction();
    return $this->_ref_user_to;
  }

  /**
   * loadRef Fwd, load from user & to user
   *
   * @return int|void
   */
  function loadRefsFwd(){
    $this->loadRefUserFrom(); 
    $this->loadRefUserTo(); 
  }
}