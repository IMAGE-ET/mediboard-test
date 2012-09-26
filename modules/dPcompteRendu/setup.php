<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CSetupdPcompteRendu extends CSetup {
  
  /**
   * Build an SQL query to replace a template string
   * Will check over content_html table to specify update query
   * @param string $search
   * @param string $newname
   * @param bool $force_content_table
   * @return string The SQL Query
   */
  static function replaceTemplateQuery($search, $replace, $force_content_table = false) {
    $search  = htmlentities($search);
    $replace = htmlentities($replace);
    
    $ds = CSQLDataSource::get("std");
    
    // Content specific table 
    if ($force_content_table || ($ds->loadTable("compte_rendu") && $ds->loadField("compte_rendu", "content_id"))) {
      return "UPDATE compte_rendu AS cr, content_html AS ch
        SET ch.content = REPLACE(`content`, '$search', '$replace')
        WHERE cr.object_id IS NULL
        AND cr.content_id = ch.content_id";
    }
    
    // Single table
    return "UPDATE `compte_rendu` 
      SET `source` = REPLACE(`source`, '$search', '$replace') 
      WHERE `object_id` IS NULL";    
  }
  
  /**
   * Build an SQL query to rename a template field 
   * Will check over content_html table to specify update query
   * @param string $search
   * @param string $newname
   * @param bool $force_content_table
   * @return string The SQL Query
   */
  static function renameTemplateFieldQuery($oldname, $newname, $force_content_table = false) {
    return self::replaceTemplateQuery("[$oldname]", "[$newname]", $force_content_table);
  }

  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcompteRendu";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE compte_rendu (" .
            "\ncompte_rendu_id BIGINT NOT NULL AUTO_INCREMENT ," .
            "\nchir_id BIGINT DEFAULT '0' NOT NULL ," .
            "\nnom VARCHAR(50) ," .
            "\nsource TEXT," .
            "\ntype ENUM('consultation', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL ," .
            "\nPRIMARY KEY (compte_rendu_id) ," .
            "\nINDEX (chir_id)" .
            "\n) /*! ENGINE=MyISAM */ COMMENT = 'Table des modeles de compte-rendu';";
    $this->addQuery($query);
    $query = "ALTER TABLE permissions" .
            "\nCHANGE permission_grant_on permission_grant_on VARCHAR(25) NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `aide_saisie` (" .
            "\n`aide_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`user_id` INT NOT NULL ," .
            "\n`module` VARCHAR(20) NOT NULL ," .
            "\n`class` VARCHAR(20) NOT NULL ," .
            "\n`field` VARCHAR(20) NOT NULL ," .
            "\n`name` VARCHAR(40) NOT NULL ," .
            "\n`text` TEXT NOT NULL ," .
            "\nPRIMARY KEY (`aide_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "CREATE TABLE `liste_choix` (
                  `liste_choix_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `valeurs` TEXT,
                  PRIMARY KEY (`liste_choix_id`) ,
                  INDEX (`chir_id`)
                ) /*! ENGINE=MyISAM */ COMMENT = 'table des listes de choix personnalisées';";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "CREATE TABLE `pack` (
                  `pack_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `modeles` TEXT,
                  PRIMARY KEY (`pack_id`) ,
                  INDEX (`chir_id`)
                ) /*! ENGINE=MyISAM */ COMMENT = 'table des packs post hospitalisation';";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `liste_choix` ADD `compte_rendu_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `liste_choix` ADD INDEX (`compte_rendu_id`) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `compte_rendu` ADD `object_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` ADD INDEX (`object_id`) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `compte_rendu` ADD `valide` TINYINT DEFAULT 0;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `compte_rendu` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` ADD INDEX (`function_id`) ;";
    $this->addQuery($query);
    $query = " ALTER TABLE `compte_rendu` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `liste_choix` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `liste_choix` ADD INDEX (`function_id`) ;";
    $this->addQuery($query);
    $query = " ALTER TABLE `liste_choix` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `aide_saisie` DROP `module` ";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('patient', 'consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "UPDATE `aide_saisie` SET `class`=CONCAT(\"C\",`class`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` CHANGE `type` `type`  VARCHAR(30) NOT NULL DEFAULT 'autre'";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $this->setTimeLimit(1800);
    $this->addDependency("dPfiles","0.14");
    $query = "ALTER TABLE `compte_rendu` CHANGE `type` `object_class` VARCHAR(30) DEFAULT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` ADD `file_category_id` INT(11) DEFAULT 0;";
    $this->addQuery($query);

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
       $query = "INSERT INTO files_category (`nom`,`class`) VALUES ('$sNom','$sClass')";
       $this->addQuery($query);
      }
      
      // Passage des types aux classes
      $query = "UPDATE `compte_rendu` SET `object_class`='$sClass' WHERE `object_class`='$sKey'";
      $this->addQuery($query);
    }
      
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `aide_saisie` ADD `function_id` int(10) unsigned NULL AFTER `user_id` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `aide_saisie` " .
               "\nCHANGE `aide_id` `aide_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `class` `class` varchar(255) NOT NULL," .
               "\nCHANGE `field` `field` varchar(255) NOT NULL," .
               "\nCHANGE `name` `name` varchar(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` " .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `source` `source` mediumtext NULL," .
               "\nCHANGE `object_class` `object_class` enum('CPatient','CConsultAnesth','COperation','CConsultation') NOT NULL DEFAULT 'CPatient'," .
               "\nCHANGE `valide` `valide` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `liste_choix` " .
               "\nCHANGE `liste_choix_id` `liste_choix_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `pack` " .
               "\nCHANGE `pack_id` `pack_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` ADD INDEX ( `object_class` );";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` ADD INDEX ( `file_category_id` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $this->setTimeLimit(1800);
    $query = "UPDATE `liste_choix` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($query);
    $query = "UPDATE `liste_choix` SET chir_id = NULL WHERE chir_id='0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $this->setTimeLimit(1800);
    $query = "DELETE FROM `pack` WHERE chir_id='0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `compte_rendu` CHANGE `file_category_id` `file_category_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `compte_rendu` SET `file_category_id` = NULL WHERE `file_category_id` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $this->setTimeLimit(1800);
    $query = "UPDATE `compte_rendu` SET `function_id` = NULL WHERE `function_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `compte_rendu` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `liste_choix` CHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `liste_choix` SET compte_rendu_id = NULL WHERE compte_rendu_id='0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `aide_saisie` CHANGE `user_id` `user_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `aide_saisie` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($query);
    $query = "UPDATE `aide_saisie` SET user_id = NULL WHERE user_id='0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `aide_saisie` ADD `depend_value` varchar(255) DEFAULT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` CHANGE `object_class` `object_class` ENUM('CPatient','CConsultAnesth','COperation','CConsultation','CSejour') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `pack`
            ADD `object_class` ENUM('CPatient','CConsultAnesth','COperation','CConsultation','CSejour') NOT NULL DEFAULT 'COperation';";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "UPDATE aide_saisie
      SET `depend_value` = `class`,
          `class` = 'CCompteRendu',
          `field` = 'source'
      WHERE `field` = 'compte_rendu';";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `compte_rendu` 
      ADD `type` ENUM ('header','body','footer'),
      CHANGE `valide` `valide` ENUM ('0','1'),
      ADD `header_id` INT (11) UNSIGNED,
      ADD `footer_id` INT (11) UNSIGNED,
      ADD INDEX (`header_id`),
      ADD INDEX (`footer_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.35");
    $query = "UPDATE `compte_rendu` 
      SET `type` = 'body'
      WHERE `object_id` IS NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $query = "UPDATE `compte_rendu` 
      SET `object_class` = 'CSejour'
      WHERE `file_category_id` = 3
      AND `object_class` = 'COperation'
      AND `object_id` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    $query = "ALTER TABLE `compte_rendu` 
      ADD `height` FLOAT;";
    $this->addQuery($query);

    $this->makeRevision("0.38");
    $query = "ALTER TABLE `compte_rendu` 
      ADD `group_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `compte_rendu` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    $this->addPrefQuery("saveOnPrint", 1);
    
    $this->makeRevision("0.40");
    $query = "UPDATE `compte_rendu` 
      SET `source` = REPLACE(`source`, '<br style=\"page-break-after: always;\" />', '<hr class=\"pagebreak\" />')";
    // attention: dans le code source de la classe Cpack, on a :
    // <br style='page-break-after:always' />
    // Mais FCKeditor le transforme en :
    // <br style="page-break-after: always;" />
    // Apres verification, c'est toujours comme ça quil a transformé, donc c'est OK.
    $this->addQuery($query);
    
    if (CModule::getInstalled('dPcabinet') && CModule::getInstalled('dPpatients')) {
      $this->addDependency("dPcabinet", "0.79");
      $this->addDependency("dPpatients", "0.73");
    }
    
    $this->makeRevision("0.41");
    $query = "ALTER TABLE `aide_saisie` 
            CHANGE `depend_value` `depend_value_1` VARCHAR (255),
            ADD `depend_value_2` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.42");
    $query = "ALTER TABLE `compte_rendu` 
            ADD `etat_envoi` ENUM ('oui','non','obsolete') NOT NULL default 'non';";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` 
            CHANGE `object_class` `object_class` ENUM ('CPatient','CConsultation','CConsultAnesth','COperation','CSejour','CPrescription') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.44");
    $this->setTimeLimit(1800);
    $query = "ALTER TABLE `compte_rendu` 
            CHANGE `object_class` `object_class` VARCHAR (80) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `liste_choix` ADD `group_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `liste_choix` ADD INDEX (`group_id`)";
    $this->addQuery($query);
        
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `aide_saisie` ADD `group_id` INT (11) UNSIGNED AFTER `function_id`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `aide_saisie` 
              ADD INDEX (`user_id`),
              ADD INDEX (`function_id`),
              ADD INDEX (`group_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    $query = self::renameTemplateFieldQuery("Opération - personnel prévu - Panseuse", "Opération - personnel prévu - Panseur");
    $this->addQuery($query);
    $query = self::renameTemplateFieldQuery("Opération - personnel réel - Panseuse", "Opération - personnel réel - Panseur");
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    $query = "ALTER TABLE `pack` 
              ADD `function_id` INT (11) UNSIGNED,
              ADD `group_id` INT (11) UNSIGNED,
              ADD INDEX (`function_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `pack` 
              CHANGE `chir_id` `chir_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.49");
    $query = "ALTER TABLE `compte_rendu` 
              ADD `margin_top`    FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_bottom` FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_left`   FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `margin_right`  FLOAT UNSIGNED NOT NULL DEFAULT '2',
              ADD `page_height`   FLOAT UNSIGNED NOT NULL DEFAULT '29.7',
              ADD `page_width`    FLOAT UNSIGNED NOT NULL DEFAULT '21'";
    $this->addQuery($query);

    $this->makeRevision("0.50");
    $query = "ALTER TABLE `compte_rendu` 
              ADD `private` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    $this->addPrefQuery("choicepratcab", "prat");

    $this->makeRevision("0.52");
    
    $query = "INSERT INTO content_html (content, cr_id) SELECT source, compte_rendu_id FROM compte_rendu";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `compte_rendu` DROP `source`";
    $this->addQuery($query);

    $query = "ALTER TABLE `compte_rendu` 
              ADD `content_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `compte_rendu` 
              ADD INDEX (`content_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE compte_rendu c JOIN content_html ch ON c.compte_rendu_id = ch.cr_id
            SET c.content_id = ch.content_id";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `content_html` DROP `cr_id`";
    $this->addQuery($query);

    $this->makeRevision("0.53");
    
    // Déplacement du contenthtml dans system

    $this->makeRevision("0.54");

    $query = "ALTER TABLE `compte_rendu`
            ADD `fast_edit` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $query = "UPDATE `aide_saisie` SET `field` = '_source' WHERE `class` = 'CCompteRendu' AND `field` = 'source'";
    $this->addQuery($query);
    
    $this->makeRevision("0.55");
    
    $query = "CREATE TABLE `modele_to_pack` (
              `modele_to_pack_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `modele_id` INT (11) UNSIGNED,
              `pack_id` INT (11) UNSIGNED
           ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `modele_to_pack` 
              ADD INDEX (`modele_id`),
              ADD INDEX (`pack_id`);
           ";
    $this->addQuery($query);
    
   
    // Insertion des modèles par référence pour les packs
    function setup_addmodeles() {
      $ds = CSQLDataSource::get("std");
      $query = "SELECT * from pack;";	
      $packs = $ds->loadList($query);

      foreach($packs as $_pack) {
        if ($_pack['modeles'] == '') continue;
        $modeles = explode("|", $_pack['modeles']);
        if (count($modeles) == 0) continue;
        
        $compterendu = new CCompteRendu;
        foreach($modeles as $_modele) {
          if (!$compterendu->load($_modele)) continue;
          $query = "INSERT INTO modele_to_pack (modele_id, pack_id)
                  VALUES ($_modele, {$_pack['pack_id']})";
          $ds->exec($query);
        }
      }
      return true;
    }
    
    $this->addFunction("setup_addmodeles");
    
    $this->makeRevision("0.56");
    
    $query = "ALTER TABLE `pack`
              DROP `modeles`";
    $this->addQuery($query);
    
    $this->makeRevision("0.57");
    
    // Modification des user logs, seulement pour ceux qui ne font 
    // reference qu'au champ "source" des compte rendus. Dans le cas où d'autres 
    // champs sont listés, il faudrait splitter le user_log en deux : 
    // un pour le CR et un pour le ContentHTML
    // Actions effectuées : 
    // - remplacement de source par content
    // - remplacement de CCompteRendu par CContentHTML
    // - mise à jour de l'object_id
    $query = "
    UPDATE user_log 
    LEFT JOIN compte_rendu ON compte_rendu.compte_rendu_id = user_log.object_id
    LEFT JOIN content_html ON content_html.content_id = compte_rendu.content_id
    
    SET 
      user_log.object_class = 'CContentHTML',
      user_log.object_id = compte_rendu.content_id,
      user_log.fields = 'content'
      
    WHERE user_log.object_class = 'CCompteRendu'
      AND user_log.object_id = compte_rendu.compte_rendu_id
      AND user_log.fields = 'source'";
    $this->addQuery($query);
    
    $this->makeRevision("0.58");
    $this->addPrefQuery("listDefault", "ulli");
    $this->addPrefQuery("listBrPrefix", "&bull;");
    $this->addPrefQuery("listInlineSeparator", ";");
    
    $this->makeRevision("0.59");
    $query = "ALTER TABLE `compte_rendu`
            ADD `fast_edit_pdf` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $query = self::replaceTemplateQuery("-- tous]", "- tous]", true);
    $this->addQuery($query);
    
    $query = self::replaceTemplateQuery("-- tous par appareil]", "- tous par appareil]", true);
    $this->addQuery($query);

    $query = self::replaceTemplateQuery("[Constantes mode", "[Constantes - mode", true);
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = self::replaceTemplateQuery("[Patient - médecin correspondants]", "[Patient - médecins correspondants]", true);
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    $this->addPrefQuery("aideTimestamp"   , "1");
    $this->addPrefQuery("aideOwner"       , "0");
    $this->addPrefQuery("aideFastMode"    , "1");
    $this->addPrefQuery("aideAutoComplete", "1");
    $this->addPrefQuery("aideShowOver"    , "1");
    
    $this->makeRevision("0.62");
    $this->addPrefQuery("mode_play", "0");
    
    $this->makeRevision("0.63");
    $query = "ALTER TABLE `compte_rendu`
              CHANGE chir_id user_id INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `pack`
              CHANGE chir_id user_id INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `liste_choix`
              CHANGE chir_id user_id INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $query = "ALTER TABLE `pack`
              ADD `fast_edit` ENUM ('0','1') NOT NULL DEFAULT '0',
              ADD `fast_edit_pdf` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $query = self::replaceTemplateQuery("[RPU - Mode", "[Sejour - Mode", true);
    
    $this->addQuery($query);
    
    $this->makeRevision("0.66");
    // Table consultation_anesth
    $this->addDependency("dPcabinet"   , "0.31");
    // Table sejour
    $this->addDependency("dPplanningOp", "0.37");
    
    $query = "ALTER TABLE `compte_rendu`
      ADD `author_id` INT(11) UNSIGNED AFTER `function_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `compte_rendu`
      ADD INDEX (`author_id`);";
    $this->addQuery($query);
    
    // Table temporaire de mappage entre le author_id (user_id du first log) et le compte_rendu_id
    $query = "CREATE TEMPORARY TABLE `owner_doc` (
      `compte_rendu_id` INT(11), `author_id` INT(11)) AS
      SELECT `compte_rendu_id`, `user_log`.`user_id` as `author_id`
      FROM `compte_rendu`, `user_log`
      WHERE `user_log`.`object_class` = 'CCompteRendu'
      AND `user_log`.`object_id` = `compte_rendu`.`compte_rendu_id`
      AND `user_log`.`type` = 'create';";
    $this->addQuery($query);
    
    $query = "UPDATE `compte_rendu`
      JOIN `owner_doc` ON `compte_rendu`.`compte_rendu_id` = `owner_doc`.`compte_rendu_id`
      SET `compte_rendu`.`author_id` = `owner_doc`.`author_id`;";
    $this->addQuery($query);
    
    // Mise à jour les compte-rendus de consultation
    $query = "UPDATE `compte_rendu`
      SET `author_id` =
          (
           SELECT `chir_id`
           FROM `plageconsult`
           LEFT JOIN `consultation` ON `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
           WHERE `consultation`.`consultation_id` = `compte_rendu`.`object_id`
           LIMIT 1
          )
      WHERE `author_id` IS NULL
      AND `compte_rendu`.`object_class` = 'CConsultation'
      AND `compte_rendu`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    // Pour les consultations d'anesthésie rattachées à une opération
    $query = "UPDATE `compte_rendu`
      SET `author_id` =
        (
          SELECT `operations`.`chir_id`
          FROM `consultation_anesth`
          LEFT JOIN `operations` ON `operations`.`operation_id` = `consultation_anesth`.`operation_id`
          WHERE `consultation_anesth`.`consultation_anesth_id` = `compte_rendu`.`object_id`
          LIMIT 1
        )
      WHERE `author_id` IS NULL
      AND `compte_rendu`.`object_class` = 'CConsultAnesth'
      AND `compte_rendu`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    // Ou non
    $query = "UPDATE `compte_rendu`
      SET `author_id` =
        (
          SELECT `plageconsult`.`chir_id`
          FROM `consultation_anesth`
          LEFT JOIN `consultation` ON `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id`
          LEFT JOIN `plageconsult` ON `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
          WHERE `consultation_anesth`.`consultation_anesth_id` = `compte_rendu`.`object_id`
          LIMIT 1
        )
      WHERE `author_id` IS NULL
      AND `compte_rendu`.`object_class` = 'CConsultAnesth'
      AND `compte_rendu`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    // Pour les opérations
    $query = "UPDATE `compte_rendu`
      SET `author_id` =
        (
          SELECT `chir_id`
          FROM `operations`
          WHERE `operations`.`operation_id` = `compte_rendu`.`object_id`
          LIMIT 1
        )
      WHERE `author_id` IS NULL
      AND `compte_rendu`.`object_class` = 'COperation'
      AND `compte_rendu`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    // Pour les séjours
    $query = "UPDATE `compte_rendu`
      SET `author_id` =
        (
          SELECT `praticien_id`
          FROM `sejour`
          WHERE `sejour`.`sejour_id` = `compte_rendu`.`object_id`
          LIMIT 1
        )
      WHERE `author_id` IS NULL
      AND `compte_rendu`.`object_class` = 'CSejour'
      AND `compte_rendu`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.67");
    $query = "ALTER TABLE `compte_rendu`
      ADD date_print DATETIME DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.68");
    $this->addPrefQuery("choice_factory", "CDomPDFConverter");
    
    $this->makeRevision("0.69");
    $query = "CREATE TABLE `correspondant_courrier` (
              `correspondant_courrier_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `compte_rendu_id` INT (11) UNSIGNED NOT NULL,
              `nom` VARCHAR (255),
              `adresse` TEXT,
              `cp_ville` VARCHAR (255),
              `email` VARCHAR (255),
              `active` ENUM ('0','1') DEFAULT '0',
              `tag` VARCHAR (255), 
              `object_class` VARCHAR (255)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `correspondant_courrier` 
              ADD INDEX (`compte_rendu_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.70");
    $query = "ALTER TABLE `correspondant_courrier`
      DROP `nom`,
      DROP `adresse`,
      DROP `cp_ville`,
      DROP `email`,
      DROP `active`,
      DROP `object_class`,
      ADD `object_guid` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.71");
    $query = "ALTER TABLE `correspondant_courrier`
      ADD `object_class` ENUM ('CMedecin','CPatient','CCorrespondantPatient') NOT NULL,
      ADD `object_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `correspondant_courrier` 
      ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `correspondant_courrier`
      SET `object_class` = SUBSTRING_INDEX(`object_guid`, '-', 1),
          `object_id` = SUBSTR(`object_guid`, LOCATE('-', `object_guid`, 2)+1);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `correspondant_courrier`
      DROP `object_guid`";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = self::replaceTemplateQuery("[Courrier - copie à", "[Courrier - copie à - simple", true);
    $this->addQuery($query);
    
    $query = self::replaceTemplateQuery("[Courrier - copie à (complet)", "[Courrier - copie à - complet", true);
    $this->addQuery($query);
    
    $this->makeRevision("0.73");
    $query = "ALTER TABLE `correspondant_courrier` 
      ADD `quantite` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.74");
    $this->addPrefQuery("multiple_docs", 0);
    
    $this->makeRevision("0.75");
    $query = "ALTER TABLE `compte_rendu`
      ADD `purge_field` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.76");
    $query = self::replaceTemplateQuery("[Patient - âge", "[Patient - années", true);
    $this->addQuery($query);
    
    $this->makeRevision("0.77");
    $query = "ALTER TABLE `pack` 
      ADD `merge_docs` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.78");
    
    $this->addPrefQuery("auto_capitalize", 0);
    
    $this->makeRevision("0.79");
    $query = "ALTER TABLE `compte_rendu` 
      ADD `modele_id` INT (11) UNSIGNED AFTER `object_id`,
      ADD `purgeable` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `compte_rendu`
      ADD INDEX (`modele_id`),
      ADD INDEX (`date_print`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.80");
    $query = "ALTER TABLE `compte_rendu`
      CHANGE `type` `type` ENUM ('header','preface','body','ending','footer') DEFAULT 'body', 
      ADD `preface_id` INT (11) UNSIGNED AFTER `header_id`,
      ADD `ending_id` INT (11) UNSIGNED AFTER `preface_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `compte_rendu` 
      ADD INDEX (`preface_id`),
      ADD INDEX (`ending_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.81");
    $query = "ALTER TABLE `compte_rendu`
      ADD `fields_missing` INT (11) UNSIGNED DEFAULT 0";
    $this->addQuery($query);
    
    $this->makeRevision("0.82");
    $this->addPrefQuery("default_font", "");
    
    $this->addPrefQuery("default_size", "");
    
    $this->makeRevision("0.83");
    $this->delPrefQuery("default_font");
    $this->delPrefQuery("default_size");
    
    $query = "ALTER TABLE `compte_rendu` 
              ADD `font` ENUM ('arial','comic','courier','georgia','lucida','tahoma','times','trebuchet','verdana') AFTER `object_class`,
              ADD `size` ENUM ('xx-small','x-small','small','medium','large','x-large','xx-large','8pt','9pt','10pt','11pt','12pt','14pt','16pt','18pt','20pt','22pt','24pt','26pt','28pt','36pt','48pt','72pt') AFTER `font`";
    $this->addQuery($query);
    
    $this->makeRevision("0.84");
    $query = self::replaceTemplateQuery("[Patient - Il/Elle", "[Patient - Il/Elle (majuscule)", true);
    $this->addQuery($query);
    
    $query = self::replaceTemplateQuery("[Patient - Le/La", "[Patient - Le/La (majuscule)", true);
    $this->addQuery($query);
    
    $this->makeRevision("0.85");
    $query = "ALTER TABLE `compte_rendu`
      CHANGE `font` `font` ENUM ('arial','calibri','comic','courier','georgia','lucida','tahoma','times','trebuchet','verdana')";
    $this->addQuery($query);
    
    $this->makeRevision("0.86");
    $query = "ALTER TABLE `compte_rendu`
      CHANGE `font` `font` ENUM ('arial','calibri','comic','courier','georgia','lucida','symbol','tahoma','times','trebuchet','verdana','zapfdingbats')";
    $this->addQuery($query);
    
    $this->mod_version = "0.87";
  }
}
?>