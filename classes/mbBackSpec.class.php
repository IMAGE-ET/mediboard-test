<?php /* $Id: mbFieldSpec.class.php 2134 2007-06-29 12:48:22Z MyttO $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

class CMbBackSpec {
  public $name = null;
  public $class = null; 
  public $field = null;
  
  public $_initiator = null; // The class actually pointed to by $class

  function __construct($name, $backProp) {
    $this->name = $name;

    $backPropParts = split(" ", $backProp);
    $this->class = $backPropParts[0];
    $this->field = $backPropParts[1];
    
    $backObject = new $this->class;
    $this->_initiator = $backObject->_specs[$this->field]->class;
  }
  
}


?>