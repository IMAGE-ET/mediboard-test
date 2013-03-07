<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$context_class  = CValue::post("context_class");
$context_id     = CValue::post("context_id");
$patient_id     = CValue::post("patient_id");

$datetime_start = CValue::post("datetime_start");
$datetime_end   = CValue::post("datetime_end");
$period         = CValue::post("period", 30); // in seconds

$graph = new CSupervisionGraph;
$graphs = $graph->loadList(array(
  "disabled" => "= '0'",
));

$n = 500;
$datetime = $datetime_start;
$times = array();
while(--$n > 0 && ($datetime < $datetime_end)) {
  $observation_result_set = new CObservationResultSet;
  $observation_result_set->context_class = $context_class;
  $observation_result_set->context_id    = $context_id;
  $observation_result_set->patient_id    = $patient_id;
  $observation_result_set->datetime      = $datetime;
  $observation_result_set->loadMatchingObject();
  $observation_result_set->store();
  
  $times[$datetime] = $observation_result_set;

  $datetime = CMbDT::dateTime("+$period SECONDS", $datetime);
}

foreach($graphs as $_graph) {
  $_axes = $_graph->loadRefsAxes();
  
  foreach($_axes as $_axis) {
    $_series = $_axis->loadRefsSeries();
    
    foreach($_series as $_serie) {
      $_samples = $_serie->getSampleData(array_keys($times));
      
      foreach($_samples as $_sample) {
        list($_datetime, $_value) = $_sample;
        
        $result = new CObservationResult;
        $result->observation_result_set_id = $times[$_datetime]->_id;
        $result->unit_id = $_serie->value_unit_id;
        $result->value_type_id = $_serie->value_type_id;
        $result->status = "I";
        $result->method = "SAMPLE";
        $result->loadMatchingObject();
        
        $result->value = $_value;
        $result->store();
      }
    }
  }
}

CAppUI::stepAjax("Données de test générées", UI_MSG_OK);
CApp::rip();

