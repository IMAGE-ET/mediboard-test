<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

exec("sh shell/ooo_state.sh", $res);

if ($res[0] == 1) {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_launched"), UI_MSG_OK);
}
else {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_stopped"), UI_MSG_WARNING);
}

?>