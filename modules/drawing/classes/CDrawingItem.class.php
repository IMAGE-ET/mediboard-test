<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Item with stored data from a drawing
 */
class CDrawingItem extends CMbMetaObject {
  /**
   * @var integer Primary key
   */
  public $drawing_item_id;

  public $user_id;
  public $content;
  public $creation_datetime;
  public $last_modification_datetime;

  /** @var CMediusers|null */
  public $_ref_mediuser;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "drawing_item";
    $spec->key    = "drawing_item_id";
    return $spec;
  }

  /** @see parent::store() */
  function store() {
    if (!$this->creation_datetime) {
      $this->creation_datetime = CMbDT::dateTime();
    }

    if ($this->_id && $this->fieldModified("content")) {
      $this->last_modification_datetime = CMbDT::dateTime();
    }

    if ($msg = parent::store()) {
      return $msg;
    }
  }

  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * load the creator
   *
   * @return CMediusers
   */
  function loadRefMediuser() {
    return $this->_ref_mediuser = $this->loadFwdRef("user_id", true);
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"] = "ref class|CMediusers";
    $props["content"] = "text";
    $props["creation_datetime"] = "dateTime notNull";
    $props["last_modification_datetime"] = "dateTime";
    return $props;
  }
}