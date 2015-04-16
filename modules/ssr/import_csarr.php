<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$sourcePath = "modules/ssr/base/nomenclature.CsARR_v3.zip";
$targetDir = "tmp/csarr";

$targetTables    = "tmp/csarr/tables.sql";

// Ajout des fichiers NX dans les tables
$listTables = array(
  "activite"             => "code_csarr_v3.txt",
  "note_activite"        => "note_code_csarr_v3.txt",
  "geste_complementaire" => "geste_compl_csarr_v3.txt",
  "modulateur"           => "modulateur_csarr_v3.txt",
  "hierarchie"           => "hier_csarr_v3.txt",
  "note_hierarchie"      => "note_hier_csarr_v3.txt",
  "activite_reference"   => "acte_ref_csarr_v3.txt"
);

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("extraction-error", UI_MSG_ERROR, $sourcePath);
} 

CAppUI::stepAjax("extraction-success", UI_MSG_OK, $sourcePath, $nbFiles);

$ds = CSQLDataSource::get("csarr");

// Création des tables
if (null == $count = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("ssr-import-tables-error", UI_MSG_ERROR, $msg);
}
CAppUI::stepAjax("ssr-import-tables-success", UI_MSG_OK, $count);

/**
 * Parse le fichier et remplit la table correspondante
 *
 * @param string $file  File path
 * @param string $table Table name
 *
 * @return void
 */
function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ignore = 0;
  $ds = CSQLDataSource::get("csarr");
  $handle = fopen($file, "r");
  
  // First line is hearders
  fgets($handle);
  
  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caractères en majusucules accentués (et d'autres caractères spéciaux)
  while ($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $data = explode("|", $line);
    $data = array_map("trim", $data);

    static $note_ignores = array(
      "À l\\'exclusion de :",
      "Cet acte comprend :",
      "Avec ou sans :",
      "Codage :",
    );
    
    // CNoteActivite: Traitements spécifiques 
    if ($table == "note_activite") {
      // Nettoyage des termes à ignorer
      foreach ($note_ignores as $_ignore) {
        if (strpos($data[4], $_ignore) === 0) {
          $data[4] = trim(substr($data[4], strlen($_ignore)));
          if (empty($data[4])) {
            $ignore++;
            continue 2;
          }
        }
      }
            
      // Détection du code à exclure
      $data[6] = "";
      if (preg_match("/\(([a-z]{3}\+\d{3})\)/i", $data[4], $matches)) {
        $data[6] = $matches[1];
      }
    }

    // CNoteHierarchie: Traitements spécifiques 
    if ($table == "note_hierarchie") {
      // Nettoyage des termes à ignorer
      foreach ($note_ignores as $_ignore) {
        if (strpos($data[4], $_ignore) === 0) {
          $data[4] = trim(substr($data[4], strlen($_ignore)));
          if (empty($data[4])) {
            $ignore++;
            continue 2;
          }
        }
      }
      
      // Détection de la hierarchie à exclure
      $data[6] = "";
      if (preg_match("/\(((\d{2}\.)+\d{2})\)/i", $data[4], $matches)) {
        $data[6] = $matches[1];
      }

      // Détection du code à exclure
      $data[7] = "";
      if (preg_match("/\(([a-z]{3}\+\d{3})\)/i", $data[4], $matches)) {
        $data[7] = $matches[1];
      }
    }

    $query = "INSERT INTO $table VALUES('".implode("','", $data)."')";

    $ds->exec($query);
    if ($ds->error()) {
      $echoue++;
    }
    else {
      $reussi++;
    }
  }

  fclose($handle);
  CAppUI::stepAjax("ssr-import-csarr-report", UI_MSG_OK, $file, $table, $ignore, $reussi, $echoue);
}

foreach ($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}
