<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

CApp::setTimeLimit(360);

$sourcePath = "modules/dPccam/base/ccam.tar.gz";
$targetDir = "tmp/ccam";

$targetTables    = "tmp/ccam/tables.sql";
$targetBaseDatas = "tmp/ccam/basedata.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
}

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Création des tables
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Création de $lineCount tables", UI_MSG_OK);

// Ajout des données de base
if (null == $lineCount = $ds->queryDump($targetBaseDatas, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des données de base - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Import des données de base effectué avec succès ($lineCount lignes)", UI_MSG_OK);

// Ajout des fichiers NX dans les tables
$listTables = array(
  "actes"               => "TB101.txt",
  "activite"            => "TB060.txt",
  "activiteacte"        => "TB201.txt",
  "arborescence"        => "TB091.txt",
  "associabilite"       => "TB220.txt",
  "association"         => "TB002.txt",
  "infotarif"           => "TB110.txt",
  "incompatibilite"     => "TB130.txt",
  "modificateur"        => "TB083.txt",
  "modificateuracte"    => "TBCCAM06.txt",
  "modificateurcompat"  => "TB009.txt",
  "modificateurforfait" => "TB011.txt",
  "modificateurnombre"  => "TB019.txt",
  "notes"               => "TBCCAM11.txt",
  "notesarborescence"   => "TB092.txt",
  "phase"               => "TB066.txt",
  "phaseacte"           => "TB310.txt",
  "procedures"          => "TB120.txt",
  "typenote"            => "TB050.txt"
);

function insertValues($ds, $table, $values, &$echoue, &$reussi) {
  $values_sql = array();
  foreach ($values as $_line) {
    $values_sql[] = "('".implode("','", $_line)."')";
  }

  $query = "INSERT INTO $table VALUES ".implode(",", $values_sql);

  $ds->exec($query);

  $count = count($values);
  if ($msg = $ds->error()) {
    $echoue += $count;
  }
  else {
    $reussi += $count;
  }
}

function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ds = CSQLDataSource::get("ccamV2");
  $handle = fopen($file, "r");

  $values = array();
  $batch = 50;

  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caractères en majusucules accentués (et d'autres caractères spéciaux)
  while ($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $values[] = explode("|", $line);

    if (count($values) == $batch) {
      insertValues($ds, $table, $values, $echoue, $reussi);
      $values = array();
    }
  }

  if (count($values)) {
    insertValues($ds, $table, $values, $echoue, $reussi);
  }

  CAppUI::stepAjax("Import du fichier $file dans la table $table : $reussi lignes ajoutée(s), $echoue échouée(s)", UI_MSG_OK);
  fclose($handle);
}

foreach ($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}

