<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Setup du module
 **/
class CSetupdPccam extends CSetup {

  protected function updateDateCodage() {
    $ds = $this->ds;

    $query = "SELECT * FROM `codage_ccam`;";
    $rows = $ds->exec($query);
    while ($_codage = $ds->fetchObject($rows, 'CCodageCCAM')) {
      $_codage->loadCodable();

      $date = null;
      switch ($_codage->codable_class) {
        case 'CConsultation':
          $_codage->_ref_codable->loadRefPlageConsult();
          $date = $_codage->_ref_codable->_date;
          break;
        case 'COperation':
          $date = $_codage->_ref_codable->date;
          break;
        case 'CSejour':
          $date = CMbDT::date('', $_codage->_ref_codable->entree);
          break;
      }

      $query = "UPDATE `codage_ccam`
                  SET `date` = '$date' WHERE `codage_ccam_id` = $_codage->_id;";

      $ds->exec($query);
    }

    return true;
  }
  /**
   * Construct
   **/
  function __construct() {
    parent::__construct();

    $this->mod_name = "dPccam";

    $this->makeRevision("all");
    $query = "CREATE TABLE `ccamfavoris` (
                `favoris_id` bigint(20) NOT NULL auto_increment,
                `favoris_user` int(11) NOT NULL default '0',
                `favoris_code` varchar(7) NOT NULL default '',
                PRIMARY KEY  (`favoris_id`)
              ) /*! ENGINE=MyISAM */ COMMENT='table des favoris'";
    $this->addQuery($query);

    $this->makeRevision("0.1");
    $query = "ALTER TABLE `ccamfavoris` 
                CHANGE `favoris_id` `favoris_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `favoris_user` `favoris_user` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.11");
    $query = "ALTER TABLE `ccamfavoris`
                ADD `object_class` VARCHAR(25) NOT NULL DEFAULT 'COperation';";
    $this->addQuery($query);

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ccamfavoris` 
                ADD INDEX (`favoris_user`);";
    $this->addQuery($query);

