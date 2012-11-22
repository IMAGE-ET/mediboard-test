<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$type_modif = CValue::getOrSession("type_modif", "annule");
$date_max   = CValue::getOrSession("_date_max", mbDate());
if(phpversion() >= "5.3") {
  $date_max   = mbDate("last day of +0 months", $date_max); 
  $date_min   = mbDate("first day of -3 months", $date_max);
} else {
  $date_max = mbTransformTime("+ 1 month", $date_max, "%Y-%m-01");
  $date_min = mbDate("- 4 month", $date_max);
  $date_max = mbDate("- 1 day", $date_max);
}
$prat_id    = CValue::get("prat_id");
$salle_id   = CValue::get("salle_id");
$bloc_id    = CValue::get("bloc_id");
$codeCCAM   = CValue::get("codeCCAM");

$prat = new CMediusers;
$prat->load($prat_id);

$salle = new CSalle;
$salle->load($salle_id);

$where = array();

$salles = $salle->loadGroupList();
$list = array();


if($type_modif == "annule") {
  $queryInPlage   = "SELECT DISTINCT(operations.operation_id) AS op_id,
                       DATE_FORMAT(plagesop.date, '%Y - %m') AS mois,
                       plagesop.date AS orderitem
                     FROM operations
                     LEFT JOIN plagesop        ON plagesop.plageop_id = operations.plageop_id
                     LEFT JOIN sallesbloc      ON sallesbloc.salle_id = operations.salle_id
                     LEFT JOIN bloc_operatoire ON sallesbloc.bloc_id = bloc_operatoire.bloc_operatoire_id
                     LEFT JOIN user_log        ON user_log.object_id = operations.operation_id
                       AND user_log.object_class = 'COperation'
                     WHERE plagesop.date BETWEEN '$date_min' AND '$date_max'
                       AND user_log.type = 'store'
                       AND DATE(user_log.date) = plagesop.date
                       AND user_log.fields LIKE '%annulee%'
                       AND operations.annulee = '1'";
  $queryHorsPlage = "SELECT DISTINCT(operations.operation_id) AS op_id,
                       DATE_FORMAT(operations.date, '%Y - %m') AS mois,
                       operations.date AS orderitem
                     FROM operations
                     LEFT JOIN sallesbloc      ON sallesbloc.salle_id = operations.salle_id
                     LEFT JOIN bloc_operatoire ON sallesbloc.bloc_id = bloc_operatoire.bloc_operatoire_id
                     LEFT JOIN user_log        ON user_log.object_id = operations.operation_id
                       AND user_log.object_class = 'COperation'
                     WHERE operations.date BETWEEN '$date_min' AND '$date_max'
                       AND user_log.type = 'store'
                       AND DATE(user_log.date) = operations.date
                       AND user_log.fields LIKE '%annulee%'
                       AND operations.annulee = '1'";
}
else {
  $queryInPlage   = "SELECT DISTINCT(operations.operation_id) AS op_id,
                       DATE_FORMAT(plagesop.date, '%Y - %m') AS mois,
                       plagesop.date AS orderitem
                     FROM operations
                     LEFT JOIN plagesop ON plagesop.plageop_id = operations.plageop_id
                     LEFT JOIN sallesbloc ON sallesbloc.salle_id = operations.salle_id
                     LEFT JOIN bloc_operatoire ON sallesbloc.bloc_id = bloc_operatoire.bloc_operatoire_id
                     LEFT JOIN user_log ON user_log.object_id = operations.operation_id
                       AND user_log.object_class = 'COperation'
                     WHERE plagesop.date BETWEEN '$date_min' AND '$date_max'
                       AND user_log.type = 'create'
                       AND DATE(user_log.date) = plagesop.date
                       AND operations.annulee = '0'";
  $queryHorsPlage = "SELECT DISTINCT(operations.operation_id) AS op_id,
                       DATE_FORMAT(operations.date, '%Y - %m') AS mois,
                       operations.date AS orderitem
                     FROM operations
                     LEFT JOIN sallesbloc ON sallesbloc.salle_id = operations.salle_id
                     LEFT JOIN bloc_operatoire ON sallesbloc.bloc_id = bloc_operatoire.bloc_operatoire_id
                     LEFT JOIN user_log ON user_log.object_id = operations.operation_id
                       AND user_log.object_class = 'COperation'
                     WHERE operations.date BETWEEN '$date_min' AND '$date_max'
                       AND user_log.type = 'create'
                       AND DATE(user_log.date) = operations.date
                       AND operations.annulee = '0'";
}

if($prat_id) {
  $queryInPlage   .= "\nAND operations.chir_id = '$prat_id'";
  $queryHorsPlage .= "\nAND operations.chir_id = '$prat_id'";
}
if($codeCCAM) {
  $queryInPlage   .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  $queryHorsPlage .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
}
$queryInPlage   .= "\nAND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
$queryHorsPlage .= "\nAND operations.salle_id ".CSQLDataSource::prepareIn(array_keys($salles));
$queryInPlage   .= "\nORDER BY orderitem, bloc_operatoire.nom, sallesbloc.nom";
$queryHorsPlage .= "\nORDER BY orderitem, bloc_operatoire.nom, sallesbloc.nom";

$resultInPlage   = $prat->_spec->ds->loadlist($queryInPlage);
$resultHorsPlage = $prat->_spec->ds->loadlist($queryHorsPlage);

for($rangeDate = $date_min; $rangeDate <= $date_max; $rangeDate = mbDate("+1 month", $rangeDate)) {
  $month = mbTransformTime(null, $rangeDate, "%Y - %m");
  $list[$month]['total'] = 0;
  $list[$month]['inPlage'] = array();
  $list[$month]['horsPlage'] = array();
}

foreach($resultInPlage as $res) {
  $operation = new COperation();
  $operation->load($res['op_id']);
  $operation->loadRefsFwd();
  
  $list[$res['mois']]['total']++;
  $list[$res['mois']]['inPlage'][$operation->_id] = $operation;
}

foreach($resultHorsPlage as $res) {
  $operation = new COperation();
  $operation->load($res['op_id']);
  $operation->loadRefsFwd();
  
  $list[$res['mois']]['total']++;
  $list[$res['mois']]['horsPlage'][$operation->_id] = $operation;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list"      , $list);
$smarty->assign("date_max"  , $date_max);
$smarty->assign("type_modif", $type_modif);

$smarty->display("vw_cancelled_operations.tpl");
