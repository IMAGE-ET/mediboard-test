<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldGroup extends CMbObject {
  var $ex_class_field_group_id = null;
  
  var $ex_class_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $formula = null;
  var $_formula = null;
  var $formula_result_field_id = null;
  
  var $_ref_ex_class = null;
  var $_ref_ex_fields = null;
  var $_ref_formula_result_field = null;
  
  static $_formula_token_re = "/\[([^\]]+)\]/";

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_group";
    $spec->key   = "ex_class_field_group_id";
    $spec->uniques["name"] = array("ex_class_id", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref class|CExClass cascade";
    $props["name"]        = "str notNull";
    $props["formula"]     = "text"; // canonical tokens
    $props["_formula"]     = "text"; // Localized tokens
    $props["formula_result_field_id"] = "ref class|CExClassField";
    return $props;
  }
	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["class_fields"] = "CExClassField ex_group_id";
    $backProps["host_fields"]  = "CExClassHostField ex_group_id";
    return $backProps;
  }
  
  function formulaToDB($update = true) {
    if (!$this->_formula) return;
    
    $field_names = $this->getFieldNames(false);
    $formula = $this->_formula;
    
    if (!preg_match_all(self::$_formula_token_re, $formula, $matches)) {
      return "Formule invalide";
    }
    
    $msg = array();
    
    foreach($matches[1] as $_match) {
      $_trimmed = trim($_match);
      if (!array_key_exists($_trimmed, $field_names)) {
        $msg[] = "\"$_match\"";
      }
      else {
        $formula = str_replace($_match, $field_names[$_trimmed], $formula);
      }
    }
    
    if (empty($msg)) {
      if ($update) {
        $this->formula = $formula;
      }
      return;
    }
    
    return "Des éléments n'ont pas été reconnus dans la formule: ".implode(", ", $msg);
  }
  
  function formulaFromDB(){
    //$this->completeField("formula"); memory limit :(
    
    if (!$this->formula) return;
    
    $field_names = $this->getFieldNames(true);
    
    $formula = $this->formula;
    
    if (!preg_match_all(self::$_formula_token_re, $formula, $matches)) {
      return "Formule invalide";
    }
    
    foreach($matches[1] as $_match) {
      $_trimmed = trim($_match);
      if (array_key_exists($_trimmed, $field_names)) {
        $formula = str_replace($_match, $field_names[$_trimmed], $formula);
      }
    }
    
    $this->_formula = $formula;
  }
  
  function getFieldNames($name_as_key = true){
    $ds = $this->_spec->ds;
    
    $req = new CRequest();
    $req->addTable("ex_class_field");
    $req->addSelect("ex_class_field.name, ex_class_field_translation.std AS locale");
    $req->addLJoin(array(
      "ex_class_field_translation" => "ex_class_field_translation.ex_class_field_id = ex_class_field.ex_class_field_id"
    ));
    $req->addWhere(array(
      "ex_group_id" => $ds->prepare("= %", $this->_id),
    ));
    
    $results = $ds->loadList($req->getRequest());
    
    if ($name_as_key) {
      return array_combine(CMbArray::pluck($results, "name"), CMbArray::pluck($results, "locale"));
    }
    
    return array_combine(CMbArray::pluck($results, "locale"), CMbArray::pluck($results, "name"));
  }
  
  function checkFormula(){
    return $this->formulaToDB(false);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->name;
    
    $this->formulaFromDB();
  }
  
  function check(){
    if ($msg = $this->checkFormula(false)) {
      return $msg;
    }
    
    $this->formulaToDB(true);
    
    if ($msg = parent::check()) {
      return $msg;
    }
  }
  
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
	
  function loadRefsFields(){
    return $this->_ref_fields = $this->loadBackRefs("class_fields");
  }
  
  function loadRefFormulaResultField($cache = true){
    return $this->_ref_formula_result_field = $this->loadFwdRef("formula_result_field_id", $cache);
  }
}
