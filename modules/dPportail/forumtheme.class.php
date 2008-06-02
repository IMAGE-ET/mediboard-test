<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Fabien	
 */

/**
 * The CForumTheme class
 */
  
class CForumTheme extends CMbObject {
    // DB Table key
    var $forum_theme_id = null;

    // DB Fields
    var $title          = null;
    var $desc           = null;
    
    // References
    var $_ref_forum_threads = null;

    function CForumTheme() {
        $this->CMbObject('forum_theme', 'forum_theme_id'); 
        $this->loadRefModule(basename(dirname(__FILE__)));
    }

    function getSpecs() {
        return array (
            'title'         => 'notNull str',
            'desc'          => 'text',
        );
    }

    function getBackRefs() {
        $backRefs = parent::getBackRefs();
        $backRefs['threads'] = 'CForumThread forum_theme_id';
        return $backRefs;
    }

    function updateFormFields() {
        parent::updateFormFields();
        $this->_view = $this->title;
    }

    function loadRefsBack(){
        $thread = new CForumThread;
        $thread->forum_theme_id = $this->_id;
        $this->_ref_forum_threads = $thread->loadMatchingList();
    }
}
?>
