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

    function CForumThread() {
        $this->CMbObject('forum_thread', 'forum_thread_id'); 
        $this->loadRefModule(basename(dirname(__FILE__)));
    }

    function getSpecs() {
        return array (
            'title'          => 'notNull str',
            'body'           => 'notNull html',
            'date'           => 'notNull dateTime',
            'user_id'        => 'notNull ref class|CMediusers',
            'forum_theme_id' => 'notNull ref class|CForumTheme'
        );
    }
    
    function updateFormFields() {
        parent::updateFormFields();
        $this->_view = $this->title;
    }

    function getBackRefs() {
        $backRefs = parent::getBackRefs();
        $backRefs['messages'] = 'CForumMessage forum_thread_id';
        return $backRefs;
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
