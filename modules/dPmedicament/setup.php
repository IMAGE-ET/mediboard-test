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
    
    $this->makeRevision("0.17");

    $query = "ALTER TABLE `produit_livret_therapeutique` 
              ADD `unite_prise` VARCHAR (255);";
    $this->addquery($query);
    
		$query = "ALTER TABLE `produit_livret_therapeutique` 
              ADD INDEX (`group_id`),
              ADD INDEX (`date_prix_hopital`),
              ADD INDEX (`date_prix_ville`);";		
		$this->addquery($query);

    $this->makeRevision("0.18");
		$query = "ALTER TABLE `produit_livret_therapeutique`
              ADD `code_ucd` INT (7) UNSIGNED ZEROFILL,
              ADD `code_cis` INT (8) UNSIGNED ZEROFILL;";
		$this->addQuery($query);
		
		$this->makeRevision("0.19");
		
		$query = "ALTER TABLE `produit_livret_therapeutique` 
              CHANGE `group_id` `owner_crc` INT (11) UNSIGNED NOT NULL,
							CHANGE `commentaire` `commentaire` VARCHAR (255);";
		$this->addQuery($query);
		
		$query = "UPDATE `produit_livret_therapeutique` 
              SET `owner_crc` = ABS(CRC32(CONCAT('CGroups-', owner_crc)) - POW(2, 31))";
    $this->addQuery($query);
		
		$this->makeRevision("0.20");
	
		function synchronizeLivret() {
			if(CModule::getActive("bcb")){
				// Initialisations des dsn
				$ds_bcb = CBcbObject::getDataSource();
				$ds_std = CSQLDataSource::get("std");

        // Chargement de tous les produits presents dans le livret de la BCB
				$query = "SELECT * FROM LIVRETTHERAPEUTIQUE";
				$produits = $ds_bcb->loadList($query);
				
				// Parcours des produits
				foreach($produits as $_produit){
					$code_cip = $_produit['CODECIP'];
					
					// Chargement des codes ucd et cis du produit
					$query = "SELECT CODE_UCD, CODECIS FROM IDENT_PRODUITS WHERE CODE_CIP = '$code_cip';";
					$codes = $ds_bcb->loadHash($query);
					
					$code_ucd = $codes['CODE_UCD'];
					$code_cis = $codes['CODECIS'];
					
					$owner_crc = $_produit['CODEETABLISSEMENT'];
					$prix_hopital = $_produit['PRIXHOPITAL'];
					$prix_ville = $_produit['PRIXVILLE'];
	        $date_prix_hopital = $_produit['DATEPRIXHOPITAL'];
	        $date_prix_ville = $_produit['DATEPRIXVILLE'];
	        $code_interne = $_produit['CODEINTERNE'];
	        $commentaire = $_produit['COMMENTAIRE'];
	
	        // Recherche du produit dans le livret therapeutique dans Mediboard
					$query = "SELECT produit_livret_id 
					          FROM produit_livret_therapeutique
					          WHERE code_cip = '$code_cip'
										AND owner_crc = '$owner_crc'";
					$produit_livret_id = $ds_std->loadResult($query);
					
					// Si le produit est present, on le met a jour
					if($produit_livret_id){
						$query = "UPDATE produit_livret_therapeutique 
	                    SET ";
											
						$query .= $prix_hopital ? "prix_hopital = '$prix_hopital', " : '';
						$query .= $prix_ville ? "prix_ville = '$prix_ville', " : '';
            $query .= $date_prix_hopital ? "date_prix_hopital = '$date_prix_hopital', ": '';
            $query .= $date_prix_ville ? "date_prix_ville = '$date_prix_ville', " : '';
            $query .= $code_interne ? "code_interne = '$code_interne', " : '';
            					
						$query .= "commentaire = '$commentaire', 
											 code_ucd = '$code_ucd', 
											 code_cis = '$code_cis' 
	                     WHERE produit_livret_id = $produit_livret_id;";
					}
					// Sinon, on le crée
					else {
						$query = "INSERT INTO produit_livret_therapeutique (`produit_livret_id`,`owner_crc`,`code_cip`,`code_ucd`,`code_cis`) 
						          VALUES ('','$owner_crc','$code_cip','$code_ucd','$code_cis')";
					}
					$ds_std->exec($query);
				}
			}
			return true;
		}
		
		$this->addFunction("synchronizeLivret");
	  
    $this->mod_version = "0.21";
  }  
}

?>