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
 * The CForumMessage class
 */
class CForumMessage extends CMbObject {
  // DB Fields
  public $body;
  public $date;
  public $user_id;
  public $forum_thread_id;

  // References
  public $_ref_forum_thread;
  public $_ref_user;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'forum_message';
    $spec->key   = 'forum_message_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['body']            = 'html notNull';
    $props['date']            = 'dateTime notNull';
    $props['user_id']         = 'ref notNull class|CMediusers';
    $props['forum_thread_id'] = 'ref notNull class|CForumThread';
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = substr($this->body, 0, 20) . '...';
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
    $this->_ref_user->loadRefFunction();
    $this->_ref_user->loadRefDiscipline();

    $this->_ref_forum_thread = new CForumThread();
    $this->_ref_forum_thread->load($this->forum_thread_id);
  }
}
