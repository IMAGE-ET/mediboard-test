<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"] = "dPcompteRendu";
$config["mod_version"] = "0.20";
$config["mod_directory"] = "dPcompteRendu";
$config["mod_setup_class"] = "CSetupdPcompteRendu";
$config["mod_type"] = "user";
$config["mod_ui_name"] = "Compte Rendu";
$config["mod_ui_icon"] = "dPcompteRendu.png";
$config["mod_description"] = "Gestion des comptes-rendus";
$config["mod_config"] = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPcompteRendu {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPcompteRendu&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE compte_rendu;"); db_error();
    db_exec("DROP TABLE aide_saisie;"); db_error();
    db_exec("DROP TABLE liste_choix;"); db_error();
    db_exec("DROP TABLE pack;"); db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE compte_rendu (" .
            "\ncompte_rendu_id BIGINT NOT NULL AUTO_INCREMENT ," .
            "\nchir_id BIGINT DEFAULT '0' NOT NULL ," .
            "\nnom VARCHAR(50) ," .
            "\nsource TEXT," .
            "\ntype ENUM('consultation', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL ," .
            "\nPRIMARY KEY (compte_rendu_id) ," .
            "\nINDEX (chir_id)" .
            "\n) TYPE=MyISAM COMMENT = 'Table des modeles de compte-rendu';";
        db_exec($sql); db_error();
        
        $sql = "ALTER TABLE permissions" .
            "\nCHANGE permission_grant_on permission_grant_on VARCHAR(25) NOT NULL";
        db_exec($sql); db_error();
      case "0.1":
        $sql = "CREATE TABLE `aide_saisie` (" .
            "\n`aide_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`user_id` INT NOT NULL ," .
            "\n`module` VARCHAR(20) NOT NULL ," .
            "\n`class` VARCHAR(20) NOT NULL ," .
            "\n`field` VARCHAR(20) NOT NULL ," .
            "\n`name` VARCHAR(40) NOT NULL ," .
            "\n`text` TEXT NOT NULL ," .
            "\nPRIMARY KEY (`aide_id`)) TYPE=MyISAM;";
        db_exec($sql); db_error();
      case "0.11":
        $sql = "CREATE TABLE `liste_choix` (
                  `liste_choix_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `valeurs` TEXT,
                  PRIMARY KEY (`liste_choix_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des listes de choix personnalisées';";
        db_exec($sql); db_error();
      case "0.12":
        $sql = "CREATE TABLE `pack` (
                  `pack_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `modeles` TEXT,
                  PRIMARY KEY (`pack_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des packs post hospitalisation';";
        db_exec($sql); db_error();
      case "0.13":
        $sql = "ALTER TABLE `liste_choix` ADD `compte_rendu_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `liste_choix` ADD INDEX (`compte_rendu_id`) ;";
        db_exec($sql); db_error();
      case "0.14":
        $sql = "ALTER TABLE `compte_rendu` ADD `object_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`object_id`) ;";
        db_exec($sql); db_error();
      case "0.15":
        $sql = "ALTER TABLE `compte_rendu` ADD `valide` TINYINT DEFAULT 0;";
        db_exec($sql); db_error();
      case "0.16":
        $sql = "ALTER TABLE `compte_rendu` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`function_id`) ;";
        db_exec($sql); db_error();
        $sql = " ALTER TABLE `compte_rendu` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
        db_exec($sql); db_error();
      case "0.17":
        $sql = "ALTER TABLE `liste_choix` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `liste_choix` ADD INDEX (`function_id`) ;";
        db_exec($sql); db_error();
        $sql = " ALTER TABLE `liste_choix` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
        db_exec($sql); db_error();
      case "0.18":
        $sql = "ALTER TABLE `aide_saisie` DROP `module` ";
        db_exec($sql); db_error();
      case "0.19":
        set_time_limit(1800);
        $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
        db_exec($sql); db_error();
      case "0.20":
        $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('patient', 'consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
        db_exec($sql); db_error();
      case "0.21":
        return "0.21";
    }
    return false;
  }
}

?>