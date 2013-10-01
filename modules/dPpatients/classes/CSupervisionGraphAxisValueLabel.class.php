<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * A supervision graph Y axis value label
 */
class CSupervisionGraphAxisValueLabel extends CMbObject {
  public $supervision_graph_value_label_id;
  
  public $supervision_graph_axis_id;
  public $value;
  public $title;

  /** @var CSupervisionGraphAxis */
  public $_ref_axis;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_value_label";
    $spec->key   = "supervision_graph_value_label_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["supervision_graph_axis_id"] = "ref notNull class|CSupervisionGraphAxis cascade";
    $props["value"]                     = "num notNull";
    $props["title"]                     = "str notNull";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult label_id";
    return $backProps;
  }

  /**
   * Load axis
   *
   * @param bool $cache Use object cache
   *
   * @return CSupervisionGraphAxis
   */
  function loadRefAxis($cache = true) {
    return $this->_ref_axis = $this->loadFwdRef("supervision_graph_axis_id", $cache);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }
}
