<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassField extends CMbObject {
  var $ex_class_field_id = null;
  
  var $ex_class_id = null;
  var $name = null; // != object_class, object_id, ex_ClassName_event_id, 
  var $prop = null; 
  
  var $_locale = null;
  var $_locale_desc = null;
  var $_locale_court = null;
  
  var $_ref_ex_class = null;
  var $_ref_translation = null;
  var $_spec_object = null;
  
  static $_indexed_types = array("ref", "date", "dateTime", "time");

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field";
    $spec->key   = "ex_class_field_id";
    $spec->uniques["field"] = array("ex_class_id", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref notNull class|CExClass cascade";
    $props["name"]        = "str notNull protected canonical";
    $props["prop"]        = "str notNull";
    $props["_locale"]     = "str";
    $props["_locale_desc"]  = "str";
    $props["_locale_court"] = "str";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = "$this->name [$this->prop]";
    
    $this->updateTranslation();
  }
  
  function loadRefExClass($cache = true){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id", $cache);
  }
  
  function loadRefTranslation() {
    $trans = new CExClassFieldTranslation;
    $trans->lang = CAppUI::pref("LOCALE");
    $trans->ex_class_field_id = $this->_id;
    $trans->loadMatchingObject();
    return $this->_ref_translation = $trans;
  }
  
  function updateTranslation(){
    $trans = $this->loadRefTranslation();
    
    $this->_locale       = $trans->std;
    $this->_locale_desc  = $trans->desc;
    $this->_locale_court = $trans->court;
    
    $this->_view = $this->_locale;
    
    return $trans;
  }
  
  function getTableName(){
    return $this->loadRefExClass()->getTableName();
  }
  
  function getSpecObject(){
    $ex_class = $this->loadRefExClass();
    $ex_class->_fields_by_name[$this->name] = $this;
    $ex_class->_ref_fields[$this->_id] = $this;
    
    $dummy = new CExObject;
    $dummy->setExClass($ex_class);
    
    return $this->_spec_object = $dummy->_specs[$this->name];
  }
  
  function updateDBFields(){
    parent::updateDBFields();
    
    if ($this->_locale || $this->_locale_desc || $this->_locale_court) {
      $trans = $this->loadRefTranslation();
      $trans->std = $this->_locale;
      $trans->desc = $this->_locale_desc;
      $trans->court = $this->_locale_court;
      if ($msg = $trans->store()) {
        mbTrace($msg, "", true);
      }
    }
  }
  
  function getSQLSpec(){
    return $this->getSpecObject()->getFullDBSpec();
  }
  
  function store(){
    if ($msg = $this->check()) return $msg;
    
    if (!preg_match('/^[a-z0-9_]+$/i', $this->name)) {
      return "Nom de champ invalide ($this->name)";
    }
    
    $ds = $this->_spec->ds;
    
    if (!$this->_id) {
      $table_name = $this->getTableName();
      $sql_spec = $this->getSQLSpec();
      $query = "ALTER TABLE `$table_name` ADD `$this->name` $sql_spec";
      
      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu être ajouté à la table '$table_name' (".$ds->error().")";
      }
      
      $spec_type = $this->getSpecObject()->getSpecType();
      
      // ajout de l'index
      if (in_array($spec_type, self::$_indexed_types)) {
        $query = "ALTER TABLE `$table_name` ADD INDEX (`$this->name`)";
        
        if (!$ds->query($query)) {
          //return "L'index sur le champ '$this->name' n'a pas pu être ajouté (".$ds->error().")";
        }
      }
    }
    
    else if ($this->fieldModified("name") || $this->fieldModified("prop")) {
      $table_name = $this->getTableName();
      $sql_spec = $this->getSQLSpec();
      mbTrace($sql_spec, "sql_spec", true);
      $query = "ALTER TABLE `$table_name` CHANGE `{$this->_old->name}` `$this->name` $sql_spec";

      if (!$ds->query($query)) {
        return "Le champ '$this->name' n'a pas pu être mis à jour (".$ds->error().")";
      }
    }
    
    return parent::store();
  }
  
  function delete(){
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }
    
    $this->completeField("name");
    
    $table_name = $this->loadRefExClass()->getTableName();
    $query = "ALTER TABLE `$table_name` DROP `$this->name`";
    $ds = $this->_spec->ds;
    
    if (!$ds->query($query)) {
      return "Le champ '$this->name' n'a pas pu être supprimé (".$ds->error().")";
    }
    
    return parent::delete();
  }
}
