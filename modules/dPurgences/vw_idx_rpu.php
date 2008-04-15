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

$tps_attente = array();
foreach ($listSejours as &$sejour) {
  // Chargement du numero de dossier
  $sejour->loadNumDossier();
  $sejour->loadRefsFwd();
  $sejour->loadRefRPU();
  
  // Chargement de l'IPP
  $sejour->_ref_patient->loadIPP();

  // Calcul du temps d'attente pour les patients deja pris en charge
  if ($sejour->_ref_rpu->_count_consultations) {
	  $entree = mbTime($sejour->_entree);
	  $consult = mbTime($sejour->_ref_rpu->_ref_consult->heure);
	  $tps_attente[$sejour->_id] = mbSubTime($entree,$consult);
  }
}

// Chargement des boxes d'urgences
$boxes = array();
foreach (CService::loadServicesUrgence() as $service) {
  foreach ($service->_ref_chambres as $chambre) {
    foreach ($chambre->_ref_lits as $lit) {
      $boxes[$lit->_id] = $lit;
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("boxes", $boxes);
$smarty->assign("tps_attente", $tps_attente);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->assign("listPrats"  , $listPrats);
$smarty->assign("listSejours", $listSejours);
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->display("vw_idx_rpu.tpl");
?>