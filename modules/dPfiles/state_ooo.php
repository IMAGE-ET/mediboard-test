<?php

/**
 * OpenOffice state
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

if (CFile::openofficeLaunched()) {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_launched"), UI_MSG_OK);
}
else {
  CAppUI::stepAjax(CAppUI::tr("config-dPfiles-CFile.ooo_stopped"), UI_MSG_WARNING);
}
