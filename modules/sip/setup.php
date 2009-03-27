<?php /* $Id: */
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
     
     $this->mod_version = "0.12";
  }
}
?>