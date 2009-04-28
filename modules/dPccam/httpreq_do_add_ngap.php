<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPccam/base/ngap.tar.gz";
$targetDir = "tmp/ngap";
$targetTables = "tmp/ngap/ngap.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Création de la table
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
$AppUI->stepAjax("Table importée", UI_MSG_OK);

?>
