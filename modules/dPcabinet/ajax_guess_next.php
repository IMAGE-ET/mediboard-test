<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$type         = CValue::get("type");
$nb           = CValue::get("number", 1);
$date         = CValue::get("date");
$chir_id      = CValue::get("chir_id");
$function_id  = CValue::get("function_id");

$plages = array();

if (!$type || !$nb) {
  CApp::json($plages);
}

// get current
$plage_consult = new CPlageconsult();
$ds = $plage_consult->getDS();
$where = array();
$where['date'] = " = '$date' ";
$where['locked'] = " != '1' ";
if ($chir_id) {
  $where['chir_id'] = " = '$chir_id' ";
  $plage_consult->loadObject($where);
}
if (!$plage_consult->_id && $function_id) {
  $mediuser = new CMediusers();
  $users = $mediuser->loadProfessionnelDeSante(PERM_READ, $function_id);
  $where["chir_id"] = $ds->prepareIn(array_keys($users));
  $plage_consult->loadObject($where);
}

if (!$plage_consult->_id) {
  CApp::json($plages);
}

// guess next dates
$dates = array();
for ($a = 1; $a <= $nb; $a++) {
  $dates[$a] = CMbDT::date("+$a $type", $date);
}
$where["date"] = $ds->prepareIn($dates);
$plages = $plage_consult->loadList($where, "date ASC");

$date_plage = array();
// fill out
foreach ($dates as $nb => $_date) {
  $date_plage[$_date] = array();
}
foreach ($plages as $_plage) {
  $date_plage[$_plage->date] = $_plage->_id;
  //$date_plage[$_plage->date][] = $_plage->_id;  //@TODO : array
}

$results = array();
foreach ($dates as $nb => $_date) {
  // try to find something else if no result on date
  if (!count($date_plage[$_date])) {
    $results[] = $_date;
    continue;
  }
  $results[] = $date_plage[$_date];
}

CApp::json($results);