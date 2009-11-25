<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmateriel
 * @version $Revision$
 * @author Sébastien Fillonneau
 */

class CSetupdPmateriel extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPmateriel";
    
    $this->makeRevision("all");
    
    $sql = "CREATE TABLE `materiel` (
          `materiel_id` int(11) NOT NULL auto_increment,
          `nom` VARCHAR(50) NOT NULL,
          `code_barre` int(11) default NULL,
          `description` TEXT default '',
          PRIMARY KEY  (materiel_id)
          ) TYPE=MyISAM COMMENT='Table du materiel';";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `stock` (
            `stock_id` int(11) NOT NULL auto_increment,
            `materiel_id` int(11) NOT NULL,
            `group_id` int(11) NOT NULL,
            `seuil_cmd` int(11) default NULL,
            `quantite` int(11) default NULL,          
            PRIMARY KEY (stock_id),
            UNIQUE KEY `materiel_id` (`materiel_id`,`group_id`)   
            ) TYPE=MyISAM COMMENT='Table des stock du materiel';";
    $this->addQuery($sql);

    $this->makeRevision("0.10");
    
    $sql = "CREATE TABLE `materiel_category` (
        \n`category_id` int(11) NOT NULL auto_increment,
        \n`category_name` VARCHAR(50) NOT NULL,
        \nPRIMARY KEY (`category_id`)
        \n) TYPE=MyISAM;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `materiel` ADD `category_id` INT(11) NOT NULL";
    $this->addQuery( $sql);

    $this->makeRevision("0.11");
    
    $sql = " CREATE TABLE `fournisseur` (
        \n`fournisseur_id` int(11) NOT NULL auto_increment,
        \n`societe` VARCHAR(50) NOT NULL,
        \n`adresse` TEXT default '',    
        \n`codepostal` VARCHAR(5),
        \n`ville` VARCHAR(100),
        \n`telephone` VARCHAR(25),
        \n`mail` VARCHAR(100),
        \n`nom` VARCHAR(50) NOT NULL,
        \n`prenom` VARCHAR(50) NOT NULL,
        \nPRIMARY KEY (`fournisseur_id`)
        \n) TYPE=MyISAM;";
    $this->addQuery($sql);

    $sql = "CREATE TABLE `ref_materiel` (
        \n`reference_id` int(11) NOT NULL auto_increment,
        \n`fournisseur_id` int(11) NOT NULL,
        \n`materiel_id` int(11) NOT NULL,   
        \n`quantite` int(11) NOT NULL,
        \n`prix` int(11) NOT NULL,
        \nPRIMARY KEY (`reference_id`)
        \n) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");

    $sql = "ALTER TABLE `materiel_category` " .
       "\nCHANGE `category_id` `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `fournisseur` " .
       "\nCHANGE `fournisseur_id` `fournisseur_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
       "\nCHANGE `societe` `societe` varchar(255) NOT NULL," .
       "\nCHANGE `ville` `ville` varchar(255) NULL," .
       "\nCHANGE `mail` `mail` varchar(50) NULL," .
       "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
       "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
       "\nCHANGE `telephone` `telephone` bigint(10) unsigned zerofill," .
       "\nCHANGE `codepostal` `codepostal` int(5) unsigned zerofill;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `materiel` " .
       "\nCHANGE `materiel_id` `materiel_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
       "\nCHANGE `category_id` `category_id` int(11) unsigned NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `ref_materiel` " .
       "\nCHANGE `reference_id` `reference_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
       "\nCHANGE `materiel_id` `materiel_id` int(11) unsigned NOT NULL," .
       "\nCHANGE `fournisseur_id` `fournisseur_id` int(11) unsigned NOT NULL," .
       "\nCHANGE `quantite` `quantite` int(11) unsigned NOT NULL," .
       "\nCHANGE `prix` `prix` float NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `stock` " .
       "\nCHANGE `stock_id` `stock_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
       "\nCHANGE `materiel_id` `materiel_id` int(11) unsigned NOT NULL," .
       "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL," .
       "\nCHANGE `seuil_cmd` `seuil_cmd` int(11) unsigned NOT NULL," .
       "\nCHANGE `quantite` `quantite` int(11) unsigned NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "CREATE TABLE `commande_materiel` (
        \n`commande_materiel_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        \n`reference_id` int(11) UNSIGNED NOT NULL,
        \n`quantite` int(11) UNSIGNED NOT NULL,
        \n`prix` FLOAT NOT NULL,
        \n`date` DATE NOT NULL,
        \n`recu` ENUM('0','1') NOT NULL DEFAULT '0',
        \nPRIMARY KEY (`commande_materiel_id`)
        \n) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `commande_materiel` 
              ADD INDEX (`reference_id`),
              ADD INDEX (`date`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `materiel` 
              ADD INDEX (`category_id`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `ref_materiel` 
              ADD INDEX (`materiel_id`),
              ADD INDEX (`fournisseur_id`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.15";
  }  
}

?>