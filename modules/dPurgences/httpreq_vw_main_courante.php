<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Type d'affichage
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", CAppUI::pref("defaultRPUSort"));

// Selection de la date
$date = CValue::getOrSession("date", mbDate());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = mbDate("-$date_tolerance DAY", $date);
$date_after  = mbDate("+1 DAY", $date);

// L'utilisateur doit-il voir les informations médicales
global $AppUI;
$user = new CMediusers();
$user->load($AppUI->user_id);
$medicalView = $user->isMedical();

$group = CGroups::loadCurrent();
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where[] = "sejour.entree_reelle BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree_reelle BETWEEN '$date_before' AND '$date_after')";
$where[] = CAppUI::pref("showMissingRPU") ? 
  "sejour.type = 'urg' OR rpu.rpu_id IS NOT NULL" :
  "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '$group->_id'";

if ($selAffichage == "prendre_en_charge"){
  $ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
  $where["consultation.consultation_id"] = "IS NULL";
} 
else if($selAffichage == "presents"){
  $where["sejour.sortie_reelle"] = "IS NULL";
  $where["sejour.annule"] = " = '0'";
} 
else if ($selAffichage == "annule_hospitalise") {
	$where["sejour.annule"] = " = '1'";
}

if ($order_col != "_entree" && $order_col != "ccmu" && $order_col != "_patient_id") {
  $order_col = "ccmu";  
}

if ($order_col == "_entree") {
  $order = "entree_reelle $order_way, rpu.ccmu $order_way";
}

if ($order_col == "ccmu") {
  $order = "rpu.ccmu $order_way, entree_reelle $order_way";
}

if ($order_col == "_patient_id") {
  $order = "patients.nom $order_way, ccmu $order_way";
}

$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$sejour) {
  // Chargement du numero de dossier
  $sejour->loadNumDossier();
  $sejour->loadRefsFwd();
  $sejour->loadRefRPU();
  $sejour->_ref_rpu->loadRefSejourMutation();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsNotes();
    
  // Chargement de l'IPP
  $sejour->_ref_patient->loadIPP();

  // Séjours antérieurs  
	$sejour->_veille = mbDate($sejour->entree_reelle) != $date;
}

// Tri pour afficher les sans CCMU en premier
if ($order_col == "ccmu") {
	function ccmu_cmp($sejour1, $sejour2) {
    $ccmu1 = CValue::first($sejour1->_ref_rpu->ccmu, "9");
    $ccmu2 = CValue::first($sejour2->_ref_rpu->ccmu, "9");
    if ($ccmu1 == "P") $ccmu1 = "1";
    if ($ccmu2 == "P") $ccmu2 = "1";
		return $ccmu2 - $ccmu1;
	}

  uasort($listSejours, "ccmu_cmp");
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

$smarty->assign("boxes"       , $boxes);
$smarty->assign("order_col"   , $order_col);
$smarty->assign("order_way"   , $order_way);
$smarty->assign("listPrats"   , $listPrats);
$smarty->assign("listSejours" , $listSejours);
$smarty->assign("selAffichage", $selAffichage);
$smarty->assign("medicalView" , $medicalView);
$smarty->assign("date"        , $date);
$smarty->assign("date_before" , $date_before);
$smarty->assign("today"       , mbDate());
$smarty->assign("isImedsInstalled"  , CModule::getActive("dPImeds"));

$smarty->display("inc_main_courante.tpl");
?>