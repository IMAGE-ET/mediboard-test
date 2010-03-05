<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dPprescription', 'configServiceAbstract');
class CConfigService extends CConfigServiceAbstract {
  // DB Fields
  var $config_service_id = null;
  var $name  = null;
  var $value = null;
  
  static $configs_SHM = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'config_service';
    $spec->key   = 'config_service_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["name"]  = "str notNull";
    $specs["value"] = "str";
    return $specs;
  }
  
  /*
   * Chargement des configs en fonction du service
   */
  function getConfigForService($service_id = "none"){
    $group_id = CGroups::loadCurrent()->_id;
    if(!isset(self::$configs_SHM)){
      self::$configs_SHM = $configs = $this->getConfigService("conf-service");
    } else {
      $configs = self::$configs_SHM;
    }
    // Si la config n'existe pas en SHM
    if($configs == null){
      $configs = $this->setConfigInSHM();
      self::$configs_SHM = $configs;
    }
    return $configs[$group_id][$service_id];
  }
  
  /*
   * Sauvegarde des configs dans la SHM
   */ 
  function setConfigInSHM(){
    $configs = $this->getAllConfigs();
    $this->setConfigService("conf-service", $configs);
    return $configs;
  }
  
  /*
   * Calcul de la totalité des configs pour les stocker dans la SHM
   */
  function getAllConfigs(){
    // Chargement des etablissements
    $group = new CGroups();
    $groups = $group->loadList();

    // Chargement des services
    $service = new CService();
    $services = $service->loadList();
    
    // Chargement de toutes les configs
    $config_service = new CConfigService();
    $all_configs = $config_service->loadList();
    
    // Creation du tableau de valeur par defaut (quelque soit l'etablissement)
    foreach($all_configs as $_config){
      if(!$_config->service_id && !$_config->group_id){
		    $configs_default[$_config->name] = $_config;
		  } else {
		    if($_config->service_id){
		      $configs_service[$_config->service_id][$_config->name] = $_config->value;
		    } else {
		      $configs_group[$_config->group_id][$_config->name] = $_config->value;
		    }
		  }
    }
    
    // Parcours des etablissements
    foreach($groups as $group_id => $group){
      $group->loadRefsService();
      // Parcours des services
	    foreach($group->_ref_services as $service_id => $_service){
		    foreach($configs_default as $_config_default){
		      $configs[$group_id][$service_id][$_config_default->name] = $_config_default->value;
          if(isset($configs_group[$group_id][$_config_default->name])){
            $configs[$group_id][$service_id][$_config_default->name] = $configs_group[$group_id][$_config_default->name];
          }
          if(isset($configs_service[$service_id][$_config_default->name])){
            $configs[$group_id][$service_id][$_config_default->name] = $configs_service[$service_id][$_config_default->name];
          }
		    }
	    }
	    // Si aucun service
	    foreach($configs_default as $_config_default){
		    if(isset($configs_group[$group_id][$_config_default->name])){
		      $configs[$group_id]["none"][$_config_default->name] = $configs_group[$group_id][$_config_default->name];
		    } else {
		      $configs[$group_id]["none"][$_config_default->name] = $_config_default->value;
		    }
	    }
    }
    return $configs;
  }
}
  
?>