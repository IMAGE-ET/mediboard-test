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
 * A supervision graph
 */
class CSupervisionGraphToPack extends CMbObject {
  public $supervision_graph_to_pack_id;

  public $graph_class;
  public $graph_id;

  public $pack_id;

  public $rank;

  /** @var CSupervisionTimedEntity */
  public $_ref_graph;

  /** @var CSupervisionGraphPack */
  public $_ref_pack;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_to_pack";
    $spec->key   = "supervision_graph_to_pack_id";
    $spec->uniques["title"] = array("graph_class", "graph_id", "pack_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["graph_class"] = "enum list|CSupervisionGraph|CSupervisionTimedData";
    $props["graph_id"]    = "ref notNull class|CSupervisionTimedEntity meta|graph_class cascade";
    $props["pack_id"]     = "ref notNull class|CSupervisionGraphPack";
    $props["rank"]        = "num notNull";
    return $props;
  }

  /**
   * Get the graph
   *
   * @return CSupervisionTimedEntity|CSupervisionGraph|CSupervisionTimedData
   */
  function loadRefGraph(){
    return $this->_ref_graph = $this->loadFwdRef("graph_id");
  }

  /**
   * Get the pack
   *
   * @return CSupervisionGraphPack
   */
  function loadRefPack(){
    return $this->_ref_pack = $this->loadFwdRef("pack_id");
  }
}
