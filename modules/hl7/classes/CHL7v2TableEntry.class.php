<?php

/**
 * HL7 Table Entry
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2TableObject");

/**
 * Class CHL7v2TableEntry 
 * HL7 Table Entry
 */
class CHL7v2TableEntry extends CHL7v2TableObject { 
  // DB Table key
  var $table_entry_id  = null;
  
  var $number          = null;
  var $code_hl7        = null;
  var $code_mb         = null;
  var $description     = null;
  var $user            = null;
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table                  = 'table_entry';
    $spec->key                    = 'table_entry_id';
    $spec->uniques["code_number"] = array("number", "code_hl7");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["number"]      = "num notNull maxLength|5 seekable";
    $props["code_hl7"]    = "str maxLength|30 protected";
    $props["code_mb"]     = "str maxLength|30 protected";
    $props["description"] = "str seekable";
    $props["user"]        = "bool notNull default|0";
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
}
?>