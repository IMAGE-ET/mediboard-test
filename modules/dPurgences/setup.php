<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPurgences extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPurgences";
    
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `rpu` (
                `rpu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `sejour_id` INT(11) UNSIGNED NOT NULL,
                `diag_infirmier` TEXT,
                `mode_entree` ENUM('6','7','8'),
                `provenance` ENUM('1','2','3','4','5','8'),
                `transport` ENUM('perso','ambu','vsab','smur','heli','fo'),
                `prise_en_charge` ENUM('med','paramed','aucun'),
                `motif` TEXT,
                `ccmu` ENUM('1','2','3','4','5','P','D') NOT NULL,
                `sortie` DATETIME,
                `mode_sortie` ENUM('6','7','8','9'),
                `destination` ENUM('1','2','3','4','6','7'),
                `orientation` ENUM('HDT','HO','SC','SI','REA','UHCD','MED','CHIR','OBST','FUGUE','SCAM','PSA','REO'),
                KEY `sejour_id` (`sejour_id`),
                KEY `ccmu` (`ccmu`),
                KEY `sortie` (`sortie`),
                PRIMARY KEY (`rpu_id`)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `rpu` 
                CHANGE `ccmu` `ccmu` ENUM( '1', 'P', '2', '3', '4', '5', 'D' )";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `rpu`
                ADD `radio_debut` DATETIME,
                ADD `radio_fin` DATETIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `rpu`
                DROP `mode_sortie`,
                DROP `sortie`";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `rpu`
                ADD `mutation_sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $query = "ALTER TABLE `rpu`
                ADD `gemsa` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `rpu`
                ADD `type_pathologie` ENUM('C','E','M','P','T');";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "ALTER TABLE `rpu`
                CHANGE `mode_entree` `mode_entree` ENUM('6','7','8') NOT NULL,
                CHANGE `transport` `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo') NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `rpu`
                CHANGE `prise_en_charge` `pec_transport` ENUM('med','paramed','aucun')";
    $this->addQuery($query);

    $this->makeRevision("0.18");
    $query = "ALTER TABLE `rpu`
                ADD `urprov` ENUM('AM','AT','DO','EC','MT','OT','RA','RC','SP','VP'),
                ADD `urmuta` ENUM('A','D','M','P','X'),
                ADD `urtrau` ENUM('I','S','T');";
    $this->addQuery($query);

    $this->makeRevision("0.19");
    $query = "ALTER TABLE `rpu`
                ADD `box_id` INT(11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `rpu`
                ADD `sortie_autorisee` ENUM ('0','1') DEFAULT '0',
                ADD INDEX (`radio_debut`),
                ADD INDEX (`radio_fin`),
                ADD INDEX (`mutation_sejour_id`),
                ADD INDEX (`box_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `rpu`
                ADD `accident_travail` DATE,
                ADD INDEX (`accident_travail`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `rpu` 
                ADD `bio_depart` DATETIME,
                ADD `bio_retour` DATETIME,
                ADD INDEX (`bio_depart`),
                ADD INDEX (`bio_retour`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
      
    $query = "CREATE TABLE `extract_passages` (
                `extract_passages_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `date_extract` DATETIME NOT NULL,
                `debut_selection` DATETIME NOT NULL,
                `fin_selection` DATETIME NOT NULL,
                `date_echange` DATETIME,
                `message` MEDIUMTEXT NOT NULL,
                `message_valide` ENUM ('0','1'),
                `nb_tentatives` INT (11)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `extract_passages` 
                ADD INDEX (`date_extract`),
                ADD INDEX (`debut_selection`),
                ADD INDEX (`fin_selection`),
                ADD INDEX (`date_echange`);";
    $this->addQuery($query);
      
    $query = "CREATE TABLE `rpu_passage` (
                `rpu_passage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `rpu_id` INT (11) UNSIGNED NOT NULL,
                `extract_passages_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `rpu_passage` 
                ADD INDEX (`rpu_id`),
                ADD INDEX (`extract_passages_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
      
    $query = "ALTER TABLE `rpu` 
                ADD `specia_att` DATETIME,
                ADD `specia_arr` DATETIME;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `rpu` 
                ADD INDEX (`specia_att`),
                ADD INDEX (`specia_arr`);";
    $this->addQuery($query);
        
    $this->makeRevision("0.25");
    $this->addPrefQuery("defaultRPUSort", "ccmu");

    $this->makeRevision("0.26");
    $this->addPrefQuery("showMissingRPU", "0");
    
    $this->makeRevision("0.27");
    
    $query = "ALTER TABLE `extract_passages` 
                ADD `type` ENUM ('rpu','urg') DEFAULT 'rpu';";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `rpu`
                ADD `pec_douleur` TEXT";
    $this->addQuery($query);
   
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `extract_passages` 
                ADD `group_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "CREATE TABLE `circonstance` (
                `code` VARCHAR (15) NOT NULL,
                `libelle` VARCHAR (100),
                `commentaire` TEXT
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AVP', 'AVP', 'Accident de transport de toute nature.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'DEFENEST', 'Chute de grande hauteur', 'Chute supérieure à 3 m.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AGRESSION', 'Autres agression, rixe ou morsure ', 'Pour toute agression ou rixe".
      "sans usage d\'arme à feu ou d\'arme blanche. Pour toute morsure ou piqures multiples ou vénéneuses.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'NOYADE', 'Noyade, plongée, eau', 'Pour les noyades, ".
      "accident de plongée ou de décompression.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ARMEFEU', 'Arme à feu', 'Pour toute agression, rixe, accident et suicide".
      " ou tentative par agent vulnérant type arme à feu.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'COUTEAU', 'Objet tranchant ou perforant', 'Pour toute agression, rixe,".
      " accident et suicide ou tentative par agent vulnérant type arme blanche.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'SPORT', 'Accident de sport ou de loisir', 'Traumatisme en rapport ".
      "avec une activité sportive ou de loisir.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'PENDU', 'Pendaison, strangulation', 'Pendaison, strangulation ".
      "sans présagé du caractère médico-légal ou non.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'FEU', 'Feu, agent thermique, fumée', 'Toute source de chaleur intense ".
      "ayant provoqué des brulures, un coup de chaleur ou une insolation. Y compris incendie, ".
      "fumée d\'incendie et dégagement de CO au décours d\'un feu.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'EXPLOSIF', 'Explosion', 'Explosion de grande intensité même suivi ".
      "ou précédé d\'un incendie, même si notion d\'écrasement.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ECRASE', 'Ecrasement', 'Notion d\'écrasement, hors contexe accident ".
      "de circulation, explosion ou incendie.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'TOXIQUE', 'Exposition à produits chimiques ou toxiques', ".
      "'Lésion en rapport avec une exposition à un produit liquide, solide ou gazeux toxique.".
      " Hors contexte NRBC, incendie, intoxication par médicament, alcool ou drogues illicites.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'CHUTE', 'Chute, traumatisme bénin', 'Traumatisme bénin du ".
      "ou non à une chute de sa hauteur ou de très faible hauteur.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'ELEC', 'Electricité, foudre', 'Effet du courant électrique ".
      "par action directe ou à distance (arc électrique, effet de la foudre).');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'PRO', 'Trauma par machine à usage professionnel', 'Toute lésion ".
      "traumatique provoquée par un matériel à usage professionnel.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'DOMJEU', 'Trauma par appareillage domestique', 'Toute lésion ".
      "traumatique provoquée par un matériel à usage domestique ou un accessoire de jeu ou de loisir.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'SECOND', 'Transfert secondaire', 'Pour tout transfert secondaire.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'AUTRE', 'Autres', 'Autre traumatisme avec circonstance particulière'".
      "'non répertorié.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( 'CATA', 'Accident nombreuses victimes', 'Accident catastrophique mettant ".
      "en cause de nombreuses victimes et nécessitant un plan d\'intervention particulier.');";
    $this->addQuery($query);
    
    $query = "INSERT INTO `circonstance` VALUES ( '00000', 'Pathologie non traumatique, non cironstancielle', ".
     "'Pathologie médicale non traumatique ou sans circonstance de survenue particulière.');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `rpu`
                ADD `circonstance` VARCHAR (50);";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `circonstance`
                ADD `circonstance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY FIRST;";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `rpu`
                CHANGE `accident_travail` `date_at` DATE DEFAULT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $this->addDependency("dPplanningOp", "1.23");
    $query = "UPDATE `sejour`,`rpu`
                SET `sejour`.`mode_entree` = `rpu`.`mode_entree`
                WHERE `rpu`.`sejour_id` = `sejour`.`sejour_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `rpu`
                DROP `mode_entree`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $this->addDependency("dPplanningOp", "1.28");
    $query = "ALTER TABLE `rpu`
                DROP `provenance`,
                DROP `destination`,
                DROP `transport`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    
    $query = "ALTER TABLE `rpu`
                ADD `motif_entree` TEXT";
    $this->addQuery($query); 
    
    $this->makeRevision("0.36");
    $query = "ALTER TABLE `rpu`
                ADD `regule_par` ENUM ('centre_15','medecin');";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    
    $query = "CREATE TABLE `box_urgences` (
                `box_urgences_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `nom` VARCHAR(30) NOT NULL,
                `description` VARCHAR(50),
                `type` ENUM('Suture','Degravillonage','Dechockage','Traumatologie','Radio','Retour_radio','Imagerie','Bio','Echo','Attente','Resultats','Sortie') NOT NULL DEFAULT 'Attente',
                `plan_x` INT(11) NULL,
                `plan_y` INT(11) NULL,
                `color` VARCHAR(6) DEFAULT 'ABE',
                `hauteur` INT(11) NOT NULL DEFAULT '1',
                `largeur` INT(11) NOT NULL DEFAULT '1',
                PRIMARY KEY (`box_urgences_id`)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);     
    $this->makeRevision("0.38");
    
    $query = "DROP TABLE `box_urgences`;";
    $this->addQuery($query);
    $this->makeRevision("0.39");
    
    $query = "ALTER TABLE `rpu` 
                ADD `code_diag` INT (11);";
    $this->addQuery($query); 
    $this->makeRevision("0.40");
    
    $query = "CREATE TABLE `motif_urgence` (
                `motif_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `chapitre_id` INT (11) UNSIGNED,
                `nom` VARCHAR (255),
                `code_diag` INT (11),
                `degre_min` INT (11),
                `degre_max` INT (11)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `motif_urgence` 
                ADD INDEX (`chapitre_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `motif_chapitre` (
                `chapitre_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.41");

    $query = "CREATE TABLE `motif_sfmu` (
                `motif_sfmu_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (255),
                `libelle` VARCHAR (255)
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.42");

    $query = "ALTER TABLE `rpu`
                ADD `motif_sfmu` INT (11) UNSIGNED";
    $this->addQuery($query);

    $this->makeRevision("0.43");

    $query = "ALTER TABLE `circonstance`
                ADD `actif` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.44");

    $query = "UPDATE `rpu`
                SET `rpu`.`circonstance` = (SELECT `circonstance_id`
                                            FROM `circonstance` WHERE `circonstance`.`code` = `rpu`.`circonstance`)
                WHERE `rpu`.circonstance IS NOT NULL";
    $this->addQuery($query);

    $this->makeRevision("0.45");

    $query = "ALTER TABLE `motif_sfmu`
                ADD `categorie` VARCHAR (255);";
    $this->addQuery($query);


    $this->makeRevision("0.46");

    $query = "ALTER TABLE `extract_passages`
                CHANGE `type` `type` ENUM ('rpu','urg','activite') DEFAULT 'rpu';";
    $this->addQuery($query);

    $this->makeRevision("0.47");

    $this->addDefaultConfig("dPurgences Display check_cotation" , "dPurgences check_cotation");
    $this->addDefaultConfig("dPurgences Display check_gemsa"    , "dPurgences check_gemsa");
    $this->addDefaultConfig("dPurgences Display check_ccmu"     , "dPurgences check_ccmu");
    $this->addDefaultConfig("dPurgences Display check_dp"       , "dPurgences check_dp");
    $this->addDefaultConfig("dPurgences Display check_can_leave", "dPurgences check_can_leave");

    $this->makeRevision("0.48");

    $config_value = @CAppUI::conf("dPurgences gestion_motif_sfmu");

    if ($config_value !== null) {
      if ($config_value == "1") {
        $config_value = "2";
      }
      $query = "INSERT INTO `configuration` (`feature`, `value`) VALUES (%1, %2)";
      $query = $this->ds->prepare($query, "dPurgences CRPU gestion_motif_sfmu", $config_value);
      $this->addQuery($query);
    }
    $this->makeRevision("0.49");

    $this->addDefaultConfig("dPurgences use_vue_topologique", "dPhospi use_vue_topologique");
    $this->makeRevision("0.50");

    $this->addDefaultConfig("dPurgences CRPU diag_prat_view"    , "dPurgences diag_prat_view");
    $this->addDefaultConfig("dPurgences CRPU display_motif_sfmu", "dPurgences display_motif_sfmu");

    $this->makeRevision("0.51");

    $query = "ALTER TABLE `rpu`
                ADD `ide_responsable_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `rpu`
                ADD INDEX (`ide_responsable_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.52");

    $query = "ALTER TABLE `motif_urgence`
                ADD `definition` TEXT,
                ADD `observations` TEXT,
                ADD `param_vitaux` TEXT,
                ADD `recommande` TEXT;";
    $this->addQuery($query);

    $query = "CREATE TABLE `motif_question` (
                `question_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `motif_id` INT (11) UNSIGNED NOT NULL,
                `degre` TINYINT (4),
                `nom` TEXT NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `motif_question`
                ADD INDEX (`motif_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.53");

    $query = "CREATE TABLE `motif_reponse` (
                `reponse_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `rpu_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `question_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `result` ENUM ('0','1') DEFAULT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `motif_reponse`
                ADD INDEX (`rpu_id`),
                ADD INDEX (`question_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.54");

    $query = "CREATE TABLE `echelle_tri` (
                `echelle_tri_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `rpu_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `proteinurie` ENUM ('positive','negative'),
                `liquide` ENUM ('meconial','teinte'),
                `antidiabet_use` ENUM ('NP','oui','non') DEFAULT 'NP',
                `anticoagul_use` ENUM ('NP','oui','non') DEFAULT 'NP',
                `anticoagulant` ENUM ('sintrom','other'),
                `antidiabetique` ENUM ('oral','insuline','oral_insuline'),
                `pupille_droite` TINYINT (4) UNSIGNED NOT NULL DEFAULT '0',
                `pupille_gauche` TINYINT (4) UNSIGNED NOT NULL DEFAULT '0',
                `ouverture_yeux` ENUM ('spontane','bruit','douleur','jamais'),
                `rep_verbale` ENUM ('oriente','confuse','inapproprie','incomprehensible','aucune'),
                `rep_motrice` ENUM ('obeit','oriente','evitement','decortication','decerebration','rien')
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `echelle_tri`
                ADD INDEX (`rpu_id`);";
    $this->addQuery($query);
    $this->makeRevision("0.55");

    $query = "ALTER TABLE `extract_passages`
                CHANGE `type` `type` ENUM ('rpu','urg','activite','uhcd') DEFAULT 'rpu';";
    $this->addQuery($query);
    $this->makeRevision("0.56");

    $query = "ALTER TABLE `extract_passages`
                ADD `rpu_sender` VARCHAR (255);";
    $this->addQuery($query);
    $this->makeRevision("0.57");

    $query = "ALTER TABLE `echelle_tri`
                ADD `reactivite_droite` ENUM ('reactif','non_reactif'),
                ADD `reactivite_gauche` ENUM ('reactif','non_reactif')";
    $this->addQuery($query);
    $this->makeRevision("0.58");

    $query = "ALTER TABLE `echelle_tri`
                ADD `enceinte` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $this->mod_version = "0.59";
  }  
}
