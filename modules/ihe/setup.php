<?php 
/**
 * Setup IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupihe extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "ihe";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");
    
    $query = "CREATE TABLE `exchange_ihe` (
                `exchange_ihe_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `version` VARCHAR (255),
                `nom_fichier` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `date_production` DATETIME NOT NULL,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP'),
                `receiver_id` INT (11) UNSIGNED,
                `type` VARCHAR (255),
                `sous_type` VARCHAR (255),
                `date_echange` DATETIME,
                `message_content_id` INT (11) UNSIGNED,
                `acquittement_content_id` INT (11) UNSIGNED,
                `statut_acquittement` VARCHAR (255),
                `message_valide` ENUM ('0','1') DEFAULT '0',
                `acquittement_valide` ENUM ('0','1') DEFAULT '0',
                `id_permanent` VARCHAR (255),
                `object_id` INT (11) UNSIGNED,
                `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `exchange_ihe` 
                ADD INDEX (`group_id`),
                ADD INDEX (`date_production`),
                ADD INDEX (`sender_id`),
                ADD INDEX (`receiver_id`),
                ADD INDEX (`date_echange`),
                ADD INDEX (`message_content_id`),
                ADD INDEX (`acquittement_content_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `receiver_ihe` (
                `receiver_ihe_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `receiver_ihe` 
              ADD INDEX (`group_id`);";
    $this->addQuery($query);         
     
    $this->makeRevision("0.02");
    
    $query = "CREATE TABLE `receiver_ihe_config` (
                `receiver_ihe_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED,
                `ITI30_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5',
                `ITI31_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5',
                `send_all_patients` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query); 
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD INDEX (`object_id`);";
    $this->addQuery($query); 
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `exchange_ihe` 
                ADD `code` VARCHAR (255);";
    $this->addQuery($query); 
    
    $this->makeRevision("0.04");
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `assigning_authority_namespace_id` VARCHAR (255),
                ADD `assigning_authority_universal_id` VARCHAR (255),
                ADD `assigning_authority_universal_type_id` VARCHAR (255);";
    $this->addQuery($query); 
    
    $this->makeRevision("0.05");
    
    $query = "ALTER TABLE `exchange_ihe` 
                ADD `identifiant_emetteur` VARCHAR (255);";
    $this->addQuery($query); 
    
    $this->makeRevision("0.06");
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                CHANGE `ITI30_HL7_version` `ITI30_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5','FR_2.1','FR_2.2','FR_2.3') DEFAULT '2.5',
                CHANGE `ITI31_HL7_version` `ITI31_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5','FR_2.1','FR_2.2','FR_2.3') DEFAULT '2.5';";
    $this->addQuery($query); 
    
    $this->makeRevision("0.07");
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `extension` ENUM ('FR');";
    $this->addQuery($query); 
    
    $this->makeRevision("0.08");
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                DROP `extension`;";
    
    $this->mod_version = "0.09";
  }
}

?>