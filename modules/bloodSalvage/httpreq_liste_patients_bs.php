<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m, $g;

$blood_salvage      = new CBloodSalvage();

$operation_id       = mbGetValueFromGetOrSession("operation_id");
$date               = mbGetValueFromGetOrSession("date", mbDate());
$hour               = mbTime();
$totaltime          = "00:00:00";


// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

// Récupération des détails des RSPO.
$listReveil = new COperation;
$where = array();
$where[] = "`plageop_id` ".CSQLDataSource::prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$leftjoin["blood_salvage"] = "operations.operation_id = blood_salvage.operation_id";
$where["blood_salvage.operation_id"] = "IS NOT NULL";
$order = "entree_reveil";
$listReveil = $listReveil->loadList($where,$order, null, null, $leftjoin);
foreach($listReveil as $key => $value) {
  $listReveil[$key]->loadRefs();
}

$smarty = new CSmartyDP();

$smarty->assign("listReveil", $listReveil);
$smarty->assign("date", $date);
$smarty->assign("hour", $hour);
$smarty->assign("operation_id", $operation_id);

$smarty->display("inc_liste_patients_bs.tpl");


?>