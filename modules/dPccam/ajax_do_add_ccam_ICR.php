<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

CCanDo::checkAdmin();

CApp::setTimeLimit(360);

$sourcePath = "modules/dPccam/base/ccam_ICR.tar.gz";
$targetDir = "tmp/ccam_ICR";
$targetTables = "tmp/ccam_ICR/ccam_ICR.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Création de la table
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Table importée", UI_MSG_OK);
?>