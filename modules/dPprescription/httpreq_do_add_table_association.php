<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $can;

$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPprescription/sql/association_moment.tar.gz";
$targetDir = "tmp/prescription/";
$targetTables = "tmp/prescription/association_moment.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
}

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("std");

// Création de la table
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Table importée", UI_MSG_OK);

?>