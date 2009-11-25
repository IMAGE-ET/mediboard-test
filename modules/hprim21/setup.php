<?php

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author Romain Ollivier
*/

class CSetuphprim21 extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hprim21";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `hprim21_patient` (
              `hprim21_patient_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`hprim21_patient_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_patient`
              ADD `patient_id` INT(11) UNSIGNED, 
              ADD `nom` VARCHAR(255) NOT NULL, 
              ADD `prenom` VARCHAR(255), 
              ADD `prenom2` VARCHAR(255), 
              ADD `alias` VARCHAR(255), 
              ADD `civilite` ENUM('M','Mme','Mlle'), 
              ADD `diplome` VARCHAR(255), 
              ADD `nom_jeune_fille` VARCHAR(255), 
              ADD `naissance` DATE, 
              ADD `sexe` ENUM('M','F','U'), 
              ADD `adresse1` VARCHAR(255), 
              ADD `adresse2` VARCHAR(255), 
              ADD `ville` VARCHAR(255), 
              ADD `departement` VARCHAR(255), 
              ADD `cp` VARCHAR(255), 
              ADD `pays` VARCHAR(255), 
              ADD `telephone1` VARCHAR(255), 
              ADD `telephone2` VARCHAR(255), 
              ADD `traitement_local1` VARCHAR(255), 
              ADD `traitement_local2` VARCHAR(255), 
              ADD `taille` INT(11), 
              ADD `poids` INT(11), 
              ADD `diagnostic` VARCHAR(255), 
              ADD `traitement` VARCHAR(255), 
              ADD `regime` VARCHAR(255), 
              ADD `commentaire1` VARCHAR(255), 
              ADD `commentaire2` VARCHAR(255), 
              ADD `classification_diagnostic` VARCHAR(255), 
              ADD `situation_maritale` ENUM('M','S','D','W','A','U'), 
              ADD `precautions` VARCHAR(255), 
              ADD `langue` VARCHAR(255), 
              ADD `statut_confidentialite` VARCHAR(255), 
              ADD `date_derniere_modif` DATETIME, 
              ADD `date_deces` DATE, 
              ADD `nature_assurance` VARCHAR(255), 
              ADD `debut_validite` DATE, 
              ADD `fin_validite` DATE, 
              ADD `matricule` VARCHAR(15), 
              ADD `rang_beneficiaire` ENUM('01','02','09','11','12','13','14','15','16','31'), 
              ADD `rang_naissance` ENUM('1','2','3','4','5','6'), 
              ADD `code_regime` TINYINT(3) UNSIGNED ZEROFILL, 
              ADD `caisse_gest` MEDIUMINT(3) UNSIGNED ZEROFILL, 
              ADD `centre_gest` MEDIUMINT(4) UNSIGNED ZEROFILL, 
              ADD `origine_droits` VARCHAR(255), 
              ADD `nature_exoneration` VARCHAR(255), 
              ADD `nom_assure` VARCHAR(255), 
              ADD `prenom_assure` VARCHAR(255), 
              ADD `nom_jeune_fille_assure` VARCHAR(255), 
              ADD `taux_PEC` FLOAT, 
              ADD `numero_AT` INT(11), 
              ADD `AT_par_tiers` ENUM('0','1'), 
              ADD `fin_droits` DATE, 
              ADD `date_accident` DATE, 
              ADD `nom_employeur` VARCHAR(255), 
              ADD `adresse1_employeur` VARCHAR(255), 
              ADD `adresse2_employeur` VARCHAR(255), 
              ADD `ville_employeur` VARCHAR(255), 
              ADD `departement_employeur` VARCHAR(255), 
              ADD `cp_employeur` VARCHAR(255), 
              ADD `pays_employeur` VARCHAR(255), 
              ADD `date_debut_grossesse` DATE, 
              ADD `emetteur_id` VARCHAR(255) NOT NULL, 
              ADD `external_id` VARCHAR(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `hprim21_complementaire` (
              `hprim21_complementaire_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`hprim21_complementaire_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_complementaire`
              ADD `hprim21_patient_id` INT(11) UNSIGNED, 
              ADD `code_organisme` VARCHAR(255), 
              ADD `numero_adherent` VARCHAR(255), 
              ADD `debut_droits` DATE, 
              ADD `fin_droits` DATE, 
              ADD `type_contrat` VARCHAR(255), 
              ADD `emetteur_id` VARCHAR(255) NOT NULL, 
              ADD `external_id` VARCHAR(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `hprim21_sejour` (
              `hprim21_sejour_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`hprim21_sejour_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_sejour`
              ADD `hprim21_patient_id` INT(11) UNSIGNED NOT NULL, 
              ADD `hprim21_medecin_id` INT(11) UNSIGNED, 
              ADD `sejour_id` INT(11) UNSIGNED, 
              ADD `date_mouvement` DATETIME, 
              ADD `statut_admission` ENUM('OP','IP','IO','ER','MP','PA'), 
              ADD `localisation_lit` VARCHAR(255), 
              ADD `localisation_chambre` VARCHAR(255), 
              ADD `localisation_service` VARCHAR(255), 
              ADD `localisation4` VARCHAR(255), 
              ADD `localisation5` VARCHAR(255), 
              ADD `localisation6` VARCHAR(255), 
              ADD `localisation7` VARCHAR(255), 
              ADD `localisation8` VARCHAR(255), 
              ADD `emetteur_id` VARCHAR(255) NOT NULL, 
              ADD `external_id` VARCHAR(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `hprim21_medecin` (
              `hprim21_medecin_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              PRIMARY KEY (`hprim21_medecin_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_medecin`
              ADD `user_id` INT(11) UNSIGNED, 
              ADD `nom` VARCHAR(255), 
              ADD `prenom` VARCHAR(255), 
              ADD `prenom2` VARCHAR(255), 
              ADD `alias` VARCHAR(255), 
              ADD `civilite` VARCHAR(255), 
              ADD `diplome` VARCHAR(255), 
              ADD `type_code` VARCHAR(255), 
              ADD `emetteur_id` VARCHAR(255) NOT NULL, 
              ADD `external_id` VARCHAR(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `hprim21_patient`
            ADD `nom_soundex2` VARCHAR(255) AFTER `nom_jeune_fille`, 
            ADD `prenom_soundex2` VARCHAR(255) AFTER `nom_soundex2`, 
            ADD `nomjf_soundex2` VARCHAR(255) AFTER `prenom_soundex2`, 
            CHANGE `naissance` `naissance` CHAR(10), 
            CHANGE `code_regime` `code_regime` MEDIUMINT(3) UNSIGNED ZEROFILL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_patient`
            ADD INDEX ( `patient_id` ),
            ADD INDEX ( `nom` ),
            ADD INDEX ( `prenom` ),
            ADD INDEX ( `prenom2` ),
            ADD INDEX ( `nom_jeune_fille` ),
            ADD INDEX ( `nom_soundex2` ),
            ADD INDEX ( `prenom_soundex2` ),
            ADD INDEX ( `nomjf_soundex2` ),
            ADD INDEX ( `naissance` ),
            ADD INDEX ( `sexe` ),
            ADD INDEX ( `emetteur_id` ),
            ADD INDEX ( `external_id` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_sejour`
            ADD INDEX ( `hprim21_patient_id` ),
            ADD INDEX ( `hprim21_medecin_id` ),
            ADD INDEX ( `sejour_id` ),
            ADD INDEX ( `date_mouvement` ),
            ADD INDEX ( `emetteur_id` ),
            ADD INDEX ( `external_id` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_medecin`
            ADD INDEX ( `user_id` ),
            ADD INDEX ( `nom` ),
            ADD INDEX ( `prenom` ),
            ADD INDEX ( `emetteur_id` ),
            ADD INDEX ( `external_id` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_complementaire`
            ADD INDEX ( `hprim21_patient_id` ),
            ADD INDEX ( `emetteur_id` ),
            ADD INDEX ( `external_id` );";
    $this->addQuery($sql);
    

    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `hprim21_patient` 
              ADD INDEX (`date_derniere_modif`),
              ADD INDEX (`date_deces`),
              ADD INDEX (`debut_validite`),
              ADD INDEX (`fin_validite`),
              ADD INDEX (`fin_droits`),
              ADD INDEX (`date_accident`),
              ADD INDEX (`date_debut_grossesse`);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `hprim21_complementaire` 
              ADD INDEX (`debut_droits`),
              ADD INDEX (`fin_droits`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.12";
  }
}
