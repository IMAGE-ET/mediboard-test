<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPccam/base/forfaits_ccam.tar.gz";
$targetDir = "tmp/forfaits_ccam";
$targetTables = "tmp/forfaits_ccam/forfaits_ccam.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Cr�ation de la table
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requ�te SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Table import�e", UI_MSG_OK);

?>
