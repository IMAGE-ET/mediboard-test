<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

CMbObject::$useObjectCache = false;

/**
 * Get the value matching ^="(.*)"$
 *
 * @param string $value The value to get the result from
 *
 * @return string
 */
function getValue($value) {
  if (preg_match('/^="(.*)"$/', $value, $matches)) {
    $value = $matches[1];
  }
  
  return trim($value, " \t\n\r\0\"'");
}

/**
 * Removes all all numeric chars from a string
 *
 * @param string $value The value to get the number of
 *
 * @return string
 */
function getNum($value) {
  return preg_replace("/[^0-9]/", "", $value);
}
 
CApp::setTimeLimit(3600);
CSessionHandler::writeClose();

if (empty($_FILES["import"]["tmp_name"])) {
  return;
}

$dir = "tmp/import_etab_externe";
CMbPath::forceDir($dir);

$archive = "$dir/archive.zip";

move_uploaded_file($_FILES["import"]["tmp_name"], $archive);

// Extract the data files
if (null == $nbFiles = CMbPath::extract($archive, $dir)) {
  CAppUI::setMsg("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
  return;
}
else {
  CAppUI::setMsg("$nbFiles fichiers extraits", UI_MSG_OK);
}

$files = glob("$dir/*.csv");

foreach ($files as $_file) {
  $fp = fopen($_file, "r");
  
  $csv = new CCSVFile($fp);
  $csv->readLine(); // first line
  
  while ($line = $csv->readLine()) {
    if (!isset($line[1])) {
      continue;
    }
    
    $line = array_map("getValue", $line);
    
    $etab = new CEtabExterne();
    $etab->finess         = getNum($line[0]);
    if ($etab->loadMatchingObject()) {
      continue;
    }
    
    $etab->siret          = getNum($line[1]);
    $etab->ape            = $line[2];
    $etab->nom            = $line[3];
    $etab->raison_sociale = $line[3];
    $etab->adresse        = "$line[4]\n$line[5]\n$line[6]";
    $etab->cp             = $line[7];
    $etab->ville          = $line[8];
    $etab->tel            = getNum($line[9]);
    $etab->fax            = getNum($line[10]);
    $etab->repair();
    
    $type = $etab->_id ? "modify" : "create";
    
    if ($msg = $etab->store()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
    else {
      CAppUI::setMsg("CEtabExterne-msg-$type", UI_MSG_OK);
    }
  }
  
  fclose($fp);
  unlink($_file);
}

CAppUI::callbackAjax('window.parent.$("systemMsg").show().insert', CAppUI::getMsg());
CApp::rip();
