<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $mbpath, $dPconfig, $can;

$can->needsAdmin();

$mbpath = "";
CMbArray::extract($_POST, "m");
CMbArray::extract($_POST, "dosql");
CMbArray::extract($_POST, "suppressHeaders");
$ajax = CMbArray::extract($_POST, "ajax");

$mbConfig = new CMbConfig;

$result = $mbConfig->update($_POST);
if (PEAR::isError($result)) {
  CAppUI::setMsg("Configure-failed-modify", UI_MSG_ERROR, $result->getMessage());
}
else {
  CAppUI::setMsg("Configure-success-modify");
}

$mbConfig->load();
$dPconfig = $mbConfig->values;

// Cas Ajax
if ($ajax) {
  echo CAppUI::getMsg();
  CApp::rip();
}

?>