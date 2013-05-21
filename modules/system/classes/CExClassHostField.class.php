<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CExClassHostField extends CMbObject {
  public $ex_class_host_field_id;
  
  public $ex_group_id;
  
  //public $host_type;
  public $host_class;
  public $field;
  
  public $coord_label_x;
  public $coord_label_y;
  public $coord_value_x;
  public $coord_value_y;
  
  public $coord_left;
  public $coord_top;
  public $coord_width;
  public $coord_height;
  
  public $_ref_ex_group;
  public $_ref_host_object;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_host_field";
    $spec->key   = "ex_class_host_field_id";
    $spec->uniques["coord_value"] = array("ex_group_id", "coord_value_x", "coord_value_y", "coord_label_x", "coord_label_y");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
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

  /**
   * @see parent::updateFormFields()
   */
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
