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
 * Class CHL7v2TableDescription 
 * HL7 Table Description
 */
class CHL7v2TableDescription extends CHL7v2TableObject { 
  // DB Table key
  public $table_description_id;
  
  // DB Fields
  public $number;
  public $description;
  public $user;
  
  // Form fields
  public $_count_entries;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'table_description';
    $spec->key         = 'table_description_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["number"]      = "num notNull maxLength|5 seekable";
    $props["description"] = "str maxLength|80 seekable";
    $props["user"]        = "bool notNull default|0";
    
    // Form fields
    $props["_count_entries"] = "num";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    return array();
  }

  /**
   * @see parent::updateFormFields()
   */
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
