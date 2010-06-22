<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

set_time_limit(360);

$sourcePath = "modules/ssr/base/nomenclatureCdARR.tar.gz";
$targetDir = "tmp/cdarr";

$targetTables    = "tmp/cdarr/tables.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("cdarr");

// Création des tables
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Création de $lineCount tables", UI_MSG_OK);



// Ajout des fichiers NX dans les tables
$listTables = array(
  "intervenant"   => "LIBINTERCD.TXT",
  "type_activite" => "TYPACTSSR.TXT",
  "activite"      => "CATSSR.TXT"
);

function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ds = CSQLDataSource::get("cdarr");
  $handle = fopen($file, "r");
  
  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caractères en majusucules accentués (et d'autres caractères spéciaux)
  while($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $datas = explode("|", $line);
    $query = "INSERT INTO $table VALUES('".implode("','", $datas)."')";
    
    $ds->exec($query);
    if($msg = $ds->error()) {
      $echoue++;
    } else {
      $reussi++;
    }
  }
  CAppUI::stepAjax("Import du fichier $file dans la table $table : $reussi lignes ajoutée(s), $echoue échouées(s)", UI_MSG_OK);
  fclose($handle);
}

foreach($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}

?>
