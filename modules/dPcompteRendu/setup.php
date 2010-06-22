<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CSetupdPcompteRendu extends CSetup {
  static function getTemplateReplaceQuery($search, $replace) {
    return 'UPDATE `compte_rendu` 
      SET `source` = REPLACE(`source`, "['.htmlentities($search).']", "['.htmlentities($replace).']") 
      WHERE `object_id` IS NULL';
  }

  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcompteRendu";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE compte_rendu (" .
            "\ncompte_rendu_id BIGINT NOT NULL AUTO_INCREMENT ," .
            "\nchir_id BIGINT DEFAULT '0' NOT NULL ," .
            "\nnom VARCHAR(50) ," .
            "\nsource TEXT," .
            "\ntype ENUM('consultation', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL ," .
            "\nPRIMARY KEY (compte_rendu_id) ," .
            "\nINDEX (chir_id)" .
            "\n) TYPE=MyISAM COMMENT = 'Table des modeles de compte-rendu';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE permissions" .
            "\nCHANGE permission_grant_on permission_grant_on VARCHAR(25) NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `aide_saisie` (" .
            "\n`aide_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`user_id` INT NOT NULL ," .
            "\n`module` VARCHAR(20) NOT NULL ," .
            "\n`class` VARCHAR(20) NOT NULL ," .
            "\n`field` VARCHAR(20) NOT NULL ," .
            "\n`name` VARCHAR(40) NOT NULL ," .
            "\n`text` TEXT NOT NULL ," .
            "\nPRIMARY KEY (`aide_id`)) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `liste_choix` (
                  `liste_choix_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `valeurs` TEXT,
                  PRIMARY KEY (`liste_choix_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des listes de choix personnalisées';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "CREATE TABLE `pack` (
                  `pack_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `modeles` TEXT,
                  PRIMARY KEY (`pack_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des packs post hospitalisation';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `liste_choix` ADD `compte_rendu_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` ADD INDEX (`compte_rendu_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `compte_rendu` ADD `object_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`object_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `compte_rendu` ADD `valide` TINYINT DEFAULT 0;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `compte_rendu` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`function_id`) ;";
    $this->addQuery($sql);
    $sql = " ALTER TABLE `compte_rendu` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `liste_choix` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` ADD INDEX (`function_id`) ;";
    $this->addQuery($sql);
    $sql = " ALTER TABLE `liste_choix` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `aide_saisie` DROP `module` ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('patient', 'consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "UPDATE `aide_saisie` SET `class`=CONCAT(\"C\",`class`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type`  VARCHAR(30) NOT NULL DEFAULT 'autre'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $this->setTimeLimit(1800);
    $this->addDependency("dPfiles","0.14");
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `object_class` VARCHAR(30) DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD `file_category_id` INT(11) DEFAULT 0;";
    $this->addQuery($sql);

    $aConversion = array (
      "operation"       => array("class"=>"COperation",    "nom"=>"Opération"),
      "hospitalisation" => array("class"=>"COperation",    "nom"=>"Hospitalisation"),
      "consultation"    => array("class"=>"CConsultation", "nom"=>null),
      "consultAnesth"   => array("class"=>"CConsultAnesth","nom"=>null),
      "patient"         => array("class"=>"CPatient",      "nom"=>null),
    );
    
    foreach ($aConversion as $sKey=>$aValue) {
      $sClass = $aValue["class"];
      
      // Création de nouvelle catégories
      if ($sNom = $aValue["nom"]) {
       $sql = "INSERT INTO files_category (`nom`,`class`) VALUES ('$sNom','$sClass')";
       $this->addQuery($sql);
      }
      
      // Passage des types aux classes
      $sql = "UPDATE `compte_rendu` SET `object_class`='$sClass' WHERE `object_class`='$sKey'";
      $this->addQuery($sql);
    }
      
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `aide_saisie` ADD `function_id` int(10) unsigned NULL AFTER `user_id` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `aide_saisie` " .
               "\nCHANGE `aide_id` `aide_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `class` `class` varchar(255) NOT NULL," .
               "\nCHANGE `field` `field` varchar(255) NOT NULL," .
               "\nCHANGE `name` `name` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` " .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `source` `source` mediumtext NULL," .
               "\nCHANGE `object_class` `object_class` enum('CPatient','CConsultAnesth','COperation','CConsultation') NOT NULL DEFAULT 'CPatient'," .
               "\nCHANGE `valide` `valide` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` " .
               "\nCHANGE `liste_choix_id` `liste_choix_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `pack` " .
               "\nCHANGE `pack_id` `pack_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX ( `object_class` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX ( `file_category_id` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $this->setTimeLimit(1800);
    $sql = "UPDATE `liste_choix` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `liste_choix` SET chir_id = NULL WHERE chir_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $this->setTimeLimit(1800);
    $sql = "DELETE FROM `pack` WHERE chir_id='0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `file_category_id` `file_category_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `compte_rendu` SET `file_category_id` = NULL WHERE `file_category_id` = '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    $this->setTimeLimit(1800);
    $sql = "UPDATE `compte_rendu` SET `function_id` = NULL WHERE `function_id` = '0';";
    $this->addQuery($sql);
    $sql = "UPDATE `compte_rendu` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` CHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `liste_choix` SET compte_rendu_id = NULL WHERE compte_rendu_id='0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `aide_saisie` CHANGE `user_id` `user_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `aide_saisie` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `aide_saisie` SET user_id = NULL WHERE user_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.30");
    $sql = "ALTER TABLE `aide_saisie` ADD `depend_value` varchar(255) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.31");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `object_class` `object_class` ENUM('CPatient','CConsultAnesth','COperation','CConsultation','CSejour') NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.32");
    $sql = "ALTER TABLE `pack`
            ADD `object_class` ENUM('CPatient','CConsultAnesth','COperation','CConsultation','CSejour') NOT NULL DEFAULT 'COperation';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.33");
    $sql = "UPDATE aide_saisie
      SET `depend_value` = `class`,
          `class` = 'CCompteRendu',
          `field` = 'source'
      WHERE `field` = 'compte_rendu';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.34");
    $sql = "ALTER TABLE `compte_rendu` 
			ADD `type` ENUM ('header','body','footer'),
			CHANGE `valide` `valide` ENUM ('0','1'),
			ADD `header_id` INT (11) UNSIGNED,
			ADD `footer_id` INT (11) UNSIGNED,
			ADD INDEX (`header_id`),
			ADD INDEX (`footer_id`)";
    $this->addQuery($sql);

    $this->makeRevision("0.35");
    $sql = "UPDATE `compte_rendu` 
			SET `type` = 'body'
			WHERE `object_id` IS NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.36");
    $sql = "UPDATE `compte_rendu` 
			SET `object_class` = 'CSejour'
			WHERE `file_category_id` = 3
      AND `object_class` = 'COperation'
      AND `object_id` IS NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.37");
    $sql = "ALTER TABLE `compte_rendu` 
			ADD `height` FLOAT;";
    $this->addQuery($sql);

    $this->makeRevision("0.38");
    $sql = "ALTER TABLE `compte_rendu` 
			ADD `group_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `compte_rendu` 
			ADD INDEX (`group_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.39");
    $this->addPrefQuery("saveOnPrint", 1);
    
    $this->makeRevision("0.40");
    $sql = "UPDATE `compte_rendu` 
      SET `source` = REPLACE(`source`, '<br style=\"page-break-after: always;\" />', '<hr class=\"pagebreak\" />')";
    // attention: dans le code source de la classe Cpack, on a :
    // <br style='page-break-after:always' />
    // Mais FCKeditor le transforme en :
    // <br style="page-break-after: always;" />
    // Apres verification, c'est toujours comme ça quil a transformé, donc c'est OK.
    $this->addQuery($sql);
    
		if (CModule::getInstalled('dPcabinet') && CModule::getInstalled('dPpatients')) {
	    $this->addDependency("dPcabinet", "0.79");
	    $this->addDependency("dPpatients", "0.73");
    }
    
    $this->makeRevision("0.41");
    $sql = "ALTER TABLE `aide_saisie` 
			      CHANGE `depend_value` `depend_value_1` VARCHAR (255),
            ADD `depend_value_2` VARCHAR (255);";
    $this->addQuery($sql);

    $this->makeRevision("0.42");
    $sql = "ALTER TABLE `compte_rendu` 
            ADD `etat_envoi` ENUM ('oui','non','obsolete') NOT NULL default 'non';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.43");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` 
						CHANGE `object_class` `object_class` ENUM ('CPatient','CConsultation','CConsultAnesth','COperation','CSejour','CPrescription') NOT NULL;";
    $this->addQuery($sql);
		
    $this->makeRevision("0.44");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` 
            CHANGE `object_class` `object_class` VARCHAR (80) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.45");
    $sql = "ALTER TABLE `liste_choix` ADD `group_id` INT (11) UNSIGNED";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` ADD INDEX (`group_id`)";
    $this->addQuery($sql);
        
    $this->makeRevision("0.46");
    $sql = "ALTER TABLE `aide_saisie` ADD `group_id` INT (11) UNSIGNED AFTER `function_id`";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `aide_saisie` 
              ADD INDEX (`user_id`),
              ADD INDEX (`function_id`),
              ADD INDEX (`group_id`)";
    $this->addQuery($sql);
    
    $this->makeRevision("0.47");
    $sql = self::getTemplateReplaceQuery("Opération - personnel prévu - Panseuse", "Opération - personnel prévu - Panseur");
    $this->addQuery($sql);
    $sql = self::getTemplateReplaceQuery("Opération - personnel réel - Panseuse", "Opération - personnel réel - Panseur");
    $this->addQuery($sql);
    
    $this->makeRevision("0.48");
    $sql = "ALTER TABLE `pack` 
              ADD `function_id` INT (11) UNSIGNED,
              ADD `group_id` INT (11) UNSIGNED,
              ADD INDEX (`function_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `pack` 
              CHANGE `chir_id` `chir_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.49");
    $sql = "ALTER TABLE `compte_rendu` 
              ADD `margin_top`    FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_bottom` FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_left`   FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_right`  FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `page_height`   FLOAT UNSIGNED NOT NULL DEFAULT '29.7',
              ADD `page_width`    FLOAT UNSIGNED NOT NULL DEFAULT '21'";
    $this->addQuery($sql);

    $this->makeRevision("0.50");
    $sql = "ALTER TABLE `compte_rendu` 
              ADD `private` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.51");
    $this->addPrefQuery("choicepratcab", "prat");
    
    $this->mod_version = "0.52";
  }
}
?>