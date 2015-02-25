<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPplanningOp extends CSetup {

  protected function addDefaultConfigCIP() {
    $path = 'dPplanningOp CSejour use_charge_price_indicator';

    if (@CAppUI::conf($path)) {
      $query = 'INSERT INTO `configuration` (`feature`, `value`) VALUES (?1, ?2);';
      $query = $this->ds->prepare($query, $path, 'obl');
      $this->addQuery($query);
    }
  }

  function __construct() {
    parent::__construct();

    $this->mod_name = "dPplanningOp";

    $this->makeRevision("all");
    $query = "CREATE TABLE operations (
      operation_id BIGINT(20) UNSIGNED NOT NULL auto_increment,
      pat_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
      chir_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
      plageop_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
      CIM10_code VARCHAR(5) DEFAULT NULL,
      CCAM_code VARCHAR(7) DEFAULT NULL,
      cote ENUM('droit','gauche','bilatéral','total') NOT NULL DEFAULT 'total',
      temp_operation TIME NOT NULL DEFAULT '00:00:00',
      time_operation TIME NOT NULL DEFAULT '00:00:00',
      examen TEXT,
      materiel TEXT,
      commande_mat ENUM('o', 'n') NOT NULL DEFAULT 'n',
      info ENUM('o','n') NOT NULL DEFAULT 'n',
      date_anesth date NOT NULL DEFAULT '0000-00-00',
      time_anesth TIME NOT NULL DEFAULT '00:00:00',
      type_anesth tinyint(4) DEFAULT NULL,
      date_adm date NOT NULL DEFAULT '0000-00-00',
      time_adm TIME NOT NULL DEFAULT '00:00:00',
      duree_hospi tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
      type_adm ENUM('comp','ambu','exte') DEFAULT 'comp',
      chambre ENUM('o','n') NOT NULL DEFAULT 'o',
      ATNC ENUM('o','n') NOT NULL DEFAULT 'n',
      rques TEXT,
      rank tinyint(4) NOT NULL DEFAULT '0',
      admis ENUM('n','o') NOT NULL DEFAULT 'n',
      PRIMARY KEY  (operation_id),
      UNIQUE KEY operation_id (operation_id)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.1");
    $query = "ALTER TABLE operations
      ADD entree_bloc TIME AFTER temp_operation ,
      ADD sortie_bloc TIME AFTER entree_bloc ,
      ADD saisie ENUM( 'n', 'o' ) DEFAULT 'n' NOT NULL ,
      CHANGE plageop_id plageop_id BIGINT( 20 ) UNSIGNED";
    $this->addQuery($query);

    $this->makeRevision("0.2");
    $query = "ALTER TABLE `operations`
      ADD `convalescence` TEXT AFTER `materiel` ;";
    $this->addQuery($query);

    $this->makeRevision("0.21");
    $query = "ALTER TABLE `operations`
      ADD `depassement` INT( 4 );";
    $this->addQuery($query);

    $this->makeRevision("0.22");
    $query = "ALTER TABLE `operations`
      ADD `CCAM_code2` VARCHAR( 7 ) AFTER `CCAM_code`,
      ADD INDEX ( `CCAM_code2` ),
      ADD INDEX ( `CCAM_code` ),
      ADD INDEX ( `pat_id` ),
      ADD INDEX ( `chir_id` ),
      ADD INDEX ( `plageop_id` );";
    $this->addQuery($query);

    $this->makeRevision("0.23");
    $query = "ALTER TABLE `operations`
      ADD `modifiee` TINYINT DEFAULT '0' NOT NULL AFTER `saisie`,
      ADD `annulee` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);

    $this->makeRevision("0.24");
    $query = "ALTER TABLE `operations`
      ADD `compte_rendu` TEXT,
      ADD `cr_valide` TINYINT( 4 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);

    $this->makeRevision("0.25");
    $query = "ALTER TABLE `operations`
      ADD `pathologie` VARCHAR( 8 ) DEFAULT NULL,
      ADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);

    $this->makeRevision("0.26");
    $query = "ALTER TABLE `operations`
      ADD `libelle` TEXT DEFAULT NULL AFTER `CCAM_code2`;";
    $this->addQuery($query);

    // CR passage des champs à enregistrements supprimé car regressif
