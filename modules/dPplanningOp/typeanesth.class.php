<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPPlanningOp
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */


/**
 * The CTypeAnesth class
 */
class CTypeAnesth extends CMbObject {
  // DB Table key
  var $type_anesth_id = null;

  // DB Fields
  var $name           = null;

  function CTypeAnesth() {
    $this->CMbObject("type_anesth", "type_anesth_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "name" => "str|notNull"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "opération(s)", 
      "name"      => "operations", 
      "idfield"   => "operation_id", 
      "joinfield" => "type_anesth"
    );
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>