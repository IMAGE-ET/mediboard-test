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
  var $name = null;
  var $ext_doc = null;
  
  // References
  var $_count_operations = null;
  
  function CTypeAnesth() {
    $this->CMbObject("type_anesth", "type_anesth_id");
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["operations"] = "COperation type_anesth";
    return $backRefs;
  }
  
  function countOperations() {
    $this->_count_operations = $this->countBackRefs("operations");
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "name" => "notNull str",
      "ext_doc" => "enum list|1|2|3|4|5|6"
    );
    return array_merge($specsParent, $specs);
  }
  
}
?>