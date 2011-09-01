<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if (CFile::openoffice_launched()) {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_launched"), UI_MSG_OK);
}
else {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_stopped"), UI_MSG_WARNING);
}

?>