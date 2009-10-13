<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

class CAideSaisie extends CMbObject {
  // DB Table key
  var $aide_id = null;

  // DB References
  var $user_id            = null;
  var $function_id        = null;

  // DB fields
  var $class              = null;
  var $field              = null;
  var $name               = null;
  var $text               = null;
  var $depend_value_1     = null;
  var $depend_value_2     = null;
  
  // Form Fields
  var $_depend_field_1     = null;
  var $_depend_field_2     = null;
  var $_owner              = null;
  
  // Referenced objects
  var $_ref_user          = null;
  var $_ref_function      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'aide_saisie';
    $spec->key   = 'aide_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["user_id"]      = "ref xor|function_id class|CMediusers";
    $specs["function_id"]  = "ref xor|user_id class|CFunctions";
    $specs["class"]        = "str notNull";
    $specs["field"]        = "str notNull";
    $specs["name"]         = "str notNull";
    $specs["text"]         = "text notNull";
    $specs["depend_value_1"] = "str";
    $specs["depend_value_2"] = "str";
    
    $specs["_depend_field_1"] = "str";
    $specs["_depend_field_2"] = "str";
    $specs["_owner"]          = "enum list|user|func";
    
    return $specs;
  }
  
  function check() {
    $msg = null;
    
    $ds = $this->_spec->ds;
    
    $where = array();
    if($this->user_id){
      $where["user_id"] = $ds->prepare("= %",$this->user_id);
    }else{
      $where["function_id"] = $ds->prepare("= %",$this->function_id);
    }
    $where["class"]          = $ds->prepare("= %",$this->class);
    $where["field"]          = $ds->prepare("= %",$this->field);
    $where["depend_value_1"] = $ds->prepare("= %",$this->depend_value_1);
    $where["depend_value_2"] = $ds->prepare("= %",$this->depend_value_2);
    $where["text"]           = $ds->prepare("= %",$this->text);
    $where["aide_id"]        = $ds->prepare("!= %",$this->aide_id);
    
    $sql = new CRequest();
    $sql->addSelect("count(aide_id)");
    $sql->addTable("aide_saisie");
    $sql->addWhere($where);
    
    $nb_result = $ds->loadResult($sql->getRequest());
    
    if($nb_result){
      $msg.= "Cette aide existe déjà<br />";
    }
    
    return $msg . parent::check();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->name;
    
    // Owner
    if ($this->user_id    ) $this->_owner = "user";
    if ($this->function_id) $this->_owner = "func";

    // Depend fields
    if ($this->class) {
      $object = new $this->class;
      $helped = $object->_specs[$this->field]->helped;
      $this->_depend_field_1 = isset($helped[0]) ? $helped[0] : null; 
      $this->_depend_field_2 = isset($helped[1]) ? $helped[1] : null;
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
}

?>