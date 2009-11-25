<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPlabo
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPlabo extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPlabo";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `catalogue_labo` (" .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`pere_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\n`identifiant` VARCHAR(255) NOT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\nPRIMARY KEY (`catalogue_labo_id`) ," .
          "\nINDEX ( `pere_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `examen_labo` (" .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`identifiant` VARCHAR(255) NOT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\n`type` ENUM('bool','num','str') NOT NULL DEFAULT 'num' ," .
          "\n`unite` VARCHAR(255) DEFAULT NULL," .
          "\n`min` FLOAT DEFAULT NULL," .
          "\n`max` FLOAT DEFAULT NULL," .
          "\nPRIMARY KEY ( `examen_labo_id` ) ," .
          "\nINDEX ( `catalogue_labo_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);

    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `pack_examens_labo` (" .
          "\n`pack_examens_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`function_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\nPRIMARY KEY ( `pack_examens_labo_id` ) ," .
          "\nINDEX ( `function_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `pack_item_examen_labo` (" .
          "\n`pack_item_examen_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`pack_examens_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\nPRIMARY KEY ( `pack_item_examen_labo_id` ) ," .
          "\nINDEX ( `pack_examens_labo_id` ) ," .
          "\nINDEX ( `examen_labo_id` )" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `pack_item_examen_labo` DROP `catalogue_labo_id`";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `prescription_labo` (" .
          "\n`prescription_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`consultation_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\nPRIMARY KEY ( `prescription_labo_id` ) ," .
          "\nINDEX ( `consultation_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `prescription_labo_examen` (" .
          "\n`prescription_labo_examen_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`prescription_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\nPRIMARY KEY ( `prescription_labo_examen_id` ) ," .
          "\nINDEX ( `prescription_labo_id` ) ," .
          "\nINDEX ( `examen_labo_id` )" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $this->addDependency("dPpatients", "0.1");
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nCHANGE `consultation_id` `patient_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT 0;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nADD `praticien_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT 0 AFTER `patient_id`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nADD `date` DATETIME DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `prescription_labo_examen`" .
            "\nADD `resultat` VARCHAR( 255 ) DEFAULT NULL," .
            "\nADD `date` DATETIME DEFAULT NULL," .
            "\nADD `commentaire` TEXT DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `prescription_labo_examen`" .
            "\nCHANGE `date` `date` DATE DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `examen_labo`" .
        "\nADD `deb_application` DATE," .
        "\nADD `fin_application` DATE," .
        "\nADD `realisateur` INT(11) UNSIGNED," .
        "\nADD `applicabilite` ENUM('homme','femme','unisexe')," .
        "\nADD `age_min` INT(11) UNSIGNED," .
        "\nADD `age_max` INT(11) UNSIGNED," .
        "\nADD `technique` TEXT," .
        "\nADD `materiel` TEXT," .
        "\nADD `type_prelevement` VARCHAR(255)," .
        "\nADD `methode_prelevement` TEXT," .
        "\nADD `conservation` TEXT," .
        "\nADD `temps_conservation` INT(11) UNSIGNED," .
        "\nADD `quantité` INT(11) UNSIGNED," .
        "\nADD `jour_execution` VARCHAR(255)," .
        "\nADD `duree_execution` INT(11) UNSIGNED," .
        "\nADD `remarques` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `examen_labo`" .
        "\nCHANGE `quantité` `quantite` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `examen_labo`" .
        "\nCHANGE `type_prelevement` `type_prelevement` ENUM('sang','urine','biopsie');";
    $this->addQuery($sql);

    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `examen_labo`" .
        "\nADD `execution_lun` ENUM('0','1')," .
        "\nADD `execution_mar` ENUM('0','1')," .
        "\nADD `execution_mer` ENUM('0','1')," .
        "\nADD `execution_jeu` ENUM('0','1')," .
        "\nADD `execution_ven` ENUM('0','1')," .
        "\nADD `execution_sam` ENUM('0','1')," .
        "\nADD `execution_dim` ENUM('0','1')," .
        "\nDROP `jour_execution`;";
    $this->addQuery($sql);

    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `examen_labo`" .
        "\nCHANGE `quantite` `quantite_prelevement` FLOAT," .
        "\nADD `unite_prelevement` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $sql = "ALTER TABLE `prescription_labo`
            ADD `verouillee` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE `prescription_labo`
            ADD `validee` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `pack_item_examen_labo` ADD UNIQUE (" .
        "\n`pack_examens_labo_id` ," .
        "\n`examen_labo_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo_examen` ADD UNIQUE (" .
        "\n`prescription_labo_id` ," .
        "\n`examen_labo_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `prescription_labo`
            ADD `urgence` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `pack_examens_labo`
            ADD `code` INT(11);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `prescription_labo_examen`
            ADD `pack_examens_labo_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `catalogue_labo`
            ADD `function_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `examen_labo`
            ADD `obsolete` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `catalogue_labo`
            CHANGE `identifiant` `identifiant` VARCHAR(10) NOT NULL, 
            ADD `obsolete` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `pack_examens_labo`  
            ADD `obsolete` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $sql = "ALTER TABLE `catalogue_labo` 
              ADD INDEX (`function_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `examen_labo` 
              ADD INDEX (`deb_application`),
              ADD INDEX (`fin_application`),
              ADD INDEX (`realisateur`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo` 
              ADD INDEX (`date`),
              ADD INDEX (`praticien_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo_examen` 
              ADD INDEX (`pack_examens_labo_id`),
              ADD INDEX (`date`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.29";
  }
}
?>