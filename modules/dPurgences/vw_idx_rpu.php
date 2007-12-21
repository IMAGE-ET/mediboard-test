<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Type d'affichage
$selAffichage = mbGetValueFromPostOrSession("selAffichage","tous");

// Parametre de tri
$order_way = mbGetValueFromGetOrSession("order_way", "DESC");
$order_col = mbGetValueFromGetOrSession("order_col", "ccmu");

// Selection de la date
$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

$group = new CGroups();
$group->load($g);
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where["entree_reelle"] = "LIKE '$date%'";
$where["type"] = "= 'urg'";

if($selAffichage == "prendre_en_charge"){
  $ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
  $where["consultation.consultation_id"] = "IS NULL";
}


if($order_col != "_entree" && $order_col != "ccmu" && $order_col != "_patient_id"){
  $order_col = "ccmu";  
}

if($order_col == "_entree"){
  $order = "entree_reelle $order_way, rpu.ccmu $order_way";
}

if($order_col == "ccmu"){
  $order = "rpu.ccmu $order_way, entree_reelle $order_way";
}

if($order_col == "_patient_id"){
  $order = "patients.nom $order_way, ccmu $order_way";
}


$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);

foreach($listSejours as &$curr_sejour) {
  // Chargement du numero de dossier
  $curr_sejour->loadNumDossier();
  $curr_sejour->loadRefsFwd();
  $curr_sejour->loadRefRPU();
  
  // Chargement de l'IPP
  $curr_sejour->_ref_patient->loadIPP();
}


// Calcul du temps d'attente pour les patients deja pris en charge
$tps_attente = array();
foreach($listSejours as &$curr_sejour) {
  if($curr_sejour->_ref_rpu->_count_consultations){
  // Calcul du temps d'attente
  $entree = mbTime($curr_sejour->_entree);
 
  $consult = mbTime($curr_sejour->_ref_rpu->_ref_consult->heure);
  $tps_attente[$curr_sejour->_id] = mbSubTime($entree,$consult);
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tps_attente", $tps_attente);
$smarty->assign("userCourant", $AppUI->user_id);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->assign("listPrats"  , $listPrats);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->display("vw_idx_rpu.tpl");
?>