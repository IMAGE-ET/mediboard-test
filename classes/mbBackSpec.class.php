<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbBackSpec {
  public $owner = null;
  public $name = null;
  public $class = null; 
  public $field = null;
  public $_initiator = null; // The class actually pointed to by $class

  static function make($owner, $name, $backProp) {
    list($class, $field) = explode(' ', $backProp);
  	if (!class_exists($class)) return null;

  	$backObject = new $class;
  	
  	$backSpec = new CMbBackSpec();
    $backSpec->owner = $owner;
    $backSpec->name  = $name;
    $backSpec->class = $class;
    $backSpec->field = $field;
    $backSpec->_initiator = $backObject->_specs[$field]->class;
    
    return $backSpec;
  }
  
  /**
   * Check whether the back prop has been declared in parent class
   * @return bool true if prop is inherited, false otherwise
   */
  function isInherited() {
    if ($parentClass = get_parent_class($this->owner)) {
      if ($parent = @new $parentClass) {
        return isset($parent->_backProps[$this->name]);
	    }
    }
    
    return false;
  }
}

?>