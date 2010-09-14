<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("childs_codable_class", CApp::getChildClasses("CCodable"));

$smarty->display("configure.tpl");

?>