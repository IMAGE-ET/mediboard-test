<?php

/**
 * $Id$
 *
 * @category Mediboard
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Class CEntity
 *
 *
 */
class CEntity extends CMbObject {
  // DB Fields
  public $code;
  public $_name;
  public $short_name;
  public $description;


  public $user_id;


  public $opening_reason;
  public $opening_date;

  public $closing_reason;
  public $closing_date;

  public $activation_date;
  public $inactivation_date;

  // Forward Ref
  /** @var CMediusers */
  public $_ref_user;


  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["code"]              = "str notNull maxLength|80";
    $props["_name"]             = "str notNull";
    $props["short_name"]        = "str";
    $props["description"]       = "text";
    $props["user_id"]           = "ref class|CMediusers";
    $props["opening_reason"]    = "text";
    $props["opening_date"]      = "date";
    $props["closing_reason"]    = "text";
    $props["closing_date"]      = "date";
    $props["activation_date"]   = "date";
    $props["inactivation_date"] = "date";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->mapEntityTo();
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updateFormFields();
    $this->mapEntityFrom();
  }

  function mapEntityTo () {}

  function mapEntityFrom () {}

  function loadRefUser () {
    return $this->_ref_user = $this->loadFwdRef("user_id");
  }
}
