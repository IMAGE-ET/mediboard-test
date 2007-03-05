<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPbloc";
$config["mod_version"]     = "0.20";
$config["mod_type"]        = "user";


class CSetupdPbloc extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPbloc";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE plagesop (
              id bigint(20) NOT NULL auto_increment,
              id_chir varchar(20) NOT NULL default '0',
              id_anesth varchar(20) default NULL,
              id_spec tinyint(4) default NULL,
              id_salle tinyint(4) NOT NULL default '0',
              date date NOT NULL default '0000-00-00',
              debut time NOT NULL default '00:00:00',
              fin time NOT NULL default '00:00:00',
              PRIMARY KEY  (id)
              ) TYPE=MyISAM COMMENT='Table des plages d\'opération';";
    $this->addQuery($sql);
    $sql = "CREATE TABLE sallesbloc (
                id tinyint(4) NOT NULL auto_increment,
                nom varchar(50) NOT NULL default '',
                PRIMARY KEY  (id)
                ) TYPE=MyISAM COMMENT='Table des salles d\'opération du bloc';";
    $this->addQuery($sql);         
              
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_chir` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_anesth` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_spec` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_salle` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `date` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `plagesop` ADD `chir_id` BIGINT DEFAULT '0' NOT NULL AFTER `id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `chir_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD `anesth_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `anesth_id` ) ;";
    $this->addQuery($sql);
    function setup_swapPratIds() {
      global $AppUI;
      set_time_limit(1800);
      ignore_user_abort(1);
      $user = new CUser;
      
      // Changement des chirurgiens
      $sql = "SELECT id_chir" .
          "\nFROM plagesop" .
          "\nGROUP BY id_chir";
      $listPlages = db_loadList($sql);
      foreach($listPlages as $key => $plage) {
        $where["user_username"] = "= '".$plage["id_chir"]."'";
        $user->loadObject($where);
        if($user->user_id) {
          $sql = "UPDATE plagesop" .
              "\nSET chir_id = '$user->user_id'" .
              "\nWHERE id_chir = '$user->user_username'";
          db_exec( $sql ); db_error();
        }
      }
      
      //Changement des anesthésistes
      $sql = "SELECT id_anesth" .
          "\nFROM plagesop" .
          "\nGROUP BY id_anesth";
      $listPlages = db_loadList($sql);
      foreach($listPlages as $key => $plage) {
        $where["user_username"] = "= '".$plage["id_anesth"]."'";
        $user->loadObject($where);
        if($user->user_id) {
          $sql = "UPDATE plagesop" .
              "\nSET anesth_id = '$user->user_id'" .
              "\nWHERE id_anesth = '$user->user_username'";
          db_exec( $sql ); db_error();
        }
      }
      return true;
    }
    $this->addFunctions("setup_swapPratIds");

    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `sallesbloc` ADD `stats` TINYINT DEFAULT '0' NOT NULL AFTER `nom` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $this->addDependency("dPetablissement", "0.1");
    $sql = "ALTER TABLE `sallesbloc` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sallesbloc` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `plagesop` DROP `id_chir` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` DROP `id_anesth` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `plagesop` CHANGE `id` `plageop_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sallesbloc` CHANGE `id` `salle_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` CHANGE `id_spec` `spec_id` INT( 10 ) DEFAULT NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` CHANGE `id_salle` `salle_id` INT( 10 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `plagesop` ADD `temps_inter_op` TIME NOT NULL DEFAULT '00:15:00' ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `plagesop` " .
               "\nCHANGE `plageop_id` `plageop_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `spec_id` `spec_id` int(11) unsigned NULL," .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `sallesbloc` " .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `stats` `stats` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `debut` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plagesop` ADD INDEX ( `fin` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `plagesop` " .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL DEFAULT NULL," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `plagesop` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($sql);
    $sql = "UPDATE `plagesop` SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($sql);
    $sql = "UPDATE `plagesop` SET `spec_id` = NULL WHERE `spec_id` = '0';";
    $this->addQuery($sql);
    
    $this->mod_version = "0.20";
  }  
  
}
?>