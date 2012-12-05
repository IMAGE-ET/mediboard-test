<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * A supervision graph
 */
class CSupervisionGraphToPack extends CMbObject {
  var $supervision_graph_to_pack_id ;

  var $graph_class;
  var $graph_id;

  var $pack_id;

  var $rank;

  /**
   * @var CSupervisionTimedEntity
   */
  var $_ref_graph;

  /**
   * @var CSupervisionGraphPack
   */
  var $_ref_pack;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_to_pack";
    $spec->key   = "supervision_graph_to_pack_id";
    $spec->uniques["title"] = array("graph_class", "graph_id", "pack_id");
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["graph_class"] = "enum list|CSupervisionGraph|CSupervisionTimedData";
    $props["graph_id"]    = "ref notNull class|CSupervisionTimedEntity meta|graph_class cascade";
    $props["pack_id"]     = "ref notNull class|CSupervisionGraphPack";
    $props["rank"]        = "num notNull";
    return $props;
  }

  /**
   * @return CSupervisionTimedEntity|CSupervisionGraph|CSupervisionTimedData
   */
  function loadRefGraph(){
    return $this->_ref_graph = $this->loadFwdRef("graph_id");
  }

  /**
   * @return CSupervisionGraphPack
   */
  function loadRefPack(){
    return $this->_ref_pack = $this->loadFwdRef("pack_id");
  }
}
