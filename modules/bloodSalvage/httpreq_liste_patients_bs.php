<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

global $can, $m, $g;

$ds                 = CSQLDataSource::get("std");
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
$where[] = "`plageop_id` ".$ds->prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$order = "entree_reveil";
$listReveil = $listReveil->loadList($where, $order);
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