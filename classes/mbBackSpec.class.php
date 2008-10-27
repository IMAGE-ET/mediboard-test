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

  function __construct($name, $backProp) {
    $this->name = $name;

    list($this->class, $this->field) = explode(" ", $backProp);
    
    $backObject = new $this->class;
    $this->_initiator = $backObject->_specs[$this->field]->class;
  }
  
}


?>