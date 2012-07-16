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
 

class CMbBackSpec {
  public $owner = null;
  public $name = null;
  public $class = null; 
  public $field = null;
  public $cascade = null;
  public $_initiator = null; // The class actually pointed to by $class
  public $_notNull = null;
  public $_purgeable = null;
  public $_cascade = null;
  
  static function make($owner, $name, $backProp) {
    $parts = explode(' ', $backProp);
    
    $class = array_shift($parts);

    if (!class_exists($class)) {
      // Modules might not be installed, can't trigger error for now
      // @todo: add an 'external' keyword to the backref 
//      trigger_error("Back spec '$owner'.'$name' refers to unexisting class '$class'", E_USER_ERROR);
      return;
    }
    
    $field = array_shift($parts);

    $backObject = new $class;
    if (!array_key_exists($field, $backObject->_specs)) {
      trigger_error("Back spec '$owner'.'$name' refers to unexisting ref spec '$class'.'$field'", E_USER_ERROR);
      return;
    }
    
    $cascade = in_array("cascade", $parts);

    $backObjectSpec = $backObject->_specs[$field];
    
    $backSpec = new CMbBackSpec();
    $backSpec->owner = $owner;
    $backSpec->name  = $name;
    $backSpec->class = $class;
    $backSpec->field = $field;
    $backSpec->cascade = $cascade;
    $backSpec->_initiator = $backObjectSpec->class;
    $backSpec->_notNull   = $backObjectSpec->notNull;
    $backSpec->_purgeable = $backObjectSpec->purgeable;
    $backSpec->_cascade   = $backObjectSpec->cascade;
    $backSpec->_unlink    = $backObjectSpec->unlink;
    
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