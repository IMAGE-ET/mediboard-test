<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::read();

$operation_id      = CValue::get("operation_id");
$type_ressource_id = CValue::get("type_ressource_id");
$date              = CValue::get("date");
$besoin_ressource_id = CValue::get("besoin_ressource_id");
$usage_ressource_id = CValue::get("usage_ressource_id");


$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefPlageOp();

$type_ressource = new CTypeRessource;
$type_ressource->load($type_ressource_id);

// On récupère les types de ressources que ciblent les besoins sur l'intervention
$besoins = $operation->loadBackRefs("besoins_ressources");
$types_ressources = CMbObject::massLoadFwdRef($besoins, "type_ressource_id");

$ressources = $type_ressource->loadRefsRessources();
$hours = array();

if (!$date) {
  $date = mbDate($operation->_datetime);
}

$date_min = $date." 00:00:00";
$date_max = mbDate("+1 day", $date)." 00:00:00";

$date_before = mbDate("-1 day", $date);
$date_after = mbDate("+1 day", $date);

$date_temp = $date_min;

while ($date_temp < $date_max) {
  $hours[] = $date_temp;
  $date_temp = mbDateTime("+1 hour", $date_temp);
}


$hour_operation = mbTransformTime(null, $operation->temp_operation, "%H");
$min_operation  = mbTransformTime(null, $operation->temp_operation, "%M");

$operation->_debut_offset = CMbDate::position($operation->_datetime, $date_min, "1hour");
$operation->_fin_offset   = CMbDate::position(min($date_max, mbDateTime("+$hour_operation hours +$min_operation minutes", $operation->_datetime)), $date_min, "1hour");
$operation->_width        = $operation->_fin_offset - $operation->_debut_offset;

// Les usages sur la période définie
$usage = new CUsageRessource;
$date_op = mbDate($operation->_datetime);
$where = array();
$ljoin = array();


$ljoin["ressource_materielle"] = "ressource_materielle.ressource_materielle_id = usage_ressource.ressource_materielle_id";
$ljoin["type_ressource"] = "type_ressource.type_ressource_id = ressource_materielle.type_ressource_id";
$ljoin["besoin_ressource"] = "usage_ressource.besoin_ressource_id = besoin_ressource.besoin_ressource_id";
$ljoin["operations"] = "besoin_ressource.operation_id = operations.operation_id";
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where["type_ressource.type_ressource_id"] = "= '$type_ressource_id'";
$where[] = "(operations.plageop_id IS NULL AND operations.date = '$date_op') OR (operations.plageop_id IS NOT NULL AND plagesop.date = '$date_op')";

$usages = $usage->loadList($where, null, null, null, $ljoin);

$besoins = CMbObject::massLoadFwdRef($usages, "besoin_ressource_id");

CMbObject::massLoadFwdRef($besoins, "operation_id");

$usages_by_ressource = array();

foreach ($usages as $_usage) {
  if (!isset($usages_by_ressource[$_usage->ressource_materielle_id])) {
    $usages_by_ressource[$_usage->ressource_materielle_id] = array();
  }
  $usages_by_ressource[$_usage->ressource_materielle_id][] = $_usage;
  
  $_operation = $_usage->loadRefBesoin()->loadRefOperation();
  $_operation->loadRefPlageOp();
  $hour_operation = mbTransformTime(null, $_operation->temp_operation, "%H");
  $min_operation = mbTransformTime(null, $_operation->temp_operation, "%M");
  
  $_usage->_debut_offset = CMbDate::position(max($date_min, $_operation->_datetime), $date_min, "1hour");
  
  $_usage->_fin_offset = CMbDate::position(min($date_max, mbDateTime("+$hour_operation hours +$min_operation minutes", $_operation->_datetime)), $date_min, "1hour");
  $_usage->_width = $_usage->_fin_offset - $_usage->_debut_offset;
}

// Les indispos sur cette même période
$indispos_by_ressource = array();

foreach ($ressources as $_ressource) {
  $indispos_by_ressource[$_ressource->_id] = $_ressource->loadRefsIndispos($date_min, $date_max);
  
  foreach ($indispos_by_ressource[$_ressource->_id] as $_indispo) {
    $_indispo->_debut_offset = CMbDate::position(max($date_min, $_indispo->deb), $date_min, "1hour");
    $_indispo->_fin_offset   = CMbDate::position(min($date_max, $_indispo->fin), $date_min, "1hour");
    
    $_indispo->_width        = $_indispo->_fin_offset - $_indispo->_debut_offset;
    
    
  }
}

$smarty = new CSmartyDP;

$smarty->assign("ressources", $ressources);
$smarty->assign("hours"     , $hours);
$smarty->assign("operation" , $operation);
$smarty->assign("date"      , $date);
$smarty->assign("date_before", $date_before);
$smarty->assign("date_after", $date_after);
$smarty->assign("usages_by_ressource"  , $usages_by_ressource);
$smarty->assign("indispos_by_ressource", $indispos_by_ressource);
$smarty->assign("types_ressources"     , $types_ressources);
$smarty->assign("type_ressource_id"    , $type_ressource_id);
$smarty->assign("besoin_ressource_id"  , $besoin_ressource_id);
$smarty->assign("usage_ressource_id"   , $usage_ressource_id);

$smarty->display("inc_vw_planning_ressources.tpl");

