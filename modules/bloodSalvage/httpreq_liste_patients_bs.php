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

$date               = mbGetValueFromGetOrSession("date", mbDate());
$hour               = mbTime();
$totaltime          = "00:00:00";


// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

// Récupération des détails des RSPO.
$listRSPO = new CBloodSalvage;
$where = array();
$where[] = "`plageop_id` ".$ds->prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_reveil"] = "IS NOT NULL";
$where["sortie_reveil"] = "IS NULL";
$leftjoin["operations"] = "blood_salvage.operation_id = operations.operation_id";
$order = "entree_reveil";
$listRSPO = $listRSPO->loadList($where, $order,null, null, $leftjoin);
foreach($listRSPO as $key => $value) {
  $listRSPO[$key]->loadRefs();
  $listRSPO[$key]->_ref_operation->loadRefs();
}

$smarty = new CSmartyDP();

$smarty->assign("listRSPO", $listRSPO);
$smarty->assign("date", $date);
$smarty->assign("hour", $hour);

$smarty->display("inc_liste_patients_bs.tpl");


?>