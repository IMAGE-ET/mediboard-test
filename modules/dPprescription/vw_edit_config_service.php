<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = mbGetValueFromGetOrSession("service_id");
$group_id = CGroups::loadCurrent()->_id;

// Chargement des services de l'etablissement courant
$service = new CService();
$services = $service->loadGroupList();

// Chargement des configs pour l'etablissement courant ou non specifie
$config_service = new CConfigService();
$where = array();
$where["group_id"] = "IS NULL";
$where["service_id"] = "IS NULL";
$order = "config_service_id ASC";
$configs = $config_service->loadList($where, $order);

$all_configs = array();
foreach($configs as $_config){
  $all_configs[$_config->_id] = array("group" => "", "service" => "");
  $all_configs[$_config->_id]["name"] = $_config->name;
  $all_configs[$_config->_id]["default"] = $_config->value;
  
  // Chargement de la config pour un etablissement
  $config_group = new CConfigService();
  $config_group->name = $_config->name;
  $config_group->group_id = $group_id;
  $config_group->loadMatchingObject();  
  $all_configs[$_config->_id]["group"] = $config_group;

  // Chargement de la variable de config pour le service
  if($service_id){
	  $config_service = new CConfigService();
	  $config_service->name = $_config->name;
	  $config_service->service_id = $service_id;
	  $config_service->loadMatchingObject();
	  $all_configs[$_config->_id]["service"] = $config_service;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("configs", $configs);
$smarty->assign("all_configs", $all_configs);
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("config_service", $config_service);
$smarty->assign("group_id", $group_id);
$smarty->display("vw_edit_config_service.tpl");

?>