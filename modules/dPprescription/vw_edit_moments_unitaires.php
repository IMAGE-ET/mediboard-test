<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = mbGetValueFromGetOrSession("service_id");

// Chargement des moments unitaires
$moments = CMomentUnitaire::loadAllMoments();

$hours = range(0,23);
foreach($hours as &$hour){
	$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

// Chargement des services de l'etablissement courant
$service = new CService();
$services = $service->loadGroupList();

$service->load($service_id);

$group_id = CGroups::loadCurrent()->_id;

// Chargement des configs pour l'etablissement courant ou non specifie
$config_moment = new CConfigMomentUnitaire();
$where = array();
$where["group_id"] = "IS NULL";
$where["service_id"] = "IS NULL";
$order = "config_moment_unitaire_id ASC";
$configs = $config_moment->loadList($where, $order);

$all_configs = array();
foreach($configs as $_config){
  $all_configs[$_config->moment_unitaire_id] = array("group" => "", "service" => "");
  $all_configs[$_config->moment_unitaire_id]["default"] = $_config->heure;
  
  // Chargement de la config pour un etablissement
  $config_group = new CConfigMomentUnitaire();
  $config_group->moment_unitaire_id = $_config->moment_unitaire_id;
  $config_group->group_id = $group_id;
  $config_group->loadMatchingObject();  
  $all_configs[$_config->moment_unitaire_id]["group"] = $config_group;

  // Chargement de la variable de config pour le service
  if($service_id){
	  $config_service = new CConfigMomentUnitaire();
	  $config_service->moment_unitaire_id = $_config->moment_unitaire_id;
	  $config_service->service_id = $service_id;
	  $config_service->loadMatchingObject();
	  $all_configs[$_config->moment_unitaire_id]["service"] = $config_service;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("all_configs", $all_configs);
$smarty->assign("services", $services);
$smarty->assign("hours", $hours);
$smarty->assign("service_id", $service_id);
$smarty->assign("moments", $moments);
$smarty->assign("service", $service);
$smarty->assign("group_id", $group_id);
$smarty->display("vw_edit_moments_unitaires.tpl");

?>