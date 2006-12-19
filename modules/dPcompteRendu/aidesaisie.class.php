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
  var $user_id     = null;
  var $function_id = null;

  // DB fields
  var $class = null;
  var $field = null;
  var $name  = null;
  var $text  = null;
  
  // Referenced objects
  var $_ref_user     = null;
  var $_ref_function = null;

  function CAideSaisie() {
    $this->CMbObject("aide_saisie", "aide_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function check() {
    $msg = null;
    global $pathos;
    
    $where = array();
    if($this->user_id){
      $where["user_id"] = db_prepare("= %",$this->user_id);
    }else{
      $where["function_id"] = db_prepare("= %",$this->function_id);
    }
    $where["class"] = db_prepare("= %",$this->class);
    $where["field"] = db_prepare("= %",$this->field);
    $where["text"]  = db_prepare("= %",$this->text);
    
    $sql = new CRequest();
    $sql->addSelect("count(aide_id)");
    $sql->addTable("aide_saisie");
    $sql->addWhere($where);
    
    $nb_result = db_loadResult($sql->getRequest());
    
    if($nb_result){
      $msg.= "Cette aide existe déjà<br />";
    }
    
    return $msg . parent::check();
  }
  
  function getSpecs() {
    return array (
      "user_id"     => "ref",
      "function_id" => "ref|xor|user_id",
      "class"       => "str|notNull",
      "field"       => "str|notNull",
      "name"        => "str|notNull",
      "text"        => "text|notNull"
    );
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