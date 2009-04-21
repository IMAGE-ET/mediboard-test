<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author Romain OLLIVIER	
 */

/**
 * The CMbMail class
 */
  
class CMbMail extends CMbObject {
  // DB Fields
  var $mbmail_id     = null;
  var $from          = null;
  var $to            = null;
  var $subject       = null;
  var $source        = null;
  var $date_sent     = null;
  var $date_read     = null;
  var $archived      = null;
  var $starred       = null;
  
  // Form Fields
  var $_from_state   = null;
  var $_to_state     = null;
  
  // References
  var $_ref_user_from = null;
  var $_ref_user_to   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "mbmail";
    $spec->key   = "mbmail_id";
    return $spec;
  }

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
   * @return array
   */
  function loadVisibleList() {
    if (!$this->_ref_module) return null;
    
    global $AppUI;
    $where["to"] = "= '$AppUI->user_id'";
    $where["date_sent"] = "IS NOT NULL";
    $where[] = "date_read IS NULL OR starred = '1'";
    $order = "date_sent";
		
    $mails = array();
    $list = $this->loadList($where, $order);
    foreach ($list as $mail) {
      $mail->loadRefUserFrom();
      $mails[$mail->_to_state][$mail->_id] = $mail;
    }
    
    return $mails;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = substr($this->subject, 0, 30);
    
    $this->_from_state = "saved";
    if ($this->date_sent) $this->_from_state = "sent";
    if ($this->date_read) $this->_from_state = "read";

    $this->_to_state = "pending";
    if ($this->date_sent) $this->_to_state = "received";
    if ($this->date_read) $this->_to_state = "read";
    if ($this->archived ) $this->_to_state = "archived";
    if ($this->starred  ) $this->_to_state = "starred";    
  }
  
  function loadRefUserFrom() {
    $this->_ref_user_from = new CMediusers();
    if ($this->_ref_user_from->load($this->from)) {
      $this->_ref_user_from->loadRefFunction();
    }
  }

  function loadRefUserTo() {
    $this->_ref_user_to = new CMediusers();
    if ($this->_ref_user_to->load($this->to)) {
      $this->_ref_user_to->loadRefFunction();
    }
  }
  
  function loadRefsFwd(){
    $this->loadRefUserFrom(); 
    $this->loadRefUserTo(); 
  }
}
?>
