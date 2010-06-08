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
    $query = "CREATE TABLE `affectation_personnel` (
			`affect_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
			`user_id` INT(11) UNSIGNED NOT NULL, 
			`realise` ENUM('0','1') NOT NULL, 
			`debut` DATETIME, 
			`fin` DATETIME, 
			`object_id` INT(11) UNSIGNED NOT NULL, 
			`object_class` VARCHAR(25) NOT NULL, 
			PRIMARY KEY (`affect_id`)
			) TYPE=MYISAM COMMENT='Table des affectations du personnel';";
    $this->addQuery($query);

    $this->makeRevision("0.1");
    $query = "ALTER TABLE `affectation_personnel`
      ADD `tag` VARCHAR(80);";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "CREATE TABLE `personnel` (
	    `personnel_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
	    `user_id` INT(11) UNSIGNED NOT NULL, 
	    `emplacement` ENUM('op','reveil','service') NOT NULL, 
	    PRIMARY KEY (`personnel_id`)) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation_personnel` 
      CHANGE `user_id` `personnel_id` INT(11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `affectation_personnel` 
      SET `tag` = 'op' 
      WHERE `tag` IS NULL;";
    $this->addQuery($query);
    
    $query = "INSERT INTO `personnel`
      SELECT '', affectation_personnel.personnel_id, affectation_personnel.tag
      FROM `users`, `affectation_personnel`
      WHERE users.user_id = affectation_personnel.personnel_id 
        AND users.user_type = '14' 
        AND users.template = '0'
      GROUP BY users.user_id, affectation_personnel.tag;";
    $this->addQuery($query);

    $query = "UPDATE `affectation_personnel`, `personnel`
      SET affectation_personnel.personnel_id = personnel.personnel_id
      WHERE affectation_personnel.personnel_id = personnel.user_id
      AND affectation_personnel.tag = personnel.emplacement;";
    $this->addQuery($query);

    $query = "ALTER TABLE `affectation_personnel`
      DROP `tag`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `affectation_personnel` 
		  ADD INDEX ( `personnel_id` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `personnel`
      CHANGE `emplacement` `emplacement` ENUM('op','op_panseuse','reveil','service') NOT NULL;";
    $this->addQuery($query);
     
    $query = "INSERT INTO `personnel`
      SELECT '', personnel.user_id, 'op_panseuse'
      FROM `personnel`
      WHERE personnel.emplacement = 'op';";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `personnel`
      ADD `actif` ENUM('0','1') DEFAULT '1' NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `affectation_personnel` 
      ADD INDEX (`debut`),
      ADD INDEX (`fin`),
      ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `personnel` 
            ADD INDEX (`user_id`);";
    $this->addQuery($query);
    
		$this->makeRevision("0.15");
		$query = "ALTER TABLE `personnel` 
      CHANGE `emplacement` `emplacement` ENUM ('op','op_panseuse','reveil','service','iade') NOT NULL;";
		$this->addQuery($query);
		
		$this->makeRevision("0.16");
		$query = "CREATE TABLE `plageVacances` (
		  `plage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
		  `date_debut` DATE,
		  `date_fin` DATE,
		  `libelle` VARCHAR (255)
		) TYPE=MYISAM;";
    $this->addQuery($query);

    $query = "ALTER TABLE `plageVacances` 
      ADD INDEX (`date_debut`),
      ADD INDEX (`date_fin`);";
		$this->addQuery($query);
		
		$this->makeRevision("0.17");
		
		$query = "ALTER TABLE `plageVacances` 
      ADD `user_id` INT (11) UNSIGNED;";
	  $this->addQuery($query);
		$query = "ALTER TABLE `plageVacances` 
      ADD INDEX (`user_id`);"; 
		$this->addQuery($query);
		
    $this->makeRevision("0.18");
		$query = "ALTER TABLE `plageVacances` 
			CHANGE `user_id` `user_id` INT (11) UNSIGNED NOT NULL;";
		$this->addQuery($query);
		
		$this->makeRevision("0.19");
		$query = "ALTER TABLE `plageVacances` 
	    CHANGE `date_debut` `date_debut` DATE NOT NULL,
	    CHANGE `date_fin` `date_fin` DATE NOT NULL,
	    CHANGE `libelle` `libelle` VARCHAR (255) NOT NULL;";
		$this->addQuery($query);
		
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `plageVacances` 
      ADD `replacer_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `plageVacances` 
      ADD INDEX (`replacer_id`);";
    $this->addQuery($query);

		$this->makeRevision("0.21");
		$query = "RENAME TABLE  `plageVacances` TO  `plageconge`;";
		$this->addQuery($query);
		
    $this->mod_version = "0.22";
		
  }
}
    
