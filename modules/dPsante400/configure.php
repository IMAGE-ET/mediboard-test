<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$types = CMouvFactory::getTypes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("types", $types);
$smarty->display("configure.tpl");

?>