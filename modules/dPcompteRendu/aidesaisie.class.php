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
  var $depend_value       = null;
  
  // Referenced objects
  var $_ref_user          = null;
  var $_ref_function      = null;
  var $_ref_abstat_object = null;

  function CAideSaisie() {
    $this->CMbObject("aide_saisie", "aide_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function check() {
    $msg = null;
    
    $where = array();
    if($this->user_id){
      $where["user_id"] = $this->_spec->ds->prepare("= %",$this->user_id);
    }else{
      $where["function_id"] = $this->_spec->ds->prepare("= %",$this->function_id);
    }
    $where["class"]        = $this->_spec->ds->prepare("= %",$this->class);
    $where["field"]        = $this->_spec->ds->prepare("= %",$this->field);
    $where["depend_value"] = $this->_spec->ds->prepare("= %",$this->depend_value);
    $where["text"]         = $this->_spec->ds->prepare("= %",$this->text);
    $where["aide_id"]      = $this->_spec->ds->prepare("!= %",$this->aide_id);
    
    $sql = new CRequest();
    $sql->addSelect("count(aide_id)");
    $sql->addTable("aide_saisie");
    $sql->addWhere($where);
    
    $nb_result = $this->_spec->ds->loadResult($sql->getRequest());
    
    if($nb_result){
      $msg.= "Cette aide existe déjà<br />";
    }
    
    return $msg . parent::check();
  }
  
  function getSpecs() {
    return array (
      "user_id"      => "ref xor|function_id class|CMediusers",
      "function_id"  => "ref xor|user_id class|CFunctions",
      "class"        => "notNull str",
      "field"        => "notNull str",
      "name"         => "notNull str",
      "text"         => "notNull text",
      "depend_value" => "str"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
    if($this->class){
      $this->_ref_abstat_object = new $this->class;
    }
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
}

?>