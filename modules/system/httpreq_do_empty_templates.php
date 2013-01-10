<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

$paths = glob("tmp/templates_c/*/*");
foreach($paths as $tplPath) {
  CMbPath::remove($tplPath);
}

CAppUI::stepAjax("template-cache-removed", UI_MSG_OK, count($paths));