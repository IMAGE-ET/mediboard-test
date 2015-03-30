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

$date       = CValue::getOrSession("date", CMbDT::date());
$type_hospi = CValue::getOrSession("type_hospi", null);
$vue        = CValue::getOrSession("vue", 0);
$group_id   = CValue::get("g");
$mode       = CValue::getOrSession("mode", 0);
$hour_instantane = CValue::getOrSession("hour_instantane", CMbDT::format(CMbDT::time(), "%H"));
$prestation_id = CValue::getOrSession("prestation_id", CAppUI::pref("prestation_id_hospi"));

// Si c'est la préférence utilisateur, il faut la mettre en session
CValue::setSession("prestation_id", $prestation_id);

$mouvements = array("comp" => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "ambu" => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "urg"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "ssr"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)),
                    "psy"  => array("entrees" => array("place" => 0, "non_place" => 0),
                                    "sorties" => array("place" => 0, "non_place" => 0)));
$group = CGroups::loadCurrent();

// Récupération de la liste des services et du service selectionné
$where = array(
           "externe"  => "= '0'",
           "group_id" => "= '$group->_id'"
         );
$order      = "nom";
$service    = new CService();
$services_ids = CValue::getOrSession("services_ids", null);

// Détection du changement d'établissement
if (!isset($_SESSION["dPhospi"]["services_ids"]) || $group_id) {
  $group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
  
  $pref_services_ids = json_decode(CAppUI::pref("services_ids_hospi"));
  
  // Si la préférence existe, alors on la charge
  if (isset($pref_services_ids->{"g$group_id"})) {
    $services_ids = $pref_services_ids->{"g$group_id"};
    if ($services_ids) {
      $services_ids = explode("|", $services_ids); 
    }
    CValue::setSession("services_ids", $services_ids);
  }
  // Sinon, chargement de la liste des services en accord avec le droit de lecture
  else {
    $service = new CService();
    $where = array();
    $where["group_id"]  = "= '".CGroups::loadCurrent()->_id."'";
    $where["cancelled"] = "= '0'";
    $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
    CValue::setSession("services_ids", $services_ids);
  }
}

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

// Récupération de la liste des praticiens et du praticien selectionné
$praticien    = new CMediusers();
$praticiens   = $praticien->loadPraticiens(PERM_READ);
$praticien_id = CValue::getOrSession("praticien_id", null);

$date  = CValue::getOrSession("date" , CMbDT::date());

$limit1  = $date." 00:00:00";
$limit2  = $date." 23:59:59";

// Patients placés
$affectation                 = new CAffectation();
$ljoin                       = array();
$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["service"]            = "service.service_id = affectation.service_id";
$where                       = array();
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["sejour.type"]        = CSQLDataSource::prepareIn(array_keys($mouvements) , $type_hospi);
$where["sejour.annule"]      = "= '0'";

if ($vue) {
  $where["sejour.confirme"] = " IS NULL";
}
if ($praticien_id) {
  $where["sejour.praticien_id"] = "= '$praticien_id'";
}

// Patients non placés
$sejour                     = new CSejour();
$ljoinNP                    = array();
$ljoinNP["affectation"]     = "sejour.sejour_id = affectation.sejour_id";
$whereNP                    = array();
$whereNP["sejour.group_id"] = "= '$group->_id'";
$whereNP["sejour.annule"]   = "= '0'";
$whereNP["sejour.type"]     = CSQLDataSource::prepareIn(array_keys($mouvements), $type_hospi);
if (count($services_ids)) {
  $whereNP[] = "((sejour.service_id " . CSQLDataSource::prepareIn($services_ids) . " OR sejour.service_id IS NULL) AND affectation.affectation_id IS NULL) OR "
    ."(affectation.lit_id IS NULL AND affectation.service_id " . CSQLDataSource::prepareIn($services_ids) . ")";
}

if ($praticien_id) {
  $whereNP["sejour.praticien_id"] = "= '$praticien_id'";
}

$datetime_check = "$date $hour_instantane:00:00";

// Comptage des patients présents
$wherePresents     = $where;
if ($mode) {
  $wherePresents[]   = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
}
else {
  $wherePresents[] = "('$datetime_check' BETWEEN affectation.entree AND affectation.sortie) AND affectation.effectue = '0'";
}
$presents          = $affectation->countList($wherePresents, null, $ljoin);

$wherePresentsNP   = $whereNP;
if ($mode) {
  $wherePresentsNP[] = "'$date' BETWEEN DATE(sejour.entree) AND DATE(sejour.sortie)";
}
else {
  $wherePresentsNP[] = "'$datetime_check' BETWEEN sejour.entree AND sejour.sortie";
}

$presentsNP        = $sejour->countList($wherePresentsNP, null, $ljoinNP);

// Comptage des déplacements
if ($vue) {
  unset($where["sejour.confirme"]);
  $where["effectue"] = "= '0'";
}
$whereEntrants = $whereSortants = $where;
$whereSortants["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
$whereEntrants["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
$whereEntrants["sejour.entree"] = "!= affectation.entree";
$whereSortants["sejour.sortie"] = "!= affectation.sortie";
$dep_entrants = $affectation->countList($whereEntrants, null, $ljoin);
$dep_sortants = $affectation->countList($whereSortants, null, $ljoin);

// Comptage des entrées/sorties
foreach ($mouvements as $type => &$_mouvement) {
  if (($type_hospi && $type_hospi != $type) || ($type_hospi == "ambu")) {
    continue;
  }
  $where["sejour.type"] = $whereNP["sejour.type"] = " = '$type'";
  foreach ($_mouvement as $type_mouvement => &$_liste) {
    if ($type == "ambu" && $type_mouvement == "sorties") {
      $_liste["place"]     = 0;
      $_liste["non_place"] = 0;
      continue;
    }
    if ($type_mouvement == "entrees") {
      unset($where["affectation.sortie"]);
      $where["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
      if (isset($where["sejour.sortie"])) {
        unset($where["sejour.sortie"]);
      }
      if (isset($whereNP["sejour.sortie"])) {
        unset($whereNP["sejour.sortie"]);
      }
      $where["sejour.entree"]      = "= affectation.entree";
      $whereNP["sejour.entree"]    = "BETWEEN '$limit1' AND '$limit2'";
    }
    else {
      unset($where["affectation.entree"]);
      $where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
      if (isset($where["sejour.entree"])) {
        unset($where["sejour.entree"]);
      }
      if (isset($whereNP["sejour.entree"])) {
        unset($whereNP["sejour.entree"]);
      }
      $where["sejour.sortie"]      = "= affectation.sortie";
      $whereNP["sejour.sortie"]    = "BETWEEN '$limit1' AND '$limit2'";
    }

    $_liste["place"]     = $affectation->countList($where, null, $ljoin);
    $_liste["non_place"] = $sejour->countList($whereNP, null, $ljoinNP);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("presents"    , $presents);
$smarty->assign("presentsNP"  , $presentsNP);
$smarty->assign("mouvements"  , $mouvements);
$smarty->assign("dep_entrants", $dep_entrants);
$smarty->assign("dep_sortants", $dep_sortants);
$smarty->assign("praticiens"  , $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("type_hospi"  , $type_hospi);
$smarty->assign("vue"         , $vue);
$smarty->assign("date"        , $date);
$smarty->assign("mode"        , $mode);
$smarty->assign("hour_instantane", $hour_instantane);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("prestations_journalieres", CPrestationJournaliere::loadCurrentList());
$smarty->assign("prestation_id", $prestation_id);

$smarty->display("edit_sorties.tpl");
