<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type d'affichage
$selAffichage = mbGetValueFromPostOrSession("selAffichage","tous");

// Parametre de tri
$order_way = mbGetValueFromGetOrSession("order_way", "ASC");
$order_col = mbGetValueFromGetOrSession("order_col", "_prise_en_charge");

// Selection de la date
$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

// Chargement des urgences prises en charge
$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
  
$where["entree_reelle"] = "LIKE '$date%'";
$where["type"] = "= 'urg'";
$where["consultation.consultation_id"] = "IS NOT NULL";

if($selAffichage == "sortie"){
  $where["rpu.sortie"] = "IS NULL";
}

if($order_col != "_prise_en_charge"){
  $order_col = "_prise_en_charge";
}

if($order_col == "_prise_en_charge"){
  $order = "consultation.heure $order_way";
} else {
  $order = null;
}

$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);

foreach($listSejours as &$curr_sejour) {
  $curr_sejour->loadRefsFwd();
  $curr_sejour->loadRefRPU();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("order_col" , $order_col);
$smarty->assign("order_way" , $order_way);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->display("vw_sortie_rpu.tpl");
?>