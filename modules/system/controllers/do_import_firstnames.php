<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

ini_set("auto_detect_line_endings", true);
$targetPath = "modules/system/ressources/firstnames.csv";

$start    = CValue::post("start");
$count    = CValue::post("step");
$callback = CValue::post("callback");

CApp::setTimeLimit(600);
CApp::setMemoryLimit("512M");

CMbObject::$useObjectCache = false;

importFile($targetPath, $start, $count);

$start += $count;
CAppUI::setConf("system import_firstname start", $start);
CAppUI::setConf("system import_firstname step", $count);

if ($callback) {
  CAppUI::js("$callback($start,$count)");
}

CMbObject::$useObjectCache = true;
CApp::rip();

/**
 * import the csv firstname file
 *
 * @param string $targetPath filepath
 * @param int    $start      start from
 * @param int    $count      step of import
 *
 * return null
 */
function importFile($targetPath, $start, $count) {
  $fp = fopen($targetPath, 'r');

  //0 = first line
  if ($start == 0) {
    $start++;
  }

  $line_nb=0;
  while ($line = fgetcsv($fp, null, ";")) {
    if ($line_nb >= $start && $line_nb<($start+$count)) {
      $fn = trim($line[0]);
      $sex = trim($line[1]);

      $firstname = new CFirstNameAssociativeSex();
      $firstname->firstname = $fn;
      $firstname->sex = $sex == ("m,f" || 'f,m') ? "u" : $sex;
      $firstname->loadMatchingObjectEsc();
      if ($msg = $firstname->store()) {
        CAppUI::stepAjax($msg, UI_MSG_ERROR);
      }
      else {
        CAppUI::stepAjax("prénom <strong>$fn</strong>, mis à jour");
      }
    }

    $line_nb++;
  }

  return;
}