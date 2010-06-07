<?php /* $Id: setup.php 6144 2009-04-21 14:22:50Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6144 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupssr extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = "ssr";

    $this->makeRevision("all");
    
		// Plateau technique
    $this->makeRevision("0.01");
		$query = "CREATE TABLE `plateau_technique` (
      `plateau_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `group_id` INT (11) UNSIGNED,
      `nom` VARCHAR (255) NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
		$query = "ALTER TABLE `plateau_technique` 
      ADD INDEX (`group_id`);";
		$this->addQuery($query);

    // Equipement
    $this->makeRevision("0.02");
    $query = "CREATE TABLE `equipement` (
	    `equipement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `plateau_id` INT (11) UNSIGNED NOT NULL,
	    `nom` VARCHAR (255) NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `equipement` 
      ADD INDEX (`plateau_id`);";
    $this->addQuery($query);

    // Technicien
    $this->makeRevision("0.03");
    $this->addDependency("mediusers", "0.1");
    $query = "CREATE TABLE `technicien` (
      `technicien_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `plateau_id` INT (11) UNSIGNED NOT NULL,
      `kine_id` INT (11) UNSIGNED NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `technicien` 
      ADD INDEX (`plateau_id`),
      ADD INDEX (`kine_id`);";
    $this->addQuery($query);
    
    // Fiche d'autonomie
    $this->makeRevision("0.04");
    $query = "CREATE TABLE `fiche_autonomie` (
      `fiche_autonomie_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `sejour_id` INT (11) UNSIGNED NOT NULL,
      `alimentation` ENUM ('autonome','partielle','totale'),
      `toilette` ENUM ('autonome','partielle','totale'),
      `habillage_haut` ENUM ('autonome','partielle','totale'),
      `habillage_bas` ENUM ('autonome','partielle','totale'),
      `utilisation_toilette` ENUM ('sonde','couche','bassin','stomie'),
      `transfert_lit` ENUM ('autonome','partielle','totale'),
      `locomotion` ENUM ('autonome','partielle','totale'),
      `locomotion_materiel` ENUM ('canne','cadre','fauteuil'),
      `escalier` ENUM ('autonome','partielle','totale'),
      `pansement` ENUM ('0','1'),
      `escarre` ENUM ('0','1'),
      `comprehension` ENUM ('intacte','alteree'),
      `expression` ENUM ('intacte','alteree'),
      `memoire` ENUM ('intacte','alteree'),
      `resolution_pb` ENUM ('intacte','alteree'),
      `etat_psychique` TEXT,
      `devenir_envisage` TEXT
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `fiche_autonomie` 
      ADD INDEX (`sejour_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    $query = "ALTER TABLE `fiche_autonomie` 
      ADD `soins_cutanes` TEXT;";
    $this->addQuery($query);
    
    // Bilan SSR
    $this->makeRevision("0.06");
    $query = "CREATE TABLE `bilan_ssr` (
	    `bilan_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `sejour_id` INT (11) UNSIGNED NOT NULL,
	    `kine` VARCHAR (255) NOT NULL,
	    `ergo` VARCHAR (255) NOT NULL,
	    `psy` VARCHAR (255) NOT NULL,
	    `ortho` VARCHAR (255) NOT NULL,
	    `diet` VARCHAR (255) NOT NULL,
	    `social` VARCHAR (255) NOT NULL,
	    `apa` VARCHAR (255) NOT NULL,
	    `entree` TEXT,
	    `sortie` TEXT
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `bilan_ssr` 
      ADD INDEX (`sejour_id`);";
    $this->addQuery($query);

    // RHS
    $this->makeRevision("0.07");
    $query = "CREATE TABLE `rhs` (
      `rhs_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `sejour_id` INT (11) UNSIGNED NOT NULL,
      `date_monday` DATE NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `rhs` 
      ADD INDEX (`sejour_id`),
      ADD INDEX (`date_monday`);";
    $this->addQuery($query);
		
    // Dépendances RHS
    $this->makeRevision("0.08");
    $query = "CREATE TABLE `dependances_rhs` (
	    `dependances_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `rhs_id` INT (11) UNSIGNED NOT NULL,
	    `habillage`    ENUM ('1','2','3','4'),
	    `deplacement`  ENUM ('1','2','3','4'),
	    `alimentation` ENUM ('1','2','3','4'),
	    `continence`   ENUM ('1','2','3','4'),
	    `comportement` ENUM ('1','2','3','4'),
	    `relation`     ENUM ('1','2','3','4')
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `dependances_rhs` 
      ADD INDEX (`rhs_id`);";
    $this->addQuery($query);
    
    // Ligne d'activités RHS
    $this->makeRevision("0.09");
    $query = "CREATE TABLE `ligne_activites_rhs` (
	    `ligne_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `rhs_id` INT (11) UNSIGNED NOT NULL,
	    `executant_id` INT (11) UNSIGNED NOT NULL,
	    `code_activite_cdarr` CHAR (4),
	    `code_intervenant_cdarr` CHAR (2),
	    `qty_mon` INT (11),
	    `qty_tue` INT (11),
	    `qty_wed` INT (11),
	    `qty_thu` INT (11),
	    `qty_fri` INT (11),
	    `qty_sat` INT (11),
	    `qty_sun` INT (11)
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ligne_activites_rhs` 
      ADD INDEX (`rhs_id`),
      ADD INDEX (`executant_id`);";
    $this->addQuery($query);
    
    // Bilan SSR: suppresion des anciennes prescriptions texte, ajout du kine
    $this->makeRevision("0.10");
    $query = "ALTER TABLE `bilan_ssr` 
      ADD `kine_id` INT (11) UNSIGNED,
      DROP COLUMN `kine`,
      DROP COLUMN `ergo`,
      DROP COLUMN `psy`,
      DROP COLUMN `ortho`,
      DROP COLUMN `social`,
      DROP COLUMN `diet`,
      DROP COLUMN `apa`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `bilan_ssr` 
      ADD INDEX (`kine_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `ligne_activites_rhs` 
              CHANGE `code_activite_cdarr` `code_activite_cdarr` CHAR (4) NOT NULL,
              CHANGE `qty_mon` `qty_mon` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_tue` `qty_tue` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_wed` `qty_wed` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_thu` `qty_thu` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_fri` `qty_fri` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_sat` `qty_sat` TINYINT (4) UNSIGNED DEFAULT '0',
              CHANGE `qty_sun` `qty_sun` TINYINT (4) UNSIGNED DEFAULT '0';";
    $this->addQuery($sql);
		
		$this->makeRevision("0.12");
		$sql = "CREATE TABLE `element_prescription_to_cdarr` (
              `element_prescription_to_cdarr_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `element_prescription_id` INT (11) UNSIGNED NOT NULL,
              `code` CHAR (4) NOT NULL,
              `commentaire` VARCHAR (255)
						) TYPE=MYISAM;";
		$this->addQuery($sql);

    $sql = "ALTER TABLE `element_prescription_to_cdarr` 
              ADD INDEX (`element_prescription_id`);";
		$this->addQuery($sql);
		
		$this->makeRevision("0.13");
		$sql = "CREATE TABLE `evenement_ssr` (
              `evenement_ssr_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `element_prescription_id` INT (11) UNSIGNED NOT NULL,
              `code` CHAR (4) NOT NULL,
              `sejour_id` INT (11) UNSIGNED NOT NULL,
              `debut` DATETIME NOT NULL,
              `duree` INT (11) UNSIGNED NOT NULL,
              `therapeute_id` INT (11) UNSIGNED NOT NULL,
              `realise` ENUM ('0','1') DEFAULT '0'
		) TYPE=MYISAM;";
		$this->addQuery($sql);

    $sql = "ALTER TABLE `evenement_ssr` 
              ADD INDEX (`element_prescription_id`),
              ADD INDEX (`sejour_id`),
              ADD INDEX (`debut`),
              ADD INDEX (`therapeute_id`);";
		$this->addQuery($sql);
		
		$this->makeRevision("0.14");
		$sql = "ALTER TABLE `evenement_ssr` 
              ADD `equipement_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
		
		$sql = "ALTER TABLE `evenement_ssr` 
              ADD INDEX (`equipement_id`);";
    $this->addQuery($sql);
		
		$this->makeRevision("0.15");
		$sql = "ALTER TABLE `bilan_ssr` 
            ADD `technicien_id` INT (11) UNSIGNED;";
	  $this->addQuery($sql);
		
		$sql = "ALTER TABLE `bilan_ssr` 
            ADD INDEX (`technicien_id`);";
		$this->addQuery($sql);
		
		$sql = "UPDATE `bilan_ssr`
		        SET technicien_id = 
						  (SELECT technicien_id 
							FROM technicien
							WHERE kine_id = bilan_ssr.kine_id
							LIMIT 1);";
		$this->addQuery($sql);
		
		$sql = "ALTER TABLE `bilan_ssr` 
		        DROP kine_id";
		$this->addQuery($sql);
		
		$this->makeRevision("0.16");
		$sql = "CREATE TABLE `acte_cdarr` (
              `acte_cdarr_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `evenement_ssr_id` INT (11) UNSIGNED NOT NULL,
              `code` CHAR (4) NOT NULL
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
		 
    $sql = "ALTER TABLE `acte_cdarr` 
              ADD INDEX (`evenement_ssr_id`);";
		$this->addQuery($sql);
		
		$sql = "INSERT INTO `acte_cdarr` (`evenement_ssr_id`,`code`)
		        SELECT `evenement_ssr_id`,`code`
						FROM evenement_ssr";
		$this->addQuery($sql);
		
		$sql = "ALTER TABLE `evenement_ssr` 
              DROP `code`;";
    $this->addQuery($sql);
    
		$this->makeRevision("0.17");
		$sql = "ALTER TABLE `evenement_ssr` 
            ADD `remarque` VARCHAR (255);";
		$this->addQuery($sql);
		
		$this->makeRevision("0.18");
		$sql = "ALTER TABLE `bilan_ssr` 
            ADD `brancardage` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($sql);
		
		$this->makeRevision("0.19");
		$sql = "CREATE TABLE `replacement` (
              `replacement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `sejour_id` INT (11) UNSIGNED NOT NULL,
              `conge_id` INT (11) UNSIGNED NOT NULL,
              `replacer_id` INT (11) UNSIGNED NOT NULL
            ) TYPE=MYISAM;";
		$this->addQuery($sql);

    $sql = "ALTER TABLE `replacement` 
              ADD INDEX (`sejour_id`),
              ADD INDEX (`conge_id`),
              ADD INDEX (`replacer_id`);";
    $this->addQuery($sql);
		
		$this->mod_version = "0.20";
    
    // Data source query
    $query = "SHOW COLUMNS FROM type_activite LIKE 'libelle_court'";
    $this->addDatasource("cdarr", $query);
  }
}

?>