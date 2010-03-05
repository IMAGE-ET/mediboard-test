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
$mbConfig->update($_POST);
$mbConfig->load();
CAppUI::setMsg("Configuration modifiée");

$dPconfig = $mbConfig->values;

// Cas Ajax
if ($ajax) {
  echo CAppUI::getMsg();
  CApp::rip();
}

?>