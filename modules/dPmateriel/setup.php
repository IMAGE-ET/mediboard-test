<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */
 
// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"] = "dPmateriel";
$config["mod_version"] = "0.12";
$config["mod_directory"] = "dPmateriel";
$config["mod_setup_class"] = "CSetupdPmateriel";
$config["mod_type"] = "user";
$config["mod_ui_name"] = "Stocks Matériel";
$config["mod_ui_icon"] = "dPmateriel.png";
$config["mod_description"] = "Gestion du stock du matériel";
$config["mod_config"] = true;

if (@$a == "setup") {
  echo dPshowModuleConfig( $config );
}

class CSetupdPmateriel {

  function configure() {
    global $AppUI;
    $AppUI->redirect( "m=dPmateriel&a=configure" );
    return true;
  }

  function remove() {
    db_exec( "DROP TABLE materiel;" );
    db_exec( "DROP TABLE stock;" );
    db_exec( "DROP TABLE materiel_category;" );
    db_exec( "DROP TABLE fournisseur;" );
    db_exec( "DROP TABLE ref_materiel;" );
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
	    $sql = "CREATE TABLE materiel (
	          `materiel_id` int(11) NOT NULL auto_increment,
	          `nom` VARCHAR(50) NOT NULL,
	          `code_barre` int(11) default NULL,
	          `description` TEXT default '',
	          PRIMARY KEY  (materiel_id)
	          ) TYPE=MyISAM COMMENT='Table du materiel';";
	    db_exec( $sql ); db_error();
	    $sql = "CREATE TABLE stock (
	            `stock_id` int(11) NOT NULL auto_increment,
	            `materiel_id` int(11) NOT NULL,
	            `group_id` int(11) NOT NULL,
	            `seuil_cmd` int(11) default NULL,
	            `quantite` int(11) default NULL,					
	            PRIMARY KEY (stock_id),
	            UNIQUE KEY `materiel_id` (`materiel_id`,`group_id`)		
	            ) TYPE=MyISAM COMMENT='Table des stock du materiel';";
	    db_exec( $sql ); db_error();
      case "0.1":
        $sql = "CREATE TABLE `materiel_category` (
            \n`category_id` int(11) NOT NULL auto_increment,
            \n`category_name` VARCHAR(50) NOT NULL,
            \nPRIMARY KEY (`category_id`)
            \n) TYPE=MyISAM;";
        db_exec($sql);  db_error();
        $sql = "ALTER TABLE `materiel` ADD `category_id` INT(11) NOT NULL";
        db_exec($sql);  db_error();
      case "0.11":
        $sql = "CREATE TABLE `fournisseur` (
            \n`fournisseur_id` int(11) NOT NULL auto_increment,
            \n`societe` VARCHAR(50) NOT NULL,
            \n`adresse` TEXT default '',		
            \n`code_postal` VARCHAR(5),
            \n`ville` VARCHAR(100),
            \n`telephone` VARCHAR(25),
           	\n`mail` VARCHAR(100),
           	\n`nom` VARCHAR(50) NOT NULL,
            \n`prenom` VARCHAR(50) NOT NULL,
            \nPRIMARY KEY (`fournisseur_id`)
            \n) TYPE=MyISAM;";
        db_exec($sql);  db_error(); 
        $sql = "CREATE TABLE `ref_materiel` (
            \n`reference_id` int(11) NOT NULL auto_increment,
            \n`fournisseur_id` int(11) NOT NULL,
            \n`materiel_id` int(11) NOT NULL,		
            \n`quantite` int(11) NOT NULL,
            \n`prix` int(11) NOT NULL,
            \nPRIMARY KEY (`reference_id`)
            \n) TYPE=MyISAM;";
        db_exec($sql);  db_error();       
      case "0.12":
        return "0.12";  
    }
    return false;
  }
}

?>