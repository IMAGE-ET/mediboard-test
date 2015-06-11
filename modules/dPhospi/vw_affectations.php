<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$pathos = new CDiscipline();

$g = CGroups::loadCurrent()->_id;

// A passer en variable de configuration

$heureLimit = CAppUI::conf("dPhospi hour_limit");
$date            = CValue::getOrSession("date", CMbDT::date());
$mode            = CValue::getOrSession("mode", 0); 
$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filterFunction  = CValue::getOrSession("filterFunction");
$prestation_id   = CValue::getOrSession("prestation_id", CAppUI::pref("prestation_id_hospi"));

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

if (!$services_ids) {
  $smarty = new CSmartyDP;
  $smarty->display("inc_no_services.tpl");
  CApp::rip();
}

$emptySejour = new CSejour;
$emptySejour->_type_admission = $_type_admission;

// Récupération du service à ajouter/éditer
$totalLits = 0;

// Récupération des chambres/services
$where = array();
$where["group_id"]  = "= '$g'";
$where["cancelled"] = "= '0'";
$services = new CService();
$order = "externe, nom";
$services = $services->loadListWithPerms(PERM_READ, $where, $order);

// Chargement des services
foreach ($services as $service) {
  if (!in_array($service->_id, $services_ids)) {
    continue;
  }

  loadServiceComplet($service, $date, $mode, null, null, $prestation_id);
  CApp::$chrono->stop("Load Service Complet : '$service->_view'");
  CApp::$chrono->start();
  $totalLits += $service->_nb_lits_dispo;
}

foreach ($services as $key => $_service) {
  if (!count($_service->_ref_chambres)) {
    unset($services[$key]);
  }
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = CMbDT::date()." 01:00:00";
$endWeek = CMbDT::dateTime("+7 days", $today);

$where = array();
$where["annule"]          = "= '0'";
$where["sejour.entree"]   = "BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
if ($_type_admission == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp' OR `sejour`.`type` = 'ssr'";
}
elseif ($_type_admission) {
  $where["sejour.type"] = " = '$_type_admission'";
}
else {
  $where["sejour.type"] = "!= 'urg'";
}
if ($_type_admission != "seances") {
  $where[] = "affectation.affectation_id IS NULL";
}

$where[] = "sejour.service_id IN (".join($services_ids, ',').") OR sejour.service_id IS NULL";
$leftjoin["affectation"]  = "sejour.sejour_id = affectation.sejour_id";

// Filtre sur les fonctions
if ($filterFunction) {
  $leftjoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
  $where["users_mediboard.function_id"] = " = '$filterFunction'";
}

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, $leftjoin);
CApp::$chrono->stop("Patient à placer dans la semaine");
CApp::$chrono->start();

// Liste des patients à placer
$groupSejourNonAffectes = array();

