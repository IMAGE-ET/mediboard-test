<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

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
  static function getAllFor($service_id = "none", $group_id = ""){
    if(!$group_id){
      $group_id = CGroups::loadCurrent()->_id;
    }
    
    if(!isset(self::$configs_SHM)){
      self::$configs_SHM = $configs = self::getSHM("conf-moment");
    } else {
      $configs = self::$configs_SHM;
    }
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
    self::setSHM("conf-moment", $configs);
  }
  
  /*
   * Calcul de la totalité des configs pour les stocker dans la SHM
   */
  static function getAllConfigs(){
    return self::_getAllConfigs("CConfigMomentUnitaire", "moment_unitaire_id", "heure");
  }
  
  static function emptySHM(){
    self::_emptySHM("CConfigMomentUnitaire", "conf-moment");
  }
}
  
?>