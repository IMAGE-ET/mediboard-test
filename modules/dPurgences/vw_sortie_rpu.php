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
$view_sortie = CValue::postOrSession("view_sortie", "tous");
$service_id  = CValue::postOrSession("service_id");

// Chargement des urgences prises en charge
$ljoin = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$ljoin["consultation"] = "consultation.sejour_id = sejour.sejour_id";

// Selection de la date
$date = CValue::getOrSession("date", CMbDT::date());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $date);
$date_after  = CMbDT::date("+1 DAY", $date);
$where = array();
$group = CGroups::loadCurrent();
$where["group_id"] = " = '$group->_id'";
$where["sejour.annule"] = " = '0'";
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after')";

// RPU Existants
$where["rpu.rpu_id"] = "IS NOT NULL";

switch ($view_sortie) {
  case "tous":
    break;
  case "sortie":
    $where["sortie_reelle"] = "IS NULL";
    $where["rpu.mutation_sejour_id"] = "IS NULL";
    break;
  case "normal":
  case "mutation":
  case "transfert":
  case "deces":
    $where["sortie_reelle"] = "IS NOT NULL";
    $where["mode_sortie"] = "= '$view_sortie'";
}

$sejour = new CSejour();

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, "consultation.heure", null, "sejour.sejour_id", $ljoin);
CMbObject::massLoadFwdRef($listSejours, "patient_id");
$prats = CMbObject::massLoadFwdRef($listSejours, "praticien_id");
CMbObject::massLoadFwdRef($prats, "function_id");

foreach ($listSejours as $key=> $_sejour) {
  if ($service_id) {
    $curr_aff = $_sejour->getCurrAffectation();
    if ((!$curr_aff->_id && (!$_sejour->service_id || $_sejour->service_id != $service_id)) || $curr_aff->service_id != $service_id) {
      unset($listSejours[$key]);
      continue;
    }
  }
  $_sejour->loadRefsFwd();
  $_sejour->loadRefRPU();
  $_sejour->loadNDA();
  $_sejour->loadRefsConsultations();
  $_sejour->_veille = CMbDT::date($_sejour->entree) != $date;

  // Détail du RPU
  $rpu = $_sejour->_ref_rpu;
  $rpu->loadRefSejourMutation();
  $sejour_mutation = $rpu->_ref_sejour_mutation;
  $sejour_mutation->loadRefsAffectations();
  $sejour_mutation->loadRefsConsultations();
  $_nb_acte_sejour_rpu = 0;
  $valide = true;
  foreach ($sejour_mutation->_ref_consultations as $consult) {
    $consult->countActes();
    $_nb_acte_sejour_rpu += $consult->_count_actes;
    if (!$consult->valide) {
      $valide = false;
    }
  }
  $rpu->_ref_consult->valide = $valide;
  $sejour_mutation->_count_actes = $_nb_acte_sejour_rpu;

  foreach ($sejour_mutation->_ref_affectations as $_affectation) {
    if ($_affectation->loadRefService()->urgence) {
      unset($sejour_mutation->_ref_affectations[$_affectation->_id]);
      continue;
    }

    $_affectation->loadView();
  }
  $rpu->_ref_consult->loadRefsActes();

  // Détail du patient
  $patient = $_sejour->_ref_patient;
  $patient->loadIPP();
}

// Chargement des services
$where = array();
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation"]  = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

// Praticiens urgentistes
$group = CGroups::loadCurrent();

$listPrats = CAppUI::$user->loadPraticiens(PERM_READ, $group->service_urgences_id);

// Si accès au module PMSI : peut modifier le diagnostic principal
$access_pmsi = 0;
if (CModule::exists("dPpmsi")) {
  $module = new CModule();
  $module->mod_name = "dPpmsi";
  $module->loadMatchingObject();
  $access_pmsi = $module->getPerm(PERM_EDIT);
}

// Si praticien : peut modifier le CCMU, GEMSA et diagnostic principal
$is_praticien = CMediusers::get()->isPraticien();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("services_urg", CService::loadServicesUrgence());
$smarty->assign("services"    , $services);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("listSejours" , $listSejours);
$smarty->assign("view_sortie" , $view_sortie);
$smarty->assign("listPrats"   , $listPrats);
$smarty->assign("date"        , $date);
$smarty->assign("access_pmsi" , $access_pmsi);
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("today"       , CMbDT::date());

$smarty->display("vw_sortie_rpu.tpl");