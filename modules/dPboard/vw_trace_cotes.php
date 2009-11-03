<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $prat;

$date_interv = CValue::getOrSession("date_interv", mbDate());

// Chargement des plages du mois
$plage = new CPlageOp();
$where = array(
  "date"    => "= '". $date_interv . "'",
  "chir_id" => "= '" . $prat->_id . "'");
$order = "date, debut";

$listPlages = $plage->loadList($where, $order);

$interv = new COperation();
$where = array();
$where[] = "(plageop_id " . CSQLDataSource::prepareIn(array_keys($listPlages)) . " OR (operations.date = '$date_interv' AND operations.chir_id = '" . $prat->_id . "'))";

$listIntervs = $interv->loadList($where);

foreach($listIntervs as &$_interv) {
  $_interv->loadRefsFwd();
}

// Variables de templates
$smarty = new CSmartyDP();

$smarty->assign("date_interv", $date_interv);
$smarty->assign("listIntervs", $listIntervs);
$smarty->assign("prec", mbDate("-1 DAYS", $date_interv));
$smarty->assign("suiv", mbDate("+1 DAYS", $date_interv));

$smarty->display("vw_trace_cotes.tpl");

?>