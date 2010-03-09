<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupmediusers extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "mediusers";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `users_mediboard` (" .
          "\n`user_id` INT(11) UNSIGNED NOT NULL," .
          "\n`function_id` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0'," .
          "\nPRIMARY KEY (`user_id`)" .
          "\n) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `functions_mediboard` (" .
          "\n`function_id` TINYINT(4) UNSIGNED NOT NULL AUTO_INCREMENT," .
          "\n`group_id` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0'," .
          "\n`text` VARCHAR(50) NOT NULL," .
          "\n`color` VARCHAR(6) NOT NULL DEFAULT 'ffffff'," .
          "\nPRIMARY KEY (`function_id`)" .
          "\n) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `users_mediboard` ADD `remote` TINYINT DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `users_mediboard` ADD `adeli` int(9) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `users_mediboard` CHANGE `adeli` `adeli` VARCHAR(9);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "CREATE TABLE `discipline` (" .
            "\n`discipline_id` TINYINT(4) UNSIGNED NOT NULL AUTO_INCREMENT," .
            "\n`text` VARCHAR(100) NOT NULL," .
            "\nPRIMARY KEY (`discipline_id`)" .
            "\n) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users_mediboard` ADD `discipline_id` TINYINT(4) DEFAULT NULL AFTER `function_id`";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ADDICTOLOGIE CLINIQUE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('AIDE MEDICALE URGENTE OU MEDECINE D\'URGENCE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ALLERGOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ANATOMIE ET CYTOLOGIE PATHOLOGIQUES');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ANDROLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ANESTHESIE-REANIMATION');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ANGIOLOGIE/ MEDECINE VASCULAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('BIOLOGIE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CANCEROLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CARDIOLOGIE / PATHOLOGIE CARDIO-VASCULAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE DE LA FACE ET DU COU');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE GENERALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE INFANTILE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE MAXILLO-FACIALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE MAXILLO-FACIALE ET STOMATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE ORTHOPEDIQUE ET TRAUMATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE PLASTIQUE, RECONSTRUCTRICE ET ESTHETIQUE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE THORACIQUE ET CARDIO-VASCULAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE UROLOGIQUE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE VASCULAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('CHIRURGIE VISCERALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('DERMATOLOGIE ET VENEREOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ENDOCRINOLOGIE ET METABOLISMES');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('EVALUATION ET TRAITEMENT DE LA DOULEUR');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('GASTRO-ENTEROLOGIE ET HEPATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('GENETIQUE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('GERIATRIE / GERONTOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('GYNECOLOGIE MEDICALE, OBSTETRIQUE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('GYNECOLOGIE-OBSTETRIQUE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('HEMATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('HEMOBIOLOGIE-TRANSFUSION / TECHNOLOGIE TRANSFUSION');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('HYDROLOGIE ET CLIMATOLOGIE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('IMMUNOLOGIE ET IMMUNOPATHOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE AEROSPATIALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE DE CATASTROPHE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE DE LA REPRODUCTION ET GYNECOLOGIE MEDICAL');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE DU TRAVAIL');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE ET BIOLOGIE DU SPORT');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE GENERALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE INTERNE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE LEGALE ET EXPERTISES MEDICALES');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE NUCLEAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE PENITENTIAIRE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('MEDECINE PHYSIQUE ET DE READAPTATION');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('NEPHROLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('NEUROCHIRURGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('NEUROLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('NEUROPSYCHIATRIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('NUTRITION');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ONCOLOGIE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ONCOLOGIE OPTION RADIOTHERAPIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('OPHTALMOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('ORTHOPEDIE DENTO-MAXILLO-FACIALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('OTO-RHINO-LARYNGOLOGIE ET CHIRURGIE CERVICO-FACIALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PATHOLOGIE INFECTIEUSE ET TROPICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PEDIATRIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PHONIATRIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PNEUMOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PSYCHIATRIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('PSYCHIATRIE DE L\'ENFANT ET DE L\'ADOLESCENT');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('RADIO-DIAGNOSTIC ET IMAGERIE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('RADIOTHERAPIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('REANIMATION MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('RECHERCHE MEDICALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('RHUMATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('SANTE PUBLIQUE ET MEDECIN SOCIALE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('STOMATOLOGIE');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `discipline` (`text`) VALUES('TOXICOMANIES ET ALCOOLOGIE');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `functions_mediboard` ADD `type` ENUM('administratif', 'cabinet') DEFAULT 'administratif' NOT NULL AFTER `group_id`;";
    $this->addQuery($sql);
    
    function setup_updateFct(){
        $ds = CSQLDataSource::get("std");
 
    	if($ds->loadTable("groups_mediboard")) {
        $sql = "UPDATE `functions_mediboard`, `groups_mediboard`" .
            "\nSET `functions_mediboard`.`type` = 'cabinet'" .
            "\nWHERE `functions_mediboard`.`group_id` = `groups_mediboard`.`group_id`" .
            "\nAND `groups_mediboard`.`text` = 'Cabinets'";
        $ds->exec($sql); $ds->error();
      }
      return true;
    }
    $this->addFunction("setup_updateFct");
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `functions_mediboard` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users_mediboard` ADD INDEX ( `function_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users_mediboard` ADD INDEX ( `discipline_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `discipline` " .
               "\nCHANGE `discipline_id` `discipline_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `text` `text` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `functions_mediboard` " .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `text` `text` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users_mediboard` " .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `adeli` `adeli` int(9) unsigned zerofill NULL," .
               "\nCHANGE `remote` `remote` enum('0','1') NULL," .
               "\nCHANGE `discipline_id` `discipline_id` int(11) unsigned NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `users_mediboard` " .
               "\nADD `commentaires`  text NULL," .
               "\nADD `actif` enum('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `users_mediboard` " .
               "\nADD `deb_activite` datetime NULL," .
               "\nADD `fin_activite` datetime NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `users_mediboard` " .
               "\nCHANGE `deb_activite` `deb_activite` date NULL," .
               "\nCHANGE `fin_activite` `fin_activite` date NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $sql = "CREATE TABLE `spec_cpam` (" .
            "\n`spec_cpam_id` TINYINT(4) UNSIGNED NOT NULL," .
            "\n`text` VARCHAR(255) NOT NULL," .
            "\n`actes` VARCHAR(255) NOT NULL," .
            "\nPRIMARY KEY (`spec_cpam_id`)" .
            "\n) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users_mediboard` ADD `spec_cpam_id` TINYINT(4) DEFAULT NULL AFTER `discipline_id`";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(1,'MEDECINE GENERALE','C|K|Z|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(2,'ANESTHESIOLOGIE - REANIMATION CHIRURGICALE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(3,'PATHOLOGIE CARDIO-VASCULAIRE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(4,'CHIRURGIE GENERALE','CS|K|KC|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(5,'DERMATOLOGIE ET VENEROLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(6,'RADIODIAGNOSTIC ET IMAGERIE MEDICALE','C|K|Z|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(7,'GYNECOLOGIE OBSTETRIQUE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(8,'GASTRO-ENTEROLOGIE ET HEPATOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(9,'MEDECINE INTERNE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(10,'NEUROCHIRURGIEN','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(11,'OTO RHINO LARYNGOLOGISTE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(12,'PEDIATRE','CS|K|FPE|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(13,'PNEUMOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(14,'RHUMATOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(15,'OPHTAMOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(16,'CHIRURGIE UROLOGIQUE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(17,'NEURO PSYCHIATRIE','CNP');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(18,'STOMATOLOGIE','CS|Z|SCM|PRO|ORT|K|FDA|FDC|FDO|FDR|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(19,'CHIRURGIE DENTAIRE','C|Z|D|DC|SPR|SC|FDA|FDC|FDO|FDR');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(21,'SAGE FEMME','C|SF|SFI');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(24,'INFIRMIER','AMI');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(26,'MASSEUR KINESITHERAPEUTE','AMC');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(27,'PEDICURE','AMP');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(28,'ORTHOPHONISTE','AMO');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(29,'ORTHOPTISTE','AMY');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(30,'LABORATOIRE D\'ANALYSES MEDICALES','B|KB');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(31,'MEDECINE PHYSIQUE ET DE READAPTATION','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(32,'NEUROLOGIE','CNP|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(33,'PSYCHIATRIE GENERALE','CNP');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(35,'NEPHROLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(36,'CHIRURGIE DENTAIRE (Spéc. O.D.F.)','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(37,'ANATOMIE-CYTOLOGIE-PATHOLOGIQUES','CS|P');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(38,'MEDECIN BIOLOGISTE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(39,'LABORATOIRE POLYVALENT','B');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(40,'LABORATOIRE ANATOMO-PATHOLOGISTE','B');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(41,'CHIRURGIE ORTHOPEDIQUE et TRAUMATOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(42,'ENDOCRINOLOGIE et METABOLISMES','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(43,'CHIRURGIE INFANTILE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(44,'CHIRURGIE MAXILLO-FACIALE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(45,'CHIRURGIE MAXILLO-FACIALE ET STOMATOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(46,'CHIRURGIE PLASTIQUE RECONSTRUCTRICE ET ESTHECS','K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(47,'CHIRURGIE THORACIQUE ET CARDIO-VASCULAIRE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(48,'CHIRURGIE VASCULAIRE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(49,'CHIRURGIE VISCERALE ET DIGESTIVE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(50,'PHARMACIEN','');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(70,'GYNECOLOGIE MEDICALE','CS|K|ZM|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(71,'HEMATOLOGIE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(72,'MEDECINE NUCLEAIRE','CS|K|ZN|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(73,'ONCOLOGIE MEDICALE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(74,'ONCOLOGIE RADIOTHERAPIQUE','CS|K|Z|ZN|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(75,'PSYCHIATRIE DE L''ENFANT ET DE L''ADOLESCENT','CNP');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(76,'RADIOTHERAPIE','CS|Z|ZN|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(77,'OBSTETRIQUE','CS|K|Z|ADE|ADI|ADC|ACO|ADA|ATM');";
    $this->addQuery($sql);
    $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(78,'GENETIQUE MEDICALE','CS|K|ADE|ADI|ADC|ACO|ADA|ATM');";    
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE `users_mediboard` CHANGE `spec_cpam_id` `spec_cpam_id` int(11) unsigned NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `spec_cpam` CHANGE `spec_cpam_id` `spec_cpam_id` int(11) unsigned NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `users_mediboard` ADD `titres`  text NULL AFTER adeli;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `functions_mediboard` " .
               "\nADD `adresse` TEXT NULL," .
               "\nADD `cp` int(5) unsigned zerofill NULL," .
               "\nADD `ville` VARCHAR( 50 ) NULL," .
               "\nADD `tel` bigint(10) unsigned zerofill NULL," .
               "\nADD `fax` bigint(10) unsigned zerofill NULL," .
               "\nADD `soustitre` TEXT NULL;";
     $this->addQuery($sql);
     
     $this->makeRevision("0.24");
     $sql = "ALTER TABLE `discipline` ADD `categorie` enum('ORT','ORL','OPH','DER','STO','GAS','ARE','RAD','GYN','EST') NULL";
     $this->addQuery($sql);
     
     $this->makeRevision("0.25");
     $sql = "UPDATE `users_mediboard` SET `discipline_id` = NULL WHERE `discipline_id` = '0';";
     $this->addQuery($sql);
     
     $this->makeRevision("0.26");
     $sql = "ALTER TABLE `users_mediboard` ADD `compte` VARCHAR(23);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.27");
     $sql = "ALTER TABLE `users_mediboard` ADD `banque_id` INT(11) UNSIGNED;";
     $this->addQuery($sql);
     
     $this->makeRevision("0.28");
     $sql = "ALTER TABLE `functions_mediboard` ADD `compta_partagee` BOOL NOT NULL DEFAULT '0';";
     $this->addQuery($sql);
     
     $this->makeRevision("0.29");
     $sql = "CREATE TABLE `secondary_function` (
               `secondary_function_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `function_id` INT (11) UNSIGNED NOT NULL,
               `user_id` INT (11) UNSIGNED NOT NULL
             ) TYPE=MYISAM;";
     $this->addQuery($sql);
     $sql = "ALTER TABLE `secondary_function` 
             ADD INDEX (`function_id`),
             ADD INDEX (`user_id`);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.30");
     
     $sql = "ALTER TABLE `users_mediboard` 
						   ADD `rpps` BIGINT (11) UNSIGNED ZEROFILL AFTER `adeli`;";
     $this->addQuery($sql);
     
     $sql = "ALTER TABLE `users_mediboard` 
							 ADD INDEX (`deb_activite`),
							 ADD INDEX (`fin_activite`),
							 ADD INDEX (`banque_id`),
							 ADD INDEX (`spec_cpam_id`);";
     $this->addQuery($sql);
     
     $this->makeRevision("0.31");
      
     $sql = "INSERT INTO `spec_cpam` (`spec_cpam_id`, `text`, `actes`) VALUES(80,'SANTE PUBLIQUE ET MEDECINE SOCIALE','');";
     $this->addQuery($sql);
     
     $this->makeRevision("0.32");
     
     $sql = "ALTER TABLE `users_mediboard` ADD `code_intervenant_cdarr` CHAR (2) DEFAULT NULL;";
     $this->addQuery($sql);
     
     $this->mod_version = "0.33";
  }
}
?>