<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The setup for the dicom module
 */
class CSetupdicom extends CSetup {

  /**
   * The constructor
   *
   * @return \CSetupdicom
   */
  function __construct() {
    parent::__construct();

    $this->mod_name = "dicom";
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `dicom_sender` (
                `dicom_sender_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `user_id` INT (11) UNSIGNED,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0',
                `save_unsupported_message` ENUM ('0','1') DEFAULT '1',
                `create_ack_file` ENUM ('0','1') DEFAULT '1',
                `delete_file` ENUM ('0','1') DEFAULT '1'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `dicom_source` (
                `dicom_source_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `role` ENUM ('prod', 'qualif') NOT NULL DEFAULT 'qualif',
                `host` TEXT NOT NULL,
                `type_echange` VARCHAR (255),
                `active` ENUM ('0','1') NOT NULL DEFAULT '1',
                `loggable` ENUM ('0','1') NOT NULL DEFAULT '1',
                `port` INT (11) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `dicom_session` (
                `dicom_session_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `receiver` VARCHAR (255) NOT NULL,
                `sender` VARCHAR (255) NOT NULL,
                `begin_date` DATETIME NOT NULL,
                `end_date` DATETIME,
                `messages` TEXT,
                `status` ENUM('Rejected', 'Completed', 'Aborted'),
                `group_id` INT (11) UNSIGNED,
                `sender_id` INT (11) UNSIGNED,
                `receiver_id` INT (11) UNSIGNED,
                `dicom_exchange_id` INT (11) UNSIGNED,
                `state` VARCHAR (255) NOT NULL,
                `presentation_contexts` VARCHAR (255)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `dicom_session`
                ADD INDEX (`begin_date`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `dicom_exchange` (
                `dicom_exchange_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `requests` TEXT NOT NULL,
                `responses` TEXT NOT NULL,
                `presentation_contexts` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `date_production` DATETIME NOT NULL,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CDicomSender'),
                `receiver_id` INT (11) UNSIGNED,
                `type` VARCHAR (255),
                `sous_type` VARCHAR (255),
                `date_echange` DATETIME,
                `statut_acquittement` VARCHAR (255),
                `message_valide` ENUM ('0','1') DEFAULT '0',
                `acquittement_valide` ENUM ('0','1') DEFAULT '0',
                `id_permanent` VARCHAR (255),
                `object_id` INT (11) UNSIGNED,
                `object_class` VARCHAR (80)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `dicom_exchange`
                ADD INDEX (`date_production`);";
    
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    
//    $query = "CREATE TABLE `dicom_table_entry` (
//                `dicom_table_entry_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
//                `group_number` INT (11) NOT NULL,
//                `element_number` INT (11) NOT NULL,
//                `mb_object_class` VARCHAR (255) NOT NULL,
//                `mb_object_attr` VARCHAR (255) NOT NULL,
//                `group_id` INT (11) NOT NULL
//              ) /*! ENGINE=MyISAM */;";
//    $this->addQuery($query);

    $query = "ALTER TABLE `dicom_exchange`
                MODIFY `requests` TEXT,
                MODIFY `responses` TEXT;";
    $this->addQuery($query);

    $this->makeRevision('0.2');

    $query = "ALTER TABLE `dicom_session`
                MODIFY `presentation_contexts` TEXT;";
    $this->addQuery($query);

    $this->makeRevision('0.3');

    $query = "ALTER TABLE `dicom_exchange`
                MODIFY `presentation_contexts` TEXT;";
    $this->addQuery($query);

    $this->makeRevision('0.4');

    $query = "ALTER TABLE `dicom_source`
                ADD `user` VARCHAR (255),
                ADD `password` VARCHAR (50),
                ADD `iv` VARCHAR (255),
                ADD `libelle` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision('0.5');

    $query = "CREATE TABLE `dicom_configs` (
                `dicom_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CDicomSender'),
                `send_0032_1032` ENUM('0', '1') DEFAULT '0',
                `value_0008_0060` VARCHAR(100)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->mod_version = '0.6';
  }
}