<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphprimxml extends CSetup {
  
  function __construct() {
      parent::__construct();
    
      $this->mod_name = "hprimxml";
      $this->makeRevision("all");
      $this->makeRevision("0.10");
     
      $sql = "CREATE TABLE `destinataire_hprim` (
                `dest_hprim_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `type` ENUM ('cip','sip') NOT NULL DEFAULT 'cip',
                `url` TEXT NOT NULL,
                `username` VARCHAR (255) NOT NULL,
                `password` VARCHAR (50) NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT 0,
								`group_id` INT (11) UNSIGNED NOT NULL ,
                `password` `password` VARCHAR (50) , 
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
     
		 $sql = "ALTER TABLE `destinataire_hprim` 
              ADD INDEX (`group_id`);";
     $this->addQuery($sql);
		 
     $sql = "CREATE TABLE `echange_hprim` (
                `echange_hprim_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `date_production` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `emetteur` VARCHAR (255),
                `identifiant_emetteur` VARCHAR (255),
                `destinataire` VARCHAR (255) NOT NULL,
                `type` VARCHAR (255),
                `sous_type` VARCHAR (255),
                `date_echange` DATETIME,
                `message` MEDIUMTEXT NOT NULL,
                `acquittement` MEDIUMTEXT,
                `initiateur_id` INT (11) UNSIGNED,
								`statut_acquittement` VARCHAR (255),
								`message_valide` ENUM ('0','1'),
                `acquittement_valide` ENUM ('0','1'),
								`group_id` INT (11) UNSIGNED NOT NULL,
								`id_permanent` VARCHAR (25)
								`object_id` INT (11) UNSIGNED DEFAULT NULL,
                `object_class` VARCHAR (255) DEFAULT NULL,
                `compressed` ENUM ('0','1') DEFAULT 0,
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
		 
		 $sql = "ALTER TABLE `echange_hprim` 
                ADD INDEX (`date_production`),
                ADD INDEX (`date_echange`),
                ADD INDEX (`initiateur_id`),
								ADD INDEX (`group_id`),
								ADD INDEX (`object_id`);";
     $this->addQuery($sql);
     
		 $this->makeRevision("0.11");
		  
		 $sql = "ALTER TABLE `destinataire_hprim` 
                ADD `evenement` ENUM ('pmsi','patients','stock') DEFAULT 'patients';";
     $this->addQuery($sql);	
			
     $this->mod_version = "0.12";
  }
}

?>