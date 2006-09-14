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
    
    $this->_props["name"] = "str|notNull";
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