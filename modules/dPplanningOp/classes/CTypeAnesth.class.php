<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPPlanningOp
 * @author     Sébastien Fillonneau <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CTypeAnesth class
 */
class CTypeAnesth extends CMbObject {
  // DB Table key
  public $type_anesth_id;

  // DB Fields
  public $name;
  public $ext_doc;
  public $actif;
  
  // References
  public $_count_operations;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_anesth';
    $spec->key   = 'type_anesth_id';

    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]    = "str notNull";
    $props["ext_doc"] = "enum list|1|2|3|4|5|6";
    $props["actif"]   = "bool notNull default|1" ;

    return $props;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation type_anesth";
    $backProps["protocole"]  = "CProtocole type_anesth";

    return $backProps;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  /**
   * Count operations
   *
   * @return int
   */
  function countOperations() {
    return $this->_count_operations = $this->countBackRefs("operations");
  }
}