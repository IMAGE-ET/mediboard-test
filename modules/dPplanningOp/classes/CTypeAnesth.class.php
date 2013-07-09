<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
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
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_anesth';
    $spec->key   = 'type_anesth_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]    = "str notNull";
    $props["ext_doc"] = "enum list|1|2|3|4|5|6";
    $props["actif"]   = "bool notNull default|1" ;

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation type_anesth";
    $backProps["protocole"]  = "CProtocole type_anesth";

    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
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