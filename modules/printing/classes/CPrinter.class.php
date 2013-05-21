<?php

/**
 * CCorrespondantCourrier aed
 *
 * @category Printing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Lien entre une imprimante réseau et une fonction
 */
class CPrinter extends CMbMetaObject {
  // DB Table key
  public $printer_id;
  
  // DB Fields
  public $function_id;
  
  // Ref fields
  public $_ref_function;
  public $_ref_source;

  /**
   * @see parent::getSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'printer';
    $spec->key   = 'printer_id';
    return $spec;
  }

  /**
   * @see parent::loadTargetObject
   */
  function loadTargetObject() {
    parent::loadTargetObject();
    $object = $this->_ref_object;
    /** @var  $object CFunctions */
    $this->_view = $object->text;
  }

  /**
   * @see parent::getProps
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["function_id"]  = "ref class|CFunctions notNull";
    $props["object_id"]    = "ref notNull class|CSourcePrinter meta|object_class";
    $props["object_class"] = "str notNull class show|0";
    
    return $props;
  }

  /**
   * Charge la fonction associée à l'imprimante
   *
   * @param bool $cached Load from cache
   *
   * @return CFunctions
   */
  function loadRefFunction($cached = true){
    return $this->_ref_function = $this->loadFwdRef("function_id", $cached);
  }

  /**
   * Charge la source d'impression
   *
   * @return CSourcePrinter
   */
  function loadRefSource() {
    $source_guid = $this->object_class.'-'.$this->object_id;
    return $this->_ref_source = CMbObject::loadFromGuid($source_guid);
  }
}
