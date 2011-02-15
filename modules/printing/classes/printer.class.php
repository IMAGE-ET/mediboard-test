<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass('mbMetaObject');

class CPrinter extends CMbMetaObject {
  // DB Table key
  var $printer_id = null;
  
  // DB Fields
  var $name = null;
  var $function_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'printer';
    $spec->key   = 'printer_id';
    return $spec;
  }
  
  function loadTargetObject() {
    parent::loadTargetObject();
    $this->_view = $this->_ref_object->name;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["name"]         = "str notNull";
    $props["function_id"]  = "ref class|CFunctions notNull";
    $props["object_id"]    = "ref notNull class|CSourcePrinter meta|object_class";
    $props["object_class"] = "str notNull class show|0";
    
    return $props;
  }
}

?>