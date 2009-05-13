<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTriggerMark extends CMbObject {
  // Table key
  var $mark_id        = null;
  
  // DB Fields
  var $trigger_class   = null;
  var $trigger_number = null;
  var $mark           = null;
  var $done           = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "trigger_mark";
    $spec->key   = "mark_id";
    $spec->loggable = true;
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["trigger_class"]  = "str notNull";
  	$props["trigger_number"] = "str notNull maxLength|10";
    $props["done"]           = "bool notNull";
    $props["mark"]           = "str notNull";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = "Mark for trigger $this->trigger_class #$this->trigger_number";
  }
}
?>