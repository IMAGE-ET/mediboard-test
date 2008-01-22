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
  $curr_sejour->loadNumDossier();
  // Chargement de l'IPP
  $curr_sejour->_ref_patient->loadIPP();
}

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Contraintes sur le mode de sortie / destination
$contrainteDestination[6] = array("", 1, 2, 3, 4);
$contrainteDestination[7] = array("", 1, 2, 3, 4);
$contrainteDestination[8] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation[6] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation[7] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation[8] = array("", "FUGUE", "SCAM", "PSA", "REO");


  
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("listEtab", $listEtab);
$smarty->assign("order_col" , $order_col);
$smarty->assign("order_way" , $order_way);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->display("vw_sortie_rpu.tpl");
?>