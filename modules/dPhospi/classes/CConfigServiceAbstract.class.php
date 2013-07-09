<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CConfigServiceAbstract extends CMbObject {
  public $service_id;
  public $group_id;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["service_id"]  = "ref class|CService";
    $props["group_id"] = "ref class|CGroups";
    return $props;
  }
  
  static function setSHM($name, $config){
    SHM::put($name, $config);
  }  
  
  static function getSHM($name) {
    return SHM::get($name);
  }
  
  static function remSHM($name) {
    return SHM::rem($name);
  }

  protected static function _getAllConfigs($class, $key, $value) {
    // Chargement des etablissements
    $group = new CGroups();

    /** @var CGroups[] $groups */
    $groups = $group->loadList();

    // Chargement des services
    $service = new CService();
    $services = $service->loadList();
    
    // Chargement de toutes les configs
    /** @var self $config */
    $config = new $class();

    /** @var self[] $all_configs */
    $all_configs = $config->loadList();
    
    if ($all_configs == null) {
      return null;
    }

    /** @var self[] $configs_default */
    // Creation du tableau de valeur par defaut (quelque soit l'etablissement)
    foreach ($all_configs as $_config) {
      if (!$_config->service_id && !$_config->group_id) {
        $configs_default[$_config->$key] = $_config;
      }
      else {
        if ($_config->service_id) {
          $configs_service[$_config->service_id][$_config->$key] = $_config->$value;
        }
        else {
          $configs_group[$_config->group_id][$_config->$key] = $_config->$value;
        }
      }
    }

    $configs = array();
    
    // Parcours des etablissements
    foreach ($groups as $group_id => $group) {
      $group->loadRefsServices();
      // Parcours des services
      foreach ($group->_ref_services as $service_id => $_service) {
        foreach ($configs_default as $_config_default) {
          $configs[$group_id][$service_id][$_config_default->$key] = $_config_default->$value;
          if (isset($configs_group[$group_id][$_config_default->$key])) {
            $configs[$group_id][$service_id][$_config_default->$key] = $configs_group[$group_id][$_config_default->$key];
          }
          if (isset($configs_service[$service_id][$_config_default->$key])) {
            $configs[$group_id][$service_id][$_config_default->$key] = $configs_service[$service_id][$_config_default->$key];
          }
        }
      }
      // Si aucun service
      foreach ($configs_default as $_config_default) {
        if (isset($configs_group[$group_id][$_config_default->$key])) {
          $configs[$group_id]["none"][$_config_default->$key] = $configs_group[$group_id][$_config_default->$key];
        }
        else {
          $configs[$group_id]["none"][$_config_default->$key] = $_config_default->$value;
        }
      }
    }
    
    return $configs;
  }
  
  static function emptySHM() {
    
  }

  static function isCacheUpToDate($class, $name) {
    /** @var self $instance */
    $instance = new $class;

    $spec = $instance->_spec;
    $status_result = $instance->getDS()->loadHash("SHOW TABLE STATUS LIKE '{$spec->table}'");

    $data_date  = $status_result["Update_time"];
    $cache_date = SHM::modDate($name);

    return $cache_date > $data_date;
  }
  
  protected static function _emptySHM($class, $key) {
    if (!SHM::get($key)) {
      CAppUI::stepAjax("$class-shm-none", UI_MSG_OK);
    }
    else {
      if (!SHM::rem($key)) {
        CAppUI::stepAjax("$class-shm-rem-ko", UI_MSG_WARNING);
      }
      
      CAppUI::stepAjax("$class-shm-rem-ok", UI_MSG_OK);
    }
  }
}
