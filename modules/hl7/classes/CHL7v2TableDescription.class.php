<?php

/**
 * HL7 Table Description
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2TableDescription 
 * HL7 Table Description
 */
class CHL7v2TableDescription extends CHL7v2TableObject { 
  // DB Table key
  var $table_description_id = null;
  
  var $number               = null;
  var $description          = null;
  
  var $_count_entries = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'table_description';
    $spec->key         = 'table_description_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["number"]      = "num notNull maxLength|5 seekable";
    $props["description"] = "str maxLength|80 seekable";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->description;
    $this->_shortview = $this->number;
  }
  
  function countEntries() {
    $table_entry         = new CHL7v2TableEntry();
    $table_entry->number = $this->number;
    return $this->_count_entries = $table_entry->countMatchingList();
  }
  
}
?>