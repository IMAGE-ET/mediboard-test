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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_theme';
    $spec->key   = 'forum_theme_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['title'] = 'str notNull';
    $specs['desc']  = 'text';
    return $specs;
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
