<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("types", CMouvFactory::getTypes());
$smarty->assign("modes", array_keys(CMouvFactory::$modes));
$smarty->display("configure.tpl");

?>