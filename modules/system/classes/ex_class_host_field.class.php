<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassHostField extends CMbObject {
  var $ex_class_host_field_id = null;
  
  var $ex_class_id = null;
  var $field = null;
	
  var $coord_label_x = null;
  var $coord_label_y = null;
  var $coord_value_x = null;
  var $coord_value_y = null;
  
  var $_ref_ex_class = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_host_field";
    $spec->key   = "ex_class_host_field_id";
    //$spec->uniques["field"] = array("ex_class_id", "field");
    $spec->uniques["coord_value"] = array("ex_class_id", "coord_value_x", "coord_value_y");
    $spec->uniques["coord_label"] = array("ex_class_id", "coord_label_x", "coord_label_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref notNull class|CExClass cascade";
    $props["field"]       = "str notNull canonical";
    
    $props["coord_value_x"] = "num min|0 max|100";
    $props["coord_value_y"] = "num min|0 max|100";
    $props["coord_label_x"] = "num min|0 max|100";
    $props["coord_label_y"] = "num min|0 max|100";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
		
		
  }
  
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
}
