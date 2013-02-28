<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Classe CNote.
 *
 * @abstract Permet de créer des notes sur n'importe quel objet
 */
class CNote extends CMbMetaObject {
  // DB Table key
  public $note_id;

  // DB Fields
  public $user_id;
  public $public;
  public $degre;
  public $date;
  public $libelle;
  public $text;

  // References
  public $_ref_user;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'note';
    $spec->key   = 'note_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["user_id"]    = "ref class|CMediusers";
    $props["public"]     = "bool notNull";
    $props["degre"]      = "enum notNull list|low|medium|high default|low";
    $props["date"]       = "dateTime notNull";
    $props["libelle"]    = "str notNull";
    $props["text"]       = "text";
    $props["object_id"] .= " cascade";
    return $props;
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_view = "Note écrite par ".$this->_ref_user->_view;
  }

  function getPerm($perm) {
    if (!isset($this->_ref_object->_id)) {
      $this->loadRefsFwd();
    }
    return $this->public ?
      $this->_ref_object->getPerm($perm) :
      $this->_ref_object->getPerm($perm) && $this->_ref_user->getPerm($perm);
  }

}
