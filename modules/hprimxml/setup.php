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

    $query = "CREATE TABLE `destinataire_hprim` (
              `dest_hprim_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nom` VARCHAR (255) NOT NULL,
              `type` ENUM ('cip','sip') NOT NULL DEFAULT 'cip',
              `url` TEXT NOT NULL,
              `username` VARCHAR (255) NOT NULL,
              `password` VARCHAR (50) NOT NULL,
              `actif` ENUM ('0','1') NOT NULL DEFAULT '0',
              `group_id` INT (11) UNSIGNED NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `destinataire_hprim`
            ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $query = "CREATE TABLE `echange_hprim` (
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
              `compressed` ENUM ('0','1') DEFAULT '0'
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              ADD INDEX (`date_production`),
              ADD INDEX (`date_echange`),
              ADD INDEX (`initiateur_id`),
              ADD INDEX (`group_id`),
              ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.11");

    $query = "ALTER TABLE `destinataire_hprim`
              ADD `evenement` ENUM ('pmsi','patients','stock') DEFAULT 'patients';";
    $this->addQuery($query);

    $this->makeRevision("0.12");

    $query = "ALTER TABLE `echange_hprim`
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
    $this->addQuery($query);

    $this->makeRevision("0.13");

    $query = "ALTER TABLE `destinataire_hprim`
              CHANGE `evenement` `message` ENUM ('pmsi','patients','stock') DEFAULT 'patients';";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $this->setTimeLimit(3600);

    $query = "UPDATE `echange_hprim`
            SET `compressed` = '0' WHERE `compressed` = '1';";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              CHANGE `compressed` `purge` ENUM ('0','1') DEFAULT '0',
              CHANGE `message` `message` MEDIUMTEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.15");
    $this->setTimeLimit(3600);

    $query = "INSERT INTO content_xml (`content`, `import_id`)
              SELECT `message`, `echange_hprim_id` FROM `echange_hprim`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              DROP `message`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              ADD `message_content_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              ADD INDEX (`message_content_id`);";
    $this->addQuery($query);

    $query = "UPDATE echange_hprim e
              JOIN content_xml cx ON e.echange_hprim_id = cx.import_id
              SET  e.message_content_id = cx.content_id;";
    $this->addQuery($query);

    $query = "UPDATE content_xml
              SET import_id = NULL;";
    $this->addQuery($query);

    $query = "INSERT INTO content_xml (`content`, `import_id`)
              SELECT `acquittement`, `echange_hprim_id` FROM `echange_hprim`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              DROP `acquittement`,
              DROP `purge`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              ADD `acquittement_content_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
              ADD INDEX (`acquittement_content_id`);";
    $this->addQuery($query);

    $query = "UPDATE echange_hprim e
              JOIN content_xml cx ON e.echange_hprim_id = cx.import_id
              SET  e.acquittement_content_id = cx.content_id
              WHERE import_id IS NOT NULL;";
    $this->addQuery($query);

    $query = "UPDATE content_xml
              SET import_id = NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "CREATE TABLE `destinataire_hprim_config` (
              `dest_hprim_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_id` INT (11) UNSIGNED,
              `send_sortie_prevue` ENUM ('0','1') DEFAULT '1',
              `type_sej_hospi` VARCHAR (255),
              `type_sej_ambu` VARCHAR (255),
              `type_sej_urg` VARCHAR (255),
              `type_sej_scanner` VARCHAR (255),
              `type_sej_chimio` VARCHAR (255),
              `type_sej_dialyse` VARCHAR (255)
          ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `destinataire_hprim_config`
             ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.17");

    $query = "ALTER TABLE `destinataire_hprim`
             DROP `url`,
             DROP `username`,
             DROP `password`;";
    $this->addQuery($query);

    $this->makeRevision("0.18");

    $query = "ALTER TABLE `echange_hprim`
             ADD `emetteur_id` INT (11) UNSIGNED AFTER `emetteur`,
             ADD `destinataire_id` INT (11) UNSIGNED AFTER `destinataire`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
             ADD INDEX (`emetteur_id`),
             ADD INDEX (`destinataire_id`);";
    $this->addQuery($query);

    $query = "UPDATE `echange_hprim`
             SET `emetteur_id` = (SELECT `destinataire_hprim`.dest_hprim_id
             FROM `destinataire_hprim`
             WHERE `echange_hprim`.emetteur = `destinataire_hprim`.nom);";
    $this->addQuery($query);

    $query = "UPDATE `echange_hprim`
             SET `emetteur_id` = NULL
             WHERE `echange_hprim`.emetteur = '".CAppUI::conf("mb_id")."';";
    $this->addQuery($query);

    $query = "UPDATE `echange_hprim`
             SET `destinataire_id` = (SELECT `destinataire_hprim`.dest_hprim_id
             FROM `destinataire_hprim`
             WHERE `echange_hprim`.destinataire = `destinataire_hprim`.nom);";
    $this->addQuery($query);

    $query = "UPDATE `echange_hprim`
             SET `destinataire_id` = NULL
             WHERE `echange_hprim`.destinataire = '".CAppUI::conf("mb_id")."';";
    $this->addQuery($query);

    $this->makeRevision("0.19");

    $query = "ALTER TABLE `destinataire_hprim`
             ADD `libelle` VARCHAR (255) AFTER `nom`;";
    $this->addQuery($query);

    $this->makeRevision("0.20");

    $query = "ALTER TABLE `destinataire_hprim_config`
             ADD `receive_ack` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.21");

    $query = "ALTER TABLE `destinataire_hprim_config`
             ADD `send_all_patients` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $query = "ALTER TABLE `destinataire_hprim`
             ADD `register` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.22");

    $query = "ALTER TABLE `destinataire_hprim_config`
              CHANGE `send_all_patients` `send_all_patients` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.23");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `send_debiteurs_venue` ENUM ('0','1') DEFAULT '1',
              ADD `send_mvt_patients` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.24");

    $query = "ALTER TABLE `echange_hprim`
              CHANGE `object_class` `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation');";

    $this->makeRevision("0.25");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `send_default_serv_with_type_sej` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.26");

    $query = "ALTER TABLE `destinataire_hprim`
             ADD `code_appli` VARCHAR (255),
             ADD `code_acteur` VARCHAR (255),
             ADD `code_syst` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.27");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `type_sej_pa` VARCHAR (255),
              ADD `use_sortie_matching` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.28");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `fully_qualified` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.29");

    $query = "UPDATE `echange_soap`
              SET `type` = 'CDestinataireHprim'
              WHERE `type` = 'hprimxml';";
    $this->addQuery($query, true);

    $query = "UPDATE `source_soap`
              SET `type_echange` = 'CDestinataireHprim'
              WHERE `type_echange` = 'hprimxml';";
    $this->addQuery($query, true);

    $this->makeRevision("0.30");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `type_sej_exte` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.31");

    $query = "ALTER TABLE `echange_hprim`
              DROP `emetteur`,
              DROP `destinataire`,
              DROP INDEX `message_content_id`,
              DROP INDEX `acquittement_content_id`;";
    $this->addQuery($query);

    $this->makeRevision("0.32");

    $query = "ALTER TABLE `echange_hprim`
                CHANGE `destinataire_id` `receiver_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.33");

    $query = "ALTER TABLE `echange_hprim`
                ADD `sender_id` INT (11) UNSIGNED AFTER `emetteur_id`,
                ADD `sender_class` ENUM ('CSenderFTP','CSenderSOAP') AFTER `sender_id`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echange_hprim`
                ADD INDEX (`sender_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.34");

    $query = "CREATE TABLE `hprimxml_config` (
                `hprimxml_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP'),
                `type_sej_hospi` VARCHAR (255),
                `type_sej_ambu` VARCHAR (255),
                `type_sej_urg` VARCHAR (255),
                `type_sej_exte` VARCHAR (255),
                `type_sej_scanner` VARCHAR (255),
                `type_sej_chimio` VARCHAR (255),
                `type_sej_dialyse` VARCHAR (255),
                `type_sej_pa` VARCHAR (255),
                `use_sortie_matching` ENUM ('0','1') DEFAULT '1',
                `fully_qualified` ENUM ('0','1') DEFAULT '1'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `hprimxml_config`
              ADD INDEX (`sender_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.35");

    $query = "ALTER TABLE `echange_hprim`
                ADD INDEX (`message_content_id`),
                ADD INDEX (`acquittement_content_id`),
                DROP INDEX `emetteur_id`;";
    $this->addQuery($query);

    $this->makeRevision("0.36");

    $query = "ALTER TABLE `destinataire_hprim_config`
              ADD `uppercase_fields` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.37");

    $query = "ALTER TABLE `echange_hprim`
                CHANGE `sender_class` `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderMLLP');";
    $this->addQuery($query);

    $this->makeRevision("0.38");

    $query = "ALTER TABLE `echange_hprim`
                CHANGE `sender_class` `sender_class` ENUM ('CSenderFTP','CSenderSOAP');";
    $this->addQuery($query);

    $this->makeRevision("0.39");

    $query = "ALTER TABLE `echange_hprim`
                CHANGE `sender_class` `sender_class` VARCHAR (80);";
    $this->addQuery($query);

    $this->makeRevision("0.40");

    $query = "ALTER TABLE `destinataire_hprim_config`
                ADD `encoding` ENUM ('UTF-8','ISO-8859-1') DEFAULT 'UTF-8';";
    $this->addQuery($query);

    $query = "ALTER TABLE `destinataire_hprim_config`
                DROP `send_debiteurs_venue`,
                DROP `send_mvt_patients`";
    $this->addQuery($query);

    $this->makeRevision("0.41");

    $query = "ALTER TABLE `hprimxml_config`
                ADD `encoding` ENUM ('UTF-8','ISO-8859-1') DEFAULT 'UTF-8';";
    $this->addQuery($query);

    $this->makeRevision("0.42");

    $query = "ALTER TABLE `hprimxml_config`
                CHANGE `sender_class` `sender_class` VARCHAR (80);";
    $this->addQuery($query);

    $this->makeRevision("0.43");

    $query = "ALTER TABLE `hprimxml_config`
                ADD `repair_patient` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.44");

    $query = "ALTER TABLE `hprimxml_config`
                ADD `purge_idex_movements` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.45");

    $query = "ALTER TABLE `echange_hprim`
                ADD `reprocess` TINYINT (4) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.46");

    $query = "ALTER TABLE `destinataire_hprim_config`
                ADD `send_volet_medical` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.47");

    $query = "ALTER TABLE `destinataire_hprim_config`
                ADD `send_movement_location` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.48");

    $query = "ALTER TABLE `destinataire_hprim`
                ADD `OID` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.49");

    $query = "ALTER TABLE `destinataire_hprim`
                ADD `display_errors` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.50");

    $query = "ALTER TABLE `hprimxml_config`
                ADD `display_errors` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->mod_version = "0.51";
  }
}