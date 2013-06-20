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
 * Forum thread
 */
class CForumThread extends CForumMessage {
  // DB Fields
  public $title;
  public $forum_theme_id;

  /** @var CForumTheme */
  public $_ref_forum_theme;

  /** @var CForumMessage[] */
  public $_ref_forum_messages;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_thread';
    $spec->key   = 'forum_thread_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs['title']          = 'str notNull';
    $specs['forum_theme_id'] = 'ref notNull class|CForumTheme';
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->title;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['messages'] = 'CForumMessage forum_thread_id';
    return $backProps;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $message = new CForumMessage;
    $message->forum_thread_id = $this->_id;
    $this->_ref_forum_messages = $message->loadMatchingList();
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->_ref_forum_theme = new CForumTheme();
    $this->_ref_forum_theme->load($this->forum_theme_id);
  }
}
