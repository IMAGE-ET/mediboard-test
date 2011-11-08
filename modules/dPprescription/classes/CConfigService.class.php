<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

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
  static function getAllFor($service_id = "none", $group_id = ""){
  	if(!$group_id){
  		$group_id = CGroups::loadCurrent()->_id;
  	}
	  
		if(!isset(self::$configs_SHM)){
      self::$configs_SHM = $configs = self::getSHM("conf-service");
    } else {
      $configs = self::$configs_SHM;
    }
    // Si la config n'existe pas en SHM
    if($configs == null){
      $configs = self::setConfigInSHM();
      self::$configs_SHM = $configs;
    }
    return $configs[$group_id][$service_id];
  }
  
  /*
   * Sauvegarde des configs dans la SHM
   */ 
  static function setConfigInSHM(){
    $configs = self::getAllConfigs();
    self::setSHM("conf-service", $configs);
    return $configs;
  }
  
  /*
   * Calcul de la totalité des configs pour les stocker dans la SHM
   */
  static function getAllConfigs(){
    return self::_getAllConfigs("CConfigService", "name", "value");
  }
  
  static function emptySHM(){
    self::_emptySHM("CConfigService", "conf-service");
  }
}
  
?>