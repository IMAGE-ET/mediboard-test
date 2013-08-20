<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage xds
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * XDS Setup class
 */
class CSetupxds extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "xds";
    $this->makeRevision("all");
    $this->makeRevision("0.01");

    $query = "CREATE TABLE `exchange_xds` (
                `exchange_xds_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `identifiant_emetteur` VARCHAR (255),
                `initiateur_id` INT(11)  NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `date_production` DATETIME NOT NULL,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderFileSystem'),
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
                `object_class` ENUM ('CSejour','COperation','CConsultation'),
                `reprocess` TINYINT (4) UNSIGNED DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `exchange_xds`
                ADD INDEX (`group_id`),
                ADD INDEX (`date_production`),
                ADD INDEX (`sender_id`),
                ADD INDEX (`receiver_id`),
                ADD INDEX (`date_echange`),
                ADD INDEX (`message_content_id`),
                ADD INDEX (`acquittement_content_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $query = "CREATE TABLE `receiver_xds` (
    `receiver_xds_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `OID` VARCHAR (255),
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `receiver_xds`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->mod_version = "0.02";
  }
}
