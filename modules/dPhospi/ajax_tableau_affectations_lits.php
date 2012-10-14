<?php /* $Id: $ */

/**
 * Echange XML EAI
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

global $can;

CCanDo::checkRead();

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$g = CGroups::loadCurrent()->_id;

// A passer en variable de configuration
$heureLimit = "16:00:00";

$date            = CValue::getOrSession("date", mbDate()); 
$mode            = CValue::getOrSession("mode", 0); 
$services_ids    = CValue::getOrSession("services_ids");
$triAdm          = CValue::getOrSession("triAdm", "praticien");
$_type_admission = CValue::getOrSession("_type_admission", "ambucomp");
$filterFunction  = CValue::getOrSession("filterFunction");

$emptySejour = new CSejour;
$emptySejour->_type_admission = $_type_admission;

$totalLits = 0;

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "externe, nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

$where_service = "";
if (reset($services_ids)) {
  $where_service = "sejour.service_id IN (".join($services_ids, ',').") OR sejour.service_id IS NULL"; 
}

// Chargement des services
foreach ($services as &$service) {
  if (!in_array($service->_id, $services_ids)) {
    continue;
  }
  
  loadServiceComplet($service, $date, $mode);
  CApp::$chrono->stop("Load Service Complet : '$service->_view'");
  CApp::$chrono->start();
  $totalLits += $service->_nb_lits_dispo;
}

// Nombre de patients à placer pour la semaine qui vient (alerte)
$today   = mbDate()." 01:00:00";
$endWeek = mbDateTime("+7 days", $today);

$where["annule"]          = "= '0'";
$where["sejour.entree"]   = "BETWEEN '$today' AND '$endWeek'";
$where["sejour.group_id"] = "= '$g'";
if($_type_admission == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($_type_admission) {
  $where["sejour.type"] = " = '$_type_admission'";
} else {
  $where["sejour.type"] = "!= 'urg'";
}
if($_type_admission != "seances") {
  $where[] = "affectation.affectation_id IS NULL";
}
if ($where_service) {
  $where[]                  = $where_service;
}
$leftjoin["affectation"]  = "sejour.sejour_id = affectation.sejour_id";

// Filtre sur les fonctions
if($filterFunction){
  $leftjoin["users_mediboard"] = "sejour.praticien_id = users_mediboard.user_id";
  $where["users_mediboard.function_id"] = " = '$filterFunction'";
}

$sejour = new CSejour();
$alerte = $sejour->countList($where, null, $leftjoin);
CApp::$chrono->stop("Patient à placer dans la semaine");
CApp::$chrono->start();

$affectation = new CAffectation();
$affectation->entree = mbAddDateTime("08:00:00",$date);
$affectation->sortie = mbAddDateTime("23:00:00",$date);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("services_ids"          , $services_ids);
$smarty->assign("affectation"           , $affectation);
$smarty->assign("date"                  , $date);
$smarty->assign("demain"                , mbDate("+ 1 day", $date));
$smarty->assign("heureLimit"            , $heureLimit);
$smarty->assign("mode"                  , $mode);
$smarty->assign("emptySejour"           , $emptySejour);
$smarty->assign("filterFunction"        , $filterFunction);
$smarty->assign("totalLits"             , $totalLits);
$smarty->assign("services"              , $services);
$smarty->assign("alerte"                , $alerte);
$smarty->assign("prestations"           , CPrestation::loadCurrentList());

$smarty->display("inc_tableau_affectations_lits.tpl");

if (CAppUI::pref("INFOSYSTEM")) {
  mbTrace(CMbArray::pluck(CApp::$chrono->report, "total"), "Rapport uniquement visible avec les informations système");
}

?>