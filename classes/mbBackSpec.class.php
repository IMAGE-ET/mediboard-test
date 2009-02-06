<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbBackSpec {
  public $name = null;
  public $class = null; 
  public $field = null;
  public $_initiator = null; // The class actually pointed to by $class

  static function make($name, $backProp) {
    list($class, $field) = explode(' ', $backProp);
  	if (!class_exists($class)) return null;

  	$backObject = new $class;
  	
  	$backSpec = new CMbBackSpec();
    $backSpec->name = $name;
    $backSpec->class = $class;
    $backSpec->field = $field;
    $backSpec->_initiator = $backObject->_specs[$field]->class;
    
    return $backSpec;
  }
}

?>