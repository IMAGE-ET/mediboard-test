<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Fabien	
 */

/**
 * The CForumThread class
 */
  
class CForumThread extends CForumMessage {
	// DB Fields
  var $title               = null;
  var $forum_theme_id      = null;
  
  // References
  var $_ref_forum_theme    = null;
  var $_ref_forum_messages = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_thread';
    $spec->key   = 'forum_thread_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['title']          = 'str notNull';
    $specs['forum_theme_id'] = 'ref notNull class|CForumTheme';
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->title;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['messages'] = 'CForumMessage forum_thread_id';
    return $backProps;
  }

  function loadRefsBack() {
    $message = new CForumMessage;
    $message->forum_thread_id = $this->_id;
    $this->_ref_forum_messages = $message->loadMatchingList();
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
  	$this->_ref_forum_theme = new CForumTheme();
      $this->_ref_forum_theme->load($this->forum_theme_id);
  }
}

?>