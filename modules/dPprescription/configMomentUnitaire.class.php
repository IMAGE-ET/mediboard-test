<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass('dPprescription', 'configServiceAbstract');
class CConfigMomentUnitaire extends CConfigServiceAbstract {
  // DB Fields
  var $config_moment_unitaire_id = null;
  var $moment_unitaire_id  = null;
  var $heure = null;
  static $configs_SHM = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'config_moment_unitaire';
    $spec->key   = 'config_moment_unitaire_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["moment_unitaire_id"] = "ref class|CMomentUnitaire notNull";
    $specs["heure"] = "time";
    return $specs;
  }

  /*
   * Chargement des configs en fonction du service
   */
  function getConfigMomentForService($service_id = "none"){
    $group_id = CGroups::loadCurrent()->_id;
    if(!isset(self::$configs_SHM)){
      self::$configs_SHM = $configs = $this->getConfigService("conf-moment");
    } else {
      $configs = self::$configs_SHM;
    }
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
    $this->setConfigService("conf-moment", $configs);
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
    $config_moment = new CConfigMomentUnitaire();
    $all_configs = $config_moment->loadList();
    
    // Creation du tableau de valeur par defaut (quelque soit l'etablissement)
    foreach($all_configs as $_config){
      if(!$_config->service_id && !$_config->group_id){
		    $configs_default[$_config->moment_unitaire_id] = $_config;
		  } else {
		    if($_config->service_id){
		      $configs_service[$_config->service_id][$_config->moment_unitaire_id] = $_config->heure;
		    } else {
		      $configs_group[$_config->group_id][$_config->moment_unitaire_id] = $_config->heure;
		    }
		  }
    }
    
    // Parcours des etablissements
    foreach($groups as $group_id => $group){
      $group->loadRefsService();
      // Parcours des services
	    foreach($group->_ref_services as $service_id => $_service){
		    foreach($configs_default as $_config_default){
		      $configs[$group_id][$service_id][$_config_default->moment_unitaire_id] = $_config_default->heure;
          if(isset($configs_group[$group_id][$_config_default->moment_unitaire_id])){
            $configs[$group_id][$service_id][$_config_default->moment_unitaire_id] = $configs_group[$group_id][$_config_default->moment_unitaire_id];
          }
          if(isset($configs_service[$service_id][$_config_default->moment_unitaire_id])){
            $configs[$group_id][$service_id][$_config_default->moment_unitaire_id] = $configs_service[$service_id][$_config_default->moment_unitaire_id];
          }
		    }
	    }
	    // Si aucun service
	    foreach($configs_default as $_config_default){
		    if(isset($configs_group[$group_id][$_config_default->moment_unitaire_id])){
		      $configs[$group_id]["none"][$_config_default->moment_unitaire_id] = $configs_group[$group_id][$_config_default->moment_unitaire_id];
		    } else {
		      $configs[$group_id]["none"][$_config_default->moment_unitaire_id] = $_config_default->heure;
		    }
	    }
    }
    return $configs;
  }
}
  
?>