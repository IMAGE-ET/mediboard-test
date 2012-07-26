<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2TableEntry 
 * HL7 Table Entry
 */
class CHL7v2TableEntry extends CHL7v2TableObject { 
  // DB Table key
  var $table_entry_id  = null;
  
  var $number          = null;
  
  var $code_hl7_from   = null;
  var $code_hl7_to     = null;
  
  var $code_mb_from    = null;
  var $code_mb_to      = null;
  
  var $description     = null;
  var $user            = null;
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "table_entry";
    $spec->key   = "table_entry_id";
    $spec->uniques["number_code_hl7"] = array("number", "code_hl7_from");
    $spec->uniques["number_code_mb"]  = array("number", "code_mb_from");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["number"]        = "num notNull maxLength|5 seekable";
    $props["code_hl7_from"] = "str maxLength|30 protected";
    $props["code_hl7_to"]   = "str maxLength|30 protected";
    $props["code_mb_from"]  = "str maxLength|30 protected";
    $props["code_mb_to"]    = "str maxLength|30 protected";
    $props["description"]   = "str seekable";
    $props["user"]          = "bool notNull default|0";
    return $props;
  }
  
  function getBackProps() {
    return array();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->description;
    $this->_shortview = $this->number;
  }
  
  static function mapTo($table, $mbValue) {
    return CHL7v2::getTableHL7Value($table, $mbValue);
  }
  
  static function mapFrom($table, $hl7Value) {
    if ($value = CHL7v2::getTableMbValue($table, $hl7Value)) {
      return $value;
    }
    
    return null;
  }
}
