<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CSetupdPrepas extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPrepas";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `menu` (" .
          "\n`menu_id` int(11) unsigned NOT NULL AUTO_INCREMENT ," .
          "\n`group_id` int(11) UNSIGNED NOT NULL," .
          "\n`nom` VARCHAR( 255 ) NOT NULL ," .
          "\n`typerepas` int(11) UNSIGNED NOT NULL," .
          "\n`plat1` VARCHAR( 255 )," .
          "\n`plat2` VARCHAR( 255 )," .
          "\n`plat3` VARCHAR( 255 )," .
          "\n`plat4` VARCHAR( 255 )," .
          "\n`plat5` VARCHAR( 255 )," .
          "\n`boisson` VARCHAR( 255 )," .
          "\n`pain` VARCHAR( 255 )," .
          "\n`diabete` enum('0','1') NOT NULL DEFAULT '0'," .
          "\n`sans_sel` enum('0','1') NOT NULL DEFAULT '0'," .
          "\n`sans_residu` enum('0','1') NOT NULL DEFAULT '0'," .
          "\n`modif` enum('0','1') NOT NULL DEFAULT '1'," .
          "\n`debut` date NOT NULL," .
          "\n`fin` date NOT NULL," .
          "\n`repetition` int(11) unsigned NOT NULL," .
          "\nPRIMARY KEY ( `menu_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `plats` (" .
          "\n`plat_id` int(11) unsigned NOT NULL AUTO_INCREMENT ," .
          "\n`group_id` int(11) UNSIGNED NOT NULL," .
          "\n`nom` VARCHAR( 255 ) NOT NULL ," .
          "\n`type` enum('plat1','plat2','plat3','plat4','plat5','boisson','pain') NOT NULL DEFAULT 'plat1'," .
          "\n`typerepas` int(11) UNSIGNED NOT NULL," .
          "\nPRIMARY KEY ( `plat_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `repas_type` (" .
          "\n`typerepas_id` int(11) unsigned NOT NULL AUTO_INCREMENT ," .
          "\n`group_id` int(11) UNSIGNED NOT NULL," .
          "\n`nom` VARCHAR( 255 ) NOT NULL ," .
          "\n`debut` time NOT NULL," .
          "\n`fin` time NOT NULL," .
          "\nPRIMARY KEY ( `typerepas_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `repas` (" .
          "\n`repas_id` int(11) unsigned NOT NULL AUTO_INCREMENT ," .
          "\n`affectation_id` int(11) UNSIGNED NOT NULL," .
          "\n`menu_id` int(11) UNSIGNED NOT NULL," .
          "\n`plat1` int(11) UNSIGNED NULL," .
          "\n`plat2` int(11) UNSIGNED NULL," .
          "\n`plat3` int(11) UNSIGNED NULL," .
          "\n`plat4` int(11) UNSIGNED NULL," .
          "\n`plat5` int(11) UNSIGNED NULL," .
          "\n`boisson` int(11) UNSIGNED NULL," .
          "\n`pain` int(11) UNSIGNED NULL," .
          "\n`date` date NOT NULL," .
          "\nPRIMARY KEY ( `repas_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `repas` CHANGE `menu_id` `menu_id` int(11) UNSIGNED NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `repas` ADD `typerepas_id` int(11) UNSIGNED NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `menu` ADD `nb_repet` int(11) unsigned NOT NULL;";
    $this->addQuery($sql);
    
    function setup_menu(){
      $ds = CSQLDataSource::get("std");
 
      $sql = "SELECT * FROM menu";
      $menus = $ds->loadList($sql);
      foreach($menus as $menu){
        $nbDays  = mbDaysRelative($menu["debut"], $menu["fin"]);
        $nbWeeks = floor($nbDays / 7);
        if(!$nbWeeks){
          $menu["nb_repet"] = 1;
        }else{
          $menu["nb_repet"] = ceil($nbWeeks/$menu["repetition"]);
        }
        $sql = "UPDATE `menu` SET `nb_repet` = '".$menu["nb_repet"]."' WHERE(`menu_id`='".$menu["menu_id"]."');";
        $ds->exec($sql); $ds->error();
        $sql = "UPDATE `repas` SET `typerepas_id`='".$menu["typerepas"]."' WHERE(`menu_id`='".$menu["menu_id"]."');";
        $ds->exec($sql); $ds->error();
      }
      $sql = "ALTER TABLE `menu` DROP `fin` ";
      $ds->exec($sql); $ds->error(); 
      return true;
    }
    $this->addFunction("setup_menu");
    
    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `validationrepas` (" .
          "\n`validationrepas_id` int(11) unsigned NOT NULL AUTO_INCREMENT ," .
          "\n`service_id` int(11) UNSIGNED NOT NULL," .
          "\n`date` date NOT NULL," .
          "\n`typerepas_id` int(11) UNSIGNED NOT NULL," .
          "\nPRIMARY KEY ( `validationrepas_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `repas` ADD `modif` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `validationrepas` ADD `modif` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `menu` 
              ADD INDEX (`group_id`),
              ADD INDEX (`typerepas`),
              ADD INDEX (`debut`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plats` 
              ADD INDEX (`group_id`),
              ADD INDEX (`typerepas`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `repas` 
              ADD INDEX (`affectation_id`),
              ADD INDEX (`menu_id`),
              ADD INDEX (`plat1`),
              ADD INDEX (`plat2`),
              ADD INDEX (`plat3`),
              ADD INDEX (`plat4`),
              ADD INDEX (`plat5`),
              ADD INDEX (`boisson`),
              ADD INDEX (`pain`),
              ADD INDEX (`date`),
              ADD INDEX (`typerepas_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `repas_type` 
              ADD INDEX (`group_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `validationrepas` 
              ADD INDEX (`service_id`),
              ADD INDEX (`date`),
              ADD INDEX (`typerepas_id`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.14";
  }
}
?>