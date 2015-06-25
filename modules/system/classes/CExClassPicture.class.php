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
 * ExClass picture
 */
class CExClassPicture extends CMbObject {
  public $ex_class_picture_id;

  public $ex_group_id;
  public $subgroup_id;
  public $name;
  public $disabled;
  public $show_label;

  public $predicate_id;

  // Pixel positionned
  public $coord_left;
  public $coord_top;
  public $coord_width;
  public $coord_height;

  /** @var CExClassFieldGroup */
  public $_ref_ex_group;

  /** @var CFile */
  public $_ref_file;

  /** @var CExClass */
  public $_ref_ex_class;

  /** @var CExClassFieldPredicate */
  public $_ref_predicate;

  public $_ex_class_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec                  = parent::getSpec();
    $spec->table           = "ex_class_picture";
    $spec->key             = "ex_class_picture_id";

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                 = parent::getProps();
    $props["ex_group_id"]  = "ref notNull class|CExClassFieldGroup cascade";
    $props["subgroup_id"]  = "ref class|CExClassFieldSubgroup nullify";
    $props["name"]         = "str notNull";
    $props["disabled"]     = "bool notNull default|0";
    $props["show_label"]   = "bool notNull default|1";
    $props["predicate_id"] = "ref class|CExClassFieldPredicate autocomplete|_view|true nullify";

    // Pixel positionned
    $props["coord_left"]   = "num";
    $props["coord_top"]    = "num";
    $props["coord_width"]  = "num min|1";
    $props["coord_height"] = "num min|1";

    $props["_ex_class_id"] = "ref class|CExClass";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["files"] = "CFile object_id cascade";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->name;
  }

  /**
   * @param bool $cache Use cache
   *
   * @return CExClassFieldPredicate
   */
  function loadRefPredicate($cache = true) {
    return $this->_ref_predicate = $this->loadFwdRef("predicate_id", $cache);
  }

  /**
   * Load ex field group
   *
   * @param bool $cache Use cache
   *
   * @return CExClassFieldGroup
   */
  function loadRefExGroup($cache = true) {
    if ($cache && $this->_ref_ex_group && $this->_ref_ex_group->_id) {
      return $this->_ref_ex_group;
    }

    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }

  /**
   * Load Ex Class
   *
   * @param bool $cache Use object cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true) {
    return $this->_ref_ex_class = $this->loadRefExGroup($cache)->loadRefExClass($cache);
  }

  function loadRefFile() {
    return $this->_ref_file = $this->loadNamedFile("file.jpg");
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    $reset_position = $this->fieldModified("ex_group_id") || $this->fieldModified("disabled");

    // If we change its group, we need to reset its coordinates
    if ($reset_position) {
      $this->subgroup_id   = "";
    }

    $subgroup_modified = $this->fieldModified("subgroup_id");
    if ($reset_position || $subgroup_modified) {
      if (!$this->fieldModified("coord_left")) {
        $this->coord_left = "";
      }

      if (!$this->fieldModified("coord_top")) {
        $this->coord_top = "";
      }
    }

    parent::updatePlainFields();
  }
}
