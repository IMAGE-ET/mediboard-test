<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupsip extends CSetup {
  
  function __construct() {
      parent::__construct();
    
      $this->mod_name = "sip";
      $this->makeRevision("all");
      
      $this->makeRevision("0.11");
      
      $sql = "CREATE TABLE `destinataire_hprim` (
                `dest_hprim_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `destinataire` VARCHAR (255) NOT NULL,
                `type` ENUM ('cip','sip') NOT NULL DEFAULT 'cip',
                `url` TEXT NOT NULL,
                `username` VARCHAR (255) NOT NULL,
                `password` VARCHAR (50) NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT 0
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
     
     $sql = "CREATE TABLE `echange_hprim` (
                `echange_hprim_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `date_production` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `emetteur` VARCHAR (255) NOT NULL,
                `identifiant_emetteur` INT (11) UNSIGNED,
                `destinataire` VARCHAR (255) NOT NULL,
                `type` VARCHAR (255) NOT NULL,
                `sous_type` VARCHAR (255),
                `date_echange` DATETIME,
                `message` MEDIUMTEXT NOT NULL,
                `acquittement` MEDIUMTEXT,
                `initiateur_id` INT (11) UNSIGNED
              ) TYPE=MYISAM;";
     $this->addQuery($sql);
     
     $sql = "ALTER TABLE `echange_hprim` 
							  ADD INDEX (`date_production`),
							  ADD INDEX (`date_echange`),
							  ADD INDEX (`initiateur_id`);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.12");
     
     $sql = "ALTER TABLE `echange_hprim` 
							  CHANGE `emetteur` `emetteur` VARCHAR (255),
							  CHANGE `identifiant_emetteur` `identifiant_emetteur` VARCHAR (255),
							  CHANGE `type` `type` VARCHAR (255);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.13");
     
     $sql = "ALTER TABLE `echange_hprim` 
                ADD `statut_acquittement` VARCHAR (255);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.14");
     
     $sql = "ALTER TABLE `echange_hprim` 
							  ADD `message_valide` ENUM ('0','1'),
							  ADD `acquittement_valide` ENUM ('0','1');";
     $this->addQuery($sql);
     
     $this->makeRevision("0.15");
     
     $sql = "ALTER TABLE `destinataire_hprim` 
              ADD `group_id` INT (11) UNSIGNED NOT NULL ,
              CHANGE `password` `password` VARCHAR (50) , 
              ADD INDEX (`group_id`);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.16");
     
     $sql = "ALTER TABLE `echange_hprim` 
              ADD `group_id` INT (11) UNSIGNED NOT NULL ,
              ADD INDEX (`group_id`);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.17");
     
     $sql = "ALTER TABLE `destinataire_hprim` 
              CHANGE `destinataire` `nom` VARCHAR (255) NOT NULL;";
              
     $this->addQuery($sql);
     
     $this->makeRevision("0.18");
     
     $sql = "ALTER TABLE `echange_hprim` 
              ADD `id_permanent` INT (11) UNSIGNED zerofill;";
              
     $this->addQuery($sql);
     
     $this->makeRevision("0.18");
     
     $sql = "ALTER TABLE `echange_hprim` 
              CHANGE `id_permanent` `id_permanent` VARCHAR (25);";
              
     $this->addQuery($sql);
              
     $this->mod_version = "0.19";
  }
}
?>