<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// TODO Revoir tout ca en essayant de passer outre la limitation du LSB
class CConfigConstantesMedicales extends CConfigServiceAbstract {
  // DB Fields
  var $config_constantes_medicales_id = null;
  
  var $show_cat_tabs  = null;
  var $show_enable_all_button  = null;
  
  var $diuere_24_reset_hour  = null;
  var $redon_cumul_reset_hour  = null;
  var $sng_cumul_reset_hour  = null;
  var $lame_cumul_reset_hour  = null;
  var $drain_cumul_reset_hour  = null;
  var $drain_thoracique_cumul_reset_hour  = null;
  var $drain_pleural_cumul_reset_hour  = null;
  var $drain_mediastinal_cumul_reset_hour  = null;
  var $sonde_ureterale_cumul_reset_hour  = null;
  var $sonde_nephro_cumul_reset_hour  = null;
  var $sonde_vesicale_cumul_reset_hour  = null;
  
  var $important_constantes  = null;
  
  static $configs_SHM = null;
  static $_const_names = array();
  
  static $_conf_names = array(
    "show_cat_tabs",
    "show_enable_all_button",
    
    "diuere_24_reset_hour",
    "redon_cumul_reset_hour",
    "sng_cumul_reset_hour",
    "lame_cumul_reset_hour",
    "drain_cumul_reset_hour",
    "drain_thoracique_cumul_reset_hour",
    "drain_pleural_cumul_reset_hour",
    "drain_mediastinal_cumul_reset_hour",
    "sonde_ureterale_cumul_reset_hour",
		"sonde_nephro_cumul_reset_hour",
    "sonde_vesicale_cumul_reset_hour",
    
    "important_constantes",
  );
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "config_constantes_medicales";
    $spec->key   = "config_constantes_medicales_id";
    $spec->uniques["group"]   = array("group_id", "service_id");
    $spec->uniques["service"] = array("service_id");
    return $spec;
  }
  
  function getProps(){
    $list = implode("|", self::$_const_names);
    
    $props = parent::getProps();
    $props["show_cat_tabs"]                      = "bool";
    $props["show_enable_all_button"]             = "bool";
    
    $props["diuere_24_reset_hour"]               = "num min|0 max|23";
    $props["redon_cumul_reset_hour"]             = "num min|0 max|23";
    $props["sng_cumul_reset_hour"]               = "num min|0 max|23";
    $props["lame_cumul_reset_hour"]              = "num min|0 max|23";
    $props["drain_cumul_reset_hour"]             = "num min|0 max|23";
    $props["drain_thoracique_cumul_reset_hour"]  = "num min|0 max|23";
    $props["drain_pleural_cumul_reset_hour"]     = "num min|0 max|23";
    $props["drain_mediastinal_cumul_reset_hour"] = "num min|0 max|23";
    $props["sonde_ureterale_cumul_reset_hour"]   = "num min|0 max|23";
		$props["sonde_nephro_cumul_reset_hour"]      = "num min|0 max|23";
    $props["sonde_vesicale_cumul_reset_hour"]    = "num min|0 max|23";
    
    $props["important_constantes"]               = "set list|$list vertical";
    return $props;
  }
  
  function store(){
    if ($msg = parent::store()) {
      return $msg;
    }
    
    SHM::rem("conf-constantes_medicales");
    self::setConfigInSHM();
  }
  
  static function init(){
    global $locales;
		
    $list_all = CConstantesMedicales::$list_constantes;
		
    foreach($list_all as $name => $params) {
      $locales["CConfigConstantesMedicales.important_constantes.$name"] = CAppUI::tr("CConstantesMedicales-$name"); 
    }
    
    $list = array();
    foreach($list_all as $_const => $_params) {
      if (!isset($_params["cumul_for"])) {
        $list[] = $_const;
      }
    }
    
    self::$_const_names = $list;
  }
  
  /**
   * Chargement des configs en fonction du service
   */
  static function getAllFor($service_id = "none", $group_id = ""){
    if(!$group_id){
      $group_id = CGroups::loadCurrent()->_id;
    }
    
    if (!$service_id || $service_id === "NP") {
      $service_id = "none";
    }
    
    if(!isset(self::$configs_SHM)){
      self::$configs_SHM = $configs = self::getSHM("conf-constantes_medicales");
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
  
  /**
   * Sauvegarde des configs dans la SHM
   */ 
  static function setConfigInSHM(){
    $configs = self::getAllConfigs();
    self::setSHM("conf-constantes_medicales", $configs);
    return $configs;
  }
  
  protected static function mapConfig(&$array, $object) {
    $values = (array)$object;
    
    foreach(self::$_conf_names as $confname) {
      if (!isset($values[$confname])) continue;
      $array[$confname] = $values[$confname];
    }
  }
  
  /**
   * Calcul de la totalité des configs pour les stocker dans la SHM
   * Version speciale pour CConfigConstantesMedicales
   */
  static function getAllConfigs(){
    // Chargement des etablissements
    $group = new CGroups();
    $groups = $group->loadList();

    // Chargement des services
    $service = new CService();
    $services = $service->loadList();
    
    // Chargement de toutes les configs
    $config = new self;
    $all_configs = $config->loadList();
    
    if ($all_configs == null) {
      return;
    }
    
    $configs_default = array();
    $configs_service = array();
    $configs_group = array();
    
    // Creation du tableau de valeur par defaut (quelque soit l'etablissement)
    foreach($all_configs as $_config){
      if($_config->service_id){
        self::mapConfig($configs_service[$_config->service_id], $_config);
      } 
      elseif ($_config->group_id) {
        self::mapConfig($configs_group[$_config->group_id], $_config);
      }
      else {
        self::mapConfig($configs_default, $_config);
      }
    }
    
    // Parcours des etablissements
    foreach($groups as $group_id => $group){
      $group->loadRefsService();
      
      // Parcours des services
      foreach($group->_ref_services as $service_id => $_service){
        self::mapConfig($configs[$group_id][$service_id], $configs_default);
        
        if (isset($configs_group[$group_id])) {
          self::mapConfig($configs[$group_id][$service_id], $configs_group[$group_id]);
        }
        
        if (isset($configs_service[$service_id])) {
          self::mapConfig($configs[$group_id][$service_id], $configs_service[$service_id]);
        }
      }
      
      // Si aucun service
      foreach($configs_default as $_config_default){
        foreach(self::$_conf_names as $confname) {
          if (isset($configs_group[$group_id][$confname])) {
            $configs[$group_id]["none"][$confname] = $configs_group[$group_id][$confname];
          }
          else {
            $configs[$group_id]["none"][$confname] = $configs_default[$confname];
          }
        }
      }
    }
    
    return $configs;
  }
  
  static function emptySHM(){
    self::_emptySHM("CConfigConstantesMedicales", "conf-constantes_medicales");
  }
}

CConfigConstantesMedicales::init();
