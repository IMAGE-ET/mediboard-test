<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPfacturation extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPfacturation";
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `facture` (
 			`facture_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
 			`date` DATE NOT NULL, 
 			`sejour_id` INT(11) UNSIGNED NOT NULL, 
			PRIMARY KEY (`facture_id`)) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);
      
     $query = "CREATE TABLE `factureitem` (
 				`factureitem_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
 				`facture_id` INT(11) UNSIGNED NOT NULL, 
 				`libelle` TEXT NOT NULL, 
 				`prix_ht` FLOAT NOT NULL, 
 				`taxe` FLOAT, 
			PRIMARY KEY (`factureitem_id`)) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);   
     
     $this->makeRevision("0.10");
     $query = "ALTER TABLE `facture` ADD `prix` FLOAT NOT NULL";
     $this->addQuery($query);
     
     $this->makeRevision("0.11");
     $query = "ALTER TABLE `facture` 
              ADD INDEX (`date`),
              ADD INDEX (`sejour_id`);";
     $this->addQuery($query);
     $query = "ALTER TABLE `factureitem` 
              ADD INDEX (`facture_id`);";
     $this->addQuery($query);
  
     $this->makeRevision("0.12");
     $query = "CREATE TABLE `facturecatalogueitem` (
              `facturecatalogueitem_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `libelle` TEXT NOT NULL,
              `prix_ht` DECIMAL (10,3) NOT NULL,
              `taxe` FLOAT NOT NULL,
              `type` ENUM ('produit','service')
     ) /*! ENGINE=MyISAM */;";
     $this->addQuery($query);
     
     $this->makeRevision("0.13");
     $query = "ALTER TABLE `factureitem` 
              ADD `ref_facture_catalogue_item_id` INT (11) UNSIGNED NOT NULL,
							ADD `reduction` DECIMAL (10,3);";
     $this->addQuery($query);
     $query = "ALTER TABLE `factureitem` 
               ADD INDEX (`facture_catalogue_item_id`)";
     $this->addQuery($query);
     
     $this->mod_version = "0.14";
  }
}
?>
