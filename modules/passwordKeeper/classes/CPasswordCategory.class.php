<?php

/**
 * $Id$
 *  
 * @category Password_Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Manage a password category
 */
class CPasswordCategory extends CMbObject {
  /** @var int Category ID */
  public $category_id;

  /** @var string Category name */
  public $category_name;

  /** @var int CPasswordKeeper reference */
  public $password_keeper_id;

  /** @var CPasswordEntry[] references */
  public $_ref_passwords;

  /**
   * @see parent::getSpec()
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'password_category';
    $spec->key   = 'category_id';

    return $spec;
  }

  /**
   * @see parent::getBackProps()
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passwords"] = "CPasswordEntry category_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["category_name"]      = "str notNull maxLength|50";
    $props["password_keeper_id"] = "ref notNull class|CPasswordKeeper";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->category_name;
  }

  /**
   * Get all passwords in a CPasswordCategory
   *
   * @return CPasswordEntry[]
   */
  function loadRefsPasswords() {
    return $this->_ref_passwords = $this->loadBackRefs("passwords");
  }
}
