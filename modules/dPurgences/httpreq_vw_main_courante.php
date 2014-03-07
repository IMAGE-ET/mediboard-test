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
$service_id = CValue::postOrSession("service_id");

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

$sejour = new CSejour();
$where = array();
$ljoin["rpu"]      = "sejour.sejour_id = rpu.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after' AND sejour.annule = '0')";
$where[] = CAppUI::pref("showMissingRPU") ?
  "sejour.type = 'urg' OR rpu.rpu_id IS NOT NULL" :
  "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '$group->_id'";

switch ($selAffichage) {
  case "prendre_en_charge":
    $ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";
    $where["consultation.consultation_id"] = "IS NULL";
    break;
  case "presents":
    $where["sejour.sortie_reelle"] = "IS NULL";
    $where["sejour.annule"] = " = '0'";
    if (CAppUI::conf("dPurgences create_sejour_hospit")) {
      $where["rpu.mutation_sejour_id"] = "IS NULL";
    }
    break;
  case "annule_hospitalise":
    $where["sejour.sortie_reelle"] = "IS NOT NULL";
    $where["sejour.mode_sortie"] = " = 'mutation'";
}

if (!in_array($order_col, array("_entree", "ccmu", "_patient_id"))) {
  $order_col = "ccmu";
}

switch ($order_col) {
  case "_entree":
    $order = "entree $order_way, rpu.ccmu $order_way";
    break;
  case "ccmu":
    $order = "rpu.ccmu $order_way, entree $order_way";
    break;
  case "_patient_id":
    $order = "patients.nom $order_way, ccmu $order_way";
}

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order, null, "`sejour`.sejour_id", $ljoin);

if ($service_id) {
  foreach ($listSejours as $key => $sejour) {
    $curr_aff = $sejour->getCurrAffectation();
    if ((!$curr_aff->_id && (!$sejour->service_id || $sejour->service_id != $service_id)) || $curr_aff->service_id != $service_id) {
      unset($listSejours[$key]);
    }
  }
}

CMbObject::massLoadFwdRef($listSejours, "patient_id");
CMbObject::massLoadFwdRef($listSejours, "group_id");
$prats = CMbObject::massLoadFwdRef($listSejours, "praticien_id");
CMbObject::massLoadFwdRef($prats, "function_id");
CMbObject::massCountBackRefs($listSejours, "notes");

CMbObject::massCountBackRefs($listSejours, "rpu");
CMbObject::massCountBackRefs($listSejours, "consultations");
CMbObject::massCountBackRefs($listSejours, "prescriptions");
CMbObject::massCountBackRefs($listSejours, "documents");
CMbObject::massCountBackRefs($listSejours, "files");

foreach ($listSejours as $sejour) {
  // Chargement du numero de dossier
  $sejour->loadNDA();
  $sejour->loadRefsFwd();
  $sejour->loadRefRPU()->loadRefSejourMutation();
  $sejour->loadRefsConsultations();
  $sejour->loadRefsNotes();
  $sejour->countDocItems();
  $sejour->loadRefCurrAffectation()->loadRefService();
  $sejour->_ref_curr_affectation->loadRefLit()->loadRefChambre();

  $prescription = $sejour->loadRefPrescriptionSejour();

  if ($prescription->_id) {
    if (@CAppUI::conf("object_handlers CPrescriptionAlerteHandler")) {
      $prescription->_count_fast_recent_modif = $prescription->countAlertsNotHandled("medium");
      $prescription->_count_urgence["all"] = $prescription->countAlertsNotHandled("high");
    }
    else {
      $prescription->countFastRecentModif();
      $prescription->loadRefsLinesMedByCat();
      $prescription->loadRefsLinesElementByCat();
      $prescription->countUrgence($date);
    }

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

    CPrescription::$_load_lite = true;
    $consult_atu->loadRefsPrescriptions();
    CPrescription::$_load_lite = false;

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
if (CAppUI::conf("dPurgences view_rpu_uhcd")) {
  foreach (CService::loadServicesUHCD() as $service) {
    foreach ($service->_ref_chambres as $chambre) {
      foreach ($chambre->_ref_lits as $lit) {
        $boxes[$lit->_id] = $lit;
      }
    }
  }
}
if (CAppUI::conf("dPurgences CRPU imagerie_etendue", $group)) {
  foreach (CService::loadServicesImagerie() as $_service) {
    foreach ($_service->_ref_chambres as $_chambre) {
      foreach ($_chambre->_ref_lits as $_lit) {
        $boxes[$_lit->_id] = $_lit;
      }
    }
  }
}

// Si admin sur le module urgences, alors modification autorisée du diagnostic
// infirmier depuis la main courante.
$module = new CModule();
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
