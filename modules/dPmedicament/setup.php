<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPmedicament extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPmedicament";
       
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    
    $query = "CREATE TABLE `produit_livret_therapeutique` (
            `produit_livret_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `group_id` INT(11) UNSIGNED NOT NULL, 
            `code_cip` INT(11) NOT NULL, 
            `prix_hopital` FLOAT, 
            `prix_ville` FLOAT, 
            `date_prix_hopital` DATE, 
            `date_prix_ville` DATE,  
            `code_interne` INT(11), 
            `commentaire` TEXT, 
            PRIMARY KEY (`produit_livret_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    
    $query = "ALTER TABLE `produit_livret_therapeutique`
            ADD `libelle` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    
    $query = "ALTER TABLE `produit_livret_therapeutique`
            DROP `libelle`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "CREATE TABLE `fiche_ATC` (
							`fiche_ATC_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`code_ATC` CHAR (3) NOT NULL,
              `libelle` VARCHAR (255),
							`description` MEDIUMTEXT
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
		$this->makeRevision("0.14");
		$query = "CREATE TABLE `produit_prescription` (
						  `produit_prescription_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `code_cip` INT (7) UNSIGNED ZEROFILL,
						  `code_ucd` INT (7) UNSIGNED ZEROFILL,
						  `code_cis` INT (8) UNSIGNED ZEROFILL,
						  `libelle` VARCHAR (255) NOT NULL,
						  `quantite` FLOAT NOT NULL,
						  `unite_prise` VARCHAR (255) NOT NULL,
						  `nb_presentation` INT (11) NOT NULL,
						  `voie` VARCHAR (255)
						) /*! ENGINE=MyISAM */;";
		$this->addQuery($query);
		
		$this->makeRevision("0.15");
		$query = "ALTER TABLE `produit_prescription` 
            ADD `unite_dispensation` VARCHAR (255) NOT NULL;";
		$this->addQuery($query);
    
    $this->makeRevision("0.16");
		
    $this->mod_version = "0.17";
  }  
}

?>