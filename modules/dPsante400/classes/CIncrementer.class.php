<?php /* $Id: CIdSante400.class.php 13724 2011-11-09 15:10:29Z lryo $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 13724 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CIncrementer extends CMbObject {
  // DB Table key
  var $incrementer_id = null;
  
  // DB fields
  var $object_class = null;
  var $group_id     = null;
  var $last_update  = null;
  var $value        = null;
  var $pattern      = null;
  
  var $_ref_group   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'incrementer';
    $spec->key   = 'incrementer_id';
    $spec->uniques["unique"] = array("object_class", "group_id");
    $spec->loggable = false;
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["object_class"] = "enum notNull list|CPatient|CSejour";
    $props["group_id"]     = "ref notNull class|CGroups autocomplete|text";
    $props["last_update"]  = "dateTime notNull";
    $props["value"]        = "str notNull default|1";
    $props["pattern"]      = "str notNull";

    return $props;
  } 
  
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  static function generateIdex(CMbObject $object, $tag, $group_id) {
    $incrementer               = new self;
    $incrementer->object_class = $object->_class;
    $incrementer->group_id     = $group_id;
    
    $mutex = new CMbSemaphore("incrementer-$object->_class-$group_id");
    $mutex->acquire();
    
    // Chargement du dernier 'increment' s'il existe sinon on déclenche une erreur
    if (!$incrementer->loadMatchingObject()) {
      $mutex->release();
      
      return;
    }

    $format_value = self::formatValue($object, $incrementer->pattern, $incrementer->value);
    
    // Création de l'identifiant externe
    $id400               = new CIdSante400();
    $id400->object_id    = $object->_id;
    $id400->object_class = $object->_class;
    $id400->tag          = $tag;
    $id400->id400        = $format_value;
    $id400->last_update  = mbDateTime();
    $id400->store();
    
    // Incrementation de l'idex
    $incrementer->value++;
    $incrementer->last_update = mbDateTime();
    $incrementer->store();

    $mutex->release();

    return $id400;
  }
  
  static function getVars(CMbObject $object) {
    $vars = $object->getIncrementVars();
    $default_vars = array(
      "year" => mbTransformTime(null, null, "%Y"),
    );
    $vars = array_merge($vars, $default_vars);
    
    return $vars;
  }
  
  static function formatValue(CMbObject $object, $pattern, $value) {
    $vars = self::getVars($object);
    
    foreach ($vars as $_var => $_value) {
      $value = str_replace("[$_var]", $_value, $value); 
    } 
    
    return sprintf($pattern, $value);
  }
}