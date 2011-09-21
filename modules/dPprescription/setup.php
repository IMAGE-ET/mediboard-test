<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPprescription extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPprescription";
       
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `prescription` (
          `prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `object_class` ENUM('CSejour','CConsultation') NOT NULL DEFAULT 'CSejour',
          `object_id` INT(11) UNSIGNED,
          PRIMARY KEY (`prescription_id`)
          ) /*! ENGINE=MyISAM */ COMMENT='Table des prescriptions';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `prescription_line` (
          `prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `prescription_id` INT(11) UNSIGNED NOT NULL,
          `code_cip` VARCHAR(7) NOT NULL,
          PRIMARY KEY (`prescription_line_id`)
          ) /*! ENGINE=MyISAM */ COMMENT='Table des lignes de médicament des prescriptions';";
    $this->addQuery($query);
    
    $this->makeRevision("0.10");
    
    $query = "ALTER TABLE `prescription_line` ADD `no_poso` SMALLINT(6) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line` ADD INDEX (`no_poso`) ;" ;
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription` ADD `praticien_id` INT(11) NOT NULL AFTER `prescription_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription` ADD INDEX (`praticien_id`) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "CREATE TABLE `category_prescription` (
           `category_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `chapitre` ENUM('dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
           `nom` VARCHAR(255) NOT NULL, 
           `description` TEXT,  
            PRIMARY KEY (`category_prescription_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `element_prescription` (
           `element_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `libelle` VARCHAR(255) NOT NULL, 
           `description` TEXT, 
           PRIMARY KEY (`element_prescription_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `prescription_line_element` (
           `prescription_line_element_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `element_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `prescription_id` INT(11) UNSIGNED NOT NULL,
           `commentaire` VARCHAR(255),
           PRIMARY KEY (`prescription_line_element_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `prescription_line`
            ADD `commentaire` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `prescription`
            ADD `libelle` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "CREATE TABLE `prescription_line_comment` (
           `prescription_line_comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_id` INT(11) UNSIGNED NOT NULL, 
           `commentaire` TEXT, 
           `chapitre` ENUM('medicament','dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
            PRIMARY KEY (`prescription_line_comment_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "CREATE TABLE `moment_unitaire` (
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `libelle` VARCHAR(255) NOT NULL, 
           `heure_min` TIME, 
           `heure_max` TIME, 
           `type_moment` ENUM('matin','midi','apres_midi','soir','horaire','autre') NOT NULL,
            PRIMARY KEY (`moment_unitaire_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    // Creation d'un tableau de moments => array("libelle","type_moment")
    $moments = array();
    
    $moments[] = array("le matin", "matin");
    $moments[] = array("au lever", "matin");
    $moments[] = array("en prise matinale unique","matin");
    $moments[] = array("au cours de la matinée","matin");
    $moments[] = array("le matin à jeun","matin");
    $moments[] = array("1 heure avant le petit-déjeuner","matin");
    $moments[] = array("2 heures avant le déjeuner","matin");
    $moments[] = array("30 à 60 minutes avant le petit-déjeuner","matin");
    $moments[] = array("30 minutes avant le petit-déjeuner","matin");
    $moments[] = array("15 à 30 minutes avant le petit-déjeuner","matin");
    $moments[] = array("1/4 d\'heure avant le petit-déjeuner","matin");
    $moments[] = array("15 à 20 minutes avant le petit-déjeuner","matin");
    $moments[] = array("immédiatement avant le petit-déjeuner","matin");
    $moments[] = array("en début de petit-déjeuner","matin");
    $moments[] = array("avant le petit-déjeuner","matin");
    $moments[] = array("au petit-déjeuner","matin");
    $moments[] = array("au cours du petit-déjeuner","matin");
    $moments[] = array("après le petit-déjeuner","matin");
    $moments[] = array("15 à 30 minutes après le petit-déjeuner","matin");
    $moments[] = array("matin après la selle","matin");
    $moments[] = array("à la fin du petit-déjeuner","matin");
    $moments[] = array("le matin à jeun de préférence","matin");
    $moments[] = array("à jeun d\'alcool, le matin au petit-déjeuner","matin");
    $moments[] = array("le matin dans chaque narine","matin");
    $moments[] = array("le matin à jeun, au moins 1/2 avant le repas","matin");
    $moments[] = array("5 minutes au moins avant le petit-déjeuner","matin");
    $moments[] = array("au lever, au moins 1/2 heure avant toute prise orale","matin");
 
    $moments[] = array("le midi", "midi");
    $moments[] = array("au déjeuner","midi");
    $moments[] = array("1 heure avant le déjeuner","midi");
    $moments[] = array("15 à 30 minutes avant le déjeuner","midi");
    $moments[] = array("30 minutes avant le déjeuner","midi");
    $moments[] = array("30 à 60 minutes avant le déjeuner","midi");
    $moments[] = array("1/4 d\'heure avant le déjeuner","midi");
    $moments[] = array("avant le déjeuner","midi");
    $moments[] = array("en début de déjeuner","midi");
    $moments[] = array("au cours du déjeuner","midi");
    $moments[] = array("à la fin du déjeuner","midi");
    $moments[] = array("après le déjeuner","midi");
    $moments[] = array("15 à 30 minutes après le déjeuner","midi");
   
    $moments[] = array("l\'après-midi", "apres_midi");
    $moments[] = array("en debut d\'après-midi","apres_midi");
    $moments[] = array("en fin d\'après-midi","apres_midi");
    
    $moments[] = array("le soir", "soir");
    $moments[] = array("avant le dîner","soir");
    $moments[] = array("au coucher", "soir");
    $moments[] = array("au dîner","soir");
    $moments[] = array("en fin de journée","soir");
    $moments[] = array("le soir avant le coucher","soir");
    $moments[] = array("1h après le dîner","soir");
    $moments[] = array("2h après le dîner","soir");
    $moments[] = array("au cours du dîner","soir");
    $moments[] = array("après le dîner","soir");
    $moments[] = array("3/4 d\'heure avant le coucher","soir");
    $moments[] = array("avant le coucher","soir");
    $moments[] = array("immédiatement avant le coucher","soir");
    $moments[] = array("au moment même du coucher","soir");
    $moments[] = array("1 heure avant le coucher","soir");
    $moments[] = array("15 à 30 minutes avant le dîner","soir");
    $moments[] = array("15 à 30 minutes avant le coucher","soir");
    $moments[] = array("15 à 30 minutes après le dîner","soir");
    $moments[] = array("15 à 30 minutes après le coucher","soir");
    $moments[] = array("30 minutes avant le coucher","soir");
    $moments[] = array("1/4 d\'heure avant le dîner","soir");
    $moments[] = array("30 minutes avant le dîner","soir");
    $moments[] = array("30 à 60 minutes avant le dîner","soir");
    $moments[] = array("1 heure avant le dîner","soir");
    $moments[] = array("1 heure avant le coucher","soir");
    $moments[] = array("au début du dîner","soir");
    $moments[] = array("le soir après le brossage des dents","soir");
    $moments[] = array("le soir après la toilette","soir");
    $moments[] = array("à la fin du dîner","soir");
    $moments[] = array("2 heures après le dîner","soir");
    $moments[] = array("le soir après la toilette sur peau bien sèche","soir");
    $moments[] = array("le soir 1/4 d\'heure après la toilette","soir");
    $moments[] = array("un soir sur deux","soir");
    $moments[] = array("un soir sur trois","soir");
    $moments[] = array("immédiatement après le dîner","soir");
    $moments[] = array("1/2 heure à 1 heure avant le coucher","soir");
    $moments[] = array("2 heures aprés le dîner","soir");
    $moments[] = array("le soir dans chaque narine","soir");
    $moments[] = array("de préférence le soir au coucher","soir");
    $moments[] = array("la veille au soir","soir");
    $moments[] = array("dans chaque narine le soir","soir");
    $moments[] = array("à jeun au coucher","soir");
    $moments[] = array("au coucher et 2h30 à 4h plus tard","soir");
    $moments[] = array("au coucher, au moins 2 heures après le dîner","soir");
    $moments[] = array("2 à 3 heures avant le coucher","soir");
      
    $moments[] = array("à distance d\'un repas","autre");
    $moments[] = array("dans la journée","autre");
    $moments[] = array("à l\'induction anesthésique","autre");
    $moments[] = array("à l\'induction anesthésique et 2 heures après","autre");
    $moments[] = array("au moment des troubles","autre");
    $moments[] = array("4 fois par jour dans chaque narine","autre");
    $moments[] = array("1 heure avant un repas","autre");
    $moments[] = array("2 heures après un repas","autre");
    $moments[] = array("2 fois par jour dans chaque narine","autre");
    $moments[] = array("3 fois par jour dans chaque narine","autre");
    $moments[] = array("5 fois par jour dans chaque narine","autre");
    $moments[] = array("avant les repas","autre");
    $moments[] = array("dans une narine le matin, dans l\'autre le soir","autre");
    $moments[] = array("dans chaque narine","autre");
    $moments[] = array("matin et soir (à 8 heures d\'intervalle)","autre");
    $moments[] = array("matin et soir (à 12 heures d\'intervalle)","autre");
    
    for($i=0; $i<24; $i++){
      $moments[] = array($i."h","horaire");
    }
    
    foreach($moments as &$moment){
      $query = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($query);
    }
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `prescription_line` 
            ADD `debut` DATE, 
            ADD `duree` INT(11);";
    $this->addQuery($query);

    $this->makeRevision("0.18");
    $query = "CREATE TABLE `prise_posologie` (
           `prise_posologie_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_line_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `quantite` INT(11), 
            PRIMARY KEY (`prise_posologie_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "CREATE TABLE `association_moment` (
           `association_moment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `code_moment_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `OR` ENUM('0','1') DEFAULT '0', 
            PRIMARY KEY (`association_moment_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `prescription_line`
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `prise_posologie`
            CHANGE `quantite` `quantite` FLOAT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `prise_posologie`
            CHANGE `moment_unitaire_id` `moment_unitaire_id` INT(11) UNSIGNED DEFAULT NULL, 
            ADD `nb_fois` INT(11), 
            ADD `unite_fois` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an'), 
            ADD `nb_tous_les` INT(11), 
            ADD `unite_tous_les` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($query);

    
    $this->makeRevision("0.23"); 
    
    $moments = array();
    $moments[] = array("1 heure après les repas", "autre");
    $moments[] = array("2 heures avant le déjeuner", "midi");
    $moments[] = array("15 minutes avant le coucher", "soir");
    $moments[] = array("1 heure après le petit-déjeuner", "matin");
    $moments[] = array("1 fois par jour dans chaque narine", "autre");
    $moments[] = array("30 minute avant le repas", "autre");
    
    foreach($moments as &$moment){
      $query = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($query);
    }
   
    $this->makeRevision("0.24");
    $query = "CREATE TABLE `executant_prescription_line` (
            `executant_prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
            `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
            `nom` VARCHAR(255) NOT NULL, 
            `description` TEXT, 
            PRIMARY KEY (`executant_prescription_line_id`)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `prescription_line_element`
            ADD `executant_prescription_line_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `category_prescription_id` INT(11) UNSIGNED, 
            ADD `executant_prescription_line_id` INT(11) UNSIGNED, 
            DROP `chapitre`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `prescription`
            ADD `type` ENUM('externe','pre_admission','sejour','sortie','traitement') NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line`
            ADD `stoppe` ENUM('0','1'), 
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.28");
    $query = "ALTER TABLE `prescription_line`
            ADD `date_arret` DATE,
            DROP `stoppe`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `prescription_line` 
            ADD `valide` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `moment_unitaire`
            ADD `principal` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `prescription_line`
            CHANGE `valide` `valide_prat` ENUM('0','1') DEFAULT '0', 
            ADD `valide_pharma` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `signee` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `signee` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line`
            CHANGE `valide_prat` `signee` ENUM('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `prescription_line`
            ADD `accord_praticien` ENUM('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `prescription_line` RENAME `prescription_line_medicament`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament`
            CHANGE `prescription_line_id` `prescription_line_medicament_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `debut` DATE, 
            ADD `duree` INT(11), 
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `debut` DATE, 
            ADD `duree` INT(11), 
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prise_posologie`
            CHANGE `prescription_line_id` `object_id` INT(11) UNSIGNED NOT NULL, 
            ADD `object_class` ENUM('CPrescriptionLineMedicament','CPrescriptionLineElement') NOT NULL DEFAULT 'CPrescriptionLineMedicament';";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `date_arret` DATE;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `date_arret` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $query = "ALTER TABLE `prescription`
            CHANGE `praticien_id` `praticien_id` INT(11) UNSIGNED, 
            ADD `function_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    $query = "ALTER TABLE `category_prescription`
            CHANGE `chapitre` `chapitre` ENUM('dmi','anapath','biologie','imagerie','consult','kine','soin','dm') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.38");
    $query = "ALTER TABLE `prescription_line_medicament`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `prise_posologie`
            ADD `decalage_prise` INT(11) DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    
    $query = "ALTER TABLE `prescription` 
	          ADD INDEX (`function_id`),
 	          ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQuery($query);
	  
	  $query = "ALTER TABLE `prescription_line_element` 
						ADD INDEX (`element_prescription_id`),
						ADD INDEX (`executant_prescription_line_id`),
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQuery($query);
    
	  $query = "ALTER TABLE `prescription_line_comment` 
						ADD INDEX (`category_prescription_id`),
						ADD INDEX (`executant_prescription_line_id`),
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQUery($query);
	  
    $query = "ALTER TABLE `prise_posologie` 
	          ADD INDEX (`moment_unitaire_id`),
          	ADD INDEX (`object_id`);";
	  $this->addQuery($query);  

	  $this->makeRevision("0.41");

	  $query = "ALTER TABLE `prescription_line_medicament` 
 	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0',
            ADD `substitution_line_id` INT (11) UNSIGNED;";
	  $this->addQuery($query);
	  
	  // Pour toute les lignes qui n'ont pas de praticien_id, on stocke celui de la prescription
    $query = "UPDATE `prescription_line_medicament`,`prescription`
            SET `prescription_line_medicament`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_medicament`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_medicament`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_element`,`prescription`
            SET `prescription_line_element`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_element`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_element`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_comment`,`prescription`
            SET `prescription_line_comment`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_comment`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_comment`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($query);
    
    // Ajout du creator_id, par defaut, le creator_id est le praticien_id
	  $query = "ALTER TABLE `prescription_line_medicament` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_medicament`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($query);
    
	  $query = "ALTER TABLE `prescription_line_element` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_element`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_comment`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment` 
	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element` 
 	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
            ADD `time_arret` TIME;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element` 
            ADD `time_arret` TIME;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment` 
            ADD `time_arret` TIME;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prise_posologie` 
            ADD `unite_prise` TEXT;";
    $this->addQuery($query);
       
    function updateUnitePrise(){

     $ds_std = CSQLDataSource::get("std");
             
     // Recuperation de toutes les lignes de posologies ayant unite_prise à NULL et aucun numero de poso indiqué 
     $query = "SELECT prise_posologie.prise_posologie_id, prescription_line_medicament.code_cip 
             FROM prise_posologie, prescription_line_medicament
             WHERE prise_posologie.object_id = prescription_line_medicament.prescription_line_medicament_id
						 AND prise_posologie.unite_prise IS NULL	              
						 AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
             AND prescription_line_medicament.no_poso IS NULL";
     $res_sans_poso = $ds_std->loadList($query);

     foreach($res_sans_poso as $_prise){
      $ds_bcb = CBcbObject::getDataSource();
     	// Recuperation de l'unite de prise
     	$query = "SELECT `LIBELLE_UNITE_DE_PRISE_PLURIEL` 
                FROM `POSO_UNITES_PRISE`,`POSO_PRODUITS` 
                WHERE `POSO_PRODUITS`.`CODE_CIP` = '".$_prise["code_cip"]."'
							  AND `POSO_PRODUITS`.`CODE_UNITE_DE_PRISE` = `POSO_UNITES_PRISE`.`CODE_UNITE_DE_PRISE`
                LIMIT 1;";	
       $res = $ds_bcb->loadResult($query);	
       
       // Mise a jour de la prise
       $query = "UPDATE `prise_posologie`
                SET `unite_prise` = '".$res."'
                WHERE `prise_posologie_id` = '".$_prise["prise_posologie_id"]."';";
     	$res = $ds_std->exec( $query );
     }

     // Recuperation de toutes les lignes de posologies ayant unite_prise à NULL mais un numero de poso indiqué 
     $query = "SELECT prise_posologie.prise_posologie_id, prescription_line_medicament.code_cip, prescription_line_medicament.no_poso 
             FROM prise_posologie, prescription_line_medicament
             WHERE prise_posologie.object_id = prescription_line_medicament.prescription_line_medicament_id
             AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
             AND prise_posologie.unite_prise IS NULL
             AND prescription_line_medicament.no_poso IS NOT NULL";
     $res_avec_poso = $ds_std->loadList($query);
       
     foreach($res_avec_poso as $_prise){
      $ds_bcb = CBcbObject::getDataSource();
     	// Recuperation de l'unite de prise
     	$query = "SELECT `LIBELLE_UNITE_DE_PRISE_PLURIEL` 
              FROM `POSO_UNITES_PRISE`,`POSO_PRODUITS` 
              WHERE `POSO_PRODUITS`.`CODE_CIP` = '".$_prise["code_cip"]."'
              AND `POSO_PRODUITS`.`NO_POSO` = '".$_prise["no_poso"]."'
							AND `POSO_PRODUITS`.`CODE_UNITE_DE_PRISE` = `POSO_UNITES_PRISE`.`CODE_UNITE_DE_PRISE`;";	
       $res = $ds_bcb->loadResult($query);	
       
       // Mise a jour de la prise
       $query = "UPDATE `prise_posologie`
                SET `unite_prise` = '".$res."'
                WHERE `prise_posologie_id` = '".$_prise["prise_posologie_id"]."';";
     	$res = $ds_std->exec( $query );
     }
     return true;
    }
    $this->addFunction("updateUnitePrise");
   
    $this->makeRevision("0.42");
    $query = "ALTER TABLE `category_prescription` 
	          ADD `header` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `moment_unitaire` 
   	        ADD `heure` TIME,
 	          DROP `heure_min`,
	          DROP `heure_max`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.44");
    $query = "ALTER TABLE `prescription_line_medicament` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_element` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_comment` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($query);
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `prescription_line_medicament` 
						ADD `time_debut` TIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_element` 
						ADD `time_debut` TIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_comment` 
						ADD `time_debut` TIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `prescription_line_medicament` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
	          ADD `time_fin` TIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_element` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
	          ADD `time_fin` TIME;";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_comment` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
        	  ADD `time_fin` TIME;";
    $this->addQuery($query); 
    
    $this->makeRevision("0.47");
    $query = "CREATE TABLE `moment_complexe` (
							`moment_complexe_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`code_moment_id` INT (11) NOT NULL,
							`visible` ENUM ('0','1') DEFAULT '0'
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    $query = "CREATE TABLE `administration` (
							`administration_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
          	  `administrateur_id` INT (11) UNSIGNED NOT NULL,
							`dateTime` DATETIME,
							`quantite` FLOAT,
							`unite_prise` TEXT,
							`commentaire` TEXT,
							`object_id` INT (11) UNSIGNED NOT NULL,
							`object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement') NOT NULL
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `administration` 
						ADD INDEX (`dateTime`),
	          ADD INDEX (`administrateur_id`),
						ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.49");
    $query = "ALTER TABLE `administration` 
	          ADD `prise_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `administration` 
	          ADD INDEX (`prise_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.50");
    $query = "ALTER TABLE `prescription_line_element` 
	        ADD `conditionnel` ENUM ('0','1') DEFAULT '0',
	        ADD `condition_active` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
	        ADD `conditionnel` ENUM ('0','1') DEFAULT '0',
	        ADD `condition_active` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    $query = "ALTER TABLE `prescription_line_element` 
		    ADD `unite_decalage` ENUM ('jour','heure') DEFAULT 'jour',
	        ADD `unite_decalage_fin` ENUM ('jour','heure') DEFAULT 'jour';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
		    ADD `unite_decalage` ENUM ('jour','heure') DEFAULT 'jour',
	        ADD `unite_decalage_fin` ENUM ('jour','heure') DEFAULT 'jour';";
    $this->addQuery($query);

    $this->makeRevision("0.52");
    $query = "ALTER TABLE `prescription_line_medicament` 
	        ADD `operation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element` 
	        ADD `operation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.53");
    $query = "ALTER TABLE `prescription_line_medicament` 
	          ADD `substitute_for` INT (11) UNSIGNED,
	          ADD `substitution_active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament`
						ADD INDEX (`substitution_line_id`),
						ADD INDEX (`substitute_for`),
						ADD INDEX (`time_debut`),
						ADD INDEX (`time_arret`),
						ADD INDEX (`time_fin`),
						ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "CREATE TABLE `prescription_protocole_pack` (
						`prescription_protocole_pack_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`libelle` VARCHAR (255),
						`praticien_id` INT (11) UNSIGNED,
						`function_id` INT (11) UNSIGNED,
            `object_class` ENUM ('CSejour','CConsultation') NOT NULL
					 ) /*! ENGINE=MyISAM */;";
   $this->addQuery($query);
   
   $query = "ALTER TABLE `prescription_protocole_pack` 
					 ADD INDEX (`praticien_id`),
					 ADD INDEX (`function_id`);";
   $this->addQuery($query);
   
   $query = "CREATE TABLE `prescription_protocole_pack_item` (
						`prescription_protocole_pack_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`prescription_protocole_pack_id` INT (11) UNSIGNED NOT NULL,
						`prescription_id` INT (11) UNSIGNED NOT NULL
					) /*! ENGINE=MyISAM */;";
   $this->addQuery($query);
   
   $query = "ALTER TABLE `prescription_protocole_pack_item` 
	         ADD INDEX (`prescription_protocole_pack_id`),
	         ADD INDEX (`prescription_id`);";
   $this->addQuery($query);
  
   $this->makeRevision("0.55");
   $query = "ALTER TABLE `prescription` 
	         ADD `group_id` INT (11) UNSIGNED,
	         ADD INDEX (`group_id`);";
   $this->addQuery($query);
 
   $this->makeRevision("0.56");
   $query = "ALTER TABLE `prescription_line_medicament` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($query);
   
   $query = "ALTER TABLE `prescription_line_element` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($query);
   
   $query = "ALTER TABLE `prescription_line_comment` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($query);
   
   $this->makeRevision("0.57");
   $query = "CREATE TABLE `function_category_prescription` (
						`function_category_prescription_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`function_id` INT (11) UNSIGNED NOT NULL,
						`category_prescription_id` INT (11) UNSIGNED NOT NULL
					) /*! ENGINE=MyISAM */;";
   $this->addQuery($query);
    
	 $query = "ALTER TABLE `function_category_prescription` 
					 ADD INDEX (`function_id`),
					 ADD INDEX (`category_prescription_id`);";
   $this->addQuery($query);
   
   $this->makeRevision("0.58");
   $query = "ALTER TABLE `prescription_line_element` 
					 ADD `user_executant_id` INT (11) UNSIGNED;";
   $this->addQuery($query);
   
   $query = "ALTER TABLE `prescription_line_comment` 
					 ADD `user_executant_id` INT (11) UNSIGNED;";
   $this->addQuery($query);
   
   $this->makeRevision("0.59");
   $query = "ALTER TABLE `prise_posologie` 
					 ADD `decalage_intervention` INT (11),
					 ADD `heure_prise` TIME;";
	 $this->addQuery($query);

	 $this->makeRevision("0.60");
	 
	 $query = "ALTER TABLE `prescription_line_medicament` 
					 ADD `voie` VARCHAR (255);";
	 $this->addQuery($query);
	 	  
    $this->makeRevision("0.61");
    $query = "CREATE TABLE `perfusion` (
							`perfusion_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`prescription_id` INT (11) UNSIGNED,
							`type` ENUM ('seringue','PCA') NOT NULL,
							`libelle` VARCHAR (255),
							`vitesse` INT (10) UNSIGNED,
							`voie` VARCHAR (255),
							`date_debut` DATE,
							`time_debut` TIME,
							`duree` INT (10) UNSIGNED,
							`next_perf_id` INT (11) UNSIGNED,
							`praticien_id` INT (11) UNSIGNED,
							`creator_id` INT (11) UNSIGNED,
							`signature_prat` ENUM ('0','1'),
							`signature_pharma` ENUM ('0','1'),
							`validation_infir` ENUM ('0','1'),
							`date_arret` DATE,
							`time_arret` TIME
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perfusion` 
						ADD INDEX (`prescription_id`),
						ADD INDEX (`date_debut`),
						ADD INDEX (`time_debut`),
						ADD INDEX (`next_perf_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`creator_id`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`time_arret`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `perfusion_line` (
							`perfusion_line_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`perfusion_id` INT (11) UNSIGNED NOT NULL,
							`code_cip` INT (7) UNSIGNED ZEROFILL NOT NULL,
							`quantite` INT (11),
							`unite` VARCHAR (255),
							`date_debut` DATE,
							`time_debut` TIME
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perfusion_line` 
						ADD INDEX (`perfusion_id`),
						ADD INDEX (`date_debut`),
						ADD INDEX (`time_debut`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `perfusion` 
	          ADD `accord_praticien` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $query = "ALTER TABLE `perfusion` 
            ADD `operation_id` INT (11) UNSIGNED,
	          ADD `decalage_interv` INT (11);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perfusion` 
            ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $query = "ALTER TABLE `perfusion` 
	          CHANGE `type` `type` ENUM ('classique','seringue','PCA') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $this->addPrefQuery("mode_readonly", "0");

    $this->makeRevision("0.66");
    $query = "ALTER TABLE `prise_posologie` 
	          CHANGE `unite_tous_les` `unite_tous_les` ENUM ('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an','lundi','mardi',
																		'mercredi','jeudi','vendredi','samedi','dimanche');";
    $this->addQuery($query);
    
    $this->makeRevision("0.67");
    $query = "ALTER TABLE `administration` 
	          ADD `planification` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.68");
    $query = "ALTER TABLE `administration` 
						ADD `original_dateTime` DATETIME;";
    $this->addQuery($query);
   
    $this->makeRevision("0.69");
    $query = "ALTER TABLE `prescription_line_medicament`
						CHANGE `emplacement` `emplacement` ENUM ('service','bloc','service_bloc') NOT NULL;";
    $this->addQuery($query);
      
    $query = "ALTER TABLE `prescription_line_element`
						CHANGE `emplacement` `emplacement` ENUM ('service','bloc','service_bloc') NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
						CHANGE `emplacement` `emplacement` ENUM ('service','bloc','service_bloc') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.70");
    
    $query = "ALTER TABLE `category_prescription` 
						ADD `group_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `category_prescription` 
	          ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.71");
    $query = "ALTER TABLE `perfusion`
            CHANGE `signature_prat` `signature_prat` ENUM ('0','1') DEFAULT '0',
            CHANGE `signature_pharma` `signature_pharma` ENUM ('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $query = "UPDATE `perfusion`
            SET `signature_prat` = '0'
						WHERE `signature_prat` IS NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `perfusion`
            SET `signature_pharma` = '0'
						WHERE `signature_pharma` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = "ALTER TABLE `perfusion` 
						ADD `mode_bolus` ENUM ('sans_bolus','bolus','perfusion_bolus') DEFAULT 'sans_bolus',
						ADD `dose_bolus` FLOAT,
						ADD `periode_interdite` INT (10) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.73");
    $query = "ALTER TABLE `perfusion` 
						ADD `date_debut_adm` DATE,
						ADD `time_debut_adm` TIME,
						ADD `date_fin_adm` DATE,
						ADD `time_fin_adm` TIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.74");
    $query = "ALTER TABLE `perfusion` 
	          ADD `emplacement` ENUM ('service','bloc','service_bloc') DEFAULT 'service' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.75");
    $query = "ALTER TABLE `perfusion_line` 
						ADD `nb_tous_les` INT (11);";
    $this->addQuery($query);
    
    $this->makeRevision("0.76");
    $query = "CREATE TABLE `prescription_line_dmi` (
							`prescription_line_dmi_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`prescription_id` INT (11) UNSIGNED NOT NULL,
							`praticien_id` INT (11) UNSIGNED NOT NULL,
							`product_id` INT (11) UNSIGNED NOT NULL,
							`order_item_reception_id` INT (11) UNSIGNED NOT NULL
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_dmi` 
							ADD INDEX (`prescription_id`),
							ADD INDEX (`praticien_id`),
							ADD INDEX (`product_id`),
							ADD INDEX (`order_item_reception_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.77");
    $query = "ALTER TABLE `prescription_line_medicament`
					  ADD `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPerfusion') DEFAULT 'CPrescriptionLineMedicament',
						CHANGE `substitute_for` `substitute_for_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perfusion` 
						ADD `substitute_for_id` INT (11) UNSIGNED,
						ADD `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPerfusion') DEFAULT 'CPerfusion',
						ADD `substitution_active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.78");
    $query = "ALTER TABLE `prescription_line_medicament`
						ADD `substitution_plan_soin` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `perfusion`
						ADD `substitution_plan_soin` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "UPDATE `prescription_line_medicament`
						SET `substitution_plan_soin` = '1'
						WHERE `substitute_for_id` IS NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `perfusion`
						SET `substitution_plan_soin` = '1'
						WHERE `substitute_for_id` IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.79");
    
    // Existence du dossier medical
    $this->addDependency("dPpatients", "0.51");

    $query = "ALTER TABLE `prescription` 
	          CHANGE `object_class` `object_class` ENUM ('CSejour','CConsultation','CDossierMedical') NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
	          ADD `traitement_personnel` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    // Creation du dossier medical s'il n'existe pas deja
    $query = "INSERT INTO `dossier_medical` (dossier_medical_id, codes_cim, object_id, object_class) 
            SELECT '',NULL,patients.patient_id,'CPatient'
		 			  FROM prescription 
		 			  LEFT JOIN sejour ON prescription.object_id = sejour.sejour_id
		 		  	LEFT JOIN patients ON sejour.patient_id = patients.patient_id
				  	LEFT JOIN dossier_medical ON (dossier_medical.object_id = patients.patient_id AND dossier_medical.object_class = 'CPatient')
			      WHERE prescription.type = 'traitement'
			      AND dossier_medical.dossier_medical_id IS NULL;";
    $this->addQuery($query);
    
    // Changement du prescription_id des lignes de tp actuel pour cibler leur prescription de sejour
    // Si la prescription de sejour n'existe pas, on ne la crée pas
    $query = "CREATE TEMPORARY TABLE traitements_personnel (
						    line_tp_id INT(11), sejour_id INT(11), prescription_sejour_id INT(11), dossier_medical_patient_id INT(11), praticien_id INT(11), code_cip VARCHAR(7)
						  ) AS 
						    SELECT prescription_line_medicament_id as line_tp_id, 
						           prescription.object_id as sejour_id, 
						           prescription_sejour.prescription_id as prescription_sejour_id,
						           dossier_medical.dossier_medical_id as dossier_medical_patient_id,
											 prescription_sejour.praticien_id as praticien_id,
											 prescription_line_medicament.code_cip as code_cip
						    FROM prescription_line_medicament, prescription
						    LEFT JOIN prescription AS prescription_sejour ON (prescription_sejour.object_id = prescription.object_id AND prescription_sejour.object_class = 'CSejour' AND prescription_sejour.type = 'sejour')
						    LEFT JOIN sejour ON prescription.object_id = sejour.sejour_id
						    LEFT JOIN patients ON sejour.patient_id = patients.patient_id
						    LEFT JOIN dossier_medical ON (dossier_medical.object_id = patients.patient_id AND dossier_medical.object_class = 'CPatient')
						    WHERE prescription_line_medicament.prescription_id = prescription.prescription_id    
						    AND prescription.type = 'traitement'
						    AND prescription.object_id IS NOT NULL;";
		$this->addQuery($query);
		
		// Transfert des tp dans la prescription de sejour
		$query = "UPDATE `prescription_line_medicament`, `traitements_personnel`
	          SET `prescription_line_medicament`.`traitement_personnel` = '1', `prescription_line_medicament`.`prescription_id` = `traitements_personnel`.`prescription_sejour_id`
	          WHERE `prescription_line_medicament`.`prescription_line_medicament_id` = `traitements_personnel`.`line_tp_id`
						AND traitements_personnel.prescription_sejour_id IS NOT NULL;";
		$this->addQuery($query);
		
		// Rajout de la date de debut de sejour si elle n'existe pas deja dans la ligne de traitement personnel
    $query = "UPDATE prescription_line_medicament
						LEFT JOIN prescription ON prescription_line_medicament.prescription_id = prescription.prescription_id
						LEFT JOIN sejour ON (prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour')
						SET prescription_line_medicament.debut = DATE(sejour.entree_prevue)
						WHERE prescription_line_medicament.debut IS NULL
				    AND prescription_line_medicament.traitement_personnel = '1'";
    $this->addQuery($query);
    
    // Creation des prescription de DossierMedicalPatient
    $query = "INSERT INTO `prescription`
						SELECT '', traitements_personnel.praticien_id, 'CDossierMedical', traitements_personnel.dossier_medical_patient_id, NULL,  'traitement', NULL, NULL
						FROM traitements_personnel
						GROUP BY dossier_medical_patient_id";
    $this->addQuery($query);
    
    // Copie des lignes de prescription vers la prescription de tp
    $query = "INSERT INTO prescription_line_medicament (prescription_line_medicament_id, prescription_id, code_cip, commentaire, ald, debut, duree, unite_duree, praticien_id, 
																											creator_id, emplacement, voie)
						SELECT '', prescription.prescription_id, prescription_line_medicament.code_cip, prescription_line_medicament.commentaire, prescription_line_medicament.ald, prescription_line_medicament.debut, prescription_line_medicament.duree, prescription_line_medicament.unite_duree, 
									 prescription_line_medicament.praticien_id, prescription_line_medicament.creator_id, prescription_line_medicament.emplacement, prescription_line_medicament.voie 
						FROM traitements_personnel
						LEFT JOIN prescription ON (prescription.object_id = traitements_personnel.dossier_medical_patient_id AND prescription.object_class = 'CDossierMedical')
						LEFT JOIN prescription_line_medicament ON prescription_line_medicament.prescription_line_medicament_id = traitements_personnel.line_tp_id
						GROUP BY dossier_medical_patient_id, code_cip";
    $this->addQuery($query);
    
    $query = "DELETE FROM prescription
						WHERE prescription.object_class != 'CDossierMedical'
						AND prescription.type = 'traitement';";
    $this->addQuery($query);
    
    // Suppression des lignes de medicaments dont la prescription a été supprimée
    $query = "DELETE prescription_line_medicament.* FROM prescription_line_medicament
						LEFT JOIN prescription ON prescription_line_medicament.prescription_id = prescription.prescription_id
						WHERE prescription.prescription_id IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.80");
    $query = "CREATE TABLE `config_moment_unitaire` (
							`config_moment_unitaire_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`moment_unitaire_id` INT (11) UNSIGNED NOT NULL,
							`heure` TIME,
							`service_id` INT (11) UNSIGNED,
							`group_id` INT (11) UNSIGNED
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `config_moment_unitaire` 
						ADD INDEX (`moment_unitaire_id`),
						ADD INDEX (`heure`),
						ADD INDEX (`service_id`),
						ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `config_service` (
						`config_service_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`name` VARCHAR (255) NOT NULL,
						`value` VARCHAR (255),
						`service_id` INT (11) UNSIGNED,
						`group_id` INT (11) UNSIGNED
					) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `config_service` 
						ADD INDEX (`service_id`),
						ADD INDEX (`group_id`);";
		$this->addQuery($query);
		
		$query = "INSERT INTO `config_moment_unitaire`
						SELECT '', moment_unitaire.moment_unitaire_id, moment_unitaire.heure, NULL, NULL
						FROM `moment_unitaire`";
		$this->addQuery($query);
		
		/*
		$query = "ALTER TABLE `moment_unitaire`
						DROP heure;";
		$this->addQuery($query);
		*/

		$query = "INSERT INTO `config_service` (`config_service_id`,`name`,`value`,`service_id`,`group_id`) VALUES
						('','Tous les jours','08',NULL,NULL),
						('','1 fois par jour','08',NULL,NULL),
						('','2 fois par jour','08|14',NULL,NULL),
						('','3 fois par jour','08|14|18',NULL,NULL),
						('','4 fois par jour','08|10|12|14',NULL,NULL),
						('','5 fois par jour','08|10|12|14|16',NULL,NULL),
						('','6 fois par jour','08|10|12|14|16|18',NULL,NULL),
						('','Borne matin min','06',NULL,NULL),
						('','Borne matin max','13',NULL,NULL),
						('','Borne soir min','14',NULL,NULL),
						('','Borne soir max','21',NULL,NULL),
						('','Borne nuit min','22',NULL,NULL),
						('','Borne nuit max','05',NULL,NULL),
						('','1 fois par semaine','lundi',NULL,NULL),
						('','2 fois par semaine','lundi|mercredi',NULL,NULL),
						('','3 fois par semaine','lundi|mercredi|vendredi',NULL,NULL),
						('','4 fois par semaine','lundi|mercredi|vendredi|samedi',NULL,NULL);";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.81");
	  $query = "ALTER TABLE `prescription_line_medicament`
	          ADD `code_ucd` VARCHAR(7) NOT NULL,
	          ADD `code_cis` VARCHAR(8) NOT NULL;";
	  $this->addQuery($query);
	  
	  $query = "ALTER TABLE `perfusion_line`
	          ADD `code_ucd` VARCHAR(7) NOT NULL,
	          ADD `code_cis` VARCHAR(8) NOT NULL;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.82");
	  $query = "ALTER TABLE `prise_posologie` 
					  ADD `urgence_datetime` DATETIME;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.83");
	  $query = "ALTER TABLE `administration` 
	          CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPerfusionLine') NOT NULL;";
	  $this->addQuery($query);
	  
	  $query = "ALTER TABLE `perfusion_line` 
						DROP `date_debut`,
						DROP `time_debut`;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.84");
	  $query = "ALTER TABLE `perfusion` 
						ADD `nb_tous_les` INT (11);";
	  $this->addQuery($query);
	  
	  $query = "UPDATE `perfusion`
					  SET nb_tous_les = (SELECT perfusion_line.nb_tous_les
															 FROM perfusion_line
															 WHERE perfusion_line.perfusion_id = perfusion.perfusion_id
															 AND perfusion_line.nb_tous_les IS NOT NULL
															 GROUP BY perfusion_id)
						WHERE perfusion.vitesse IS NULL";
	  $this->addQuery($query);
	  
	  $query = "ALTER TABLE `perfusion_line`
						DROP nb_tous_les;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.85");
	  $query = "ALTER TABLE `perfusion` 
	          CHANGE `date_debut_adm` `date_pose` DATE,
            CHANGE `time_debut_adm` `time_pose` TIME,
            CHANGE `date_fin_adm` `date_retrait` DATE,
            CHANGE `time_fin_adm` `time_retrait` TIME;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.86");
	  
	  // TODO: A supprimer apres les mises a jour (d'ici 1 semaine)
	  function updatePrises(){
	    set_time_limit(360);

			$ds_std = CSQLDataSource::get("std");
			
			// Chargement des prises des medicaments
			$query = "SELECT prescription_line_medicament.code_cip, unite_prise 
			          FROM prise_posologie
								LEFT JOIN prescription_line_medicament ON prescription_line_medicament.prescription_line_medicament_id = prise_posologie.object_id 
								AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
								GROUP BY prescription_line_medicament.code_cip, unite_prise";
			$lines_by_type["prises"] = $ds_std->loadList($query);
			
			$query = "SELECT prescription_line_medicament.code_cip, unite_prise 
			          FROM administration
								LEFT JOIN prescription_line_medicament ON prescription_line_medicament.prescription_line_medicament_id = administration.object_id 
								AND  administration.object_class = 'CPrescriptionLineMedicament'
								GROUP BY prescription_line_medicament.code_cip, unite_prise";
			$lines_by_type["adm"] = $ds_std->loadList($query);
			
			$query = "SELECT code_cip, unite as unite_prise
			          FROM perfusion_line
							  GROUP BY code_cip, unite_prise";
			$lines_by_type["perf"] = $ds_std->loadList($query);
			
			$prises = array();
			foreach($lines_by_type as $lines){
				foreach($lines as $_line){
				  $code_cip = $_line["code_cip"];
				  $unite_prise = $_line["unite_prise"];
				  
				  if(!$code_cip || $unite_prise == "aucune_prise" || !$unite_prise){
				    continue;
				  }
				  $produit = new CBcbProduit();
				  $produit->load($code_cip);
				  $produit->loadRapportUnitePriseByCIS();
				  
				  $libelle_unite_presentation = $produit->libelle_unite_presentation;
				  $libelle_unite_presentation_pluriel = $produit->libelle_unite_presentation_pluriel;
			
				  $_unite_prise = preg_replace("/\/kg$/i", '', $unite_prise);
				  $coef_adm = @$produit->rapport_unite_prise[$_unite_prise][$libelle_unite_presentation];
				  
				  // si l'unite de prise ne correspond pas au libelle de presentation, on rajouter des informations dans l'unite de prise
				  if($coef_adm){
					  if (stripos($libelle_unite_presentation_pluriel, $unite_prise) === false){
					    // Prise en kg
					    if($_unite_prise != $unite_prise){
					      $prises[$code_cip][$unite_prise] = "$unite_prise ($coef_adm $libelle_unite_presentation/kg)"; 
					    }
					    else {
					      $prises[$code_cip][$unite_prise] = "$unite_prise ($coef_adm $libelle_unite_presentation)"; 
					    }
					  }
				  }
				}
			}
			
			foreach($prises as $code_cip => $prises_by_unite){
			  foreach($prises_by_unite as $unite_prise => $libelle_prise){
			    $query = "UPDATE prise_posologie, prescription_line_medicament
										SET unite_prise = '$libelle_prise'
										WHERE unite_prise = '$unite_prise'
			              AND prise_posologie.object_id = prescription_line_medicament.prescription_line_medicament_id
			              AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
										AND prescription_line_medicament.code_cip = '$code_cip'";
			    $ds_std->exec($query);
				  
				  $query = "UPDATE administration, prescription_line_medicament
										SET unite_prise = '$libelle_prise'
										WHERE unite_prise = '$unite_prise'
			              AND administration.object_id = prescription_line_medicament.prescription_line_medicament_id
			              AND administration.object_class = 'CPrescriptionLineMedicament'
										AND prescription_line_medicament.code_cip = '$code_cip'";
			    $ds_std->exec($query);
				  
				  $query = "UPDATE perfusion_line
										SET unite = '$libelle_prise'
										WHERE unite = '$unite_prise'
			              AND perfusion_line.code_cip = '$code_cip'";
			    $ds_std->exec($query);
			  }
			}
			return true;
	  }
	  $this->addFunction("updatePrises");
	  
	  $this->makeRevision("0.87");
	  $query = "ALTER TABLE `perfusion`
	          ADD `commentaire` VARCHAR (255);";
	  $this->addQuery($query);
	  
		$this->makeRevision("0.88");
		$query = "ALTER TABLE `perfusion`
            ADD `conditionnel` ENUM ('0','1')  DEFAULT '0',
            ADD `condition_active` ENUM ('0','1')  DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("0.89");
		$query = "CREATE TABLE `prescription_category_group` (
						  `prescription_category_group_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `libelle` VARCHAR (255) NOT NULL,
						  `group_id` INT (11) UNSIGNED
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `prescription_category_group` 
            ADD INDEX (`group_id`);";
	  $this->addQuery($query);
		
		$query = "CREATE TABLE `prescription_category_group_item` (
						  `prescription_category_group_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `prescription_category_group_id` INT (11) UNSIGNED NOT NULL,
						  `category_prescription_id` INT (11) UNSIGNED,
						  `type_produit` ENUM ('med','perf','inj')
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `prescription_category_group_item` 
					  ADD INDEX (`prescription_category_group_id`),
					  ADD INDEX (`category_prescription_id`);";
		$this->addQuery($query);
		
		$this->makeRevision("0.90");
		$query = "ALTER TABLE `element_prescription` 
            ADD `cancelled` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
    
    $this->makeRevision("0.91");
    $query = "ALTER TABLE `prescription_line_comment` 
              ADD `operation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `administration` 
              ADD INDEX (`original_dateTime`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `association_moment` 
              ADD INDEX (`moment_unitaire_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `element_prescription` 
              ADD INDEX (`category_prescription_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `executant_prescription_line` 
              ADD INDEX (`category_prescription_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `perfusion` 
              ADD INDEX (`date_pose`),
              ADD INDEX (`date_retrait`),
              ADD INDEX (`substitute_for_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_comment` 
              ADD INDEX (`user_executant_id`),
              ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_element` 
              ADD INDEX (`user_executant_id`),
              ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `prise_posologie` 
              ADD INDEX (`urgence_datetime`);";
    $this->addQuery($query);
		
		$this->makeRevision("0.92");
		$query = "ALTER TABLE `perfusion` 
              ADD `jour_decalage` ENUM ('I','N') DEFAULT 'I';";
		$this->addQuery($query);

    $this->makeRevision("0.93");
		$query = "CREATE TABLE `planification_systeme` (
              `planification_systeme_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `dateTime` DATETIME,
              `unite_prise` TEXT,
              `prise_id` INT (11) UNSIGNED,
              `sejour_id` INT (11) UNSIGNED NOT NULL,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPerfusionLine') NOT NULL
          ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `planification_systeme` 
              ADD INDEX (`dateTime`),
              ADD INDEX (`prise_id`),
              ADD INDEX (`sejour_id`),
              ADD INDEX (`object_id`);";
		$this->addQuery($query);
		
		$this->makeRevision("0.94");
		$this->addPrefQuery("show_transmissions_form", "0");
		
		$this->makeRevision("0.95");
		$query = "ALTER TABLE `prescription_line_medicament` 
              ADD `injection_ide` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("0.96");
		$query = "ALTER TABLE `category_prescription` 
              ADD `color` CHAR (6);";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `element_prescription` 
              ADD `color` CHAR (6);";
		$this->addQuery($query);
		
		$this->makeRevision("0.97");
		$query = "ALTER TABLE `perfusion` 
              ADD `quantite_totale` INT (11),
              ADD `duree_passage` INT (11);";
		$this->addquery($query);
		
		$query = "ALTER TABLE `perfusion_line` 
              ADD `solvant` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("0.98");
		$query = "CREATE TABLE `perfusion_variation` (
              `perfusion_variation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `perfusion_id` INT (11) UNSIGNED NOT NULL,
              `debit` INT (11) UNSIGNED NOT NULL,
              `dateTime` DATETIME NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
	 
	  $query = "ALTER TABLE `perfusion_variation` 
              ADD INDEX (`perfusion_id`),
              ADD INDEX (`dateTime`);";
		$this->addQuery($query);
		
		$this->makeRevision("0.99");
		$this->addPrefQuery("easy_mode", "0");
		
		$this->makeRevision("1.00");
		$query = "ALTER TABLE `prescription_line_dmi` 
              ADD `date` DATETIME NOT NULL;";
		$this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_dmi` 
              ADD INDEX (`date`);";
		$this->addQuery($query);
    
    $this->makeRevision("1.01");
    $query = "ALTER TABLE `prescription_line_dmi`
              ADD `operation_id` INT (11) UNSIGNED NOT NULL,
              ADD `septic` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `prescription_line_dmi` ADD INDEX (`operation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.02");
    $query = "ALTER TABLE `prescription_line_dmi` ADD `type` ENUM ('purchase','loan','deposit') NOT NULL DEFAULT 'purchase'";
    $this->addQuery($query);

    $this->makeRevision("1.03");
		
		// Renommage des tables et des champs
    $query = "ALTER TABLE `perfusion` RENAME `prescription_line_mix`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix`
            CHANGE `perfusion_id` `prescription_line_mix_id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($query);
   
    $query = "ALTER TABLE `prescription_line_mix`
            CHANGE `next_perf_id` `next_line_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perfusion_line` RENAME `prescription_line_mix_item`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix_item`
            CHANGE `perfusion_line_id` `prescription_line_mix_item_id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix_item`
            CHANGE `perfusion_id` `prescription_line_mix_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `perfusion_variation` RENAME `prescription_line_mix_variation`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix_variation`
            CHANGE `perfusion_variation_id` `prescription_line_mix_variation_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix_variation`
            CHANGE `perfusion_id` `prescription_line_mix_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    // Ajout du nouveau champ dans l'enum
    $query = "ALTER TABLE `administration` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPerfusionLine','CPrescriptionLineMixItem') NOT NULL;";
    $this->addQuery($query);
      
    $query = "ALTER TABLE `planification_systeme` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPerfusionLine','CPrescriptionLineMixItem') NOT NULL;";
    $this->addQuery($query);
              
    $query = "ALTER TABLE `prescription_line_medicament` 
            CHANGE `substitute_for_class` `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPerfusion','CPrescriptionLineMix') DEFAULT 'CPrescriptionLineMedicament'";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_mix` 
            CHANGE `substitute_for_class` `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPerfusion','CPrescriptionLineMix') DEFAULT 'CPrescriptionLineMix'";
    $this->addQuery($query);

    $this->addDependency("dPhospi", "0.23");
    $query = "ALTER TABLE `transmission_medicale` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration','CPerfusion','CPrescriptionLineMix');";
    $this->addQuery($query);
    
    // Update des tables
    $query = "UPDATE administration
            SET `object_class` = 'CPrescriptionLineMixItem'
            WHERE `object_class` = 'CPerfusionLine'";
    $this->addQuery($query);
    
    $query = "UPDATE planification_systeme
            SET `object_class` = 'CPrescriptionLineMixItem'
            WHERE `object_class` = 'CPerfusionLine'";
    $this->addQuery($query);
    
    $query = "UPDATE prescription_line_medicament
            SET `substitute_for_class` = 'CPrescriptionLineMix'
            WHERE `substitute_for_class` = 'CPerfusion'";
    $this->addQuery($query);
    
    $query = "UPDATE prescription_line_mix
            SET `substitute_for_class` = 'CPrescriptionLineMix'
            WHERE `substitute_for_class` = 'CPerfusion'";
    $this->addQuery($query);
    
    $query = "UPDATE transmission_medicale
            SET `object_class` = 'CPrescriptionLineMix'
            WHERE `object_class` = 'CPerfusion'";
    $this->addQuery($query);
    
    // Modification de l'enum
    $query = "ALTER TABLE `administration` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPrescriptionLineMixItem') NOT NULL;";
    $this->addQuery($query);
      
    $query = "ALTER TABLE `planification_systeme` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement','CPrescriptionLineMixItem') NOT NULL;";
    $this->addQuery($query);
              
    $query = "ALTER TABLE `prescription_line_medicament` 
            CHANGE `substitute_for_class` `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineMix') DEFAULT 'CPrescriptionLineMedicament'";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_mix` 
            CHANGE `substitute_for_class` `substitute_for_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineMix') DEFAULT 'CPrescriptionLineMix'";
    $this->addQuery($query);

    $query = "ALTER TABLE `transmission_medicale` 
            CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration','CPrescriptionLineMix');";
    $this->addQuery($query);
    
    // Update des users log
    $query = "UPDATE user_log
            SET object_class = 'CPrescriptionLineMix'
            WHERE object_class = 'CPerfusion'";
    $this->addQuery($query);
    
    $query = "UPDATE user_log
            SET object_class = 'CPrescriptionLineMixItem'
            WHERE object_class = 'CPerfusionLine'";
    $this->addQuery($query);
    
    $query = "UPDATE user_log
            SET object_class = 'CPrescriptionLineMixVariation'
            WHERE object_class = 'CPerfusionVariation'";
    $this->addQuery($query);
		
		$this->makeRevision("1.04");
		$query = "ALTER TABLE `prescription_line_mix` 
		        ADD `type_line` ENUM ('perfusion','aerosol','oxygene','alimentation') NOT NULL DEFAULT 'perfusion';";
		$this->addQuery($query);
		
		$this->makeRevision("1.05");
		$query = "ALTER TABLE `prescription_line_mix` 
            CHANGE `type` `type` ENUM ('classique','seringue','PCA','masque','lunettes','sonde');";
		$this->addQuery($query);
    
    $this->makeRevision("1.06");
    $query = "ALTER TABLE `prescription_line_dmi` ADD `quantity` INT (11) UNSIGNED NOT NULL DEFAULT '1';";
    $this->addQuery($query);
		
		$this->makeRevision("1.07");
		$query = "ALTER TABLE `prescription_line_element` 
              ADD `ide_domicile` ENUM ('0','1') DEFAULT '0',
              ADD `cip_dm` INT (7) UNSIGNED ZEROFILL,
              ADD `quantite_dm` FLOAT;";
		$this->addQuery($query);
		
		$this->makeRevision("1.08");
		$query = "ALTER TABLE `prescription_line_mix` 
							CHANGE `type` `type` ENUM ('classique','seringue','PCA','masque','lunettes','sonde','nebuliseur_ultrasonique','nebuliseur_pneumatique','doseur','inhalateur'),
							ADD `interface` VARCHAR (255);";
		$this->addQuery($query);
		
		$this->makeRevision("1.09");
		$query = "ALTER TABLE `prescription_line_mix`
              ADD `unite_duree` ENUM ('heure','jour') DEFAULT 'heure',
							ADD `unite_duree_passage` ENUM ('minute','heure') DEFAULT 'minute'";
		$this->addQuery($query);
    
    $this->makeRevision("1.10");
    $query = "ALTER TABLE `prescription_line_dmi` ADD `signed` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
		
		$this->makeRevision("1.11");
		$query = "ALTER TABLE `category_prescription` ADD `prescription_executant` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);					
							
		$this->makeRevision("1.12");
		$query = "ALTER TABLE `prescription` ADD `fast_access` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.13");
		$query = "ALTER TABLE `category_prescription` 
              CHANGE `chapitre` `chapitre` ENUM ('anapath','biologie','imagerie','consult','kine','soin','dm','dmi','med_elt') NOT NULL";
		$this->addQuery($query);
		
		$this->makeRevision("1.14");
		$query = "ALTER TABLE `prescription_line_element` 
             ADD `perop` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_medicament` 
              ADD `perop` ENUM ('0','1') DEFAULT '0',
							ADD `stupefiant` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_mix` 
              ADD `perop` ENUM ('0','1') DEFAULT '0';";
	  $this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_mix_item` 
              ADD `stupefiant` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.15");
		$query = "ALTER TABLE `planification_systeme` 
              ADD `administration_id` INT (11) UNSIGNED;";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `planification_systeme` 
              ADD INDEX (`administration_id`);";
	  $this->addQuery($query);
		
		$this->makeRevision("1.16");
		$query = "ALTER TABLE `prise_posologie` 
              ADD `unite_decalage_intervention` ENUM ('minute','heure') DEFAULT 'heure';";
		$this->addQuery($query);
				
		$this->makeRevision("1.17");
		$query = "ALTER TABLE `administration`
              ADD `planification_systeme_id` INT (11) UNSIGNED;";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `administration` 
              ADD INDEX (`planification_systeme_id`);";
		$this->addQuery($query);			

    $query = "ALTER TABLE `planification_systeme` DROP `administration_id`;";
		$this->addQuery($query);				
		
		$this->makeRevision("1.18");
		$query = "ALTER TABLE `prescription_line_mix_item` 
              CHANGE `quantite` `quantite` FLOAT;";
		$this->addQuery($query);
										
		$this->makeRevision("1.19");
		$query = "ALTER TABLE `category_prescription` 
              CHANGE `chapitre` `chapitre` ENUM ('anapath','biologie','imagerie','consult','kine','soin','dm','dmi','med_elt','ds') NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.20");
		$query = "UPDATE `administration`
		         LEFT JOIN prescription_line_element ON (prescription_line_element.prescription_line_element_id = administration.object_id AND administration.object_class = 'CPrescriptionLineElement')
             LEFT JOIN element_prescription ON element_prescription.element_prescription_id = prescription_line_element.element_prescription_id
						 LEFT JOIN category_prescription ON category_prescription.category_prescription_id = element_prescription.category_prescription_id
						 SET `unite_prise` = category_prescription.chapitre
             WHERE `unite_prise` IS NOT NULL 
						 AND object_class = 'CPrescriptionLineElement'
						 AND unite_prise != 'aucune_prise'";
    $this->addQuery($query);
		
	 $query = "UPDATE `planification_systeme`
             LEFT JOIN prescription_line_element ON (prescription_line_element.prescription_line_element_id = planification_systeme.object_id AND planification_systeme.object_class = 'CPrescriptionLineElement')
             LEFT JOIN element_prescription ON element_prescription.element_prescription_id = prescription_line_element.element_prescription_id
             LEFT JOIN category_prescription ON category_prescription.category_prescription_id = element_prescription.category_prescription_id
             SET `unite_prise` = category_prescription.chapitre
             WHERE `unite_prise` IS NOT NULL 
             AND object_class = 'CPrescriptionLineElement'";
    $this->addQuery($query);
		
		$this->makeRevision("1.21");
		$query = "ALTER TABLE `prescription_line_mix`
              CHANGE `vitesse` `volume_debit` INT (11) UNSIGNED,
              ADD `duree_debit`  INT (11) UNSIGNED DEFAULT '1'";
		$this->addQuery($query);
		
		$this->makeRevision("1.22");
		$query = "ALTER TABLE `prescription` ADD `planif_removed` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.23");
		$query = "ALTER TABLE `prescription` ADD `score` ENUM ('0','1','2');";
		$this->addQuery($query);
		
		$this->makeRevision("1.24");
		
		$query = "ALTER TABLE `prescription_line_medicament` CHANGE `commentaire` `commentaire` TEXT;";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_element` CHANGE `commentaire` `commentaire` TEXT;";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_mix` CHANGE `commentaire` `commentaire` TEXT;";
		$this->addQuery($query);
	
		$this->makeRevision("1.25");
		
		$query = "INSERT INTO `config_service` (`config_service_id`,`name`,`value`,`service_id`,`group_id`) VALUES
      ('','Affichage alertes de modifications','".
		  CAppUI::conf("dPprescription CPrescription time_alerte_modification")."',NULL,NULL)";
		$this->addQuery($query);
		
    $this->makeRevision("1.26");
		$query = "UPDATE `prescription_line_medicament` SET `voie` = 'none' WHERE `voie` IS NULL";
		$this->addQuery($query);

    $this->makeRevision("1.27");
		$query = "ALTER TABLE `prescription_line_medicament` ADD INDEX (`code_cip`);";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_medicament` ADD INDEX (`code_ucd`);";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_medicament` ADD INDEX (`code_cis`);";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_mix_item` ADD INDEX (`code_cip`);";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_mix_item` ADD INDEX (`code_ucd`);";
    $this->addQuery($query);
    
		$query = "ALTER TABLE `prescription_line_mix_item` ADD INDEX (`code_cis`);";
    $this->addQuery($query);
    
		$this->makeRevision("1.28");
		
    $query = "ALTER TABLE `prescription_line_medicament` ADD `inscription` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element` ADD `inscription` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_comment` ADD `inscription` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
		
    $this->makeRevision("1.29");    
    $query = "ALTER TABLE `prescription_line_mix` CHANGE `volume_debit` `volume_debit` FLOAT UNSIGNED";
    $this->addQuery($query);

		$this->makeRevision("1.30");
		$query = "ALTER TABLE `prise_posologie` 
              ADD `condition` VARCHAR (255);";
		$this->addQuery($query);
    
		$this->makeRevision("1.31");
		$query = "ALTER TABLE `prise_posologie` ADD `datetime` DATETIME;";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prise_posologie` ADD INDEX (`datetime`);";
		$this->addQuery($query);
		
		$this->makeRevision("1.32");
		$query = "ALTER TABLE `category_prescription` ADD `cible_importante` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.33");
		$query = "ALTER TABLE `prescription_protocole_pack` 
              ADD `group_id` INT (11) UNSIGNED, 
              ADD INDEX (`group_id`);";
		$this->addQuery($query);
		
		$this->makeRevision("1.34");
		$this->addPrefQuery("hide_old_lines", "0");
		
		$this->makeRevision("1.35");
		$query = "ALTER TABLE `prescription_line_mix` 
              CHANGE `decalage_interv` `decalage_line` INT (11),
              CHANGE `jour_decalage` `jour_decalage` ENUM ('E','I','S','N'),
              ADD `unite_decalage` ENUM ('jour','heure'),
              ADD `decalage_line_fin` INT (11),
              ADD `jour_decalage_fin` ENUM ('I','S'),
              ADD `unite_decalage_fin` ENUM ('jour','heure');";
		$this->addQuery($query);
		
		$query = "UPDATE prescription_line_mix
		          SET unite_decalage = 'heure';";
		$this->addQuery($query);
		
		$this->makeRevision("1.36");
		$query = "ALTER TABLE `prescription_line_mix_variation` 
              CHANGE `debit` `debit` FLOAT NOT NULL;";
		$this->addQuery($query);
		
		$this->makeRevision("1.37");
		$query = "ALTER TABLE `category_prescription`
		          ADD `only_cible` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.38");

    $query = "UPDATE `config_service` SET `name` = 'Poste 1' WHERE `name` = 'Borne matin min'";
		$this->addQuery($query);
		
		$query = "UPDATE `config_service` SET `name` = 'Poste 2' WHERE `name` = 'Borne soir min'";
    $this->addQuery($query);
    
		$query = "UPDATE `config_service` SET `name` = 'Poste 3' WHERE `name` = 'Borne nuit min'";
    $this->addQuery($query);
    
		$query = "INSERT INTO `config_service` (`config_service_id`,`name`,`value`,`service_id`,`group_id`) VALUES
      ('','Poste 4',NULL,NULL,NULL),
      ('','Libelle poste 1','Matin',NULL,NULL),
      ('','Libelle poste 2','Soir',NULL,NULL),
      ('','Libelle poste 3','Nuit',NULL,NULL),
      ('','Libelle poste 4','',NULL,NULL),
			('','Nombre postes avant','3',NULL,NULL),
      ('','Nombre postes apres','3',NULL,NULL);";
    $this->addQuery($query);
    
		$query = "DELETE FROM `config_service` WHERE `name` = 'Borne matin max';";
		$this->addQuery($query);
		
    $query = "DELETE FROM `config_service` WHERE `name` = 'Borne soir max';";
    $this->addQuery($query);
    
    $query = "DELETE FROM `config_service` WHERE `name` = 'Borne nuit max';";
    $this->addQuery($query);

	  $this->makeRevision("1.39");
    $query = "ALTER TABLE `prescription` 
              ADD `advanced_protocole` ENUM ('0','1') DEFAULT '0' AFTER `fast_access`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_comment` 
              ADD `protocole_id` INT (11) UNSIGNED,
              ADD INDEX (`protocole_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_dmi` 
              ADD `protocole_id` INT (11) UNSIGNED,
              ADD INDEX (`protocole_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element` 
              ADD `protocole_id` INT (11) UNSIGNED,
              ADD INDEX (`protocole_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_medicament` 
              ADD `protocole_id` INT (11) UNSIGNED,
              ADD INDEX (`protocole_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_mix` 
              ADD `protocole_id` INT (11) UNSIGNED,
              ADD INDEX (`protocole_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `administration`
              ADD `constantes_medicales_id` INT (11) UNSIGNED,
              ADD INDEX (`constantes_medicales_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `constante_item` (
              `constante_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `category_prescription_id` INT (11) UNSIGNED NOT NULL,
              `field_constante` VARCHAR (80),
              `commentaire` VARCHAR (255)) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `constante_item` 
              ADD INDEX (`category_prescription_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.40");
    $query = "ALTER TABLE `constante_item`
              DROP `category_prescription_id`,
              ADD `element_prescription_id` INT (11) UNSIGNED NOT NULL AFTER `constante_item_id`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription` 
              ADD `checked_lines` ENUM ('0','1') DEFAULT '0' AFTER `advanced_protocole`;";
    $this->addQuery($query);
    
		$this->makeRevision("1.41");
		$query = "ALTER TABLE `element_prescription` 
              ADD `prescriptible_kine` ENUM ('0','1') DEFAULT '0',
              ADD `prescriptible_infirmiere` ENUM ('0','1') DEFAULT '0',
              ADD `prescriptible_AS` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.42");
		$query = "ALTER TABLE `element_prescription`
              ADD `rdv` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$this->makeRevision("1.43");
		$query = "ALTER TABLE `sejour_task` 
              ADD `prescription_line_element_id` INT (11) UNSIGNED;";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `sejour_task` 
              ADD INDEX (`prescription_line_element_id`);";
		$this->addQuery($query);	
		
		$this->makeRevision("1.44");
		$this->addPrefQuery("show_hour_onmouseover_plan_soins", "1");
    
    $this->makeRevision("1.45");
    $query = "ALTER TABLE `prescription_line_element`
              ADD `recusee` ENUM ('0','1') DEFAULT '0';";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_medicament`
              ADD `recusee` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.46");
    $this->addPrefQuery("lt_checked_externe" , "1");
    $this->addPrefQuery("dci_checked_externe", "1");
    $this->addPrefQuery("duplicata_checked_externe", "1");
    $this->addPrefQuery("date_empty_externe" , "0");
    
    $this->makeRevision("1.47");
    $query = "UPDATE `prescription`
              SET checked_lines = '1'
              WHERE advanced_protocole = '0'";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription` DROP advanced_protocole";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription`
              CHANGE `checked_lines` `checked_lines` ENUM ('0','1') DEFAULT '1'";
    $this->addQuery($query);
    
		$this->makeRevision("1.48");
		$query = "ALTER TABLE `administration`
              DROP `planification_systeme_id`;";
		$this->addQuery($query);
		
		$this->makeRevision("1.49");
		$query = "ALTER TABLE `prescription_line_medicament`
              CHANGE `jour_decalage` `jour_decalage` ENUM ('E','I','S','N','A') DEFAULT 'E';";
		$this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_element`
              CHANGE `jour_decalage` `jour_decalage` ENUM ('E','I','S','N','A') DEFAULT 'E';";
    $this->addQuery($query);
		
		$query = "ALTER TABLE `prescription_line_comment`
              CHANGE `jour_decalage` `jour_decalage` ENUM ('E','I','S','N','A') DEFAULT 'E';";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_mix`
              CHANGE `jour_decalage` `jour_decalage` ENUM ('E','I','S','N','A');";
    $this->addQuery($query);
		
		$this->makeRevision("1.50");
		
		$query = "ALTER TABLE `prescription_line_medicament`
              CHANGE `jour_decalage_fin` `jour_decalage_fin` ENUM ('I','S','A');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_element`
              CHANGE `jour_decalage_fin` `jour_decalage_fin` ENUM ('I','S','A');";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prescription_line_comment`
              CHANGE `jour_decalage_fin` `jour_decalage_fin` ENUM ('I','S','A');";
    $this->addQuery($query);

    $query = "ALTER TABLE `prescription_line_mix`
              CHANGE `jour_decalage_fin` `jour_decalage_fin` ENUM ('I','S','A');";
    $this->addQuery($query);
		
		$this->makeRevision("1.51");
		$query = "ALTER TABLE `prise_posologie` 
              ADD `type_decalage` ENUM ('I','A') DEFAULT 'I';";
		$this->addQuery($query);
		
		$this->mod_version = "1.52";
  }
}

?>