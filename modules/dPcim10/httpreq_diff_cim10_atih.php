<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;
$can->needsAdmin();

set_time_limit(360);

$do_delete = mbGetValueFromget('do_delete');

$cim = new CCodeCIM10();
$ds = $cim->_spec->ds;

if ($do_delete) {
  $ds->exec("DELETE FROM `master` WHERE `author` = 'atih'");
  $ds->exec("DELETE FROM `libelle` WHERE `author` = 'atih';");
  $AppUI->stepAjax("Code supplémentaires de l'ATIH supprimés", UI_MSG_OK);
  CApp::rip();
}

// Extraction des codes supplémentaires de l'ATIH
$targetDir = "tmp/cim10";
$sourcePath = "modules/dPcim10/base/cim_atih.tar.gz";
$targetPath = "tmp/cim10/cim_atih.csv";
/*if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive CIM_ATIH.csv", UI_MSG_ERROR);
} 
$AppUI->stepAjax("Extraction de $nbFiles fichier(s) [CIM10 ATIH]", UI_MSG_OK);*/

// Vérification des différences entre la norme internationale et les ajouts de l'ATIH
$list_diff = array();
$fp = fopen($targetPath, 'r');

while($line = fgetcsv($fp, null, '|')) {
  // Remove dots, replace + by X
  $line[0] = str_replace(array('.', '+'), array('', 'X'), trim($line[0]));
  
  $cim = new CCodeCIM10($line[0], true);
  if (!$cim->exist) {
    $list_diff[] = $line;
  }
}

fclose($fp);

if (count($list_diff))
  $AppUI->stepAjax("Il existe ".count($list_diff)." codes supplémentaires dans la CIM v.11", UI_MSG_WARNING);
else
  $AppUI->stepAjax("Il n'y a pas de code supplémentaires dans la CIM v.11", UI_MSG_OK);

foreach($list_diff as $diff) {
  $abbrev = $diff[0];
  $full_code = CCodeCIM10::addPoint($abbrev);
  
  // Insertion des nouveaux codes
  $query = "INSERT into master (`code`, `abbrev`, `level`, `type`, `valid`, `author`) VALUES (
    '".$full_code."','".$abbrev."','".strlen($full_code)."','S','1','atih')";
  $ds->exec($query);
  
  // On récupère la clé primaire du code ajouté
  $query = "SELECT * FROM master WHERE 1 ORDER BY SID DESC LIMIT 1";
  $result = $ds->exec($query);
  $row = $ds->fetchArray($result);
  $SID = $row['SID'];
  
  // On ajoute les niveaux supérieurs
  $prev_SID = 0;
  $offset = 0;
  for ($i = 1; $i <= 7; $i++) {
    if ($i <= 2) {
      $query = "SELECT * FROM master WHERE abbrev LIKE('%".substr($abbrev, 0, $i)."%') ORDER BY code ASC LIMIT 1";
    } 
    else {
      $query = "SELECT * FROM master WHERE abbrev = '".substr($abbrev, 0, $i)."' LIMIT 1";
    }
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $level_SID = $row['SID'];
    
    if ($level_SID && $prev_SID != $level_SID) {
      $query = "UPDATE `master` SET `id".($i-$offset)."` = $level_SID WHERE SID = $SID";
      $ds->exec($query);
      $prev_SID = $level_SID;
    }
    else $offset++; 
  }
  
  $label = str_replace("'", "\\'", removeAccent($diff[3], true));
  
  // Insertion des libellés dans toutes les langues
  $query = "INSERT into libelle (
             `SID`, `source`, `valid`, `author`,
             `libelle`, `FR_OMS`, `EN_OMS`, `GE_DIMDI`, `GE_AUTO`, `FR_CHRONOS`) VALUES (
    $SID,'S','1','atih',
    '".$label."','".$label."','".$label."','".$label."','".$label."','".$label."')";
  $ds->exec($query);
  
  // Inutile vu qu'on n'a pas les descriptions dans le CSV
  // On récupère la clé primaire du dernier libellé ajouté
  /*$query = "SELECT * FROM libelle WHERE 1 ORDER BY LID DESC LIMIT 1";
  $result = $ds->exec($query);
  $row = $ds->fetchArray($result);
  $LID = $row['LID'];
  
  // Insertion des liens vers les libellés dans toutes les langues
  $query = "INSERT into `descr` (`SID`, `LID`) VALUES ($SID, $LID)";
  $ds->exec($query);*/
}

$AppUI->stepAjax(count($list_diff)." codes supplémentaires ont été ajoutés dans la CIM10", UI_MSG_OK);

?>