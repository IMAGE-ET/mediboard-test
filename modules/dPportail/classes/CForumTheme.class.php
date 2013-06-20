<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Portail
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Forum theme
 */
class CForumTheme extends CMbObject {
  // DB Table key
  public $forum_theme_id;

  // DB Fields
  public $title;
  public $desc;
  
  /** @var CForumThread[] */
  public $_ref_forum_threads;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_theme';
    $spec->key   = 'forum_theme_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['title'] = 'str notNull';
    $props['desc']  = 'text';
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['threads'] = 'CForumThread forum_theme_id';
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->title;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack(){
    $thread = new CForumThread;
    $thread->forum_theme_id = $this->_id;
    $this->_ref_forum_threads = $thread->loadMatchingList();
  }
}
