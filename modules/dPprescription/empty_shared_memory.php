<?php /* $Id: httpreq_do_empty_shared_memory.php 8987 2010-05-24 15:58:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8987 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// This script has to be launched via installer
global $can;

// Only check permissions when connected to mediboard, not to the installer
if ($can) {
  $can->needsAdmin();
}

// CConfigService
$class = "CConfigService";
$name = "conf-service";
if (!SHM::get($name)) {
  CAppUI::stepAjax("$class-shm-none", UI_MSG_OK);
}
else {
  if (!SHM::rem($name)) {
    CAppUI::stepAjax("$class-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("$class-shm-rem-ok", UI_MSG_OK);
}

// CConfigMomentUnitaire
$class = "CConfigMomentUnitaire";
$name = "conf-moment";
if (!SHM::get($name)) {
  CAppUI::stepAjax("$class-shm-none", UI_MSG_OK);
}
else {
  if (!SHM::rem($name)) {
    CAppUI::stepAjax("$class-shm-rem-ko", UI_MSG_WARNING);
  }
  
  CAppUI::stepAjax("$class-shm-rem-ok", UI_MSG_OK);
}

