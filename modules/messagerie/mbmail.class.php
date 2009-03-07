<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision: $
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
  var $date_archived = null;
  
  // Form Fields
  var $_state        = null;
  
  // References
  var $_ref_user_from = null;
  var $_ref_user_to   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "mbmail";
    $spec->key   = "mbmail_id";
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["from"]          = "ref notNull class|CMediusers";
    $specs["to"]            = "ref notNull class|CMediusers";
    $specs["subject"]       = "str notNull";
    $specs["source"]        = "html";
    $specs["date_sent"]     = "dateTime";
    $specs["date_read"]     = "dateTime";
    $specs["date_archived"] = "dateTime";
    $specs["starred"]       = "bool";

    $specs["_state"]        = "enum list|saved|sent|read|archived";
    
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = substr($this->subject, 0, 30);
    
  }
  
  function loadRefsFwd(){
    $this->_ref_user_from = new CMediusers();
    if ($this->_ref_user_from->load($this->from)) {
      $this->_ref_user_from->loadRefFunction();
    }
    
    
    $this->_ref_user_to = new CMediusers();
    if ($this->_ref_user_to->load($this->to)) {
      $this->_ref_user_to->loadRefFunction();
    }
  }
}
?>
