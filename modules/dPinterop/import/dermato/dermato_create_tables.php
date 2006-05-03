<?php /* $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$sql = "CREATE TABLE `dermato_import_patients` (
  `patient_id` bigint(20) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `nom_jeune_fille` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `naissance` date NOT NULL default '0000-00-00',
  `sexe` enum('m','f','j') NOT NULL default 'm',
  `adresse` varchar(100) NOT NULL default '',
  `ville` varchar(50) NOT NULL default '',
  `cp` varchar(5) NOT NULL default '',
  `tel` varchar(10) NOT NULL default '',
  `tel2` varchar(10) default NULL,
  `medecin_traitant` bigint(20) NOT NULL default '0',
  `medecin1` bigint(20) default NULL,
  `medecin2` bigint(20) default NULL,
  `medecin3` bigint(20) default NULL,
  `incapable_majeur` enum('n','o') NOT NULL default 'n',
  `ATNC` enum('n','o') NOT NULL default 'n',
  `matricule` varchar(50) NOT NULL default '',
  `rques` text,
  PRIMARY KEY  (`patient_id`)
) COMMENT='import des patients DERMATO';";
db_exec( $sql ); db_error();
echo "Table des patients créée<br />";

$sql = "CREATE TABLE `dermato_import_praticiens` (
  `praticien_id` bigint(20) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`praticien_id`)
) COMMENT='import des praticiens DERMATO';";
db_exec( $sql ); db_error();
echo "Table des praticiens créée<br />";

$sql = "CREATE TABLE `dermato_import_medecins` (
  `medecin_id` bigint(20) NOT NULL default '0',
  `nom` varchar(50) default NULL,
  `prenom` varchar(50) default NULL,
  `specialite` varchar(50) default NULL,
  `tel1` varchar(10) default NULL,
  `tel2` varchar(10) default NULL,
  `email` varchar(50) default NULL,
  `adresse` varchar(100) default NULL,
  `ville` varchar(50) default NULL,
  `cp` varchar(5) default NULL,
  PRIMARY KEY  (`medecin_id`)
) COMMENT='import des medecins traitants des DERMATO';";
db_exec( $sql ); db_error();
echo "Table des medecins créée<br />";

$sql = "CREATE TABLE `dermato_import_consultations1` (
  `consultation1_id` bigint(20) NOT NULL default '0',
  `chir_id` bigint(20) NOT NULL default '0',
  `libelle` varchar(50) default NULL,
  `date` date NOT NULL default '0000-00-00',
  `freq` time NOT NULL default '00:15:00',
  `debut` time NOT NULL default '00:00:00',
  `fin` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`consultation1_id`)
) COMMENT='import des consultations 1 DERMATO';";
db_exec( $sql ); db_error();
echo "Table des consultations1 créée<br />";

$sql = "CREATE TABLE `dermato_import_consultations2` (
  `consultation_id` bigint(20) NOT NULL default '0',
  `patient_id` bigint(20) NOT NULL default '0',
  `plageconsult_id` bigint(20) NOT NULL default '0',
  `heure` time NOT NULL default '00:00:00',
  `duree` tinyint(4) NOT NULL default '1',
  `motif` text,
  `secteur1` float NOT NULL default '0',
  `secteur2` float NOT NULL default '0',
  `rques` text,
  `compte_rendu` text,
  `chrono` tinyint(4) NOT NULL default '16',
  `annule` tinyint(4) NOT NULL default '0',
  `paye` tinyint(4) NOT NULL default '0',
  `cr_valide` tinyint(4) NOT NULL default '0',
  `examen` text,
  `traitement` text,
  `premiere` tinyint(4) NOT NULL default '0',
  `tarif` varchar(50) default NULL,
  `type_tarif` enum('cheque','CB','especes','tiers','autre') default NULL,
  PRIMARY KEY  (`consultation_id`)
) COMMENT='import des consultations 2 DERMATO';";
db_exec( $sql ); db_error();
echo "Table des consultations2 créée<br />";

$sql = "CREATE TABLE `dermato_import_rdv` (
  `rdv_id` bigint(20) NOT NULL default '0',
  `patient_id` bigint(20) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `debut` time NOT NULL default '00:00:00',
  `fin` time NOT NULL default '00:00:00',
  `praticien_id` bigint(20) NOT NULL default '0',
  `libelle` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`rdv_id`)
) COMMENT='table des rdv a venir DERMATO';";
db_exec( $sql ); db_error();
echo "Table des RDV créée<br />";

$sql = "CREATE TABLE `dermato_import_courriers` (
  `pat_id` bigint(20) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `chemin` text NOT NULL,
  PRIMARY KEY  (`nom`)
) COMMENT='import des courriers DERMATO';";
db_exec( $sql ); db_error();
echo "Table des courriers créée<br />";

$sql = "CREATE TABLE `dermato_import_fichiers` (
  `pat_id` bigint(20) NOT NULL default '0',
  `nom` varchar(50) NOT NULL default '',
  `chemin` text NOT NULL,
  PRIMARY KEY  (`nom`)
) COMMENT='import des fichiers DERMATO';";
db_exec( $sql ); db_error();
echo "Table des fichiers créée<br />";