    $this->makeRevision("0.13");
    $query = "CREATE TABLE `frais_divers` (
                `frais_divers_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `type_id` INT (11) UNSIGNED NOT NULL,
                `coefficient` FLOAT NOT NULL DEFAULT '1',
                `quantite` INT (11) UNSIGNED,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0',
                `montant_depassement` DECIMAL  (10,3),
                `montant_base` DECIMAL  (10,3),
                `executant_id` INT (11) UNSIGNED NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` VARCHAR (255) NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `frais_divers` 
                ADD INDEX (`type_id`),
                ADD INDEX (`executant_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $query = "CREATE TABLE `frais_divers_type` (
                `frais_divers_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (16) NOT NULL,
                `libelle` VARCHAR (255) NOT NULL,
                `tarif` DECIMAL (10,3) NOT NULL,
                `facturable` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $this->addPrefQuery("new_search_ccam", "1");

    $this->makeRevision("0.15");

    $query = "ALTER TABLE `frais_divers` 
                CHANGE `facturable` `facturable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.16");

    $this->addPrefQuery("multiple_select_ccam", "0");

    $this->makeRevision("0.17");

    $this->addPrefQuery("user_executant", "0");

    $this->makeRevision("0.18");
    $this->addDependency("dPcabinet", "0.1");
    $this->addDependency("dPplanningOp", "1.07");

    $query = "ALTER TABLE `frais_divers`
                ADD `execution` DATETIME NOT NULL;";

    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `consultation` ON (`frais_divers`.`object_id` = `consultation`.`consultation_id`)
                INNER JOIN `plageconsult` ON (`consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`)
                SET `frais_divers`.`execution` = CONCAT(`plageconsult`.`date`, ' ', `consultation`.`heure`)
                WHERE `frais_divers`.`object_class` = 'CConsultation';";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `operations` ON (`frais_divers`.`object_id` = `operations`.`operation_id`)
                INNER JOIN `plagesop` ON (`operations`.`plageop_id` = `plagesop`.`plageop_id`)
                SET `frais_divers`.`execution` = CONCAT(`plagesop`.`date`, ' ', `operations`.`time_operation`)
                WHERE `frais_divers`.`object_class` = 'COperation'
                AND `operations`.`date` IS NULL;";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `operations` ON (`frais_divers`.`object_id` = `operations`.`operation_id`)
                SET `frais_divers`.`execution` = CONCAT(`operations`.`date`, ' ', `operations`.`time_operation`)
                WHERE `frais_divers`.`object_class` = 'COperation'
                AND `operations`.`date` IS NOT NULL;";
    $this->addQuery($query);

    $query = "UPDATE `frais_divers`
                INNER JOIN `sejour` ON (`frais_divers`.`object_id` = `sejour`.`sejour_id`)
                SET `frais_divers`.`execution` = `sejour`.`entree`
                WHERE `frais_divers`.`object_class` = 'CSejour';";
    $this->addQuery($query);
    $this->makeRevision("0.19");

    $query = "ALTER TABLE `frais_divers`
                ADD `num_facture` INT (11) UNSIGNED NOT NULL DEFAULT '1'";
    $this->addQuery($query);

    $query = "ALTER TABLE `frais_divers`
                ADD INDEX (`execution`),
                ADD INDEX (`object_class`);";
    $this->addQuery($query);

    $this->makeRevision("0.20");

    $query = "ALTER TABLE `acte_ccam`
                ADD `position_dentaire` VARCHAR (255),
                ADD `numero_forfait_technique` INT (11) UNSIGNED,
                ADD `numero_agrement` BIGINT (20) UNSIGNED,
                ADD `rapport_exoneration` ENUM ('4','7','C','R');";
    $this->addQuery($query);

    $this->makeRevision('0.21');

    $query = "CREATE TABLE `codage_ccam` (
       `codage_ccam_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
       `association_rule` ENUM('G1', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG1', 'EG2',
       'EG3', 'EG4', 'EG5', 'EG6', 'EG7', 'EH', 'EI', 'GA', 'GB', 'G2'),
       `association_mode` ENUM('auto', 'user_choice') DEFAULT 'auto',
       `codable_class` ENUM('CConsultation', 'CSejour', 'COperation') NOT NULL,
       `codable_id` INT (11) UNSIGNED NOT NULL,
       `praticien_id` INT (11) UNSIGNED NOT NULL,
       `locked` ENUM('0', '1') NOT NULL DEFAULT '0'
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `codage_ccam`
      ADD INDEX (`codable_class`, `codable_id`),
      ADD INDEX (`praticien_id`),
      ADD UNIQUE INDEX  (`codable_class`, `codable_id`, `praticien_id`);";
    $this->addQuery($query);

    $this->makeRevision('0.22');

    $query = "ALTER TABLE `codage_ccam`
      ADD `nb_acts` INT (2) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision('0.23');

    $query = "ALTER TABLE `codage_ccam`
      CHANGE `association_rule` `association_rule` ENUM('G1', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG1', 'EG2',
       'EG3', 'EG4', 'EG5', 'EG6', 'EG7', 'EH', 'EI', 'GA', 'GB', 'G2', 'M');";
    $this->addQuery($query);

    $this->makeRevision('0.24');

    $query = "ALTER TABLE  `codage_ccam`
      DROP `nb_acts`;";
    $this->addQuery($query);

    $this->makeRevision('0.25');

    $query = "ALTER TABLE `codage_ccam` DROP INDEX `codable_class_2`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `codage_ccam`
      ADD `activite_anesth` ENUM('0', '1') NOT NULL DEFAULT '0',
      ADD UNIQUE INDEX uk_codage_ccam (`codable_class`, `codable_id`, `praticien_id`, `activite_anesth`);";
    $this->addQuery($query);

    $this->makeRevision('0.26');

    $this->addPrefQuery('actes_comp_supp_favoris', '1');

    $query = "ALTER TABLE `acte_ccam`
                ADD `accord_prealable` ENUM ('0', '1') DEFAULT '0',
                ADD `date_demande_accord` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision('0.27');

    $query = "ALTER TABLE `codage_ccam`
      ADD `date` DATE NOT NULL,
      DROP INDEX uk_codage_ccam;";
    $this->addQuery($query);

    $query = "ALTER TABLE `frais_divers`
                ADD `gratuit` ENUM('0', '1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->addMethod('updateDateCodage');

    $this->makeRevision('0.28');

    $query = "CREATE TABLE `devis_codage` (
      `devis_codage_id` INT(11) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
      `codable_class` ENUM('CConsultation', 'COperation') NOT NULL,
      `codable_id` INT(11) UNSIGNED NOT NULL,
      `patient_id` INT(11) UNSIGNED NOT NULL,
      `praticien_id` INT(11) UNSIGNED NOT NULL,
      `creation_date` DATETIME NOT NULL,
      `date` DATE,
      `event_type` ENUM('CConsultation', 'COperation') DEFAULT 'CConsultation',
      `libelle` VARCHAR (255),
      `comment` TEXT,
      `codes_ccam` VARCHAR(255),
      `facture` ENUM ('0','1') DEFAULT '0',
      `tarif` VARCHAR(50),
      `exec_tarif` DATETIME,
      `consult_related_id` INT (11) UNSIGNED,
      `base` FLOAT(6),
      `dh` FLOAT(6),
      `ht` FLOAT(6),
      `tax_rate` FLOAT
    );";
    $this->addQuery($query);

    $query = "ALTER TABLE `devis_codage`
                ADD INDEX (`codable_class`),
                ADD INDEX (`codable_id`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `codage_ccam`
                CHANGE `codable_class` `codable_class` ENUM('CConsultation', 'CSejour', 'COperation', 'CDevisCodage') NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision('0.29');

    $this->addPrefQuery('precode_modificateur_7', CAppUi::conf('dPccam CCodable precode_modificateur_7'));
    $this->addPrefQuery('precode_modificateur_J', CAppUi::conf('dPccam CCodable precode_modificateur_J'));

    $this->mod_version = '0.30';

    // Data source query

    // Nouvelle version CCAM
    $query = "SHOW TABLES LIKE 'p_acte'";
    $this->addDatasource("ccamV2", $query);

    // Tarifs de convergence
    if (array_key_exists('ccamV2', CAppUI::conf('db'))) {
      $dsn = CSQLDataSource::get('ccamV2');
      if ($dsn->fetchRow($dsn->exec('SHOW TABLES LIKE \'convergence\';'))) {
        // Nouvelle table de convergence
        $query = "SELECT COUNT(*) FROM `convergence` HAVING COUNT(*) = 3761;";
        $this->addDatasource("ccamV2", $query);
      }
      else {
        $query = "SHOW TABLES LIKE 'convergence'";
        $this->addDatasource("ccamV2", $query);
      }
    }

    // Tarifs NGAP
    $query = "SHOW TABLES LIKE 'tarif_ngap';";
    $this->addDatasource("ccamV2", $query);

    if (array_key_exists('ccamV2', CAppUI::conf('db'))) {
      $dsn = CSQLDataSource::get('ccamV2');
      if ($dsn->fetchRow($dsn->exec('SHOW TABLES LIKE \'tarif_ngap\';'))) {
        // Suppression des actes CNP et VNP (codes exacts CNPSY et VNPSY)
        $query = "SELECT * FROM `tarif_ngap` WHERE `code` = 'CNPSY';";
        $this->addDatasource("ccamV2", $query);
      }
    }

    // Nouvelle architecture CCAM
    $query = "SHOW TABLES LIKE 'p_acte';";
    $this->addDatasource("ccamV2", $query);

    // Version 39 de la CCAM
    $table = "p_phase_acte";
    $column = "PRIXUNITAIRE2";
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $this->addDatasource("ccamV2", $query);
  }
}
