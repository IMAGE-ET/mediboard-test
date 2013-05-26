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
 * Form constraint
 */
class CExClassConstraint extends CMbObject {
  public $ex_class_constraint_id;

  //public $ex_class_id;
  public $ex_class_event_id;
  public $field;
  public $operator;
  public $value;

  /** @var CExClassEvent */
  public $_ref_ex_class_event;

  /** @var CMbObject */
  public $_ref_target_object;

  /** @var CMbFieldSpec */
  public $_ref_target_spec;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_constraint";
    $spec->key   = "ex_class_constraint_id";
    $spec->uniques["constraint"] = array("ex_class_event_id", "field", "value");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["ex_class_event_id"] = "ref notNull class|CExClassEvent";
    $props["field"]       = "str notNull";
    $props["operator"]    = "enum notNull list|=|!=|>|>=|<|<=|startsWith|endsWith|contains default|=";
    $props["value"]       = "str notNull";
    return $props;
  }

  /**
   * @param CMbObject $object
   *
   * @return array(CMbObject,string)
   */
  function getFieldAndObject(CMbObject $object){
    return self::getFieldAndObjectStatic($object, $this->field);
  }

  static function getFieldAndObjectStatic(CMbObject $object, $field) {
    if (strpos($field, "CONNECTED_USER") === 0) {
      $object = CMediusers::get();
      $object->_specs = CExClassEvent::getHostObjectSpecs($object);

      if ($field != "CONNECTED_USER") {
        $field = substr($field, 15);
      }
    }

    return array($object, $field);
  }

  /**
   * @param CModelObject $ref_object
   *
   * @return CMbFieldSpec|null
   */
  function resolveSpec(CModelObject $ref_object){
    /** @var CModelObject $ref_object */
    /** @var string $field */
    list($ref_object, $field) = $this->getFieldAndObject($ref_object);

    $parts = explode("-", $field);
    $connected_user = CExClassEvent::getConnectedUserSpec();

    if (count($parts) == 1) {
      if ($field == "CONNECTED_USER") {
        $spec = $connected_user;
      }
      else {
        $spec = $ref_object->_specs[$field];
      }
    }
    else {
      $subparts = explode(".", $parts[0]);

      if ($subparts[0] == "CONNECTED_USER") {
        $_spec = $connected_user;
      }
      else {
        $_spec = $ref_object->_specs[$subparts[0]];
      }

      if (count($subparts) > 1) {
        $class = $subparts[1];
      }
      else {
        if (!$_spec->class) {
          return null;
        }

        $class = $_spec->class;
      }

      $obj = new $class;

      if ($parts[1] == "CONNECTED_USER") {
        $spec = $connected_user;
      }
      else {
        $spec = $obj->_specs[$parts[1]];
      }
    }

    return $spec;
  }

  /**
   * Resolve an object from an object and a formard ref path
   *
   * @param CMbObject $object The object to resolve forward ref object
   * @param string    $field  The path to resolve
   *
   * @return array|null
   */
  static function resolveObjectFieldStatic(CMbObject $object, $field) {
    $parts = explode("-", $field);

    if (count($parts) == 1) {
      return array(
        "object" => $object,
        "field"  => $parts[0],
      );
    }
    else {
      $subparts = explode(".", $parts[0]);
      $_field = $subparts[0];

      $_spec = $object->_specs[$_field];

      if (count($subparts) <= 1 && !$_spec->class) {
        return null;
      }

      return array(
        "object" => $object->loadFwdRef($_field),
        "field"  => $parts[1],
      );
    }
  }

  /**
   * @param CMbObject $object
   *
   * @return array(CMBobject,string)
   */
  function resolveObjectField(CMbObject $object){
    list($object, $field) = $this->getFieldAndObject($object);

    return self::resolveObjectFieldStatic($object, $field);
  }

  /**
   * @return CMbObject
   */
  function loadTargetObject(){
    $this->loadRefExClassEvent();
    $this->completeField("field", "value");

    if (!$this->_id) {
      return $this->_ref_target_object = new CMbObject;
    }

    $ref_object = new $this->_ref_ex_class_event->host_class;

    $spec = $this->resolveSpec($ref_object);

    if ($spec instanceof CRefSpec && $this->value && preg_match("/[a-z][a-z0-9_]+-[0-9]+/i", $this->value)) {
      $this->_ref_target_object = CMbObject::loadFromGuid($this->value);
    }
    else {
      // empty object
      $this->_ref_target_object = new CMbObject;
    }

    $this->_ref_target_spec = $spec;

    return $this->_ref_target_object;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->loadRefExClassEvent();

    $host_class = $this->_ref_ex_class_event->host_class;

    /** @var CMbObject $object */
    /** @var string $field */
    list($object, $field) = $this->getFieldAndObject(new $host_class);
    $host_class = $object->_class;

    $parts = explode("-", $field);
    $subparts = explode(".", $parts[0]);

    // first part
    if (count($subparts) > 1) {
      $this->_view = CAppUI::tr("$host_class-{$subparts[0]}")." de type ".CAppUI::tr("{$subparts[1]}");
    }
    // second part
    else {
      if (count($parts) > 1) {
        $this->_view = CAppUI::tr("$host_class-{$parts[0]}");
      }
      else {
        $this->_view = CAppUI::tr("$host_class-{$field}");
      }
    }

    // 2 levels
    if (count($parts) > 1) {
      if (isset($subparts[1])) {
        $class = $subparts[1];
      }
      else {
        $_spec = $object->_specs[$subparts[0]];
        $class = $_spec->class;
      }

      /*if ($_spec instanceof CRefSpec) {
        $class =
      }*/

      $this->_view .= " / ".CAppUI::tr("{$class}-{$parts[1]}");
    }
  }

  /**
   * @param CMbObject $object
   *
   * @return bool
   */
  function checkConstraint(CMbObject $object) {
    $this->completeField("field", "value");

    $object_field = $this->resolveObjectField($object);

    if (!$object_field) {
      return false;
    }

    $object->loadView();

    /** @var CMbObject $object */
    $object = $object_field["object"];
    $field  = $object_field["field"];

    // cas ou l'objet retrouvé n'a pas le champ (meta objet avec classe differente)
    if (!isset($object->_specs[$field])) {
      return false;
    }

    $object->loadView();

    if ($field == "CONNECTED_USER") {
      $value = $object->_guid;
    }
    else {
      $value = $object->$field;

      if ($object->_specs[$field] instanceof CRefSpec) {
        $_obj = $object->loadFwdRef($field);
        $value = $_obj->_guid;
      }
    }

    return CExClass::compareValues($value, $this->operator, $this->value);
  }

  /**
   * @return CExClassEvent
   */
  function loadRefExClassEvent(){
    return $this->_ref_ex_class_event = $this->loadFwdRef("ex_class_event_id");
  }
}
