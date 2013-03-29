<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $mbpath, $dPconfig;

CCAnDo::checkAdmin();

$mbpath = "";
CMbArray::extract($_POST, "m");
CMbArray::extract($_POST, "dosql");
CMbArray::extract($_POST, "suppressHeaders");
$ajax = CMbArray::extract($_POST, "ajax");

$config_db = CAppUI::conf("config_db");

if ($config_db) {
  $configs = array_map_recursive('stripslashes', $_POST);

  // DB Version
  $inserts = array();
  $ds = CSQLDataSource::get("std");

  $list = array();
  CMbConfig::buildConf($list, $configs, null);

  foreach ($list as $key => $value) {
    $query = $ds->prepare("INSERT INTO `config_db`
      VALUES (%1, %2)
      ON DUPLICATE KEY UPDATE value = %3", $key, $value, $value);

    if ($ds->exec($query) === false) {
      CAppUI::setMsg("Configure-failed-modify", UI_MSG_ERROR);
    }
    else {
      CAppUI::setMsg("Configure-success-modify");
    }
  }
  CMbConfig::loadValuesFromDB();
}
else {
  $mbConfig = new CMbConfig();

  $result = $mbConfig->update($_POST);
  if (PEAR::isError($result)) {
    CAppUI::setMsg("Configure-failed-modify", UI_MSG_ERROR, $result->getMessage());
  }
  else {
    CAppUI::setMsg("Configure-success-modify");
  }

  $mbConfig->load();
  $dPconfig = $mbConfig->values;
}

// Cas Ajax
if ($ajax) {
  echo CAppUI::getMsg();
  CApp::rip();
}
