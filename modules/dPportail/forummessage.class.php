<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Fabien	
 */

/**
 * The CForumMessage class
 */
  
class CForumMessage extends CMbObject {
  // DB Fields
  var $body               = null;
  var $date               = null;
  var $user_id            = null;
  var $forum_thread_id    = null;
  
  // References
  var $_ref_forum_thread  = null;
  var $_ref_user          = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_message';
    $spec->key   = 'forum_message_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['body']            = 'html notNull';
    $specs['date']            = 'dateTime notNull';
    $specs['user_id']         = 'ref notNull class|CMediusers';
    $specs['forum_thread_id'] = 'ref notNull class|CForumThread';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = substr($this->body, 0, 20) . '...';
  }
  
  function loadRefsFwd(){
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
    $this->_ref_user->loadRefFunction();
    $this->_ref_user->loadRefDiscipline();
    
    $this->_ref_forum_thread = new CForumThread();
    $this->_ref_forum_thread->load($this->forum_thread_id);
  }
}
?>
