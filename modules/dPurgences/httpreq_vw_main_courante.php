<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Type d'affichage
$selAffichage = CValue::postOrSession("selAffichage", CAppUI::conf("dPurgences default_view"));

// Parametre de tri
$order_way = CValue::getOrSession("order_way", "DESC");
$order_col = CValue::getOrSession("order_col", CAppUI::pref("defaultRPUSort"));

// Selection de la date
$date = CValue::getOrSession("date", CMbDT::date());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $date);
$date_after  = CMbDT::date("+1 DAY", $date);

// L'utilisateur doit-il voir les informations médicales
$user = CMediusers::get();
$medicalView = $user->isMedical();

$group = CGroups::loadCurrent();
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$sejour = new CSejour;
$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after' AND sejour.annule = '0')";
$where[] = CAppUI::pref("showMissingRPU") ? 
  "sejour.type = 'urg' OR rpu.rpu_id IS NOT NULL" :
  "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '$group->_id'";

if ($selAffichage == "prendre_en_charge") {
  $ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
  $where["consultation.consultation_id"] = "IS NULL";
} 

if ($selAffichage == "presents") {
  $where["sejour.sortie_reelle"] = "IS NULL";
  $where["sejour.annule"] = " = '0'";

  if (CAppUI::conf("dPurgences create_sejour_hospit")) {
    $where["rpu.mutation_sejour_id"] = "IS NULL";
  }
} 

if ($selAffichage == "annule_hospitalise") {
  $where["sejour.sortie_reelle"] = "IS NOT NULL";
  $where["sejour.mode_sortie"] = " = 'mutation'";
}

if ($order_col != "_entree" && $order_col != "ccmu" && $order_col != "_patient_id") {
  $order_col = "ccmu";  
}

if ($order_col == "_entree") {
  $order = "entree $order_way, rpu.ccmu $order_way";
}

if ($order_col == "ccmu") {
  $order = "rpu.ccmu $order_way, entree $order_way";
}

if ($order_col == "_patient_id") {
  $order = "patients.nom $order_way, ccmu $order_way";
}

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);
foreach ($listSejours as &$sejour) {
  // Chargement du numero de dossier
  $sejour->loadNDA();
  $sejour->loadRefsFwd();
  $sejour->loadRefRPU();
  $sejour->_ref_rpu->loadRefSejourMutation();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsNotes();
  $sejour->countDocItems();
  $sejour->loadRefPrescriptionSejour();

  $prescription = $sejour->_ref_prescription_sejour;
  if ($prescription) {
    $prescription->loadRefsPrescriptionLineMixes();
    $prescription->loadRefsLinesMedByCat();
    $prescription->loadRefsLinesElementByCat();

    $sejour->_ref_prescription_sejour->countRecentModif();
  }

  // Chargement de l'IPP
  $sejour->_ref_patient->loadIPP();

  // Séjours antérieurs  
  $sejour->_veille = CMbDT::date($sejour->entree) != $date;

  // Ajout des documents de la consultation dans le compteur
  $consult_atu = $sejour->_ref_consult_atu;

  if ($consult_atu->_id) {
    $sejour->_nb_files += $consult_atu->_nb_files;
    $sejour->_nb_docs += $consult_atu->_nb_docs;
    $sejour->_nb_files_docs += $consult_atu->_nb_files + $consult_atu->_nb_docs;

    $consult_atu->loadRefsPrescriptions();

    if (isset($consult_atu->_ref_prescriptions["externe"])) {
      $sejour->_nb_docs++;
      $sejour->_nb_files_docs++;
    }
  }
}

// Tri pour afficher les sans CCMU en premier
if ($order_col == "ccmu") {
  function ccmu_cmp($sejour1, $sejour2) {
    $ccmu1 = CValue::first($sejour1->_ref_rpu->ccmu, "9");
    $ccmu2 = CValue::first($sejour2->_ref_rpu->ccmu, "9");
    if ($ccmu1 == "P") {
      $ccmu1 = "1";
    }
    if ($ccmu2 == "P") {
      $ccmu2 = "1";
    }
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

// Si admin sur le module urgences, alors modification autorisée du diagnostic
// infirmier depuis la main courante.
$module = new CModule;
$module->mod_name = "dPurgences";
$module->loadMatchingObject();
$admin_urgences = $module->canAdmin();

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
$smarty->assign("today"       , CMbDT::date());
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("admin_urgences", $admin_urgences);
$smarty->assign("type"        , "MainCourante");
$smarty->display("inc_main_courante.tpl");