//    $this->makeRevision("0.27");

    $this->makeRevision("0.28");
    $query = "ALTER TABLE `operations`
      ADD `codes_ccam` VARCHAR( 160 ) AFTER `CIM10_code`";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `codes_ccam` )";
    $this->addQuery($query);

    $this->makeRevision("0.29");
    $query = "UPDATE `operations`
      SET `codes_ccam` = CONCAT_WS('|', `CCAM_code`, `CCAM_code2`)";
    $this->addQuery($query);

    $this->makeRevision("0.30");
    $query = "ALTER TABLE `operations`
      ADD `pose_garrot` TIME AFTER `entree_bloc` ,
      ADD `debut_op` TIME AFTER `pose_garrot` ,
      ADD `fin_op` TIME AFTER `debut_op` ,
      ADD `retrait_garrot` TIME AFTER `fin_op` ;";
    $this->addQuery($query);

    $this->makeRevision("0.31");
    $query = "ALTER TABLE `operations`
      ADD `salle_id` BIGINT AFTER `plageop_id` ,
      ADD `date` DATE AFTER `salle_id`;";
    $this->addQuery($query);

    $this->makeRevision("0.32");
    $query = "ALTER TABLE `operations`
      ADD `venue_SHS` VARCHAR( 8 ) AFTER `chambre`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `venue_SHS` );";
    $this->addQuery($query);

    $this->makeRevision("0.33");
    $query = "ALTER TABLE `operations`
      ADD `code_uf` VARCHAR( 3 ) AFTER `venue_SHS`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD `libelle_uf` VARCHAR( 40 ) AFTER `code_uf`;";
    $this->addQuery($query);

    $this->makeRevision("0.34");
    $query = "ALTER TABLE `operations`
      ADD `entree_reveil` TIME AFTER `sortie_bloc` ,
      ADD `sortie_reveil` TIME AFTER `entree_reveil` ;";
    $this->addQuery($query);

    $this->makeRevision("0.35");
    $query = "ALTER TABLE `operations`
      ADD `entree_adm` DATETIME AFTER `admis`;";
    $this->addQuery($query);
    $query = "UPDATE `operations` SET
      `entree_adm` = ADDTIME(date_adm, time_adm)
      WHERE `admis` = 'o'";
    $this->addQuery($query);

    $this->makeRevision("0.36");
    $this->addDependency("dPbloc", "0.15");
    // Réparation des opérations avec `duree_hospi` = '255'
    $query = "UPDATE `operations`, `plagesop` SET
      `operations`.`date_adm` = `plagesop`.`date`,
      `operations`.`duree_hospi` = '1'
      WHERE `operations`.`duree_hospi` = '255'
      AND `operations`.`plageop_id` = `plagesop`.`plageop_id`";
    $this->addQuery($query);

    // Création de la table
    $query = "CREATE TABLE `sejour` (
      `sejour_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
      `patient_id` INT UNSIGNED NOT NULL ,
      `praticien_id` INT UNSIGNED NOT NULL ,
      `entree_prevue` DATETIME NOT NULL ,
      `sortie_prevue` DATETIME NOT NULL ,
      `entree_reelle` DATETIME,
      `sortie_reelle` DATETIME,
      `chambre_seule` ENUM('o','n') NOT NULL DEFAULT 'o',
      PRIMARY KEY ( `sejour_id` )
    ) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `patient_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `praticien_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `entree_prevue` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `sortie_prevue` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `entree_reelle` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `sortie_reelle` )";
    $this->addQuery($query);

    // Migration de l'ancienne table
    $query = "ALTER TABLE `sejour`
      ADD `tmp_operation_id` INT UNSIGNED NOT NULL AFTER `sejour_id`";
    $this->addQuery($query);
    $query = "INSERT INTO `sejour` (
        `sejour_id` ,
        `tmp_operation_id` ,
        `patient_id` ,
        `praticien_id` ,
        `entree_prevue` ,
        `sortie_prevue` ,
        `entree_reelle` ,
        `sortie_reelle` ,
        `chambre_seule`
      )
      SELECT
        '',
        `operation_id`,
        `pat_id`,
        `chir_id`,
        ADDTIME(`date_adm`, `time_adm`),
        ADDDATE(ADDTIME(`date_adm`, `time_adm`), `duree_hospi`),
        `entree_adm` ,
        NULL ,
        `chambre`
      FROM `operations`
      WHERE `operations`.`pat_id` != 0";
    $this->addQuery($query);

    // Ajout d'une référence vers les sejour
    $query = "ALTER TABLE `operations`
      ADD `sejour_id` INT UNSIGNED NOT NULL AFTER `operation_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `sejour_id` )";
    $this->addQuery($query);
    $query = "UPDATE `operations`, `sejour`
      SET `operations`.`sejour_id` = `sejour`.`sejour_id`
      WHERE `sejour`.`tmp_operation_id` = `operations`.`operation_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      DROP `tmp_operation_id` ";
    $this->addQuery($query);

    $this->makeRevision("0.37");
    // Migration de nouvelles propriétés
    $query = "ALTER TABLE `sejour`
      ADD `type` ENUM( 'comp', 'ambu', 'exte' ) DEFAULT 'comp' NOT NULL AFTER `praticien_id` ,
      ADD `annule` TINYINT DEFAULT '0' NOT NULL AFTER `type` ,
      ADD `venue_SHS` VARCHAR( 8 ) AFTER `annule` ,
      ADD `saisi_SHS` ENUM( 'o', 'n' ) DEFAULT 'n' NOT NULL AFTER `venue_SHS` ,
      ADD `modif_SHS` TINYINT DEFAULT '0' NOT NULL AFTER `saisi_SHS`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `type` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `annule` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `venue_SHS` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `saisi_SHS` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `modif_SHS` )";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`type` = `operations`.`type_adm`,
      `sejour`.`annule` = `operations`.`annulee`,
      `sejour`.`venue_SHS` = `operations`.`venue_SHS`,
      `sejour`.`saisi_SHS` = `operations`.`saisie`,
      `sejour`.`modif_SHS` = `operations`.`modifiee`
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);

    $this->makeRevision("0.38");
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`entree_reelle` = NULL
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`
      AND `operations`.`admis` = 'n'";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      CHANGE `date_anesth` `date_anesth` DATE";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `date_anesth` = NULL
      WHERE `date_anesth` = '0000-00-00'";
    $this->addQuery($query);

    $this->makeRevision("0.39");
    $query = "ALTER TABLE sejour
      ADD rques TEXT;";
    $this->addQuery($query);

    $this->makeRevision("0.40");
    $query = "ALTER TABLE operations
      ADD pause TIME NOT NULL DEFAULT '00:00:00' AFTER temp_operation";
    $this->addQuery($query);

    $this->makeRevision("0.41");
    $query = "ALTER TABLE `sejour`
      ADD `pathologie` VARCHAR( 8 ) DEFAULT NULL,
      ADD `septique` TINYINT DEFAULT '0' NOT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`pathologie` = `operations`.`pathologie`,
      `sejour`.`septique` = `operations`.`septique`
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);

    $this->makeRevision("0.42");
    $query = "ALTER TABLE `sejour`
      ADD `code_uf` VARCHAR( 8 ) DEFAULT NULL AFTER venue_SHS,
      ADD `libelle_uf` TINYINT DEFAULT '0' NOT NULL AFTER code_uf;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`code_uf` = `operations`.`code_uf`,
      `sejour`.`libelle_uf` = `operations`.`libelle_uf`
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);

    $this->makeRevision("0.43");
    $query = "ALTER TABLE `sejour`
      ADD `convalescence` TEXT DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`convalescence` = `operations`.`convalescence`
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    $query = "ALTER TABLE `sejour`
      DROP `code_uf`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      DROP `libelle_uf`;";
    $this->addQuery($query);
    $query = " ALTER TABLE `sejour`
      ADD `modalite_hospitalisation` ENUM( 'office', 'libre', 'tiers' ) NOT NULL DEFAULT 'libre' AFTER `type`;";
    $this->addQuery($query);

    $this->makeRevision("0.45");
    $query = "ALTER TABLE `operations`
      DROP `entree_adm`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      DROP `admis`;";
    $this->addQuery($query);

    $this->makeRevision("0.46");
    $query = "ALTER TABLE `sejour`
      ADD `DP`  VARCHAR(5) DEFAULT NULL AFTER `rques`;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
      `sejour`.`DP` = `operations`.`CIM10_code`
      WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);

    $this->makeRevision("0.47");
    $query = "CREATE TABLE protocole (
      protocole_id INT UNSIGNED NOT NULL auto_increment,
      chir_id INT UNSIGNED NOT NULL DEFAULT '0',
      type ENUM('comp','ambu','exte') DEFAULT 'comp',
      DP VARCHAR(5) DEFAULT NULL,
      convalescence TEXT DEFAULT NULL,
      rques_sejour TEXT DEFAULT NULL,
      pathologie VARCHAR(8) DEFAULT NULL,
      septique TINYINT DEFAULT '0' NOT NULL,
      codes_ccam VARCHAR(160) DEFAULT NULL,
      libelle TEXT DEFAULT NULL,
      temp_operation TIME NOT NULL DEFAULT '00:00:00',
      examen TEXT DEFAULT NULL,
      materiel TEXT DEFAULT NULL,
      duree_hospi TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
      rques_operation TEXT DEFAULT NULL,
      depassement TINYINT DEFAULT NULL,
      PRIMARY KEY  (protocole_id)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole`
      ADD INDEX (`chir_id`)";
    $this->addQuery($query);
    $query = "INSERT INTO `protocole` (
        `protocole_id`, `chir_id`,
        `type`,
        `DP`,
        `convalescence`,
        `rques_sejour`,
        `pathologie`,
        `septique`,
        `codes_ccam`,
        `libelle`,
        `temp_operation`,
        `examen`,
        `materiel`,
        `duree_hospi`,
        `rques_operation`,
        `depassement`
      )
      SELECT
        '',
        `operations`.`chir_id`,
        `operations`.`type_adm`,
        `operations`.`CIM10_code`,
        `operations`.`convalescence`,
        '',
        '',
        '',
        `operations`.`codes_ccam`,
        `operations`.`libelle`,
        `operations`.`temp_operation`,
        `operations`.`examen`,
        `operations`.`materiel`,
        `operations`.`duree_hospi`,
        `operations`.`rques`,
        `operations`.`depassement`
       FROM `operations`
       WHERE `operations`.`pat_id` = 0";
    $this->addQuery($query);
    $query = "DELETE FROM `operations`
      WHERE `pat_id` = 0";
    $this->addQuery($query);

    $this->makeRevision("0.48");
    $query = "ALTER TABLE `sejour`
      CHANGE `modalite_hospitalisation` `modalite` ENUM( 'office', 'libre', 'tiers' ) DEFAULT 'libre' NOT NULL";
    $this->addQuery($query);

    $this->makeRevision("0.49");
    $query = "UPDATE `operations`
      SET `date` = NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.50");
    $query = "ALTER TABLE `operations`
      ADD `anesth_id` INT UNSIGNED DEFAULT NULL AFTER `chir_id`";
    $this->addQuery($query);

    $this->makeRevision("0.51");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `sejour`
      ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `praticien_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);

    $this->makeRevision("0.52");
    $query = "ALTER TABLE `operations`
      DROP INDEX `operation_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `anesth_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      DROP `pat_id`,
      DROP `CCAM_code`,
      DROP `CCAM_code2`,
      DROP `compte_rendu`,
      DROP `cr_valide`,
      DROP `date_adm`,
      DROP `time_adm`,
      DROP `chambre`,
      DROP `type_adm`,
      DROP `venue_SHS`,
      DROP `saisie`,
      DROP `modifiee`,
      DROP `CIM10_code`,
      DROP `convalescence`,
      DROP `pathologie`,
      DROP `septique` ;";
    $this->addQuery($query);

    $this->makeRevision("0.53");
    $query = "CREATE TABLE `type_anesth` (
      `type_anesth_id` INT UNSIGNED NOT NULL auto_increment,
      `name` VARCHAR(50) DEFAULT NULL,
      PRIMARY KEY  (type_anesth_id)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('1', 'Non définie');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('2', 'Rachi');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('3', 'Rachi + bloc');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('4', 'Anesthésie loco-régionale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('5', 'Anesthésie locale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('6', 'Neurolept');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('7', 'Anesthésie générale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('8', 'Anesthesie generale + bloc');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth`
      VALUES ('9', 'Anesthesie peribulbaire');";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `type_anesth`=`type_anesth`+1;";
    $this->addQuery($query);

    $this->makeRevision("0.54");
    $query = "ALTER TABLE `operations`
      ADD `induction` TIME AFTER `sortie_reveil`";
    $this->addQuery($query);

    $this->makeEmptyRevision("0.55");


    $this->makeEmptyRevision("0.56");

    $query = "ALTER TABLE `operations`
      CHANGE `operation_id` `operation_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE `sejour_id` `sejour_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `chir_id` `chir_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `anesth_id` `anesth_id` int(11) UNSIGNED NULL,
      CHANGE `plageop_id` `plageop_id` int(11) UNSIGNED NULL,
      CHANGE `code_uf` `code_uf` VARCHAR(3) NULL,
      CHANGE `libelle_uf` `libelle_uf` VARCHAR(35) NULL,
      CHANGE `salle_id` `salle_id` int(11) UNSIGNED NULL,
      CHANGE `codes_ccam` `codes_ccam` VARCHAR(255) NULL,
      CHANGE `libelle` `libelle` VARCHAR(255) NULL,
      CHANGE `type_anesth` `type_anesth` int(11) UNSIGNED NULL,
      CHANGE `rank` `rank` tinyint NOT NULL DEFAULT '0',
      CHANGE `annulee` `annulee` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` DROP `duree_hospi`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole`
      CHANGE `protocole_id` `protocole_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE `chir_id` `chir_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `pathologie` `pathologie` VARCHAR(3) NULL,
      CHANGE `codes_ccam` `codes_ccam` VARCHAR(255) NULL,
      CHANGE `libelle` `libelle` VARCHAR(255) NULL,
      CHANGE `duree_hospi` `duree_hospi` mediumint NOT NULL DEFAULT '0',
      CHANGE `septique` `septique` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      CHANGE `sejour_id` `sejour_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE `patient_id` `patient_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `praticien_id` `praticien_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `group_id` `group_id` int(11) UNSIGNED NOT NULL DEFAULT '1',
      CHANGE `venue_SHS` `venue_SHS` int(8) UNSIGNED zerofill NULL,
      CHANGE `annule` `annule` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `modif_SHS` `modif_SHS` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `septique` `septique` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `pathologie` `pathologie` VARCHAR(3) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `type_anesth`
      CHANGE `type_anesth_id` `type_anesth_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE `name` `name` VARCHAR(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      CHANGE `saisi_SHS` `saisi_SHS` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
      CHANGE `chambre_seule` `chambre_seule` ENUM('o','n','0','1') NOT NULL DEFAULT 'o';";
    $this->addQuery($query);
    $query = "UPDATE `sejour`
      SET `saisi_SHS`='0'
      WHERE `saisi_SHS`='n';";
    $this->addQuery($query);
    $query = "UPDATE `sejour`
      SET `saisi_SHS`='1'
      WHERE `saisi_SHS`='o';";
    $this->addQuery($query);
    $query = "UPDATE `sejour`
      SET `chambre_seule`='0'
      WHERE `chambre_seule`='n';";
    $this->addQuery($query);
    $query = "UPDATE `sejour`
      SET `chambre_seule`='1'
      WHERE `chambre_seule`='o';";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      CHANGE `saisi_SHS` `saisi_SHS` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `chambre_seule` `chambre_seule` ENUM('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      CHANGE `ATNC` `ATNC` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
      CHANGE `commande_mat` `commande_mat` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
      CHANGE `info` `info` ENUM('o','n','0','1') NOT NULL DEFAULT 'n';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `ATNC`='0'
      WHERE `ATNC`='n';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `ATNC`='1'
      WHERE `ATNC`='o';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `info`='0'
      WHERE `info`='n';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `info`='1'
      WHERE `info`='o';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `commande_mat`='0'
      WHERE `commande_mat`='n';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `commande_mat`='1'
      WHERE `commande_mat`='o';";
    $this->addQuery($query);

    $query = "ALTER TABLE `operations`
      CHANGE `ATNC` `ATNC` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `commande_mat` `commande_mat` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `info` `info` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.57");
    $query = "ALTER TABLE `operations`
      DROP `date_anesth`,
      DROP `time_anesth`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      CHANGE `entree_bloc` `entree_salle` TIME NULL,
      CHANGE `sortie_bloc` `sortie_salle` TIME NULL,
      ADD `entree_bloc` TIME NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.58");
    $query = "ALTER TABLE `sejour`
      ADD `ATNC` ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `hormone_croissance` ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `lit_accompagnant`   ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `isolement`          ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `television`         ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `repas_diabete`      ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `repas_sans_sel`     ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `repas_sans_residu`  ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($query);
    $query = "UPDATE sejour SET ATNC = '1' WHERE sejour_id IN (SELECT sejour_id FROM `operations` WHERE ATNC = '1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` DROP `ATNC`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole` CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($query);

    $this->makeRevision("0.59");
    $query = "UPDATE `operations` SET annulee = 0 WHERE annulee = ''";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET annule = 0 WHERE annule = ''";
    $this->addQuery($query);

    $this->makeRevision("0.60");
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `salle_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `date` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `time_operation` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX ( `annulee` )";
    $this->addQuery($query);

    $this->makeRevision("0.61");
    $query = "ALTER TABLE `operations`
      CHANGE `induction` `induction_debut` TIME";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD `induction_fin` TIME AFTER `induction_debut`";
    $this->addQuery($query);

    $this->makeRevision("0.62");
    $query = "ALTER TABLE `operations`
      ADD `anapath` ENUM('0','1') NOT NULL DEFAULT '0',
      ADD `labo` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.63");
    $query = "UPDATE `operations`
      SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($query);

    $this->makeRevision("0.64");
    $query = "ALTER TABLE `operations`
      ADD `forfait` FLOAT NULL AFTER `depassement`,
      ADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole`
      ADD `forfait` FLOAT NULL AFTER `depassement`,
      ADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($query);

    $this->makeRevision("0.65");
    $query = "ALTER TABLE `sejour`
      ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($query);

    $this->makeRevision("0.66");
    $this->addPrefQuery("mode", "1");

    $this->makeRevision("0.67");
    $query = "UPDATE `user_preferences`
      SET `key` = 'mode_dhe' WHERE `key` = 'mode';";
    $this->addQuery($query, true);

    $this->makeRevision("0.68");
    $query = "ALTER TABLE `sejour`
      ADD `mode_sortie` ENUM( 'normal', 'transfert', 'deces' );";
    $this->addQuery($query);

    $this->makeRevision("0.69");
    $query = "ALTER TABLE `sejour`
      ADD `prestation_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.70");
    $query = "ALTER TABLE `sejour`
      ADD `facturable` ENUM('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.71");
    $query = "ALTER TABLE `sejour`
      ADD `reanimation` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `chambre_seule`;";
    $this->addQuery($query);

    $this->makeRevision("0.72");
    $query = "ALTER TABLE `sejour`
      ADD `zt` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `reanimation`;";
    $this->addQuery($query);

    $this->makeRevision("0.73");
    $query = "ALTER TABLE `sejour`
      CHANGE `reanimation` `reanimation` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `zt` `zt` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET `sejour`.`reanimation` = 0, `sejour`.`zt` = 0;";
    $this->addQuery($query);

    $this->makeRevision("0.74");
    $query = "ALTER TABLE `operations`
      CHANGE `cote` `cote` ENUM('droit','gauche','bilatéral','total','inconnu') NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.75");
    $query = "ALTER TABLE `sejour`
      ADD `etablissement_transfert_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.76");
    $query = "ALTER TABLE `operations`
      ADD `horaire_voulu` TIME;";
    $this->addQuery($query);

    $this->makeRevision("0.77");
    $query = "ALTER TABLE `sejour`
      CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy','urg') NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.78");
    $query = "ALTER TABLE `type_anesth`
      ADD `ext_doc` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);

    $this->makeRevision("0.79");
    $query = "ALTER TABLE `sejour`
      ADD `DR` VARCHAR(5),
      CHANGE `pathologie` `pathologie` CHAR(3)";
    $this->addQuery($query);

    $this->makeRevision("0.80");
    $query = "UPDATE operations, plagesop
      SET operations.salle_id = plagesop.salle_id
      WHERE operations.salle_id IS NULL
      AND operations.plageop_id = plagesop.plageop_id;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      CHANGE `salle_id` `salle_id` INT( 11 ) UNSIGNED NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.81");
    $query = "ALTER TABLE `operations`
      CHANGE `salle_id` `salle_id` INT( 11 ) UNSIGNED DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET salle_id = NULL WHERE salle_id = 0;";
    $this->addQuery($query);

    $this->makeRevision("0.82");
    $this->addDependency("dPsante400", "0.1");
    $query = "INSERT INTO `id_sante400` (id_sante400_id, object_class, object_id, tag, last_update, id400)
      SELECT NULL , 'CSejour', `sejour_id` , 'SHS group:1', NOW( ) , `venue_SHS`
      FROM `sejour`
      WHERE `venue_SHS` IS NOT NULL
      AND `venue_SHS` != 0";
    $this->addQuery($query);

    $this->makeRevision("0.83");
    $query = "ALTER TABLE `sejour`
      DROP `venue_SHS`";
    $this->addQuery($query);

    $this->makeRevision("0.84");
    $query = "ALTER TABLE `sejour`
      ADD `repas_sans_porc` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.85");
    $query = "ALTER TABLE `protocole`
      ADD `protocole_prescription_chir_id` INT (11) UNSIGNED,
      ADD `protocole_prescription_anesth_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD INDEX (`protocole_prescription_chir_id`),
      ADD INDEX (`protocole_prescription_anesth_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.86");
    $query = "ALTER TABLE `operations` ADD `depassement_anesth` FLOAT NULL AFTER `fournitures`;";
    $this->addQuery($query);

    $this->makeRevision("0.87");
    $this->addDependency("dPcompteRendu", "0.1");
    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("Opération - CCAM - code",        "Opération - CCAM1 - code");
    $this->addQuery($query);

    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("Opération - CCAM - description", "Opération - CCAM1 - description");
    $this->addQuery($query);

    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("Opération - CCAM complet", "Opération - CCAM - codes");
    $this->addQuery($query);

    $this->makeRevision("0.88");
    $query = "ALTER TABLE `operations`
        CHANGE `anapath` `anapath` ENUM ('1','0','?') DEFAULT '?',
        CHANGE `labo` `labo` ENUM ('1','0','?') DEFAULT '?';";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `anapath` = '?'
      WHERE `anapath` = '0'";
    $this->addQuery($query);
    $query = "UPDATE `operations`
      SET `labo` = '?'
      WHERE `labo` = '0'";
    $this->addQuery($query);

    $this->makeRevision("0.89");
    $query = "ALTER TABLE `protocole`
      ADD `for_sejour` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);

    $this->makeRevision("0.90");
    $query = "ALTER TABLE `sejour`
    ADD `adresse_par_prat_id` INT (11),
    ADD `adresse_par_etab_id` INT (11),
    ADD `libelle` VARCHAR (255)";
    $this->addQuery($query);

    $this->makeRevision("0.91");
    $query = "ALTER TABLE `protocole`
      ADD `libelle_sejour` VARCHAR (255)";
    $this->addQuery($query);

    $this->makeRevision("0.92");
    $query = "ALTER TABLE `operations`
      ADD `cote_admission` ENUM ('droit','gauche') AFTER `horaire_voulu`,
      ADD `cote_consult_anesth` ENUM ('droit','gauche') AFTER `cote_admission`,
      ADD `cote_hospi` ENUM ('droit','gauche') AFTER `cote_consult_anesth`,
      ADD `cote_bloc` ENUM ('droit','gauche') AFTER `cote_hospi`;";
    $this->addQuery($query);

    $this->makeRevision("0.93");
    $query = "ALTER TABLE `operations`
      ADD `prothese` ENUM ('1','0','?')  DEFAULT '?' AFTER `labo`,
      ADD `date_visite_anesth` DATETIME,
      ADD `prat_visite_anesth_id` INT (11) UNSIGNED,
      ADD `rques_visite_anesth` TEXT,
      ADD `autorisation_anesth` ENUM ('0','1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX (`date_visite_anesth`),
      ADD INDEX (`prat_visite_anesth_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.94");
    $this->addPrefQuery("dPplanningOp_listeCompacte", "1");

    $this->makeRevision("0.95");
    $query = "ALTER TABLE `sejour`
      ADD `service_id` INT (11) UNSIGNED AFTER `zt`,
      ADD INDEX (`etablissement_transfert_id`),
      ADD INDEX (`service_id`),
      ADD INDEX (`prestation_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.96");
    $query = "ALTER TABLE `protocole`
      ADD `service_id_sejour` INT (11) UNSIGNED,
      ADD INDEX (`temp_operation`),
      ADD INDEX (`service_id_sejour`);";
    $this->addQuery($query);

    $this->makeRevision("0.97");
    $query = "ALTER TABLE `sejour`
      ADD `etablissement_entree_transfert_id` INT (11) UNSIGNED,
      ADD INDEX (`etablissement_entree_transfert_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.98");
    $query = "ALTER TABLE `sejour`
      ADD `facture` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.99");
    $query = "ALTER TABLE `operations`
      ADD `facture` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.00");
    $query = "ALTER TABLE `sejour`
      CHANGE `type` `type` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult') NOT NULL;";
    $this->addQuery($query);

    $this->makeEmptyRevision("1.01");

    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX (`type_anesth`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
      ADD INDEX (`adresse_par_prat_id`),
      ADD INDEX (`adresse_par_etab_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.02");
    $query = "ALTER TABLE `sejour`
      CHANGE `chambre_seule` `chambre_seule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.03");
    $query = "UPDATE sejour
      SET sortie_prevue = entree_reelle
      WHERE entree_reelle IS NOT NULL
      AND sortie_prevue < entree_reelle";
    $this->addQuery($query);

    $this->makeRevision("1.04");
    $query = "ALTER TABLE `sejour`
      CHANGE `mode_sortie` `mode_sortie` ENUM ('normal','transfert','mutation','deces') DEFAULT 'normal',
      ADD `service_mutation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD INDEX (`service_mutation_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.05");
    $query = "ALTER TABLE `sejour`
      ADD `entree` DATETIME AFTER `sortie_reelle`,
      ADD `sortie` DATETIME AFTER `entree`";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET
      `sejour`.`entree` = IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`),
      `sejour`.`sortie` = IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`)";
    $this->addQuery($query);

    $this->makeRevision("1.06");
    $query = "ALTER TABLE `sejour`
      ADD INDEX (`entree`),
      ADD INDEX (`sortie`);";
    $this->addQuery($query);

    $this->makeRevision("1.07");
    $query = "ALTER TABLE `sejour`
      ADD `service_entree_mutation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.08");
    $query = "ALTER TABLE `sejour`
      ADD `forfait_se` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.09");
    $query = "ALTER TABLE `sejour`
      ADD `recuse` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.10");
    $query = "ALTER TABLE `protocole`
      CHANGE `service_id_sejour` `service_id` INT (11) UNSIGNED  NOT NULL;";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD INDEX (`service_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.11");
    $query = "ALTER TABLE `protocole`
      CHANGE `service_id` `service_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "UPDATE `protocole`
      SET service_id = NULL
      WHERE service_id = '0'";
    $this->addQuery($query);

    $this->makeRevision("1.12");
    $query = "CREATE TABLE `color_libelle_sejour` (
      `color_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `libelle` VARCHAR (255) NOT NULL,
      `color` CHAR (6) NOT NULL DEFAULT 'ffffff'
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.13");
    $query = "ALTER TABLE `operations`
      ADD `debut_prepa_preop` TIME,
      ADD `fin_prepa_preop` TIME";
    $this->addQuery($query);

    $this->makeRevision("1.14");
    $query = "CREATE TABLE `interv_hors_plages` (
      `interv_hors_plage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("1.15");
    $query = "ALTER TABLE sejour
      ADD commentaires_sortie TEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.16");
    $query = "ALTER TABLE `protocole`
              CHANGE `protocole_prescription_chir_id` `protocole_prescription_chir_id` VARCHAR (255),
              CHANGE `protocole_prescription_anesth_id` `protocole_prescription_anesth_id` VARCHAR (255);";
    $this->addQuery($query);

    $query = "UPDATE `protocole`
              SET protocole_prescription_chir_id = CONCAT('CPrescription-', protocole_prescription_chir_id)
              WHERE protocole_prescription_chir_id IS NOT NULL;";
    $this->addQuery($query);

    $query = "UPDATE `protocole`
              SET protocole_prescription_anesth_id = CONCAT('CPrescription-', protocole_prescription_anesth_id)
              WHERE protocole_prescription_anesth_id IS NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("1.17");
    $query = "ALTER table `protocole`
              ADD `protocole_prescription_chir_class` ENUM ('CPrescription', 'CPrescriptionProtocolePack') AFTER `protocole_prescription_chir_id`,
              ADD `protocole_prescription_anesth_class` ENUM ('CPrescription', 'CPrescriptionProtocolePack') AFTER `protocole_prescription_anesth_id`;";
    $this->addQuery($query);

    $query = "UPDATE `protocole`
              SET protocole_prescription_chir_class = SUBSTRING_INDEX(protocole_prescription_chir_id, '-', 1),
              protocole_prescription_chir_id = SUBSTRING(protocole_prescription_chir_id, LENGTH(SUBSTRING_INDEX(protocole_prescription_chir_id,'-',1))+2),
              protocole_prescription_anesth_class = SUBSTRING_INDEX(protocole_prescription_anesth_id, '-', 1),
              protocole_prescription_anesth_id = SUBSTRING(protocole_prescription_anesth_id, LENGTH(SUBSTRING_INDEX(protocole_prescription_anesth_id,'-',1))+2);";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
              CHANGE protocole_prescription_chir_id protocole_prescription_chir_id INT(11),
              CHANGE protocole_prescription_anesth_id protocole_prescription_anesth_id INT(11)";

    $this->makeRevision("1.18");
    $query = "ALTER TABLE `sejour`
              ADD `consult_accomp` ENUM ('oui','non','nc') DEFAULT 'nc';";
    $this->addQuery($query);

    $this->makeRevision("1.19");
    $query = "ALTER TABLE `groups_config`
      ADD `dPplanningOp_COperation_DHE_mode_simple` ENUM ('0', '1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);

    $this->makeRevision("1.20");

    $query = "ALTER TABLE `protocole`
              CHANGE `chir_id` `chir_id` INT (11) UNSIGNED,
              ADD `function_id` INT (11) UNSIGNED AFTER `chir_id`,
              ADD `group_id` INT (11) UNSIGNED AFTER `function_id`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
              ADD INDEX (`function_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.21");

    $query = "ALTER TABLE `sejour`
                ADD `discipline_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                ADD INDEX (`discipline_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.22");
    $query = "ALTER TABLE `sejour`
      ADD `mode_entree` ENUM('6','7','8') AFTER `codes_ccam`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD INDEX (`mode_entree`);";
    $this->addQuery($query);

    $this->makeRevision("1.23");

    $query = "ALTER TABLE `protocole`
              ADD `cote` ENUM ('droit','gauche','bilatéral','total','inconnu') AFTER `libelle`";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
              ADD `assurance_maladie` VARCHAR (255),
              ADD `rques_assurance_maladie` TEXT,
              ADD `assurance_accident` VARCHAR (255),
              ADD `rques_assurance_accident` TEXT,
              ADD `date_accident` DATE,
              ADD `nature_accident` ENUM ('P','T','D','S','J','C','L','B','U');";
    $this->addQuery($query);

    $this->makeRevision("1.24");
    $query = "ALTER TABLE `sejour`
      CHANGE `etablissement_entree_transfert_id` `etablissement_entree_id` INT (11) UNSIGNED,
      CHANGE `etablissement_transfert_id` `etablissement_sortie_id` INT(11) UNSIGNED,
      CHANGE `service_entree_mutation_id` `service_entree_id` INT (11) UNSIGNED,
      CHANGE `service_mutation_id` `service_sortie_id` INT (11) UNSIGNED";
    $this->addQuery($query);

    $this->getFieldRenameQueries("CSejour", "etablissement_entree_transfert_id", "etablissement_entree_id");
    $this->getFieldRenameQueries("CSejour", "etablissement_transfert_id", "etablissement_sortie_id");
    $this->getFieldRenameQueries("CSejour", "service_entree_mutation_id", "service_entree_id");
    $this->getFieldRenameQueries("CSejour", "service_mutation_id", "service_sortie_id");

    $this->makeRevision("1.25");
    $query = "ALTER TABLE `sejour`
              ADD `ald` ENUM ('0','1') DEFAULT '0' AFTER discipline_id;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
              CHANGE `recuse` `recuse` ENUM ('-1','0','1') DEFAULT '-1';";
    $this->addQuery($query);

    $this->makeRevision("1.26");
    $query = "UPDATE `sejour`
      SET `etablissement_entree_id` = `sejour`.`adresse_par_etab_id`
      WHERE `etablissement_entree_id` IS NULL;";

    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      DROP `adresse_par_etab_id`;";
    $this->addQuery($query);

    $this->makeRevision("1.27");

    $this->addDependency("dPcompteRendu", "0.1");
    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("RPU - Provenance", "Sejour - Provenance");
    $this->addQuery($query);

    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("RPU - Destination", "Sejour - Destination");
    $this->addQuery($query);

    $query = CSetupdPcompteRendu::renameTemplateFieldQuery("RPU - Tranport", "Sejour - Transport");
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD `provenance` ENUM('1','2','3','4','5','6', '7', '8'),
      ADD `destination` ENUM('1','2','3','4','6','7'),
      ADD `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo') NOT NULL;";
    $this->addQuery($query);

    // Déplacer les champs que si le module dPurgences est actif
    if (CModule::getActive("dPurgences")) {
      $query = "UPDATE sejour, rpu
      SET `sejour`.`provenance` = `rpu`.`provenance`
      WHERE `rpu`.`sejour_id` = `sejour`.`sejour_id`";
      $this->addQuery($query);

      $query = "UPDATE sejour, rpu
      SET `sejour`.`destination` = `rpu`.`destination`
      WHERE `rpu`.`sejour_id` = `sejour`.`sejour_id`";
      $this->addQuery($query);

      $query = "UPDATE sejour, rpu
        SET `sejour`.`transport` = `rpu`.`transport`
        WHERE `rpu`.`sejour_id` = `sejour`.`sejour_id`";
      $this->addQuery($query);
    }

    $this->makeRevision("1.28");
    $query = "ALTER TABLE `sejour`
      CHANGE `transport` `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo');";
    $this->addQuery($query);

    $this->makeRevision("1.29");
    $query = "ALTER TABLE `sejour`
      ADD `type_pec` ENUM ('M','C','O');";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD `type_pec` ENUM ('M','C','O');";
    $this->addQuery($query);

    $this->makeRevision("1.30");
    $query = "ALTER TABLE `sejour`
    ADD `grossesse_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.31");
    $query = "ALTER TABLE `sejour`
      ADD `duree_uscpo` INT (11) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD `duree_uscpo` INT (11) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.32");
    $query = "ALTER TABLE `sejour`
                ADD `confirme` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                ADD INDEX ( `confirme` )";
    $this->addQuery($query);

    $this->makeRevision("1.33");

    $query = "ALTER TABLE `operations`
                ADD `duree_uscpo` INT (11) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);

    $query = "UPDATE `operations`
                LEFT JOIN `sejour` ON `sejour`.`sejour_id` = `operations`.`sejour_id`
                SET `operations`.`duree_uscpo` = `sejour`.`duree_uscpo`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                DROP `duree_uscpo`";
    $this->addQuery($query);

    $this->makeRevision("1.34");

    $query = "ALTER TABLE `sejour`
                CHANGE `zt` `UHCD` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.35");
    $query = "ALTER TABLE `operations`
      ADD `cloture_activite_1` ENUM ('0','1') DEFAULT '0',
      ADD `cloture_activite_4` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD `cloture_activite_1` ENUM ('0','1') DEFAULT '0',
      ADD `cloture_activite_4` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.36");
    $query = "ALTER TABLE `sejour`
       CHANGE `saisi_SHS` `entree_preparee` ENUM ('0','1') DEFAULT '0',
       ADD `sortie_preparee` ENUM ('0','1') DEFAULT '0',
       CHANGE `modif_SHS` `entree_modifiee` ENUM ('0','1') DEFAULT '0',
       ADD `sortie_modifiee` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.37");
    $query = "ALTER TABLE `protocole`
      ADD `presence_preop` TIME,
      ADD `presence_postop` TIME;";
    $this->addQuery($query);

    $this->makeRevision("1.38");
    $query = "ALTER TABLE `operations`
      ADD `presence_preop` TIME,
      ADD `presence_postop` TIME;";
    $this->addQuery($query);

    $this->makeRevision("1.39");
    $query = "ALTER TABLE `operations`
              ADD `rank_voulu` TINYINT (4) NOT NULL DEFAULT '0' AFTER `rank`;";
    $this->addQuery($query);

    $this->makeRevision("1.40");
    $query = "ALTER TABLE `sejour`
                ADD `forfait_fsd` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.41");
    $query = "ALTER TABLE `sejour`
                CHANGE `forfait_fsd` `forfait_sd` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.42");
    $query = "ALTER TABLE `operations`
      ADD `duree_preop` TIME;";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD `duree_preop` TIME;";
    $this->addQuery($query);

    $this->makeRevision("1.43");
    $query = "ALTER TABLE `operations`
      ADD `passage_uscpo` ENUM ('0','1') AFTER duree_uscpo;";
    $this->addQuery($query);

    $this->makeRevision("1.44");
    $query = "ALTER TABLE `operations` CHANGE `date_visite_anesth` `date_visite_anesth` DATE;";
    $this->addQuery($query);

    $this->makeRevision("1.45");
    $query = "ALTER TABLE `sejour`
                ADD `transport_sortie` ENUM ('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo'),
                ADD `rques_transport_sortie` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.46");
    $query = "ALTER TABLE `protocole`
      ADD `uf_hebergement_id` INT (11) UNSIGNED AFTER `group_id`,
      ADD `uf_medicale_id` INT (11) UNSIGNED AFTER `uf_hebergement_id`,
      ADD `uf_soins_id` INT (11) UNSIGNED AFTER `uf_medicale_id`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD `uf_hebergement_id` INT (11) UNSIGNED AFTER `group_id`,
      ADD `uf_medicale_id` INT (11) UNSIGNED AFTER `uf_hebergement_id`,
      ADD `uf_soins_id` INT (11) UNSIGNED AFTER `uf_medicale_id`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD INDEX (`uf_hebergement_id`),
      ADD INDEX (`uf_medicale_id`),
      ADD INDEX (`uf_soins_id`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
      ADD INDEX (`uf_hebergement_id`),
      ADD INDEX (`uf_medicale_id`),
      ADD INDEX (`uf_soins_id`);";

    $this->makeRevision("1.47");

    $query = "ALTER TABLE `type_anesth`
      ADD `actif` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.48");

    $query = "ALTER TABLE `operations`
              ADD `exam_extempo` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.49");
    $query = "CREATE TABLE `pose_dispositif_vasculaire` (
              `pose_dispositif_vasculaire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `operation_id` INT (11) UNSIGNED,
              `sejour_id` INT (11) UNSIGNED NOT NULL,
              `date` DATETIME NOT NULL,
              `lieu` VARCHAR (255),
              `urgence` ENUM ('0','1') NOT NULL DEFAULT '0',
              `operateur_id` INT (11) UNSIGNED NOT NULL,
              `encadrant_id` INT (11) UNSIGNED,
              `type_materiel` ENUM ('cvc','cvc_tunnelise','cvc_dialyse','cvc_bioactif','chambre_implantable','autre') NOT NULL,
              `voie_abord_vasc` TEXT
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `pose_dispositif_vasculaire`
              ADD INDEX (`operation_id`),
              ADD INDEX (`sejour_id`),
              ADD INDEX (`date`),
              ADD INDEX (`operateur_id`),
              ADD INDEX (`encadrant_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.50");
    $query = "ALTER TABLE `operations`
              ADD `flacons_anapath` TINYINT (4) AFTER `anapath`,
              ADD `labo_anapath` VARCHAR (255)  AFTER `flacons_anapath`,
              ADD `description_anapath` TEXT    AFTER `labo_anapath`;";
    $this->addQuery($query);

    $this->makeRevision("1.51");
    $query = "ALTER TABLE `operations`
      ADD `chir_2_id` INT (11) UNSIGNED AFTER `chir_id`,
      ADD `chir_3_id` INT (11) UNSIGNED AFTER `chir_2_id`,
      ADD `chir_4_id` INT (11) UNSIGNED AFTER `chir_3_id`;";
    $this->addQuery($query);

    $this->makeRevision("1.52");
    $query = "ALTER TABLE `operations`
      ADD `envoi_mail` DATETIME;";
    $this->addQuery($query);

    $this->makeRevision("1.53");
    $query = "UPDATE `sejour`
      SET `sejour`.`recuse` = '0'
      WHERE `sejour`.`type` != 'ssr';";
    $this->addQuery($query);

    $this->makeRevision("1.54");
    $query = "ALTER TABLE `operations`
              ADD `conventionne` ENUM ('0','1') DEFAULT '1' AFTER `depassement`;";
    $this->addQuery($query);

    $this->makeRevision("1.55");
    $query = "ALTER TABLE `operations`
              ADD `flacons_bacterio` TINYINT (4) AFTER `labo`,
              ADD `labo_bacterio` VARCHAR (255)  AFTER `flacons_bacterio`,
              ADD `description_bacterio` TEXT    AFTER `labo_bacterio`;";
    $this->addQuery($query);

    $this->makeRevision("1.56");
    $query = "CREATE TABLE `charge_price_indicator` (
                `charge_price_indicator_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (255) NOT NULL,
                `type` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult') NOT NULL DEFAULT 'ambu',
                `group_id` INT (11) UNSIGNED NOT NULL,
                `libelle` VARCHAR (255),
                `actif` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `charge_price_indicator`
                ADD INDEX (`group_id`),
                ADD INDEX (`code`);";
    $this->addQuery($query);

    $this->makeRevision("1.57");
    $query = "ALTER TABLE `sejour`
                ADD `charge_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                ADD INDEX (`grossesse_id`),
                ADD INDEX (`service_entree_id`),
                ADD INDEX (`charge_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.58");
    $query = "ALTER TABLE `charge_price_indicator`
              ADD `type_pec` ENUM ('M','C','O'),
              ADD INDEX (`type_pec`)";
    $this->addQuery($query);

    $this->makeRevision("1.59");
    $query = "ALTER TABLE `protocole`
      ADD `exam_extempo` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.60");
    $query = "ALTER TABLE `protocole`
      CHANGE `cote` `cote` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu');";
    $this->addQuery($query);

    $query = "ALTER TABLE `operations`
      CHANGE `cote` `cote` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu') NOT NULL DEFAULT 'inconnu',
      CHANGE `cote_admission` `cote_admission` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu'),
      CHANGE `cote_consult_anesth` `cote_consult_anesth` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu'),
      CHANGE `cote_hospi` `cote_hospi` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu'),
      CHANGE `cote_bloc` `cote_bloc` ENUM ('droit','gauche','haut','bas','bilatéral','total','inconnu');";
    $this->addQuery($query);

    $this->makeRevision("1.61");
    $query = "ALTER TABLE `operations`
      ADD `poste_sspi_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `operations`
      ADD INDEX (`chir_2_id`),
      ADD INDEX (`chir_3_id`),
      ADD INDEX (`chir_4_id`),
      ADD INDEX (`poste_sspi_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.62");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `sejour`
      ADD `isolement_date` DATETIME AFTER `isolement`,
      ADD `raison_medicale` TEXT AFTER `isolement_date`;";
    $this->addQuery($query);

    $this->makeRevision("1.63");
    $query = "ALTER TABLE `operations`
      CHANGE `sortie_reveil` `sortie_reveil_possible` TIME,
      ADD `sortie_reveil_reel` TIME AFTER `sortie_reveil_possible`;";
    $this->addQuery($query);

    $query = "UPDATE `operations`
      SET `sortie_reveil_reel` = `sortie_reveil_possible`";
    $this->addQuery($query);

    $this->makeRevision("1.64");
    $query = "ALTER TABLE `sejour`
      ADD `isolement_fin` DATETIME AFTER `isolement_date`;";
    $this->addQuery($query);

    $this->makeRevision("1.65");
    $query = "ALTER TABLE `operations`
      ADD `examen_operation_id` INT (11) UNSIGNED,
      ADD INDEX (`examen_operation_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.66");
    $this->getFieldRenameQueries("COperation", "sortie_reveil", "sortie_reveil_possible");

    $this->makeRevision("1.67");

    $query = "ALTER TABLE `sejour`
                CHANGE `assurance_maladie` `assurance_maladie` INT (11) UNSIGNED,
                CHANGE `assurance_accident` `assurance_accident` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                ADD INDEX (`assurance_maladie`),
                ADD INDEX (`assurance_accident`);";
    $this->addQuery($query);

    $this->makeRevision("1.68");
    $query = "CREATE TABLE `mode_entree_sejour` (
                `mode_entree_sejour_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (40) NOT NULL,
                `mode` VARCHAR (20) NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL,
                `libelle` VARCHAR (255),
                `actif` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `mode_entree_sejour`
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `mode_sortie_sejour` (
                `mode_sortie_sejour_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (40) NOT NULL,
                `mode` VARCHAR (20) NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL,
                `libelle` VARCHAR (255),
                `actif` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `mode_sortie_sejour`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
                ADD `mode_entree_id` INT (11) UNSIGNED AFTER `mode_entree`,
                ADD `mode_sortie_id` INT (11) UNSIGNED AFTER `mode_sortie`,
                ADD INDEX (`mode_entree_id`),
                ADD INDEX (`mode_sortie_id`);";
    $this->addQuery($query);
    $this->makeRevision("1.69");

    $query = "ALTER TABLE `protocole` 
              ADD `type_anesth` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `operations` 
              ADD `ASA` ENUM ('1','2','3','4','5') DEFAULT '1',
              ADD `position` ENUM ('DD','DV','DL','GP','AS','TO','GYN');";
    $this->addQuery($query);
    
    $query = "UPDATE `operations`, `consultation_anesth`
      SET `operations`.`ASA` = `consultation_anesth`.`ASA`,
      `operations`.`position` = `consultation_anesth`.`position`
      WHERE `consultation_anesth`.`operation_id` = `operations`.`operation_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.70");
    
    $query = "ALTER TABLE `sejour` DROP INDEX `assurance_maladie`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` DROP INDEX `assurance_accident`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
           DROP `assurance_accident`,
           DROP `assurance_maladie`,
           DROP `rques_assurance_accident`,
           DROP `rques_assurance_maladie`;";
    $this->addQuery($query);

    $this->makeRevision("1.71");
    $query = "ALTER TABLE `sejour`
      CHANGE `ATNC` `ATNC` ENUM ('0', '1')";
    $this->addQuery($query);

    $query = "UPDATE `sejour`
      SET `ATNC` = NULL
      WHERE `ATNC` = '0';";
    $this->addQuery($query);

    $this->makeRevision("1.72");
    $query = "CREATE TABLE `regle_sectorisation` (
              `regle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `service_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `function_id` INT (11) UNSIGNED,
              `praticien_id` INT (11) UNSIGNED,
              `duree_min` INT (11),
              `duree_max` INT (11),
              `date_min` DATETIME,
              `date_max` DATETIME,
              `type_adminission` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult'),
              `type_pec` ENUM ('M','C','O'),
              `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0')
              /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `regle_sectorisation`
              ADD INDEX (`service_id`),
              ADD INDEX (`function_id`),
              ADD INDEX (`praticien_id`),
              ADD INDEX (`date_min`),
              ADD INDEX (`date_max`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.73");
    $query = "ALTER TABLE `regle_sectorisation`
    CHANGE `type_adminission` `type_admission` ENUM( 'comp', 'ambu', 'exte', 'seances', 'ssr', 'psy', 'urg', 'consult' )
    NULL DEFAULT NULL ";
    $this->addQuery($query);
    
    $this->makeRevision("1.74");
    $query = "ALTER TABLE `operations`
                CHANGE `ASA` `ASA` ENUM ('1','2','3','4','5','6') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.75");
    if (!CAppUI::conf("dPplanningOp CSejour use_recuse")) {
      $query = "UPDATE `sejour`
        SET `sejour`.`recuse` = '0'
        WHERE `sejour`.`type` != 'ssr'";
      $this->addQuery($query);
    }

    $this->makeRevision("1.76");

    $query = "ALTER TABLE `sejour`
                CHANGE `mode_sortie` `mode_sortie` ENUM ('normal','transfert','mutation','deces');";
    $this->addQuery($query);

    $this->makeRevision("1.77");
    $query = "ALTER TABLE `operations`
                ADD `graph_pack_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.78");
    $query = "ALTER TABLE `protocole`
                ADD `duree_heure_hospi` TINYINT (4) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.79");
    $query = "ALTER TABLE `operations`
                ADD `tarif` VARCHAR (255);";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
                ADD `tarif` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.80");
    $query = "ALTER TABLE `operations`
                ADD `remise_chir` TIME,
                ADD `tto` TIME,
                ADD `rques_personnel` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.81");
    $query = "ALTER TABLE `operations`
                CHANGE `ASA` `ASA` ENUM ('1','2','3','4','5','6');";
    $this->addQuery($query);

    $this->makeRevision("1.82");
    $query = "ALTER TABLE `mode_entree_sejour`
                CHANGE `actif` `actif` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.83");
    $query = "ALTER TABLE `mode_sortie_sejour`
                CHANGE `actif` `actif` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.84");
    $query = "CREATE TABLE `operation_workflow` (
      `miner_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `operation_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      `date` DATE NOT NULL,
      `remined` ENUM ('0','1') NOT NULL DEFAULT '0',
      `date_operation` DATETIME NOT NULL,
      `date_creation` DATETIME,
      `date_cancellation` DATETIME,
      `date_consult_chir` DATETIME,
      `date_consult_anesth` DATETIME,
      `date_creation_consult_chir` DATETIME,
      `date_creation_consult_anesth` DATETIME,
      `date_visite_anesth` DATE
    )/*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `operation_workflow`
      ADD INDEX (`operation_id`),
      ADD INDEX (`date`)";
    $this->addQuery($query);

    $this->makeRevision("1.85");
    $query = "ALTER TABLE `protocole`
                ADD `facturable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.86");
    $query = "ALTER TABLE `operations`
      ADD `sortie_locker_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.87");
    $query = "ALTER TABLE `sejour`
      ADD `confirme_date` DATETIME AFTER `confirme`;";
    $this->addQuery($query);

    $query = "UPDATE `sejour`
      SET `confirme_date` = `sejour`.`sortie_prevue`
      WHERE `sejour`.`confirme` = '1'";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      DROP `confirme`,
      CHANGE `confirme_date` `confirme` DATETIME";
    $this->addQuery($query);

    $query = "ALTER TABLE `sejour`
      ADD `confirme_user_id` INT (11) UNSIGNED AFTER `confirme`;";
    $this->addQuery($query);
    $this->makeRevision("1.88");

    // Synchronisation de la date de l'intervention avec celle de la plage
    $query = "UPDATE `operations`
                LEFT JOIN plagesop ON plagesop.plageop_id = `operations`.`plageop_id`
                SET `operations`.`date` = plagesop.date
                WHERE `operations`.plageop_id IS NOT NULL";
    $this->addQuery($query);
    $this->makeRevision('1.89');

    $query = "ALTER TABLE `protocole`
                ADD `charge_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $this->addDefaultConfigCIP();
    $this->makeRevision('1.90');

    $query = "ALTER TABLE `type_anesth`
                ADD `group_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `type_anesth`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $this->makeRevision('1.91');

    $query = "ALTER TABLE `sejour`
                ADD `exec_tarif` DATETIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
                ADD INDEX (`exec_tarif`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `operations`
                ADD `exec_tarif` DATETIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
                ADD INDEX (`exec_tarif`);";
    $this->addQuery($query);
    $this->makeRevision('1.92');

    $query = "ALTER TABLE `sejour`
                ADD `reception_sortie` DATETIME,
                ADD `completion_sortie` DATETIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
                ADD INDEX (`reception_sortie`),
                ADD INDEX (`completion_sortie`);";
    $this->addQuery($query);

    $this->makeRevision("1.93");
    $query = "ALTER TABLE `charge_price_indicator`
                CHANGE `group_id` `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                ADD `color` VARCHAR (6) NOT NULL DEFAULT 'ffffff';";
    $this->addQuery($query);

    $this->makeRevision("1.94");
    $query = "ALTER TABLE `operation_workflow`
                ADD `postmined` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.95");
    $query = "ALTER TABLE `operations`
                ADD `poste_preop_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("1.96");
    $query = "ALTER TABLE `sejour`
                ADD `technique_reanimation` TEXT;";
    $this->addQuery($query);

    $this->makeRevision("1.97");
    $query = "ALTER TABLE `operations`
                ADD `entree_chir` TIME AFTER induction_debut,
                ADD `entree_anesth` TIME AFTER entree_chir;";
    $this->addQuery($query);
    $this->makeRevision("1.98");

    $this->makeRevision("1.99");
    $query = "ALTER TABLE `sejour`
      ADD `consult_related_id` INT (11) UNSIGNED AFTER `group_id`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `operations`
      ADD `consult_related_id` INT (11) UNSIGNED AFTER `salle_id`;";
    $this->addQuery($query);

    $this->addFunctionalPermQuery("allowed_check_entry", "0");
    $this->makeRevision("2.00");

    $query = "CREATE TABLE `sejour_affectation` (
                `sejour_affectation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `sejour_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour_affectation`
                ADD INDEX (`sejour_id`),
                ADD INDEX (`user_id`);";
    $this->addQuery($query);
    $this->makeRevision("2.01");

    $query = "ALTER TABLE `operations`
              DROP `entree_chir`,
              DROP `entree_anesth`;";
    $this->addQuery($query);

    $this->makeRevision("2.02");
    $dsn = CSQLDataSource::get('std');
    if (!$dsn->fetchRow($dsn->exec('SHOW TABLES LIKE \'libelleop\';'))) {
      $query = "CREATE TABLE `libelleop` (
                  `libelleop_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                  `statut` ENUM ('valide','no_valide','indefini'),
                  `nom` VARCHAR (255) NOT NULL,
                  `date_debut` DATETIME,
                  `date_fin` DATETIME,
                  `services` VARCHAR (255),
                  `mots_cles` VARCHAR (255),
                  `numero` INT (11) NOT NULL DEFAULT '0',
                  `version` INT (11) DEFAULT '1',
                  `group_id` INT (11) UNSIGNED
                )/*! ENGINE=MyISAM */;";
      $this->addQuery($query, true);

      $query = "ALTER TABLE `libelleop`
                ADD INDEX `date_debut` (`date_debut`),
                ADD INDEX `group_id` (`group_id`),
                ADD INDEX `date_fin` (`date_fin`);";
      $this->addQuery($query, true);
    }

    $this->makeRevision("2.03");
    if (!$dsn->fetchRow($dsn->exec('SHOW TABLES LIKE \'liaison_libelle\';'))) {
      $query = "CREATE TABLE `liaison_libelle` (
                `liaison_libelle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `libelleop_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `operation_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `numero` TINYINT (4) UNSIGNED DEFAULT '1')/*! ENGINE=MyISAM */;";
      $this->addQuery($query, true);

      $query = "ALTER TABLE `liaison_libelle`
                ADD INDEX `libelleop_id` (`libelleop_id`),
                ADD INDEX `operation_id` (`operation_id`);";
      $this->addQuery($query, true);
    }
    $this->makeRevision("2.04");
    $query = "ALTER TABLE `sejour`
                ADD `handicap` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision('2.05');
    $query = "ALTER TABLE `mode_sortie_sejour`
                ADD `destination` ENUM('1','2','3','4','6','7'),
                ADD `orientation` ENUM('HDT','HO','SC','SI','REA','UHCD','MED','CHIR','OBST','FUGUE','SCAM','PSA','REO');";
    $this->addQuery($query);

    $this->makeRevision("2.06");
    $query = "ALTER TABLE `regle_sectorisation`
                ADD `priority` INT (11) NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->mod_version = '2.07';
  }
}