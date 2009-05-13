<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


class CSetupdPsante400 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPsante400";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `id_sante400` (" .
            "\n`id_sante400_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`object_class` VARCHAR( 25 ) NOT NULL ," .
            "\n`object_id` INT NOT NULL ," .
            "\n`tag` VARCHAR( 80 ) ," .
            "\n`last_update` DATETIME NOT NULL ," .
            "\nPRIMARY KEY ( `id_sante400_id` ) ," .
            "\nINDEX ( `object_class` , `object_id` , `tag` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `id_sante400` ADD `id400` VARCHAR( 8 ) NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `id_sante400` CHANGE `id400` `id400` VARCHAR( 10 ) NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `id_sante400` " .
               "\nCHANGE `id_sante400_id` `id_sante400_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NOT NULL;";
    $this->addQuery($sql);

    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `id_sante400` DROP INDEX `object_class` ;";    
    $this->addQuery($sql);
    $sql = "ALTER TABLE `id_sante400` ADD INDEX ( `object_class` ) ;";    
    $this->addQuery($sql);
    $sql = "ALTER TABLE `id_sante400` ADD INDEX ( `object_id` ) ;";    
    $this->addQuery($sql);
    $sql = "ALTER TABLE `id_sante400` ADD INDEX ( `tag` ) ;";    
    $this->addQuery($sql);
    $sql = "ALTER TABLE `id_sante400` ADD INDEX ( `last_update` ) ;";    
    $this->addQuery($sql);
    $sql = "ALTER TABLE `id_sante400` ADD INDEX ( `id400` ) ;";    
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "UPDATE `id_sante400` 
			SET `tag`='labo code4' 
			WHERE `tag`='LABO' 
			AND `object_class`='CMediusers' ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "UPDATE `id_sante400`
			SET `tag` = CONCAT('NDOS ', LEFT(`tag`, 8))
			WHERE `object_class` = 'CSejour'
			AND `tag` LIKE 'CIDC:___ DMED:________'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "CREATE TABLE `trigger_mark` (
			`mark_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`trigger_class` VARCHAR (255) NOT NULL,
			`trigger_number` VARCHAR (10) NOT NULL,
			`mark` VARCHAR (255) NOT NULL,
			`done` ENUM ('0','1') NOT NULL
		) TYPE=MYISAM;";
        
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `trigger_mark` ADD UNIQUE `trigger_unique` (
			`trigger_class` ,
			`trigger_number`
		);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `trigger_mark` ADD INDEX ( `mark` );";
    $this->addQuery($sql);
    
    $this->mod_version = "0.18";
  } 
}
?>