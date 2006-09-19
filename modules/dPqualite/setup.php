<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]    = "dPqualite";
$config["mod_version"] = "0.1";
$config["mod_type"]    = "user";
$config["mod_config"]  = true;

if (@$a == "setup") {
  echo dPshowModuleConfig( $config );
}

class CSetupdPqualite {

  function configure() {
    global $AppUI;
    $AppUI->redirect( "m=dPqualite&a=configure" );
    return true;
  }

  function remove() {
  	db_exec("DROP TABLE doc_ged_suivi;");    db_error();
    db_exec("DROP TABLE doc_ged;");          db_error();
    db_exec("DROP TABLE doc_chapitres;");    db_error();
    db_exec("DROP TABLE doc_themes;");       db_error();
    db_exec("DROP TABLE doc_categories;");   db_error();
    db_exec("DROP TABLE fiches_ei;");        db_error();
    db_exec("DROP TABLE ei_categories;");    db_error();
    db_exec("DROP TABLE ei_item;");          db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE `doc_ged_suivi` (
               `doc_ged_suivi_id` int(11) NOT NULL auto_increment,
               `user_id` INT(11) NOT NULL DEFAULT 0,
               `doc_ged_id` INT(11) NOT NULL DEFAULT 0,
               `file_id` INT(11) DEFAULT NULL,
               `etat` TINYINT(4),
               `remarques` TEXT DEFAULT NULL,
               `date` DATETIME,
               `actif` TINYINT(1) DEFAULT 0,
               PRIMARY KEY  (doc_ged_suivi_id)
               ) TYPE=MyISAM COMMENT='Table de suivie des procedures';";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `doc_ged` (
               `doc_ged_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
               `group_id` INT( 11 ) NOT NULL DEFAULT 0,
               `doc_chapitre_id` INT( 11 ) NOT NULL DEFAULT 0,
               `doc_theme_id` INT( 11 ) NOT NULL DEFAULT 0,
               `doc_categorie_id` INT( 11 ) NOT NULL DEFAULT 0,
               `user_id` INT(11) NOT NULL DEFAULT 0,
               `titre` VARCHAR(50) DEFAULT NULL,
               `etat` TINYINT(4),
               `annule` TINYINT(1) NOT NULL DEFAULT 0,
               `version` float default NULL,
               `num_ref` MEDIUMINT(9) UNSIGNED NULL,
               PRIMARY KEY ( doc_ged_id )
               ) TYPE = MYISAM COMMENT = 'Table des procedures';";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `doc_chapitres` (
               `doc_chapitre_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
               `nom` VARCHAR( 50 ) DEFAULT NULL ,
               `code` VARCHAR( 10 ) DEFAULT NULL ,
               PRIMARY KEY ( doc_chapitre_id )
               ) TYPE = MYISAM COMMENT = 'Table des chapitres pour les procedures';";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `doc_themes` (
               `doc_theme_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
               `nom` VARCHAR( 50 ) DEFAULT NULL ,
               PRIMARY KEY ( doc_theme_id )
               ) TYPE = MYISAM COMMENT = 'Table des theme pour les procedures';";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `doc_categories` (
               `doc_categorie_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
               `nom` VARCHAR( 50 ) DEFAULT NULL ,
               `code` VARCHAR( 1 ) DEFAULT NULL ,
               PRIMARY KEY ( doc_categorie_id )
               ) TYPE = MYISAM COMMENT = 'Table des categories pour les procedures';";
        db_exec( $sql ); db_error();
        
        $sql = "INSERT INTO `doc_categories` VALUES (1, 'Manuel qualité', 'A');";  db_exec( $sql ); db_error();
        $sql = "INSERT INTO `doc_categories` VALUES (2, 'Procédure', 'B');";       db_exec( $sql ); db_error();
        $sql = "INSERT INTO `doc_categories` VALUES (3, 'Protocole', 'C');";       db_exec( $sql ); db_error();
        $sql = "INSERT INTO `doc_categories` VALUES (4, 'Enregistement', 'D');";   db_exec( $sql ); db_error();
        $sql = "INSERT INTO `doc_categories` VALUES (5, 'Données', 'E');";         db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `ei_categories` (
                `ei_categorie_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
                `nom` VARCHAR( 50 ) DEFAULT NULL ,
                PRIMARY KEY ( ei_categorie_id )
                ) TYPE = MYISAM COMMENT = 'Table des categories des EI'";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE `ei_item` (
                `ei_item_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
                `ei_categorie_id` int( 11 ) NOT NULL DEFAULT 0 ,
                `nom` VARCHAR( 50 ) DEFAULT NULL ,
                PRIMARY KEY ( ei_item_id )
                ) TYPE = MYISAM COMMENT = 'Table des item des categories des EI'";
        db_exec( $sql ); db_error();

        $sql = "CREATE TABLE `fiches_ei` (
                `fiche_ei_id` int ( 11 ) NOT NULL AUTO_INCREMENT ,
                `user_id` int ( 11 ) NOT NULL DEFAULT 0,
                `valid_user_id` int ( 11 ) DEFAULT NULL,
                `date_fiche` DATETIME,
                `date_incident` DATETIME,
                `date_validation` DATETIME,
                `evenements` VARCHAR( 255 ) DEFAULT NULL,
                `lieu` VARCHAR( 50 ) DEFAULT NULL,
                `type_incident` TINYINT(1) NOT NULL DEFAULT 0,
                `elem_concerne` int( 1 ) NOT NULL DEFAULT 0,
                `elem_concerne_detail` TEXT DEFAULT NULL,
                `autre` TEXT DEFAULT NULL,
                `descr_faits` TEXT DEFAULT NULL,
                `mesures` TEXT DEFAULT NULL,
                `descr_consequences` TEXT DEFAULT NULL,
                `gravite` int(1) NOT NULL DEFAULT 0,
                `plainte` TINYINT(1) NOT NULL DEFAULT 0,
                `commission` TINYINT(1) NOT NULL DEFAULT 0,
                `deja_survenu` int(1) DEFAULT NULL,
                `degre_urgence` int(1) DEFAULT NULL,
                PRIMARY KEY ( fiche_ei_id )
                ) TYPE = MYISAM COMMENT ='Table des fiches incidents'";
        db_exec( $sql ); db_error();
      case "0.1":
        return "0.1";  
    }
    return false;
  }
}

?>