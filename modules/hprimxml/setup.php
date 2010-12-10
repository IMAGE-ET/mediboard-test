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
    					`group_id` INT (11) UNSIGNED NOT NULL
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
    					`id_permanent` VARCHAR (25),
    					`object_id` INT (11) UNSIGNED DEFAULT NULL,
              `object_class` VARCHAR (255) DEFAULT NULL,
              `compressed` ENUM ('0','1') DEFAULT 0
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
    
    $this->makeRevision("0.12");
    
    $sql = "ALTER TABLE `echange_hprim` 
            ADD INDEX (`emetteur`),
            ADD INDEX (`identifiant_emetteur`),
            ADD INDEX (`destinataire`),
            ADD INDEX (`type`),
            ADD INDEX (`sous_type`),
            ADD INDEX (`statut_acquittement`),
            ADD INDEX (`message_valide`),
            ADD INDEX (`acquittement_valide`),
    				ADD INDEX (`id_permanent`),
    				ADD INDEX (`object_class`);";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.13");
    
    $sql = "ALTER TABLE `destinataire_hprim` 
              CHANGE `evenement` `message` ENUM ('pmsi','patients','stock') DEFAULT 'patients';";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.14");
    $this->setTimeLimit(3600);
    		 
    $sql = "UPDATE `echange_hprim` 
            SET `compressed` = '0' WHERE `compressed` = '1';";
    $this->addQuery($sql); 
    
    $sql = "ALTER TABLE `echange_hprim` 
              CHANGE `compressed` `purge` ENUM ('0','1') DEFAULT 0,
    					CHANGE `message` `message` MEDIUMTEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $this->setTimeLimit(3600);
    
    $sql = "INSERT INTO content_xml (`content`, `import_id`) 
              SELECT `message`, `echange_hprim_id` FROM `echange_hprim`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              DROP `message`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              ADD `message_content_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              ADD INDEX (`message_content_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE echange_hprim e 
              JOIN content_xml cx ON e.echange_hprim_id = cx.import_id
              SET  e.message_content_id = cx.content_id;";
    $this->addQuery($sql);
    
    $sql = "UPDATE content_xml
              SET import_id = NULL;";
    $this->addQuery($sql);
    
    $sql = "INSERT INTO content_xml (`content`, `import_id`) 
              SELECT `acquittement`, `echange_hprim_id` FROM `echange_hprim`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              DROP `acquittement`, 
              DROP `purge`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              ADD `acquittement_content_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
              ADD INDEX (`acquittement_content_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE echange_hprim e 
              JOIN content_xml cx ON e.echange_hprim_id = cx.import_id
              SET  e.acquittement_content_id = cx.content_id
              WHERE import_id IS NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "UPDATE content_xml
              SET import_id = NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "CREATE TABLE `destinataire_hprim_config` (
              `dest_hprim_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_id` INT (11) UNSIGNED,
              `send_sortie_prevue` ENUM ('0','1') DEFAULT '1',
              `type_sej_hospi` VARCHAR (255),
              `type_sej_ambu` VARCHAR (255),
              `type_sej_urg` VARCHAR (255),
              `type_sej_scanner` VARCHAR (255),
              `type_sej_chimio` VARCHAR (255),
              `type_sej_dialyse` VARCHAR (255)
          ) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `destinataire_hprim_config` 
             ADD INDEX (`object_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    
    $sql = "ALTER TABLE `destinataire_hprim`
             DROP `url`,
             DROP `username`,
             DROP `password`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    
    $sql = "ALTER TABLE `echange_hprim` 
             ADD `emetteur_id` INT (11) UNSIGNED AFTER `emetteur`,
             ADD `destinataire_id` INT (11) UNSIGNED AFTER `destinataire`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `echange_hprim` 
             ADD INDEX (`emetteur_id`),
             ADD INDEX (`destinataire_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `echange_hprim` 
             SET `emetteur_id` = (SELECT `destinataire_hprim`.dest_hprim_id
             FROM `destinataire_hprim` 
             WHERE `echange_hprim`.emetteur = `destinataire_hprim`.nom);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `echange_hprim` 
             SET `emetteur_id` = NULL
             WHERE `echange_hprim`.emetteur = '".CAppUI::conf("mb_id")."';";
    $this->addQuery($sql);
    
    $sql = "UPDATE `echange_hprim` 
             SET `destinataire_id` = (SELECT `destinataire_hprim`.dest_hprim_id
             FROM `destinataire_hprim` 
             WHERE `echange_hprim`.destinataire = `destinataire_hprim`.nom);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `echange_hprim` 
             SET `destinataire_id` = NULL
             WHERE `echange_hprim`.destinataire = '".CAppUI::conf("mb_id")."';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    
    $sql = "ALTER TABLE `destinataire_hprim` 
             ADD `libelle` VARCHAR (255) AFTER `nom`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    
    $sql = "ALTER TABLE `destinataire_hprim_config` 
             ADD `receive_ack` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    
    $sql = "ALTER TABLE `destinataire_hprim_config` 
             ADD `send_all_patients` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `destinataire_hprim` 
             ADD `register` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    
    $sql = "ALTER TABLE `destinataire_hprim_config` 
              CHANGE `send_all_patients` `send_all_patients` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    
    $sql = "ALTER TABLE `destinataire_hprim_config`
              ADD `send_debiteurs_venue` ENUM ('0','1') DEFAULT '1',
              ADD `send_mvt_patients` ENUM ('0','1') DEFAULT '0';"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    
    $sql = "ALTER TABLE `echange_hprim`
              CHANGE `object_class` `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation');";
    
    $this->makeRevision("0.25");
    
    $sql = "ALTER TABLE `destinataire_hprim_config`
              ADD `send_default_serv_with_type_sej` ENUM ('0','1') DEFAULT '0';"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    
    $sql = "ALTER TABLE `destinataire_hprim`
             ADD `code_appli` VARCHAR (255),
             ADD `code_acteur` VARCHAR (255),
             ADD `code_syst` VARCHAR (255);";
    $this->addQuery($sql);
    
    // Prochain upgrade supprimer les champs : destinataire et emetteur
    $this->addDependency("webservices", "0.16");
    
    $this->makeRevision("0.27");
    
    $sql = "ALTER TABLE `destinataire_hprim_config`
              ADD `type_sej_pa` VARCHAR (255),
              ADD `use_sortie_matching` ENUM ('0','1') DEFAULT '1';"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    
    $sql = "ALTER TABLE `destinataire_hprim_config`
              ADD `fully_qualified` ENUM ('0','1') DEFAULT '1';"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    
    $sql = "UPDATE `echange_soap` 
              SET `type` = 'CDestinataireHprim' 
              WHERE `type` = 'hprimxml';"; 
    $this->addQuery($sql);
    
    $sql = "UPDATE `source_soap` 
              SET `type_echange` = 'CDestinataireHprim' 
              WHERE `type_echange` = 'hprimxml';"; 
    $this->addQuery($sql);
    
    $this->mod_version = "0.30";
  }
}

?>