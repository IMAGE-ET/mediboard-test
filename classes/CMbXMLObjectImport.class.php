<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CMbXMLObjectImport {
  static $all = array();
  
  public $class;
  public $instance;
  
  public $values;
  public $ids;
  
  public $refs;
  public $collections;
  
  public $similar;
  
  function __construct($class, $values, $ids) {
    $this->class  = $class;
    $this->values = $values;
    $this->ids    = $ids;
    
    $this->instance = new $class;
    $this->similar = $this->instance->getSimilar($values);
    
    $this->mapToObject();
    
    foreach ($ids as $fwd => $id) {
      mbTrace($this->getObjectGuidByFwdRef($this->instance, $fwd));
    }
    
    self::$all[$ids["id"]] = $this;
  }
  
  function getObjectGuidByFwdRef(CMbObject $object, $fwd) {
    // Primary key
    if ($fwd === "id" || $fwd === $object->_spec->key) {
      return $object;
    }
    
    $spec = $object->_specs[$fwd]; // We assume it's alway a CRefSpec
    
    $class = $spec->meta ? $object->{$spec->meta} : $spec->class;
    
    if (!$class) {
      return null;
    }
    
    $id = $object->$fwd;
    return "$class-$id";
  }
  
  function mapToObject(){
    $obj = $this->instance;
    
    foreach ($this->values as $field => $value) {
      $obj->$field = $value;
    }
    
    foreach ($this->ids as $field => $value) {
      $obj->$field = $value;
    }
  }
}

