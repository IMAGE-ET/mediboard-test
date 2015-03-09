<?php 

/**
 * $Id$
 *  
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

if (CFile::shrinkPDF(CAppUI::conf("root_dir") . "/modules/printing/samples/test_page.pdf")) {
  CAppUI::stepMessage(UI_MSG_OK, "Le fichier a été shrinké");
}
else {
  CAppUI::stepMessage(UI_MSG_ERROR, "Le fichier n'a pas été shrinké");
}