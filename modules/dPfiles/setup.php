<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPfiles extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPfiles";
    
    $this->makeRevision("all");
    $this->setTimeLimit(120);
    if(!$this->ds->loadTable("files_mediboard")) {
      $query = "CREATE TABLE files_mediboard (
                      file_id int(11) NOT NULL auto_increment,
                      file_real_filename varchar(255) NOT NULL default '',
                      file_consultation bigint(20) NOT NULL default '0',
                      file_operation bigint(20) NOT NULL default '0',
                      file_name varchar(255) NOT NULL default '',
                      file_parent int(11) default '0',
                      file_description text,
                      file_type varchar(100) default NULL,
                      file_owner int(11) default '0',
                      file_date datetime default NULL,
                      file_size int(11) default '0',
                      file_version float NOT NULL default '0',
                      file_icon varchar(20) default 'obj/',
                      PRIMARY KEY  (file_id),
                      KEY idx_file_consultation (file_consultation),
                      KEY idx_file_operation (file_operation),
                      KEY idx_file_parent (file_parent)
                    ) TYPE=MyISAM;";
      $this->addQuery($query);
      $query = "CREATE TABLE files_index_mediboard (
                      file_id int(11) NOT NULL default '0',
                      word varchar(50) NOT NULL default '',
                      word_placement int(11) default '0',
                      PRIMARY KEY  (file_id,word),
                      KEY idx_fwrd (word),
                      KEY idx_wcnt (word_placement)
                      ) TYPE=MyISAM;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`" .
            "\nDROP `file_parent`," .
            "\nDROP `file_description`," .
            "\nDROP `file_version`," .
            "\nDROP `file_icon`;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`" .
              "\nADD `file_object_id` INT(11) NOT NULL DEFAULT '0' AFTER `file_real_filename`," .
              "\nADD `file_class` VARCHAR(30) NOT NULL DEFAULT 'CPatients' AFTER `file_object_id`;";
      $this->addQuery($query);
      $query = "UPDATE `files_mediboard`" .
              "SET `file_object_id` = `file_consultation`," .
              "\n`file_class` = 'CConsultation'" .
              "\nWHERE `file_consultation` != 0;";
      $this->addQuery($query);
      $query = "UPDATE `files_mediboard`" .
              "SET `file_object_id` = `file_operation`," .
              "\n`file_class` = 'COperation'" .
              "\nWHERE `file_operation` != 0;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`" .
            "\nDROP `file_consultation`," .
            "\nDROP `file_operation`;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard` ADD INDEX (`file_real_filename`);";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard` ADD UNIQUE (`file_real_filename`);";
      $this->addQuery($query);
    }else{
      $this->addTable("files_mediboard");
      $this->addTable("files_index_mediboard");
    }
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `files_mediboard` ADD `file_category_id` INT(11) NOT NULL DEFAULT '1' AFTER `file_type`";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_mediboard` ADD INDEX (`file_category_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `files_category` (" .
            "\n`file_category_id` INT(11) NOT NULL auto_increment, " .
            "\n`nom` VARCHAR(50) NOT NULL DEFAULT ''," .
            "\n`class` VARCHAR(30) DEFAULT NULL," .
            "\nPRIMARY KEY (file_category_id)" .
            "\n) TYPE=MyISAM;";
    $this->addQuery($query);
    $query = "INSERT INTO `files_category` VALUES('1', 'Divers', NULL)";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query="ALTER TABLE `files_mediboard` CHANGE `file_category_id` `file_category_id` INT( 11 ) NULL ";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query="ALTER TABLE `files_mediboard` CHANGE `file_category_id` `file_category_id` INT( 11 ) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `files_mediboard` DROP INDEX `file_real_filename` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_mediboard` DROP INDEX `file_real_filename_2` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_mediboard` ADD UNIQUE ( `file_real_filename` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_mediboard` ADD INDEX ( `file_class` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_category` ADD INDEX ( `class` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `files_mediboard` " .
               "\nCHANGE `file_id` `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `file_object_id` `file_object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_class` `file_class` varchar(255) NOT NULL DEFAULT 'CPatients'," .
               "\nCHANGE `file_type` `file_type` varchar(255) NULL," .
               "\nCHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_owner` `file_owner` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_date` `file_date` datetime NOT NULL," .
               "\nCHANGE `file_size` `file_size` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_category` " .
               "\nCHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `class` `class` varchar(255) NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `files_mediboard` ADD INDEX ( `file_object_id` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `files_mediboard` CHANGE `file_category_id` `file_category_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `files_mediboard` SET `file_category_id` = NULL WHERE `file_category_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `files_mediboard` SET `file_owner` = 1 WHERE `file_owner` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `files_mediboard` " .
               "\nCHANGE `file_object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_class` `object_class` varchar(255) NOT NULL DEFAULT 'CPatients';";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "UPDATE `files_category` 
			SET `class` = 'CSejour'
			WHERE `file_category_id` = 3;";
    $this->addQuery($query);

    $this->makeRevision("0.19");
    $query = "ALTER TABLE `files_category` 
			ADD `validation_auto` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `files_mediboard` 
            ADD `etat_envoi` ENUM ('oui','non','obsolete') NOT NULL default 'non';";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `files_category` 
			CHANGE `validation_auto` `send_auto` ENUM( '0', '1' ) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `files_mediboard` 
              ADD INDEX (`file_owner`),
              ADD INDEX (`file_date`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `files_mediboard` 
              ADD `private` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `files_mediboard`
             ADD `rotate` INT (11) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `files_mediboard`
            CHANGE `rotate` `rotation` ENUM ('0','90','180','270')";
    $this->addQuery($query);
    
    $this->mod_version = "0.26";
  }
}
?>