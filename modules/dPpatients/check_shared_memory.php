<?php /* $Id: httpreq_check_shared_memory.php 9837 2010-08-18 13:42:01Z lryo $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 9837 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$class = "CConfigConstantesMedicales";
$name = "conf-constantes_medicales";
$configs = CConfigConstantesMedicales::getSHM($name);

if(!$configs) {
  CAppUI::stepAjax("$class-shm-none", UI_MSG_OK);
}
elseif ($configs != CConfigConstantesMedicales::getAllConfigs()) {
  CAppUI::stepAjax("$class-shm-ko", UI_MSG_WARNING);
}
else {
  CAppUI::stepAjax("$class-shm-ok", UI_MSG_OK);
}
