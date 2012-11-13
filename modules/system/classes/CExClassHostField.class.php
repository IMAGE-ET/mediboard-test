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
  
  var $ex_group_id = null;
  
  //var $host_type = null;
  var $host_class = null;
  var $field = null;
  
  var $coord_label_x = null;
  var $coord_label_y = null;
  var $coord_value_x = null;
  var $coord_value_y = null;
  
  var $coord_left    = null;
  var $coord_top     = null;
  var $coord_width   = null;
  var $coord_height  = null;
  
  var $_ref_ex_group = null;
  var $_ref_host_object = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_host_field";
    $spec->key   = "ex_class_host_field_id";
    $spec->uniques["coord_value"] = array("ex_group_id", "coord_value_x", "coord_value_y", "coord_label_x", "coord_label_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_group_id"]       = "ref notNull class|CExClassFieldGroup cascade";
    
    //$props["host_type"]     = "enum list|host|reference1|reference2 default|host";
    $props["host_class"]    = "str notNull";
    $props["field"]         = "str notNull canonical";
    
    $props["coord_value_x"] = "num min|0 max|100";
    $props["coord_value_y"] = "num min|0 max|100";
    $props["coord_label_x"] = "num min|0 max|100";
    $props["coord_label_y"] = "num min|0 max|100";
    
    // Pixel positionned
    $props["coord_left"]   = "num";
    $props["coord_top"]    = "num";
    $props["coord_width"]  = "num min|1";
    $props["coord_height"] = "num min|1";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->field; // FIXME
  }
  
  function loadRefExGroup($cache = true){
    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }
  
  function getHostObject(CExObject $ex_object) {
    if ($this->host_class == $ex_object->object_class) {
      return $this->_ref_host_object = $ex_object->_ref_object;
    }
    
    if ($this->host_class == $ex_object->reference_class) {
      return $this->_ref_host_object = $ex_object->_ref_reference_object_1;
    }
    
    if ($this->host_class == $ex_object->reference2_class) {
      return $this->_ref_host_object = $ex_object->_ref_reference_object_2;
    }
    
    $this->_ref_host_object = new $this->host_class;
  }
}
