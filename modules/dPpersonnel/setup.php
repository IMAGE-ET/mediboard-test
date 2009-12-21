<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPpersonnel extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpersonnel";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `affectation_personnel` (
             `affect_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
             `user_id` INT(11) UNSIGNED NOT NULL, 
             `realise` ENUM('0','1') NOT NULL, 
             `debut` DATETIME, 
             `fin` DATETIME, 
             `object_id` INT(11) UNSIGNED NOT NULL, 
             `object_class` VARCHAR(25) NOT NULL, 
             PRIMARY KEY (`affect_id`)
             ) TYPE=MYISAM COMMENT='Table des affectations du personnel';";
    
    $this->addQuery($sql);

    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `affectation_personnel`
            ADD `tag` VARCHAR(80);";
    $this->addQuery($sql);

    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `personnel` (
            `personnel_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `user_id` INT(11) UNSIGNED NOT NULL, 
            `emplacement` ENUM('op','reveil','service') NOT NULL, 
            PRIMARY KEY (`personnel_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `affectation_personnel` 
            CHANGE `user_id` `personnel_id` INT(11) UNSIGNED NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `affectation_personnel` 
            SET `tag` = 'op' 
            WHERE `tag` IS NULL;";
    $this->addQuery($sql);
    
    $sql = "INSERT INTO `personnel`
            SELECT '', affectation_personnel.personnel_id, affectation_personnel.tag
            FROM `users`, `affectation_personnel`
            WHERE users.user_id = affectation_personnel.personnel_id 
              AND users.user_type = '14' 
              AND users.template = '0'
            GROUP BY users.user_id, affectation_personnel.tag;";
    $this->addQuery($sql);

    $sql = "UPDATE `affectation_personnel`, `personnel`
            SET affectation_personnel.personnel_id = personnel.personnel_id
            WHERE affectation_personnel.personnel_id = personnel.user_id
              AND affectation_personnel.tag = personnel.emplacement;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `affectation_personnel`
            DROP `tag`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `affectation_personnel` ADD INDEX ( `personnel_id` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `personnel`
            CHANGE `emplacement` `emplacement` ENUM('op','op_panseuse','reveil','service') NOT NULL;";
    $this->addQuery($sql);
     
    $sql = "INSERT INTO `personnel`
            SELECT '', personnel.user_id, 'op_panseuse'
            FROM `personnel`
            WHERE personnel.emplacement = 'op';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `personnel`
            ADD `actif` ENUM('0','1') DEFAULT '1' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `affectation_personnel` 
              ADD INDEX (`debut`),
              ADD INDEX (`fin`),
              ADD INDEX (`object_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `personnel` 
              ADD INDEX (`user_id`);";
    $this->addQuery($sql);
    
		$this->makeRevision("0.15");
		$sql = "ALTER TABLE `personnel` 
              CHANGE `emplacement` `emplacement` ENUM ('op','op_panseuse','reveil','service','iade') NOT NULL;";
		$this->addQuery($sql);
		
    $this->mod_version = "0.16";
  }
}
    