if (CCanDo::edit()) {
  $where = array();
  $where["sejour.annule"] = "= '0'";
  $where[] = "sejour.service_id IN (".join($services_ids, ',').") OR sejour.service_id IS NULL";

  $order = null;
  switch ($triAdm) {
    case "date_entree":
      $order = "entree_prevue ASC";
      break;

    case "praticien":
      $order = "users_mediboard.function_id, users.user_last_name, users.user_first_name";
      break;

    case "patient":
      $order = "patients.nom, patients.prenom";
      break;

    default:
      $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }
  
  switch ($_type_admission) {
    case "ambucomp":
      $where[] = "sejour.type = 'ambu' OR sejour.type = 'comp' OR sejour.type = 'ssr'";
      break;

    case "0":
      break;

    default:
      $where["sejour.type"] = "= '$_type_admission'"; 
  }
  
  // Admissions de la veille
  $dayBefore = CMbDT::date("-1 days", $date);
  $where["sejour.entree"] = "BETWEEN '$dayBefore 00:00:00' AND '$date 01:59:59'";
  $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order, null, $prestation_id);
  CApp::$chrono->stop("Non affectés: veille");
  CApp::$chrono->start();
  
  // Admissions du matin
  $where["sejour.entree"] = "BETWEEN '$date 02:00:00' AND '$date ".CMbDT::time("-1 second", $heureLimit)."'";
  $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order, null, $prestation_id);
  CApp::$chrono->stop("Non affectés: matin");
  CApp::$chrono->start();
  
  // Admissions du soir
  $where["sejour.entree"] = "BETWEEN '$date $heureLimit' AND '$date 23:59:59'";
  $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order, null, $prestation_id);
  CApp::$chrono->stop("Non affectés: soir");
  CApp::$chrono->start();
  
  // Admissions antérieures
  $twoDaysBefore = CMbDT::date("-2 days", $date);
  $where["sejour.entree"] = "<= '$twoDaysBefore 23:59:59'";
  $where["sejour.sortie"] = ">= '$date 00:00:00'";

  $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order, null, $prestation_id);
  CApp::$chrono->stop("Non affectés: avant");
  CApp::$chrono->start();
  
  // Affectations dans les couloirs
  $where = array();
  $where[] = "affectation.service_id IN (".join($services_ids, ',').")";
  $where["sejour.annule"] = " = '0'";
  $where[] = "(affectation.entree BETWEEN '$date 00:00:00' AND '$date 23:59:59')
            OR (affectation.sortie BETWEEN '$date 00:00:00' AND '$date 23:59:59')";
  $groupSejourNonAffectes["couloir"] = loadAffectationsCouloirs($where, $order, null, $prestation_id);
}

$imeds_active = CModule::getActive("dPImeds");

$functions_filter = array();
foreach ($groupSejourNonAffectes as $_keyGroup => $_group) {
  if ($_keyGroup == "couloir") {
    continue;
  }
  if ($imeds_active) {
    CSejour::massLoadNDA($_group);
  }
  /** @var CSejour[] $_group */
  foreach ($_group as $_key => $_sejour) {
    $_sejour->loadRefChargePriceIndicator();
    $functions_filter[$_sejour->_ref_praticien->function_id] = $_sejour->_ref_praticien->_ref_function;
    if ($filterFunction && $filterFunction != $_sejour->_ref_praticien->function_id) {
      unset($groupSejourNonAffectes[$_keyGroup][$_key]);
    }
  }
}

$affectation = new CAffectation();
$affectation->entree = CMbDT::addDateTime("08:00:00", $date);
$affectation->sortie = CMbDT::addDateTime("23:00:00", $date);

// Chargement conf prestation
$systeme_presta = CAppUI::conf("dPhospi prestations systeme_prestations", "CGroups-$g");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services_ids"          , $services_ids);
$smarty->assign("services"              , $services);
$smarty->assign("affectation"           , $affectation);
$smarty->assign("pathos"                , $pathos);
$smarty->assign("date"                  , $date);
$smarty->assign("demain"                , CMbDT::date("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("emptySejour"           , $emptySejour);
$smarty->assign("filterFunction"        , $filterFunction);
$smarty->assign("triAdm"                , $triAdm);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("groupSejourNonAffectes", $groupSejourNonAffectes);
$smarty->assign("functions_filter"      , $functions_filter);
$smarty->assign("prestation_id"         , $prestation_id);
$smarty->assign("systeme_presta"        , $systeme_presta);

//chargement des prestations
if ($systeme_presta == "standard") {
  $prestations = CPrestation::loadCurrentList();
  $smarty->assign("prestations"           , $prestations);
}
else {
  $prestations_journalieres = CPrestationJournaliere::loadCurrentList();
  $smarty->assign("prestations_journalieres", $prestations_journalieres);
}

$smarty->display("vw_affectations.tpl");

if (CAppUI::pref("INFOSYSTEM")) {
  mbTrace(CMbArray::pluck(CApp::$chrono->report, "total"), "Rapport uniquement visible avec les informations système");
}
