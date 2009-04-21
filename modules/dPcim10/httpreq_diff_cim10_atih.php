<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;
$can->needsAdmin();

set_time_limit(360);

// Extraction des codes supplémentaires de l'ATIH
$targetDir = "tmp/cim10";
$sourcePath = "modules/dPcim10/base/cim_atih.tar.gz";
$targetPath = "tmp/cim10/cim_atih.csv";
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive CIM_ATIH.csv", UI_MSG_ERROR);
} 
$AppUI->stepAjax("Extraction de $nbFiles fichier(s) [CIM10 ATIH]", UI_MSG_OK);

// Vérification des différences entre la norme internationale et les ajouts de l'ATIH
$list_diff = array();
$fp = fopen($targetPath, 'r');

while($line = fgetcsv($fp, null, '|')) {
  $code = trim($line[0]);
  $cim = new CCodeCIM10($code, true);
  if (!$cim->exist) {
    $list_diff[] = $line;
  }
}

fclose($fp);

if (count($list_diff))
  $AppUI->stepAjax("Il existe ".count($list_diff)." codes supplémentaires dans la CIM v.11", UI_MSG_WARNING);
else
  $AppUI->stepAjax("Il n'y a pas de code supplémentaires dans la CIM v.11", UI_MSG_OK);

?>