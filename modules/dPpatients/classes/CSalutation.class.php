<?php

/**
 * $Id$
 *
 * @category patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSalutation extends CMbMetaObject {
  /** @var integer Primary key */
  public $salutation_id;

  /** @var integer Owner ID */
  public $owner_id;

  /** @var string Starting formula */
  public $starting_formula;

  /** @var string Closing formula */
  public $closing_formula;

  /** @var CMediusers Owner reference */
  public $_ref_owner;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec                          = parent::getSpec();
    $spec->table                   = "salutation";
    $spec->key                     = "salutation_id";
    $spec->uniques["owner_object"] = array('owner_id', 'object_class', 'object_id');

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                     = parent::getProps();
    $props["owner_id"]         = "ref notNull class|CMediusers cascade";
    $props["starting_formula"] = "str notNull";
    $props["closing_formula"]  = "str notNull";

    $props["object_id"] .= " cascade";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "[{$this->starting_formula} => {$this->closing_formula}]";
  }

  function loadRefOwner() {
    return $this->_ref_owner = $this->loadFwdRef('owner_id', true);
  }

  /**
   * Load all salutation from a given class
   *
   * @param string       $object_class Target object class
   * @param integer|null $object_id    Target object ID
   * @param int          $perm         Permission needed on owners
   * @param integer|null $owner_id     Specific owner ID
   *
   * @return CSalutation[]
   */
  static function loadAllSalutations($object_class, $object_id = null, $perm = PERM_EDIT, $owner_id = null) {
    if (!$owner_id) {
      $users    = new CMediusers();
      $users    = $users->loadListWithPerms($perm, array('actif' => "= '1'"));
      $user_ids = ($users) ? CMbArray::pluck($users, '_id') : array(CMediusers::get()->_id);
      unset($users);
    }
    else {
      $user_ids = array($owner_id);
    }

    /** @var CSalutation $salutation */
    $salutation = new self();
    $ds         = $salutation->_spec->ds;

    $where = array(
      'owner_id'     => $ds->prepareIn($user_ids),
      'object_class' => $ds->prepare('= ?', $object_class)
    );

    if ($object_id) {
      $where['object_id'] = $ds->prepare('= ?', $object_id);
    }

    return $salutation->loadList($where);
  }
}
