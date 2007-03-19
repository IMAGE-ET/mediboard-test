<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

set_time_limit(360);
ini_set("memory_limit", "128M");

$filepath = "modules/dPccam/base/ccamV2.tar.gz";
$filedir = "tmp/ccam";

if ($nbFiles = CMbPath::extract($filepath, $filedir)) {
  echo "<div class='message'>Extraction de $nbFiles fichier(s)</div>";
} else {
  echo "<div class='error'>Erreur, impossible d'extraire l'archive</div>";
  exit(0);
}

$base = $AppUI->cfg["baseCCAM"];

do_connect($base);

$path = "tmp/ccam/ccamV2.sql";
$sqlLines = file($path);
$query = "";
foreach($sqlLines as $lineNumber => $sqlLine) {
  $sqlLine = trim($sqlLine);
  $sqlLine = utf8_decode($sqlLine);
  if (($sqlLine != "") && (substr($sqlLine, 0, 2) != "--") && (substr($sqlLine, 0, 1) != "#")) {
    $query .= $sqlLine;
    if (preg_match("/;\s*$/", $sqlLine)) {
      db_exec($query, $base);
      if($msg = db_error($base)) {
        echo "<div class='error'>Une erreur s'est produite : $msg</div>";
        exit(0);
      }
      $query = "";
    }
  }
}

echo "<div class='message'>import effectué avec succès</div>";

?>
