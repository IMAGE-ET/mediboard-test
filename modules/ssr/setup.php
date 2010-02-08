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

    $this->mod_version = "0.07";
  }
}

?>