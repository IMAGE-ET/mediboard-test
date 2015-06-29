<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$categories_id = CValue::getOrSession("categories_id");
$service_id    = CValue::getOrSession("service_id");
$date          = CValue::getOrSession("date");
$real_time     = CValue::getOrSession("real_time", 0);
$nb_decalage   = CValue::get("nb_decalage");

// Chargement du service
$service = new CService();
$service->load($service_id);

// Si le service en session n'est pas dans l'etablissement courant
if (CGroups::loadCurrent()->_id != $service->group_id) {
  $service_id = "";
  $service = new CService();
}

// Chargement des configs de services
if (!$service_id) {
  $service_id = "none";
}
$configs = CConfigService::getAllFor($service_id);

// Si la date actuelle est inférieure a l'heure affichée sur le plan de soins, on affiche le plan de soins de la veille
$datetime_limit = CMbDT::dateTime($configs["Poste 1"].":00:00");

if (!$date) {
  if (CMbDT::dateTime() < $datetime_limit) {
    $date = CMbDT::date("- 1 DAY", $date);
  }
  else {
    $date = CMbDT::date();
  }
}

if (!$nb_decalage) {
  $nb_decalage = $configs["Nombre postes avant"];
}

$categories = CPrescription::getCategoriesForPeriod($service_id, $date, $real_time);

// Récupération de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$_service = new CService();
$services = $_service->loadGroupList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("service"      , $service);
$smarty->assign("categories"   , $categories);
$smarty->assign("date"         , $date);
$smarty->assign("nb_decalage"  , $nb_decalage);
$smarty->assign("services"     , $services);
$smarty->assign("categories_id", $categories_id);
$smarty->assign('real_time'    , $real_time);
$smarty->assign('day'          , CMbDT::date());

$smarty->display('vw_plan_soins_service.tpl');