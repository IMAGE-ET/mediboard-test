<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPbloc extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPbloc";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE plagesop (
              id bigint(20) NOT NULL auto_increment,
              id_chir varchar(20) NOT NULL default '0',
              id_anesth varchar(20) default NULL,
              id_spec tinyint(4) default NULL,
              id_salle tinyint(4) NOT NULL default '0',
              date date NOT NULL default '0000-00-00',
              debut time NOT NULL default '00:00:00',
              fin time NOT NULL default '00:00:00',
              PRIMARY KEY  (id)
              ) /*! ENGINE=MyISAM */ COMMENT='Table des plages d\'opération';";
    $this->addQuery($query);
    $query = "CREATE TABLE sallesbloc (
                id tinyint(4) NOT NULL auto_increment,
                nom varchar(50) NOT NULL default '',
                PRIMARY KEY  (id)
                ) /*! ENGINE=MyISAM */ COMMENT='Table des salles d\'opération du bloc';";
    $this->addQuery($query);         
              
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `id_chir` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `id_anesth` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `id_spec` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `id_salle` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `date` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `plagesop` ADD `chir_id` BIGINT DEFAULT '0' NOT NULL AFTER `id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `chir_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD `anesth_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `anesth_id` ) ;";
    $this->addQuery($query);
    function setup_swapPratIds() {
      $ds = CSQLDataSource::get("std");
 
      set_time_limit(1800);
      ignore_user_abort(1);
      $user = new CUser;
      
      // Changement des chirurgiens
      $query = "SELECT id_chir" .
          "\nFROM plagesop" .
          "\nGROUP BY id_chir";
      $listPlages = $ds->loadList($query);
      foreach($listPlages as $key => $plage) {
        $where["user_username"] = "= '".$plage["id_chir"]."'";
        $user->loadObject($where);
        if($user->user_id) {
          $query = "UPDATE plagesop" .
              "\nSET chir_id = '$user->user_id'" .
              "\nWHERE id_chir = '$user->user_username'";
          $ds->exec( $query ); $ds->error();
        }
      }
      
      //Changement des anesthésistes
      $query = "SELECT id_anesth" .
          "\nFROM plagesop" .
          "\nGROUP BY id_anesth";
      $listPlages = $ds->loadList($query);
      foreach($listPlages as $key => $plage) {
        $where["user_username"] = "= '".$plage["id_anesth"]."'";
        $user->loadObject($where);
        if($user->user_id) {
          $query = "UPDATE plagesop" .
              "\nSET anesth_id = '$user->user_id'" .
              "\nWHERE id_anesth = '$user->user_username'";
          $ds->exec( $query ); $ds->error();
        }
      }
      return true;
    }
    $this->addFunction("setup_swapPratIds");

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `sallesbloc` ADD `stats` TINYINT DEFAULT '0' NOT NULL AFTER `nom` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `sallesbloc` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `plagesop` DROP `id_chir` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` DROP `id_anesth` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `plagesop` CHANGE `id` `plageop_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc` CHANGE `id` `salle_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` CHANGE `id_spec` `spec_id` INT( 10 ) DEFAULT NULL ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` CHANGE `id_salle` `salle_id` INT( 10 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `plagesop` ADD `temps_inter_op` TIME NOT NULL DEFAULT '00:15:00' ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `plagesop` " .
               "\nCHANGE `plageop_id` `plageop_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `spec_id` `spec_id` int(11) unsigned NULL," .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `sallesbloc` " .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `stats` `stats` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `debut` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop` ADD INDEX ( `fin` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `plagesop` " .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL DEFAULT NULL," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `plagesop` SET `spec_id` = NULL WHERE `spec_id` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `plagesop`
            ADD `max_intervention` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `plagesop`
            CHANGE `max_intervention` `max_intervention` INT(11);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "CREATE TABLE `bloc_operatoire` (
            `bloc_operatoire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
            `group_id` INT (11) UNSIGNED NOT NULL,
            `nom` VARCHAR (255) NOT NULL);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `bloc_operatoire` ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "INSERT INTO `bloc_operatoire` (`nom`, `group_id`)
            SELECT 'Bloc principal', `group_id`
            FROM `groups_mediboard`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sallesbloc` 
            CHANGE `group_id` `bloc_id` INT( 11 ) UNSIGNED NOT NULL";
    $this->addQuery($query);
    
    $query = "UPDATE `sallesbloc` 
            SET `bloc_id` = (
              SELECT `bloc_operatoire_id` 
              FROM `bloc_operatoire` 
              WHERE `sallesbloc`.`bloc_id` = `bloc_operatoire`.`group_id`
              LIMIT 1
            );";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `plagesop` 
            ADD `spec_repl_id` INT (11) UNSIGNED,
            ADD `delay_repl` INT (11);";
    $this->addQuery($query);
    $query = "ALTER TABLE `plagesop`
            ADD INDEX (`spec_repl_id`),
            ADD INDEX (`delay_repl`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `plagesop` 
            ADD `actes_locked` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `plagesop` ADD `unique_chir` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `sallesbloc`
              ADD `dh` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
   
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `bloc_operatoire`
      ADD `days_locked` INT (11) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);
    
    global $dPconfig;
    $days_locked = (isset($dPconfig["dPbloc"]["CPlageOp"]["days_locked"]) ?
        CAppUI::conf("dPbloc CPlageOp days_locked") : 0);
    
    $query = "UPDATE `bloc_operatoire`
     SET days_locked = '$days_locked'";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `plagesop` 
      ADD `verrouillage` ENUM ('defaut','non','oui') DEFAULT 'defaut' AFTER max_intervention;";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $this->addPrefQuery("suivisalleAutonome", 0);

    $this->makeRevision("0.30");
    $query = "CREATE TABLE `blocage` (
      `blocage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `salle_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255),
      `deb` DATE NOT NULL,
      `fin` DATE NOT NULL
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `blocage` 
      ADD INDEX (`salle_id`),
      ADD INDEX (`deb`),
      ADD INDEX (`fin`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    
    $query = "CREATE TABLE `ressource_materielle` (
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `type_ressource_id` INT (11) UNSIGNED NOT NULL,
      `group_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `deb_activite` DATE,
      `fin_activite` DATE,
      `retablissement` ENUM ('0','1') DEFAULT '0'
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `ressource_materielle`
      ADD INDEX (`type_ressource_id`),
      ADD INDEX (`group_id`),
      ADD INDEX (`deb_activite`),
      ADD INDEX (`fin_activite`),
      ADD INDEX (`retablissement`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `type_ressource` (
      `type_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `group_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `type_ressource` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `usage_ressource` (
      `usage_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL,
      `besoin_id` INT (11) UNSIGNED NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `usage_ressource` 
      ADD INDEX (`ressource_materielle_id`),
      ADD INDEX (`besoin_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `indispo_ressource` (
      `indispo_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ressource_materielle_id` INT (11) UNSIGNED NOT NULL,
      `deb` DATE NOT NULL,
      `fin` DATE NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `besoin_ressource` (
      `besoin_ressource_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `type_ressource_id` INT (11) UNSIGNED NOT NULL,
      `protocole_id` INT (11) UNSIGNED NOT NULL,
      `operation_id` INT (11) UNSIGNED NOT NULL,
      `commentaire` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `besoin_ressource` 
      ADD INDEX (`type_ressource_id`),
      ADD INDEX (`protocole_id`),
      ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `usage_ressource`
      CHANGE `besoin_id` `besoin_ressource_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `indispo_ressource`
      CHANGE `deb` `deb` DATETIME NOT NULL,
      CHANGE `fin` `fin` DATETIME NOT NULL;";
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `ressource_materielle` 
      CHANGE `retablissement` `retablissement` TIME;";
    $this->addQuery($query);
    
    $this->mod_version = "0.34";
  }
}
?>