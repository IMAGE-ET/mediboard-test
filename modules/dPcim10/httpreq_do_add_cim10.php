<?php /* $Id: httpreq_do_add_cim10.php,v 1.3 2006/04/25 22:42:02 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision: 1.3 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once("Archive/Tar.php");

set_time_limit(360);
ini_set("memory_limit", "64M");

$filepath = "modules/dPcim10/base/cim10.tar.gz";
$filedir = "tmp/cim10";

$tarball = new Archive_Tar($filepath);
if ($tarball->extract($filedir)) {
  $nbFiles = @count($tarball->listContent());
  echo "<div class='message'>Extraction de $nbFiles fichier(s)</div>";
} else {
  echo "<div class='error'>Erreur, impossible d'extraire l'archive</div>";
  exit(0);
}

$base = $AppUI->cfg["baseCIM10"];

do_connect($base);

$path = "tmp/cim10/cim10.sql";
$sqlLines = file($path);
$query = "";
foreach($sqlLines as $lineNumber => $sqlLine) {
  $sqlLine = trim($sqlLine);
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