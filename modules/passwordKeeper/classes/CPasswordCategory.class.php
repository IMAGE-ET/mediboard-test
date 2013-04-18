<?php

/**
 * $Id$
 *  
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * Manage a password category
 */
class CPasswordCategory extends CMbObject {
  /** @var  Category ID */
  public $category_id;

  /** @var  Category name */
  public $category_name;

  /** @var  CPasswordKeeper reference */
  public $password_keeper_id;

  /** @var  CPasswordEntry references */
  public $_ref_passwords;

  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = 'password_category';
    $spec->key   = 'category_id';

    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passwords"] = "CPasswordEntry category_id";

    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();
    $props["category_name"]      = "str notNull maxLength|50";
    $props["password_keeper_id"] = "ref notNull class|CPasswordKeeper";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->category_name;
  }

  function loadRefsBack() {
    return $this->_ref_passwords = $this->loadBackRefs("passwords");
  }
}