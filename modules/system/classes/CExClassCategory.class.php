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
 * Ex class category
 */
class CExClassCategory extends CMbObject {
  public $ex_class_category_id;

  public $group_id;
  public $title;
  public $description;
  public $color;

  /** @var CExClass[] */
  public $_ref_ex_classes;

  /** @var self[] */
  static $_list_cache = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_category";
    $spec->key   = "ex_class_category_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["title"]       = "str notNull";
    $props["color"]       = "color";
    $props["description"] = "text";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["ex_classes"] = "CExClass category_id";
    return $backProps;
  }

  /**
   * Load ex classes
   *
   * @return CExClass[]
   */
  function loadRefsExClasses() {
    return $this->_ref_ex_classes = $this->loadBackRefs("ex_classes", "name");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = $this->title;
  }

  /**
   * @see parent::store()
   */
  function store(){
    if (!$this->_id) {
      $this->group_id = CGroups::loadCurrent()->_id;
    }

    return parent::store();
  }
}
