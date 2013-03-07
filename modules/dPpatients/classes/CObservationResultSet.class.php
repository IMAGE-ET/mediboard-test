<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation result set, based on the HL7 OBR message
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBR.html
 */
class CObservationResultSet extends CMbObject {
  var $observation_result_set_id = null;
  
  var $patient_id            = null;
  var $datetime              = null;
  var $context_class         = null;
  var $context_id            = null;

  /**
   * @var CMbObject
   */
  var $_ref_context          = null;

  /**
   * @var CPatient
   */
  var $_ref_patient          = null;

  /**
   * @var CObservationResult[]
   */
  var $_ref_results          = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_result_set";
    $spec->key   = "observation_result_set_id";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]    = "ref notNull class|CPatient";
    $props["datetime"]      = "dateTime notNull";
    $props["context_class"] = "str notNull";
    $props["context_id"]    = "ref class|CMbObject meta|context_class";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult observation_result_set_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->getFormattedValue("datetime");
  }

  /**
   * @param bool $cache
   *
   * @return CMbObject
   */
  function loadRefContext($cache = true) {
    return $this->_ref_context = $this->loadFwdRef("context_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }

  /**
   * @return CObservationResult[]
   */
  function loadRefsResults(){
    return $this->_ref_results = $this->loadBackRefs("observation_results");
  }

  /**
   * @param CMbObject $object
   *
   * @return array
   */
  static function getResultsFor(CMbObject $object) {
    $request = new CRequest;
    $request->addTable("observation_result");
    $request->addSelect("*");
    $request->addLJoin(array(
      "observation_result_set" => "observation_result_set.observation_result_set_id = observation_result.observation_result_set_id",
    ));
    $request->addWhere(array(
      "observation_result_set.context_class" => "= '$object->_class'",
      "observation_result_set.context_id"    => "= '$object->_id'",
    ));
    $request->addOrder("observation_result.observation_result_id");
    
    $results = $object->_spec->ds->loadList($request->getRequest());
    
    $times = array();
    $data = array();
    
    foreach ($results as $_result) {
      $_time = CMbDate::toUTCTimestamp($_result["datetime"]);
      $times[$_time] = $_time;
      
      $data[$_result["value_type_id"]][$_result["unit_id"]][] = array(
        $_time,
        floatval($_result["value"]),
      );
    }
    
    return array($data, array_values($times));
  }

  /**
   * @param COperation $interv
   *
   * @return array
   */
  static function buildGraphs(COperation $interv, $pack_id) {
    list($results, /*$times*/) = CObservationResultSet::getResultsFor($interv);

    $time_min = $interv->entree_salle;
    $time_max = CMbDT::time("+".CMbDT::minutesRelative("00:00:00", $interv->temp_operation)." MINUTES", $interv->entree_salle);

    $date = CMbDT::date($interv->_datetime);

    $time_debut_op_iso = "$date $time_min";
    $time_fin_op_iso   = "$date $time_max";

    $round_minutes = 10;
    $round = $round_minutes * 60000; // FIXME

    $time_min = floor(CMbDate::toUTCTimestamp("$date $time_min") / $round) * $round;
    $time_max =  ceil(CMbDate::toUTCTimestamp("$date $time_max") / $round) * $round;

    $pack = new CSupervisionGraphPack();
    $pack->load($pack_id);
    $graph_links = $pack->loadRefsGraphLinks();

    $graphs = array();
    foreach ($graph_links as $_gl) {
      $_go = $_gl->loadRefGraph();
      $graphs[] = $_go;

      if ($_go instanceof CSupervisionGraph) {
        $_go->buildGraph($results, $time_min, $time_max);
      }
    }

    /*
    $graph_object = new CSupervisionGraph;
    $graph_objects = $graph_object->loadList(array(
      "disabled" => "= '0'",
    ));

    $graphs = array();
    foreach ($graph_objects as $_go) {
      $graphs[] = $_go->buildGraph($results, $time_min, $time_max);
    }*/

    $yaxes_count = 0;
    foreach ($graphs as $_graph) {
      if ($_graph instanceof CSupervisionGraph) {
        $yaxes_count = max($yaxes_count, count($_graph->_graph_data["yaxes"]));
      }
    }

    foreach ($graphs as $_graph) {
      if ($_graph instanceof CSupervisionGraph) {
        if (count($_graph->_graph_data["yaxes"]) < $yaxes_count) {
          $_graph->_graph_data["yaxes"] = array_pad($_graph->_graph_data["yaxes"], $yaxes_count, CSupervisionGraphAxis::$default_yaxis);
        }
      }
    }

    return array(
      $graphs, $yaxes_count,
      $time_min, $time_max,
      $time_debut_op_iso, $time_fin_op_iso,
    );
  }
}
