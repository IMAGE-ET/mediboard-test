<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPccam extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPccam";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `ccamfavoris` (
      `favoris_id` bigint(20) NOT NULL auto_increment,
      `favoris_user` int(11) NOT NULL default '0',
      `favoris_code` varchar(7) NOT NULL default '',
      PRIMARY KEY  (`favoris_id`)
      ) TYPE=MyISAM COMMENT='table des favoris'";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `ccamfavoris` 
			CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `ccamfavoris`
			ADD `object_class` VARCHAR(25) NOT NULL DEFAULT 'COperation';";
    $this->addQuery($query);

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ccamfavoris` 
                ADD INDEX (`favoris_user`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "CREATE TABLE `frais_divers` (
                `frais_divers_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `type_id` INT (11) UNSIGNED NOT NULL,
                `coefficient` FLOAT NOT NULL DEFAULT '1',
                `quantite` INT (11) UNSIGNED,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0',
                `montant_depassement` DECIMAL  (10,3),
                `montant_base` DECIMAL  (10,3),
                `executant_id` INT (11) UNSIGNED NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` VARCHAR (255) NOT NULL
              ) TYPE=MYISAM;";
    $this->addQuery($query);

    $query = "ALTER TABLE `frais_divers` 
                ADD INDEX (`type_id`),
                ADD INDEX (`executant_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `frais_divers_type` (
                `frais_divers_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (16) NOT NULL,
                `libelle` VARCHAR (255) NOT NULL,
                `tarif` DECIMAL (10,3) NOT NULL,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->mod_version = "0.14";    

    // Data source query
    $query = "SELECT *
      FROM `actes`
      WHERE CODE = 'GEQP002'";
    $this->addDatasource("ccamV2", $query);
  }
}
?>