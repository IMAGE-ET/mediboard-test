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

$sql = "ALTER TABLE `dermato_import_patients` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_patients` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des patients ajouté<br />";

$sql = "ALTER TABLE `dermato_import_praticiens` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_praticiens` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des praticiens ajouté<br />";

$sql = "ALTER TABLE `dermato_import_medecins` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_medecins` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des medecins ajouté<br />";

$sql = "ALTER TABLE `dermato_import_consultations1` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_consultations1` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des consultations1 ajouté<br />";

$sql = "ALTER TABLE `dermato_import_consultations2` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_consultations2` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des consultations2 ajouté<br />";

$sql = "ALTER TABLE `dermato_import_rdv` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_rdv` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des RDV ajouté<br />";

$sql = "ALTER TABLE `dermato_import_courriers` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_courriers` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des courriers ajouté<br />";

$sql = "ALTER TABLE `dermato_import_fichiers` ADD `mb_id` BIGINT;";
db_exec( $sql ); db_error();
$sql = "ALTER TABLE `dermato_import_fichiers` ADD INDEX ( `mb_id` );";
db_exec( $sql ); db_error();
echo "mb_id de la table des fichiers ajouté<br />";