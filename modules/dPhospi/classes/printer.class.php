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
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["name"]         = "str notNull";
    $specs["function_id"]  = "ref class|CFunctions notNull";
    $specs["object_id"]    = "ref notNull class|CMbObject meta|object_class cascade purgeable show|1";
    $specs["object_class"] = "str notNull class show|0";
   return $specs;
  }
}

?>