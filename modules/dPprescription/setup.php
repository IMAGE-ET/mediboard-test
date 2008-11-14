<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

class CSetupdPprescription extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPprescription";
       
    $this->makeRevision("all");
    
    $sql = "CREATE TABLE `prescription` (
          `prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `object_class` ENUM('CSejour','CConsultation') NOT NULL DEFAULT 'CSejour',
          `object_id` INT(11) UNSIGNED,
          PRIMARY KEY (`prescription_id`)
          ) TYPE=MyISAM COMMENT='Table des prescriptions';";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `prescription_line` (
          `prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `prescription_id` INT(11) UNSIGNED NOT NULL,
          `code_cip` VARCHAR(7) NOT NULL,
          PRIMARY KEY (`prescription_line_id`)
          ) TYPE=MyISAM COMMENT='Table des lignes de médicament des prescriptions';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.10");
    
    $sql = "ALTER TABLE `prescription_line` ADD `no_poso` SMALLINT(6) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line` ADD INDEX (`no_poso`) ;" ;
    $this->addQuery($sql);

    $sql = "ALTER TABLE `prescription` ADD `praticien_id` INT(11) NOT NULL AFTER `prescription_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription` ADD INDEX (`praticien_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `category_prescription` (
           `category_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `chapitre` ENUM('dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
           `nom` VARCHAR(255) NOT NULL, 
           `description` TEXT,  
            PRIMARY KEY (`category_prescription_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `element_prescription` (
           `element_prescription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `libelle` VARCHAR(255) NOT NULL, 
           `description` TEXT, 
           PRIMARY KEY (`element_prescription_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `prescription_line_element` (
           `prescription_line_element_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `element_prescription_id` INT(11) UNSIGNED NOT NULL, 
           `prescription_id` INT(11) UNSIGNED NOT NULL,
           `commentaire` VARCHAR(255),
           PRIMARY KEY (`prescription_line_element_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `prescription_line`
            ADD `commentaire` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `prescription`
            ADD `libelle` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "CREATE TABLE `prescription_line_comment` (
           `prescription_line_comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_id` INT(11) UNSIGNED NOT NULL, 
           `commentaire` TEXT, 
           `chapitre` ENUM('medicament','dmi','anapath','biologie','imagerie','consult','kine','soin') NOT NULL, 
            PRIMARY KEY (`prescription_line_comment_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line`
            ADD `ald` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "CREATE TABLE `moment_unitaire` (
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `libelle` VARCHAR(255) NOT NULL, 
           `heure_min` TIME, 
           `heure_max` TIME, 
           `type_moment` ENUM('matin','midi','apres_midi','soir','horaire','autre') NOT NULL,
            PRIMARY KEY (`moment_unitaire_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
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
      $sql = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($sql);
    }
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `prescription_line` 
            ADD `debut` DATE, 
            ADD `duree` INT(11);";
    $this->addQuery($sql);

    $this->makeRevision("0.18");
    $sql = "CREATE TABLE `prise_posologie` (
           `prise_posologie_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `prescription_line_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `quantite` INT(11), 
            PRIMARY KEY (`prise_posologie_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $sql = "CREATE TABLE `association_moment` (
           `association_moment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `code_moment_id` INT(11) UNSIGNED NOT NULL, 
           `moment_unitaire_id` INT(11) UNSIGNED NOT NULL, 
           `OR` ENUM('0','1') DEFAULT '0', 
            PRIMARY KEY (`association_moment_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $sql = "ALTER TABLE `prescription_line`
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE `prise_posologie`
            CHANGE `quantite` `quantite` FLOAT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `prise_posologie`
            CHANGE `moment_unitaire_id` `moment_unitaire_id` INT(11) UNSIGNED DEFAULT NULL, 
            ADD `nb_fois` INT(11), 
            ADD `unite_fois` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an'), 
            ADD `nb_tous_les` INT(11), 
            ADD `unite_tous_les` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);

    
    $this->makeRevision("0.23"); 
    
    $moments = array();
    $moments[] = array("1 heure après les repas", "autre");
    $moments[] = array("2 heures avant le déjeuner", "midi");
    $moments[] = array("15 minutes avant le coucher", "soir");
    $moments[] = array("1 heure après le petit-déjeuner", "matin");
    $moments[] = array("1 fois par jour dans chaque narine", "autre");
    $moments[] = array("30 minute avant le repas", "autre");
    
    foreach($moments as &$moment){
      $sql = " INSERT INTO `moment_unitaire` ( `moment_unitaire_id` , `libelle` , `heure_min`, `heure_max`, `type_moment` ) VALUES ( '' , '".$moment[0]."', NULL, NULL, '".$moment[1]."');";
      $this->addQuery($sql);
    }
   
    $this->makeRevision("0.24");
    $sql = "CREATE TABLE `executant_prescription_line` (
            `executant_prescription_line_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,  
            `category_prescription_id` INT(11) UNSIGNED NOT NULL, 
            `nom` VARCHAR(255) NOT NULL, 
            `description` TEXT, 
            PRIMARY KEY (`executant_prescription_line_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `executant_prescription_line_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `category_prescription_id` INT(11) UNSIGNED, 
            ADD `executant_prescription_line_id` INT(11) UNSIGNED, 
            DROP `chapitre`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `prescription`
            ADD `type` ENUM('externe','pre_admission','sejour','sortie','traitement') NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line`
            ADD `stoppe` ENUM('0','1'), 
            ADD `praticien_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.28");
    $sql = "ALTER TABLE `prescription_line`
            ADD `date_arret` DATE,
            DROP `stoppe`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    $sql = "ALTER TABLE `prescription_line` 
            ADD `valide` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.30");
    $sql = "ALTER TABLE `moment_unitaire`
            ADD `principal` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.31");
    $sql = "ALTER TABLE `prescription_line`
            CHANGE `valide` `valide_prat` ENUM('0','1') DEFAULT '0', 
            ADD `valide_pharma` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.32");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `signee` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `signee` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line`
            CHANGE `valide_prat` `signee` ENUM('0','1') DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.33");
    $sql = "ALTER TABLE `prescription_line`
            ADD `accord_praticien` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.34");
    $sql = "ALTER TABLE `prescription_line` RENAME `prescription_line_medicament`";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament`
            CHANGE `prescription_line_id` `prescription_line_medicament_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `debut` DATE, 
            ADD `duree` INT(11), 
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `debut` DATE, 
            ADD `duree` INT(11), 
            ADD `unite_duree` ENUM('minute','heure','demi_journee','jour','semaine','quinzaine','mois','trimestre','semestre','an');";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prise_posologie`
            CHANGE `prescription_line_id` `object_id` INT(11) UNSIGNED NOT NULL, 
            ADD `object_class` ENUM('CPrescriptionLineMedicament','CPrescriptionLineElement') NOT NULL DEFAULT 'CPrescriptionLineMedicament';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.35");
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `date_arret` DATE;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `date_arret` DATE;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.36");
    $sql = "ALTER TABLE `prescription`
            CHANGE `praticien_id` `praticien_id` INT(11) UNSIGNED, 
            ADD `function_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.37");
    $sql = "ALTER TABLE `category_prescription`
            CHANGE `chapitre` `chapitre` ENUM('dmi','anapath','biologie','imagerie','consult','kine','soin','dm') NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.38");
    $sql = "ALTER TABLE `prescription_line_medicament`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `child_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.39");
    $sql = "ALTER TABLE `prise_posologie`
            ADD `decalage_prise` INT(11) DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment`
            ADD `decalage_line` INT(11), 
            ADD `fin` DATE;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.40");
    
    $sql = "ALTER TABLE `prescription` 
	          ADD INDEX (`function_id`),
 	          ADD INDEX (`object_id`);";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament` 
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQuery($sql);
	  
	  $sql = "ALTER TABLE `prescription_line_element` 
						ADD INDEX (`element_prescription_id`),
						ADD INDEX (`executant_prescription_line_id`),
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQuery($sql);
    
	  $sql = "ALTER TABLE `prescription_line_comment` 
						ADD INDEX (`category_prescription_id`),
						ADD INDEX (`executant_prescription_line_id`),
						ADD INDEX (`prescription_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`debut`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`child_id`),
						ADD INDEX (`fin`);";
	  $this->addQUery($sql);
	  
    $sql = "ALTER TABLE `prise_posologie` 
	          ADD INDEX (`moment_unitaire_id`),
          	ADD INDEX (`object_id`);";
	  $this->addQuery($sql);  

	  $this->makeRevision("0.41");

	  $sql = "ALTER TABLE `prescription_line_medicament` 
 	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0',
            ADD `substitution_line_id` INT (11) UNSIGNED;";
	  $this->addQuery($sql);
	  
	  // Pour toute les lignes qui n'ont pas de praticien_id, on stocke celui de la prescription
    $sql = "UPDATE `prescription_line_medicament`,`prescription`
            SET `prescription_line_medicament`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_medicament`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_medicament`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `prescription_line_element`,`prescription`
            SET `prescription_line_element`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_element`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_element`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `prescription_line_comment`,`prescription`
            SET `prescription_line_comment`.`praticien_id` = `prescription`.`praticien_id`
            WHERE `prescription_line_comment`.`praticien_id` IS NULL
            AND `prescription`.`prescription_id` = `prescription_line_comment`.`prescription_id`
            AND `prescription`.`object_id` IS NOT NULL;";
    $this->addQuery($sql);
    
    // Ajout du creator_id, par defaut, le creator_id est le praticien_id
	  $sql = "ALTER TABLE `prescription_line_medicament` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `prescription_line_medicament`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($sql);
    
	  $sql = "ALTER TABLE `prescription_line_element` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `prescription_line_element`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment` 
           	ADD `creator_id` INT (11) UNSIGNED NOT NULL,
            ADD INDEX (`creator_id`);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `prescription_line_comment`
            SET `creator_id` = `praticien_id`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment` 
	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element` 
 	          ADD `valide_infirmiere` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament` 
            ADD `time_arret` TIME;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element` 
            ADD `time_arret` TIME;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_comment` 
            ADD `time_arret` TIME;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prise_posologie` 
            ADD `unite_prise` TEXT;";
    $this->addQuery($sql);
       
    function updateUnitePrise(){

     $ds_std = CSQLDataSource::get("std");
     $ds_bcb = CSQLDataSource::get("bcb");
             
     // Recuperation de toutes les lignes de posologies ayant unite_prise à NULL et aucun numero de poso indiqué 
     $sql = "SELECT prise_posologie.prise_posologie_id, prescription_line_medicament.code_cip 
             FROM prise_posologie, prescription_line_medicament
             WHERE prise_posologie.object_id = prescription_line_medicament.prescription_line_medicament_id
						 AND prise_posologie.unite_prise IS NULL	              
						 AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
             AND prescription_line_medicament.no_poso IS NULL";
     $res_sans_poso = $ds_std->loadList($sql);

     foreach($res_sans_poso as $_prise){
     	// Recuperation de l'unite de prise
     	$sql = "SELECT `LIBELLE_UNITE_DE_PRISE_PLURIEL` 
                FROM `POSO_UNITES_PRISE`,`POSO_PRODUITS` 
                WHERE `POSO_PRODUITS`.`CODE_CIP` = '".$_prise["code_cip"]."'
							  AND `POSO_PRODUITS`.`CODE_UNITE_DE_PRISE` = `POSO_UNITES_PRISE`.`CODE_UNITE_DE_PRISE`
                LIMIT 1;";	
       $res = $ds_bcb->loadResult($sql);	
       
       // Mise a jour de la prise
       $sql = "UPDATE `prise_posologie`
                SET `unite_prise` = '".$res."'
                WHERE `prise_posologie_id` = '".$_prise["prise_posologie_id"]."';";
     	$res = $ds_std->exec( $sql );
     }

     // Recuperation de toutes les lignes de posologies ayant unite_prise à NULL mais un numero de poso indiqué 
     $sql = "SELECT prise_posologie.prise_posologie_id, prescription_line_medicament.code_cip, prescription_line_medicament.no_poso 
             FROM prise_posologie, prescription_line_medicament
             WHERE prise_posologie.object_id = prescription_line_medicament.prescription_line_medicament_id
             AND prise_posologie.object_class = 'CPrescriptionLineMedicament'
             AND prise_posologie.unite_prise IS NULL
             AND prescription_line_medicament.no_poso IS NOT NULL";
     $res_avec_poso = $ds_std->loadList($sql);
       
     foreach($res_avec_poso as $_prise){
     	// Recuperation de l'unite de prise
     	$sql = "SELECT `LIBELLE_UNITE_DE_PRISE_PLURIEL` 
              FROM `POSO_UNITES_PRISE`,`POSO_PRODUITS` 
              WHERE `POSO_PRODUITS`.`CODE_CIP` = '".$_prise["code_cip"]."'
              AND `POSO_PRODUITS`.`NO_POSO` = '".$_prise["no_poso"]."'
							AND `POSO_PRODUITS`.`CODE_UNITE_DE_PRISE` = `POSO_UNITES_PRISE`.`CODE_UNITE_DE_PRISE`;";	
       $res = $ds_bcb->loadResult($sql);	
       
       // Mise a jour de la prise
       $sql = "UPDATE `prise_posologie`
                SET `unite_prise` = '".$res."'
                WHERE `prise_posologie_id` = '".$_prise["prise_posologie_id"]."';";
     	$res = $ds_std->exec( $sql );
     }
     return true;
    }
    $this->addFunctions("updateUnitePrise");
   
    $this->makeRevision("0.42");
    $sql = "ALTER TABLE `category_prescription` 
	          ADD `header` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.43");
    $sql = "ALTER TABLE `moment_unitaire` 
   	        ADD `heure` TIME,
 	          DROP `heure_min`,
	          DROP `heure_max`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.44");
    $sql = "ALTER TABLE `prescription_line_medicament` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_element` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_comment` 
	          ADD `jour_decalage` ENUM ('E','I','S','N');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.45");
    $sql = "ALTER TABLE `prescription_line_medicament` 
						ADD `time_debut` TIME;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_element` 
						ADD `time_debut` TIME;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_comment` 
						ADD `time_debut` TIME;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.46");
    $sql = "ALTER TABLE `prescription_line_medicament` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
	          ADD `time_fin` TIME;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_element` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
	          ADD `time_fin` TIME;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_line_comment` 
	          ADD `jour_decalage_fin` ENUM ('I','S'),
	          ADD `decalage_line_fin` INT (11),
        	  ADD `time_fin` TIME;";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.47");
    $sql = "CREATE TABLE `moment_complexe` (
							`moment_complexe_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`code_moment_id` INT (11) NOT NULL,
							`visible` ENUM ('0','1') DEFAULT '0'
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.48");
    $sql = "CREATE TABLE `administration` (
							`administration_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
          	  `administrateur_id` INT (11) UNSIGNED NOT NULL,
							`dateTime` DATETIME,
							`quantite` FLOAT,
							`unite_prise` TEXT,
							`commentaire` TEXT,
							`object_id` INT (11) UNSIGNED NOT NULL,
							`object_class` ENUM ('CPrescriptionLineMedicament','CPrescriptionLineElement') NOT NULL
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `administration` 
						ADD INDEX (`dateTime`),
	          ADD INDEX (`administrateur_id`),
						ADD INDEX (`object_id`);";
    $this->addQuery($sql);

    $this->makeRevision("0.49");
    $sql = "ALTER TABLE `administration` 
	          ADD `prise_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `administration` 
	          ADD INDEX (`prise_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.50");
    $sql = "ALTER TABLE `prescription_line_element` 
	        ADD `conditionnel` ENUM ('0','1') DEFAULT '0',
	        ADD `condition_active` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament` 
	        ADD `conditionnel` ENUM ('0','1') DEFAULT '0',
	        ADD `condition_active` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.51");
    $sql = "ALTER TABLE `prescription_line_element` 
		    ADD `unite_decalage` ENUM ('jour','heure') DEFAULT 'jour',
	        ADD `unite_decalage_fin` ENUM ('jour','heure') DEFAULT 'jour';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament` 
		    ADD `unite_decalage` ENUM ('jour','heure') DEFAULT 'jour',
	        ADD `unite_decalage_fin` ENUM ('jour','heure') DEFAULT 'jour';";
    $this->addQuery($sql);

    $this->makeRevision("0.52");
    $sql = "ALTER TABLE `prescription_line_medicament` 
	        ADD `operation_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_element` 
	        ADD `operation_id` INT (11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.53");
    $sql = "ALTER TABLE `prescription_line_medicament` 
	          ADD `substitute_for` INT (11) UNSIGNED,
	          ADD `substitution_active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `prescription_line_medicament`
						ADD INDEX (`substitution_line_id`),
						ADD INDEX (`substitute_for`),
						ADD INDEX (`time_debut`),
						ADD INDEX (`time_arret`),
						ADD INDEX (`time_fin`),
						ADD INDEX (`operation_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.54");
    $sql = "CREATE TABLE `prescription_protocole_pack` (
						`prescription_protocole_pack_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`libelle` VARCHAR (255),
						`praticien_id` INT (11) UNSIGNED,
						`function_id` INT (11) UNSIGNED,
            `object_class` ENUM ('CSejour','CConsultation') NOT NULL
					 ) TYPE=MYISAM;";
   $this->addQuery($sql);
   
   $sql = "ALTER TABLE `prescription_protocole_pack` 
					 ADD INDEX (`praticien_id`),
					 ADD INDEX (`function_id`);";
   $this->addQuery($sql);
   
   $sql = "CREATE TABLE `prescription_protocole_pack_item` (
						`prescription_protocole_pack_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`prescription_protocole_pack_id` INT (11) UNSIGNED NOT NULL,
						`prescription_id` INT (11) UNSIGNED NOT NULL
					) TYPE=MYISAM;";
   $this->addQuery($sql);
   
   $sql = "ALTER TABLE `prescription_protocole_pack_item` 
	         ADD INDEX (`prescription_protocole_pack_id`),
	         ADD INDEX (`prescription_id`);";
   $this->addQuery($sql);
  
   $this->makeRevision("0.55");
   $sql = "ALTER TABLE `prescription` 
	         ADD `group_id` INT (11) UNSIGNED,
	         ADD INDEX (`group_id`);";
   $this->addQuery($sql);
 
   $this->makeRevision("0.56");
   $sql = "ALTER TABLE `prescription_line_medicament` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($sql);
   
   $sql = "ALTER TABLE `prescription_line_element` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($sql);
   
   $sql = "ALTER TABLE `prescription_line_comment` 
	         ADD `emplacement` ENUM ('service','bloc') DEFAULT 'service' NOT NULL;";
   $this->addQuery($sql);
   
   $this->makeRevision("0.57");
   $sql = "CREATE TABLE `function_category_prescription` (
						`function_category_prescription_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						`function_id` INT (11) UNSIGNED NOT NULL,
						`category_prescription_id` INT (11) UNSIGNED NOT NULL
					) TYPE=MYISAM;";
   $this->addQuery($sql);
    
	 $sql = "ALTER TABLE `function_category_prescription` 
					 ADD INDEX (`function_id`),
					 ADD INDEX (`category_prescription_id`);";
   $this->addQuery($sql);
   
   $this->makeRevision("0.58");
   $sql = "ALTER TABLE `prescription_line_element` 
					 ADD `user_executant_id` INT (11) UNSIGNED;";
   $this->addQuery($sql);
   
   $sql = "ALTER TABLE `prescription_line_comment` 
					 ADD `user_executant_id` INT (11) UNSIGNED;";
   $this->addQuery($sql);
   
   $this->makeRevision("0.59");
   $sql = "ALTER TABLE `prise_posologie` 
					 ADD `decalage_intervention` INT (11),
					 ADD `heure_prise` TIME;";
	 $this->addQuery($sql);

	 $this->makeRevision("0.60");
	 
	 $sql = "ALTER TABLE `prescription_line_medicament` 
					 ADD `voie` VARCHAR (255);";
	 $this->addQuery($sql);
	 
	 function updateVoie(){
     $ds_std = CSQLDataSource::get("std");
     $ds_bcb = CSQLDataSource::get("bcb");
             
     // Recuperation des tous les codes cip
     $sql = "SELECT DISTINCT code_cip 
             FROM prescription_line_medicament;";
     $codes_cip = $ds_std->loadColumn($sql);
     
     // Parcours des cip et stockage de la premiere voie trouvée
     foreach($codes_cip as $code_cip){
       $sql = "SELECT IDENT_VOIES.LIBELLEVOIE FROM `IDENT_VOIES`
							 LEFT JOIN `IDENT_PRODUITS_VOIES` ON `IDENT_PRODUITS_VOIES`.`CODEVOIE` = `IDENT_VOIES`.`CODEVOIE`
							 WHERE `IDENT_PRODUITS_VOIES`.`CODECIP` = '$code_cip';";
       $voie = $ds_bcb->loadHash($sql);
       $codeCip_voie[$code_cip] = $voie["LIBELLEVOIE"];
     }
     foreach($codeCip_voie as $code_cip => $libelle_voie){
	     $sql = "UPDATE `prescription_line_medicament`
	             SET `voie` = '$libelle_voie'
							 WHERE `code_cip` = '$code_cip'
               AND `voie` IS NULL;";
	     $res = $ds_std->exec($sql);
     }
     return true;
    }
    $this->addFunctions("updateVoie");
	  
    $this->makeRevision("0.61");
    $sql = "CREATE TABLE `perfusion` (
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
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `perfusion` 
						ADD INDEX (`prescription_id`),
						ADD INDEX (`date_debut`),
						ADD INDEX (`time_debut`),
						ADD INDEX (`next_perf_id`),
						ADD INDEX (`praticien_id`),
						ADD INDEX (`creator_id`),
						ADD INDEX (`date_arret`),
						ADD INDEX (`time_arret`);";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `perfusion_line` (
							`perfusion_line_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`perfusion_id` INT (11) UNSIGNED NOT NULL,
							`code_cip` INT (7) UNSIGNED ZEROFILL NOT NULL,
							`quantite` INT (11),
							`unite` VARCHAR (255),
							`date_debut` DATE,
							`time_debut` TIME
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `perfusion_line` 
						ADD INDEX (`perfusion_id`),
						ADD INDEX (`date_debut`),
						ADD INDEX (`time_debut`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.62");
    $sql = "ALTER TABLE `perfusion` 
	          ADD `accord_praticien` ENUM ('0','1');";
    $this->addQuery($sql);
    
	  $this->mod_version = "0.63";
  }  
}

?>